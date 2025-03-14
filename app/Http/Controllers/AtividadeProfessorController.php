<?php

namespace App\Http\Controllers;

use App\Models\AtividadeProfessor;
use App\Models\Atividade;
use App\Models\Ano;
use App\Models\Disciplina;
use App\Models\Habilidade;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AtividadeProfessorController extends Controller
{
    public function index()
    {
        $atividadesProfessores = AtividadeProfessor::where('professor_id', auth()->id())->get();
        return view('atividades_professores.index', compact('atividadesProfessores'));
    }

    public function create()
    {
        // Busca todas as disciplinas, anos e habilidades
        $disciplinas = Disciplina::all();
        $anos = Ano::all();
        $habilidades = Habilidade::all();

        // Passa as variáveis para a view
        return view('atividades_professores.create', compact('disciplinas', 'anos', 'habilidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
            'ano_id' => 'required|exists:anos,id',
            'habilidade_id' => 'required|exists:habilidades,id',
        ]);
    
        // Busca uma atividade aleatória com base nos filtros
        $atividade = Atividade::where('disciplina_id', $request->disciplina_id)
            ->where('ano_id', $request->ano_id)
            ->where('habilidade_id', $request->habilidade_id)
            ->inRandomOrder()
            ->first();
    
        if ($atividade) {
            AtividadeProfessor::create([
                'professor_id' => auth()->id(),
                'atividade_id' => $atividade->id,
            ]);
    
            return redirect()->route('atividades_professores.index')->with('success', 'Atividade gerada com sucesso!');
        }
    
        return redirect()->back()->with('error', 'Nenhuma atividade encontrada com os filtros selecionados.');
    }

    public function show($id)
    {
        $atividadeProfessor = AtividadeProfessor::with('atividade')->findOrFail($id);
        return view('atividades_professores.show', compact('atividadeProfessor'));
    }

    public function destroy(AtividadeProfessor $atividadeProfessor)
    {
        $atividadeProfessor->delete();
        return redirect()->route('atividades_professores.index')->with('success', 'Atividade removida com sucesso!');
    }
    public function downloadPdf($id)
{
    // Busca a atividade do professor
    $atividadeProfessor = AtividadeProfessor::findOrFail($id);

    // Gera o PDF
    $pdf = Pdf::loadView('atividades_professores.pdf', compact('atividadeProfessor'));

    // Define o nome do arquivo PDF
    $filename = 'atividade_' . $atividadeProfessor->id . '.pdf';

    // Faz o download do PDF
    return $pdf->download($filename);
}
}
