<?php
namespace App\Http\Controllers;

use App\Models\NotaAvaliacao;
use App\Models\TutoriaAvaliacao; // ATUALIZADO
use App\Models\TutoriaCriterio; // ATUALIZADO
use Illuminate\Http\Request;

class NotaAvaliacaoController extends Controller
{
    // Listar todas as notas (opcional)
    public function index()
    {
        $notas = NotaAvaliacao::with(['avaliacao.escola', 'avaliacao.tutor', 'criterio'])->get();
        return view('notas.index', compact('notas'));
    }

    // Mostrar detalhes de uma nota específica
    public function show(NotaAvaliacao $nota)
    {
        return view('notas.show', compact('nota'));
    }

    // Formulário de criação (associado a uma avaliação existente)
    public function create(TutoriaAvaliacao $avaliacao)
    {
        $avaliacao->load('escola');
        $criterios = TutoriaCriterio::all();
        return view('notas.create', compact('avaliacao', 'criterios'));
    }

    // ATUALIZADO
    public function store(Request $request, TutoriaAvaliacao $avaliacao)
    {
        $request->validate([
            'criterio_id' => 'required|exists:tutoria_criterios,id', // ATUALIZADO
            'nota' => 'required|integer|between:1,5',
        ]);

        // Verifica se o critério já foi avaliado
        $notaExistente = NotaAvaliacao::where('avaliacao_id', $avaliacao->id)
            ->where('criterio_id', $request->criterio_id)
            ->exists();

        if ($notaExistente) {
            return back()->with('error', 'Este critério já foi avaliado nesta visita!');
        }

        NotaAvaliacao::create([
            'avaliacao_id' => $avaliacao->id,
            'criterio_id' => $request->criterio_id,
            'nota' => $request->nota,
        ]);

        return redirect()->route('tutoria.avaliacoes.show', $avaliacao->id)->with('success', 'Nota adicionada!');
    }

    // Formulário de edição
    public function edit(NotaAvaliacao $nota)
    {
        $criterios = CriterioAvaliacao::all();
        return view('notas.edit', compact('nota', 'criterios'));
    }

    // Atualizar nota
    public function update(Request $request, NotaAvaliacao $nota)
    {
        $request->validate([
            'criterio_id' => 'required|exists:criterios_avaliacao,id',
            'nota' => 'required|integer|between:1,5',
        ]);

        $nota->update($request->all());
        return redirect()->route('avaliacoes.show', $nota->avaliacao_id)->with('success', 'Nota atualizada!');
    }

    // Excluir nota
    public function destroy(NotaAvaliacao $nota)
    {
        $avaliacaoId = $nota->avaliacao_id;
        $nota->delete();
        return redirect()->route('avaliacoes.show', $avaliacaoId)->with('success', 'Nota removida!');
    }
}