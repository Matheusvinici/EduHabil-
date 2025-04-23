<?php

namespace App\Http\Controllers;

use App\Models\TutoriaAvaliacao;
use App\Models\TutoriaCriterio;
use App\Models\Escola;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TutoriaAvaliacaoController extends Controller
{
    public function index(Request $request)
    {
        $query = TutoriaAvaliacao::with(['tutor', 'escola', 'criterios'])
            ->addSelect(['media' => function($query) {
                $query->select(DB::raw('AVG(nota)'))
                    ->from('avaliacao_criterios')
                    ->whereColumn('avaliacao_tutoria_id', 'tutoria_avaliacoes.id')
                    ->groupBy('avaliacao_tutoria_id');
            }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tutor', fn($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhereHas('escola', fn($q) => $q->where('nome', 'like', "%$search%"))
                  ->orWhere('data_visita', 'like', "%$search%");
        }

        $tutoria_avaliacoes = $query->orderByDesc('data_visita')->paginate(10);
        return view('tutoria.avaliacoes.index', compact('tutoria_avaliacoes'));
    }

    public function dashboard()
    {
        // Obter todas as escolas com cálculo dinâmico da média
        $escolas = Escola::with(['avaliacoes' => function($query) {
            $query->select('id', 'escola_id', 'data_visita', 'tutor_id')
                ->with(['tutor', 'criterios' => function($q) {
                    $q->select('avaliacao_criterios.avaliacao_tutoria_id', 
                               'avaliacao_criterios.nota',
                               'tutoria_criterios.categoria');
                }]);
        }])
        ->get()
        ->map(function($escola) {
            // Calcular média geral da escola
            $escola->media_avaliacao = $escola->avaliacoes->flatMap(function($avaliacao) {
                return $avaliacao->criterios->pluck('nota');
            })->avg() ?? 0;
            
            // Última avaliação
            $escola->ultima_avaliacao = $escola->avaliacoes->sortByDesc('data_visita')->first();
            
            return $escola;
        });

        // Filtrar por quadrantes
        $escolasVermelho = $escolas->filter(fn($e) => $e->media_avaliacao >= 1 && $e->media_avaliacao <= 4);
        $escolasAmarelo = $escolas->filter(fn($e) => $e->media_avaliacao >= 5 && $e->media_avaliacao <= 6);
        $escolasVerde = $escolas->filter(fn($e) => $e->media_avaliacao >= 7 && $e->media_avaliacao <= 8);
        $escolasAzul = $escolas->filter(fn($e) => $e->media_avaliacao >= 9 && $e->media_avaliacao <= 10);

        return view('tutoria.dashboard', compact(
            'escolasVermelho',
            'escolasAmarelo',
            'escolasVerde',
            'escolasAzul'
        ));
    }

    public function quadrante($quadrante)
    {
        $quadrantes = [
            'vermelho' => [1, 4],
            'amarelo' => [5, 6],
            'verde' => [7, 8],
            'azul' => [9, 10]
        ];

        if (!array_key_exists($quadrante, $quadrantes)) {
            abort(404);
        }

        [$min, $max] = $quadrantes[$quadrante];

        $escolas = Escola::with(['avaliacoes.criterios'])
            ->get()
            ->map(function($escola) {
                $escola->media_avaliacao = $escola->avaliacoes->flatMap(function($avaliacao) {
                    return $avaliacao->criterios->pluck('nota');
                })->avg() ?? 0;
                return $escola;
            })
            ->filter(function($escola) use ($min, $max) {
                return $escola->media_avaliacao >= $min && $escola->media_avaliacao <= $max;
            });

        $cores = [
            'vermelho' => 'danger',
            'amarelo' => 'warning',
            'verde' => 'success',
            'azul' => 'primary'
        ];
        $quadranteColor = $cores[$quadrante];

        return view('tutoria.quadrante', compact('escolas', 'quadrante', 'quadranteColor'));
    }

    public function create()
    {
        $tutores = User::where('role', 'tutor')->get();
        $escolas = Escola::all();
        $criterios = TutoriaCriterio::all();

        return view('tutoria.avaliacoes.create', compact('tutores', 'escolas', 'criterios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'escola_id' => 'required|exists:escolas,id',
            'data_visita' => 'required|date',
            'avaliacoes' => 'required|array',
            'avaliacoes.*' => 'required|integer|min:0|max:10'
        ]);

        // Criar avaliação
        $avaliacao = TutoriaAvaliacao::create([
            'tutor_id' => auth()->id(),
            'escola_id' => $request->escola_id,
            'data_visita' => $request->data_visita,
            'observacoes' => $request->observacoes
        ]);

        // Salvar as notas dos critérios na tabela avaliacao_criterios
        foreach ($request->avaliacoes as $criterio_id => $nota) {
            DB::table('avaliacao_criterios')->insert([
                'avaliacao_tutoria_id' => $avaliacao->id,
                'criterio_avaliacao_id' => $criterio_id,
                'nota' => $nota,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->route('tutoria.avaliacoes.index')
            ->with('success', 'Avaliação registrada com sucesso.');
    }

    public function edit(TutoriaAvaliacao $tutoria_avaliacao)
    {
        $tutores = User::where('role', 'tutor')->get();
        $escolas = Escola::all();
        $criterios = TutoriaCriterio::all();
        
        // Carregar as notas existentes
        $notas = DB::table('avaliacao_criterios')
            ->where('avaliacao_tutoria_id', $tutoria_avaliacao->id)
            ->pluck('nota', 'criterio_avaliacao_id');

        return view('tutoria.avaliacoes.edit', compact(
            'tutoria_avaliacao', 
            'tutores', 
            'escolas', 
            'criterios',
            'notas'
        ));
    }

    public function update(Request $request, TutoriaAvaliacao $tutoria_avaliacao)
    {
        $request->validate([
            'tutor_id' => 'required|exists:users,id',
            'escola_id' => 'required|exists:escolas,id',
            'data_visita' => 'required|date',
            'avaliacoes' => 'required|array',
            'avaliacoes.*' => 'required|integer|min:0|max:10'
        ]);

        $tutoria_avaliacao->update([
            'tutor_id' => $request->tutor_id,
            'escola_id' => $request->escola_id,
            'data_visita' => $request->data_visita,
            'observacoes' => $request->observacoes
        ]);

        // Atualizar notas na tabela avaliacao_criterios
        DB::table('avaliacao_criterios')
            ->where('avaliacao_tutoria_id', $tutoria_avaliacao->id)
            ->delete();

        foreach ($request->avaliacoes as $criterio_id => $nota) {
            DB::table('avaliacao_criterios')->insert([
                'avaliacao_tutoria_id' => $tutoria_avaliacao->id,
                'criterio_avaliacao_id' => $criterio_id,
                'nota' => $nota,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->route('tutoria.avaliacoes.index')
            ->with('success', 'Avaliação atualizada com sucesso!');
    }

    public function destroy($id)
    {
        DB::transaction(function() use ($id) {
            // Remover critérios associados
            DB::table('avaliacao_criterios')
                ->where('avaliacao_tutoria_id', $id)
                ->delete();
                
            // Remover a avaliação
            TutoriaAvaliacao::where('id', $id)->delete();
        });
        
        return redirect()->route('tutoria.avaliacoes.index')
            ->with('success', 'Avaliação deletada com sucesso!');
    }
}