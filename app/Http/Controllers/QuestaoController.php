<?php

namespace App\Http\Controllers;

use App\Models\Questao;
use App\Models\Ano;
use App\Models\Disciplina;
use App\Models\Habilidade;


use Illuminate\Http\Request;

class QuestaoController extends Controller
{
    // Método para exibir o formulário de criação
    public function create()
    {
        $anos = Ano::all();
        $disciplinas = Disciplina::all();
        $habilidades = Habilidade::all();
       
        return view('questoes.create', compact('anos', 'disciplinas', 'habilidades'));
    }
    // Método para salvar a nova questão
    public function store(Request $request)
{
    // Validação dos dados
    $request->validate([
        'ano_id' => 'required|exists:anos,id',
        'disciplina_id' => 'required|exists:disciplinas,id',
        'habilidade_id' => 'required|exists:habilidades,id',
        'enunciado' => 'required',
        'alternativa_a' => 'required',
        'alternativa_b' => 'required',
        'alternativa_c' => 'required',
        'alternativa_d' => 'required',
        'resposta_correta' => 'required|in:A,B,C,D',
    ]);

    // Cria a questão
    Questao::create([
        'ano_id' => $request->ano_id,
        'disciplina_id' => $request->disciplina_id,
        'habilidade_id' => $request->habilidade_id,
        'enunciado' => $request->enunciado,
        'alternativa_a' => $request->alternativa_a,
        'alternativa_b' => $request->alternativa_b,
        'alternativa_c' => $request->alternativa_c,
        'alternativa_d' => $request->alternativa_d,
        'resposta_correta' => $request->resposta_correta,
    ]);

    return redirect()->route('questoes.index')->with('success', 'Questão cadastrada com sucesso!');
}

    // Método para listar todas as questões
    public function index()
    {
        $questoes = Questao::all();
        return view('questoes.index', compact('questoes'));
    }
            public function show($id)
        {
            $questao = Questao::findOrFail($id);
            return view('questoes.show', compact('questao'));
        }


        public function edit(Questao $questao)
        
        {
            $anos = Ano::all();
            $disciplinas = Disciplina::all();
            $habilidades = Habilidade::all();

            return view('questoes.edit', compact('questao', 'anos', 'disciplinas', 'habilidades'));
        }

        public function update(Request $request, Questao $questao)
        {
            $request->validate([
                'ano_id' => 'required|exists:anos,id',
                'disciplina_id' => 'required|exists:disciplinas,id',
                'habilidade_id' => 'required|exists:habilidades,id',
                'resposta_correta' => 'required|in:A,B,C,D',
                'enunciado' => 'required|string',
                'alternativa_a' => 'required|string',
                'alternativa_b' => 'required|string',
                'alternativa_c' => 'required|string',
                'alternativa_d' => 'required|string',
            ]);
        
            $questao->update($request->all());
        
            return redirect()->route('questoes.index')->with('success', 'Questão atualizada com sucesso!');
        }
       
     
        // Exclui uma questão
        public function destroy(Questao $questao)
        {
            $questao->delete();
            return redirect()->route('questoes.index')->with('success', 'Questão excluída com sucesso!');
        }
        
}
