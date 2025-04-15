<?php

namespace App\Http\Controllers;

use App\Models\TutoriaAvaliacao;
use App\Models\TutoriaCriterio;
use App\Models\Escola;
use App\Models\User;
use Illuminate\Http\Request;

class TutoriaAvaliacaoController extends Controller
{
    public function index(Request $request)
    {
        $query = TutoriaAvaliacao::with(['tutor', 'escola']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tutor', fn($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhereHas('escola', fn($q) => $q->where('nome', 'like', "%$search%"))
                  ->orWhere('data_visita', 'like', "%$search%");
        }

        $tutoria_avaliacoes = $query->orderByDesc('data_visita')->paginate(10);
        return view('tutoria_avaliacoes.index', compact('tutoria_avaliacoes'));
    }

    public function create()
    {
        $tutores = User::where('role', 'tutor')->get();
        $escolas = Escola::all();
        $criterios = TutoriaCriterio::all();

        return view('tutoria_avaliacoes.create', compact('tutores', 'escolas', 'criterios'));
    }

    public function store(Request $request)
    {
    $avaliacao = TutoriaAvaliacao::create([
        'tutor_id' => auth()->id(),
        'escola_id' => $request->escola_id,
        'data_visita' => $request->data_visita,
        'observacoes' => $request->observacoes,
    ]);

    // Correção: pegando do array 'avaliacoes'
    if ($request->has('avaliacoes')) {
        foreach ($request->avaliacoes as $criterio_id => $nota) {
            $avaliacao->criterios()->attach($criterio_id, ['nota' => $nota]);
        }
    }

    return redirect()->route('tutoria_avaliacoes.index')->with('success', 'Avaliação registrada com sucesso.');
    }


    public function edit(TutoriaAvaliacao $tutoria_avaliacao)
    {
        $tutores = User::where('role', 'tutor')->get();
        $escolas = Escola::all();
        $criterios = TutoriaCriterio::all();

        return view('tutoria_avaliacoes.edit', compact('tutoria_avaliacao', 'tutores', 'escolas', 'criterios'));
    }

    public function update(Request $request, TutoriaAvaliacao $tutoria_avaliacao)
    {
        $request->validate([
            'tutor_id' => 'required|exists:users,id',
            'escola_id' => 'required|exists:escolas,id',
            'data_visita' => 'required|date',
        ]);

        $tutoria_avaliacao->update($request->only('tutor_id', 'escola_id', 'data_visita'));

        // Atualizar as notas dos critérios, se houver
        if ($request->has('avaliacoes')) {
            $tutoria_avaliacao->avaliacoesCriterios()->delete();

            foreach ($request->avaliacoes as $criterio_id => $nota) {
                $tutoria_avaliacao->avaliacoesCriterios()->create([
                    'criterio_id' => $criterio_id,
                    'nota' => $nota
                ]);
            }
        }

        return redirect()->route('tutoria_avaliacoes.index')->with('message', 'Avaliação atualizada!')->with('type', 'success');
    }

    
    public function destroy($id)
    {
        // Encontra a avaliação
        $avaliacao = TutoriaAvaliacao::findOrFail($id);
        
        // Primeiro deleta os registros da tabela pivô (avaliacao_criterios)
        $avaliacao->avaliacaoCriterios()->delete();
        
        // Depois deleta a avaliação em si
        $avaliacao->delete();
        
        return redirect()->route('tutoria_avaliacoes.index')
            ->with('success', 'Avaliação deletada com sucesso!');
    }
    
    
}
