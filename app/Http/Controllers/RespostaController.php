<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resposta;
use App\Models\Prova;
use App\Models\Questao;

class RespostaController extends Controller
{
    // Exibe a lista de provas disponíveis para o aluno responder
    public function index()
    {
        // Busca todas as provas disponíveis
        $provas = Prova::withCount('questoes')->get();

        return view('respostas.index', compact('provas'));
    }

    // Exibe o formulário para responder uma prova específica
    public function create(Prova $prova)
    {
        // Carrega as questões da prova
        $questoes = $prova->questoes;
        return view('respostas.create', compact('prova', 'questoes'));
    }

    // Salva as respostas do aluno
    public function store(Request $request, Prova $prova)
    {
        // Validação das respostas
        $request->validate([
            'respostas' => 'required|array',
            'respostas.*' => 'required|in:A,B,C,D'
        ]);

        // Salva cada resposta do aluno
        foreach ($request->respostas as $questao_id => $resposta) {
            $questao = Questao::find($questao_id);
            $correta = ($questao->resposta_correta == $resposta);

            Resposta::create([
                'user_id' => auth()->id(),
                'prova_id' => $prova->id,
                'questao_id' => $questao_id,
                'resposta' => $resposta,
                'correta' => $correta,
            ]);
        }

        return redirect()->route('respostas.index')->with('success', 'Prova finalizada!');
    }

    // Exibe os detalhes de uma prova respondida
    public function show(Prova $prova)
    {
        // Busca as respostas do usuário autenticado para a prova específica
        $respostas = $prova->respostas()->where('user_id', auth()->id())->get();
        $acertos = $respostas->where('correta', true)->count();
        $total = $prova->questoes->count();

        return view('respostas.show', compact('prova', 'respostas', 'acertos', 'total'));
    }
}