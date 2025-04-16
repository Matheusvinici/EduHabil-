<?php

namespace App\Http\Controllers;

use App\Models\TutoriaAcompanhamento;

use App\Models\TutoriaAvaliacao;
use App\Models\Escola;
use App\Models\User;
use Illuminate\Http\Request;

class TutoriaAcompanhamentoController extends Controller
{
    public function index(Request $request)
    {
        $query = TutoriaAcompanhamento::with(['avaliacao.escola', 'criterio', 'responsavel'])
            ->orderBy('prioridade', 'desc')
            ->orderBy('prazo');

        if ($request->escola_id) {
            $query->whereHas('avaliacao', function($q) use ($request) {
                $q->where('escola_id', $request->escola_id);
            });
        }

        if ($request->prioridade) {
            $query->where('prioridade', $request->prioridade);
        }

        $acompanhamentos = $query->paginate(20);

        // Resumo para o dashboard
        $resumo = [
            'alta' => TutoriaAcompanhamento::where('prioridade', 'alta')->count(),
            'media' => TutoriaAcompanhamento::where('prioridade', 'media')->count(),
            'baixa' => TutoriaAcompanhamento::where('prioridade', 'baixa')->count(),
        ];

        $escolas = Escola::orderBy('nome')->get();

        return view('tutoria_acompanhamento.index', compact('acompanhamentos', 'resumo', 'escolas'));
    }

    public function createFromEvaluation($avaliacaoId)
    {
        $avaliacao = TutoriaAvaliacao::with(['criterios', 'escola'])->findOrFail($avaliacaoId);
        $users = User::where('role', 'tutor')->orWhere('role', 'coordenador')->get();
        
        return view('tutoria.acompanhamento_create', compact('avaliacao', 'users'));
    }

   public function create($avaliacaoId)
{
    $avaliacao = TutoriaAvaliacao::with(['escola', 'tutor', 'criterios'])->findOrFail($avaliacaoId);
    $tutores = User::where('role', 'tutor')->orWhere('role', 'admin')->get();
    
    return view('tutoria.acompanhamento.create', compact('avaliacao', 'tutores'));
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

    foreach ($request->criterios as $criterioId) {
        TutoriaAcompanhamento::create([
            'avaliacao_id' => $request->avaliacao_id,
            'criterio_id' => $criterioId,
            'prioridade' => $request->prioridade,
            'acao_melhoria' => $request->acao_melhoria,
            'responsavel_id' => $request->responsavel_id,
            'prazo' => $request->prazo,
            'observacoes' => $request->observacoes,
            'status' => 'pendente'
        ]);
    }

    return redirect()->route('tutoria.acompanhamento.index')
        ->with('success', 'Acompanhamento criado com sucesso!');
}

    public function edit($id)
    {
        $acompanhamento = TutoriaAcompanhamento::findOrFail($id);
        $users = User::where('role', 'tutor')->orWhere('role', 'coordenador')->get();
        
        return view('tutoria.acompanhamento_edit', compact('acompanhamento', 'users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'prioridade' => 'sometimes|in:alta,media,baixa',
            'acao_melhoria' => 'sometimes|string|max:500',
            'responsavel_id' => 'sometimes|exists:users,id',
            'prazo' => 'sometimes|date',
            'status' => 'sometimes|in:pendente,em_andamento,concluido',
        ]);

        $acompanhamento = TutoriaAcompanhamento::findOrFail($id);
        $acompanhamento->update($request->all());

        return redirect()->route('tutoria.acompanhamento')
            ->with('success', 'Acompanhamento atualizado com sucesso!');
    }
}