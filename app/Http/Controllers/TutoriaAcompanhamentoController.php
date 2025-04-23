<?php

namespace App\Http\Controllers;

use App\Models\TutoriaAcompanhamento;
use App\Models\TutoriaAvaliacao;
use App\Models\Escola;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TutoriaAcompanhamentoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
    
        $query = TutoriaAcompanhamento::with(['avaliacao.escola', 'criterio', 'responsavel'])
            ->orderBy('prioridade', 'desc')
            ->orderBy('prazo');
    
        // Se o usuário tem escola vinculada, filtra apenas os acompanhamentos dessa escola
        if ($user->escola_id) {
            $query->whereHas('avaliacao', fn($q) => $q->where('escola_id', $user->escola_id));
        }
    
        // Filtros adicionais via request
        if ($request->escola_id) {
            $query->whereHas('avaliacao', fn($q) => $q->where('escola_id', $request->escola_id));
        }
    
        if ($request->prioridade) {
            $query->where('prioridade', $request->prioridade);
        }
    
        if ($request->status) {
            $query->where('status', $request->status);
        }
    
        $acompanhamentos = $query->paginate(20);
        $escolas = Escola::orderBy('nome')->get();
    
        // Resumo para cards
        $resumo = [
            'total' => TutoriaAcompanhamento::count(),
            'alta' => TutoriaAcompanhamento::where('prioridade', 'alta')->count(),
            'media' => TutoriaAcompanhamento::where('prioridade', 'media')->count(),
            'baixa' => TutoriaAcompanhamento::where('prioridade', 'baixa')->count(),
            'pendente' => TutoriaAcompanhamento::where('status', 'pendente')->count(),
            'andamento' => TutoriaAcompanhamento::where('status', 'em_andamento')->count(),
            'concluido' => TutoriaAcompanhamento::where('status', 'concluido')->count(),
        ];
    
        return view('tutoria.acompanhamento.index', compact('acompanhamentos', 'escolas', 'resumo'));
    }
    

    public function escola(Escola $escola)
{
    $acompanhamentos = TutoriaAcompanhamento::with(['avaliacao', 'criterio', 'responsavel', 'historico.user'])
        ->whereHas('avaliacao', fn($q) => $q->where('escola_id', $escola->id))
        ->orderBy('prioridade', 'desc')
        ->orderBy('prazo')
        ->get();

    // Certifique-se de passar a avaliação
    $avaliacao = TutoriaAvaliacao::where('escola_id', $escola->id)->first();

    // Adicionando a variável $users, que irá trazer os tutores ou coordenadores
    $users = User::whereIn('role', ['tutor', 'coordenador'])->get();

    return view('tutoria.acompanhamento.escola', compact('acompanhamentos', 'escola', 'avaliacao', 'users'));
}

    

    public function createFromEvaluation($avaliacaoId)
    {
        $avaliacao = TutoriaAvaliacao::with(['criterios' => function($query) {
            $query->wherePivot('nota', '<=', 4);
        }, 'escola'])->findOrFail($avaliacaoId);

        $users = User::whereIn('role', ['tutor', 'coordenador'])->get();

        return view('tutoria.acompanhamento.create', compact('avaliacao', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'avaliacao_id' => 'required|exists:tutoria_avaliacoes,id',
            'criterios' => 'required|array',
            'criterios.*' => 'exists:tutoria_criterios,id',
            'prioridade' => 'required|in:alta,media,baixa',
            'acao_melhoria' => 'required|string|max:500',
            'responsavel_id' => 'required|exists:users,id',
            'prazo' => 'required|date|after_or_equal:today',
            'observacoes' => 'nullable|string'
        ]);

        DB::transaction(function() use ($request) {
            foreach ($request->criterios as $criterioId) {
                $acompanhamento = TutoriaAcompanhamento::create([
                    'avaliacao_id' => $request->avaliacao_id,
                    'criterio_id' => $criterioId,
                    'prioridade' => $request->prioridade,
                    'acao_melhoria' => $request->acao_melhoria,
                    'responsavel_id' => $request->responsavel_id,
                    'prazo' => $request->prazo,
                    'observacoes' => $request->observacoes,
                    'status' => 'pendente'
                ]);

                // Registrar histórico
                $acompanhamento->historico()->create([
                    'user_id' => auth()->id(),
                    'acao' => 'Criação do acompanhamento',
                    'detalhes' => "Ação de melhoria: {$request->acao_melhoria}",
                    'data' => now()
                ]);
            }
        });

        return redirect()->route('tutoria.acompanhamento.index')
            ->with('success', 'Acompanhamento(s) criado(s) com sucesso!');
    }

    public function edit($id)
    {
        $acompanhamento = TutoriaAcompanhamento::with(['avaliacao', 'criterio', 'responsavel', 'historico.user'])
            ->findOrFail($id);
            
        $users = User::whereIn('role', ['tutor', 'coordenador'])->get();

        return view('tutoria.acompanhamento.edit', compact('acompanhamento', 'users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'prioridade' => 'required|in:alta,media,baixa',
            'acao_melhoria' => 'required|string|max:500',
            'responsavel_id' => 'required|exists:users,id',
            'prazo' => 'required|date',
            'status' => 'required|in:pendente,em_andamento,concluido',
            'observacoes' => 'nullable|string'
        ]);

        $acompanhamento = TutoriaAcompanhamento::findOrFail($id);
        
        $acompanhamento->update([
            'prioridade' => $request->prioridade,
            'acao_melhoria' => $request->acao_melhoria,
            'responsavel_id' => $request->responsavel_id,
            'prazo' => $request->prazo,
            'status' => $request->status,
            'observacoes' => $request->observacoes
        ]);

        // Registrar histórico se houve mudança de status
        if ($acompanhamento->wasChanged('status')) {
            $acompanhamento->historico()->create([
                'user_id' => auth()->id(),
                'acao' => 'Atualização de status',
                'detalhes' => "Status alterado para: " . ucfirst($request->status),
                'data' => now()
            ]);
        }

        return redirect()->route('tutoria.acompanhamento.index')
            ->with('success', 'Acompanhamento atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $acompanhamento = TutoriaAcompanhamento::findOrFail($id);
        $acompanhamento->historico()->delete();
        $acompanhamento->delete();

        return redirect()->route('tutoria.acompanhamento.index')
            ->with('success', 'Acompanhamento removido com sucesso!');
    }
}