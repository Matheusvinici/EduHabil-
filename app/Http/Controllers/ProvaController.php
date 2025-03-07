<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prova;
use App\Models\Ano;
use App\Models\Disciplina;
use App\Models\Unidade;
use App\Models\Habilidade;
use App\Models\Questao;

class ProvaController extends Controller
{
    public function create()
    {
        return view('provas.create', [
            'anos' => Ano::all(),
            'disciplinas' => Disciplina::all(),
            'unidades' => Unidade::all(),
            'habilidades' => Habilidade::all()

        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ano_id' => 'required|exists:anos,id',
            'disciplina_id' => 'required|exists:disciplinas,id',
            'unidade_id' => 'required|exists:unidades,id',
            'habilidade_id' => 'required|exists:habilidades,id',
            'nome' => 'required|string|max:255',
            'data' => 'required|date',
            'observacoes' => 'nullable|string',
        ]);
    
        // Cria a prova
        $prova = Prova::create([
            'user_id' => auth()->id(), // Professor que criou a prova
            'ano_id' => $request->ano_id,
            'disciplina_id' => $request->disciplina_id,
            'unidade_id' => $request->unidade_id,
            'habilidade_id' => $request->habilidade_id,
            'nome' => $request->nome,
            'data' => $request->data,
            'observacoes' => $request->observacoes,
        ]);
    
        // Seleciona 10 questões aleatórias com base nos critérios
        $questoes = Questao::where('ano_id', $request->ano_id)
            ->where('disciplina_id', $request->disciplina_id)
            ->where('unidade_id', $request->unidade_id)
            ->where('habilidade_id', $request->habilidade_id)
            ->inRandomOrder()
            ->limit(10)
            ->get();
    
        // Associa as questões à prova
        $prova->questoes()->attach($questoes->pluck('id'));
    
        return redirect()->route('provas.index')->with('success', 'Prova criada com sucesso!');
    }

    public function index()
    {
        $provas = Prova::where('user_id', auth()->id())->get();
        return view('provas.index', compact('provas'));
    }

    public function gerarPDF(Prova $prova)
    {
        $user = auth()->user(); // Pega o usuário logado
        $pdf = app('dompdf.wrapper');
    
        // Carrega a view e passa as informações necessárias
        $pdf->loadView('provas.pdf', [
            'prova' => $prova,
            'user' => $user
        ]);
    
        // Gera o PDF e o faz o download
        return $pdf->download('prova_' . $prova->id . '.pdf');
    }
    
}
