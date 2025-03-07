<?php

namespace App\Http\Controllers;

use App\Models\Questao;
use App\Models\Ano;
use App\Models\Disciplina;
use App\Models\Habilidade;
use App\Models\Unidade;

use Illuminate\Http\Request;

class QuestaoController extends Controller
{
    // Método para exibir o formulário de criação
    public function create()
    {
        $anos = Ano::all();
        $disciplinas = Disciplina::all();
        $habilidades = Habilidade::all();
        $unidades = Unidade::all();

        return view('questoes.create', compact('anos', 'disciplinas', 'habilidades', 'unidades'));
    }

    // Método para salvar a nova questão
    public function store(Request $request)
{
    $request->validate([
        'ano_id' => 'required',
        'disciplina_id' => 'required',
        'habilidade_id' => 'required',
        'unidade_id' => 'required',
        'enunciado' => 'required',
        'alternativa_a' => 'required',
        'alternativa_b' => 'required',
        'alternativa_c' => 'required',
        'alternativa_d' => 'required',
        'resposta_correta' => 'required',
    ]);

    Questao::create([
        'ano_id' => $request->ano_id,
        'disciplina_id' => $request->disciplina_id,
        'habilidade_id' => $request->habilidade_id,
        'unidade_id' => $request->unidade_id,
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

    // Método para exibir o formulário de edição
    public function edit($id)
    {
        $questao = Questao::findOrFail($id);
        $anos = Ano::all();
        $disciplinas = Disciplina::all();
        $habilidades = Habilidade::all();
        $unidades = Unidade::all();

        return view('questoes.edit', compact('questao', 'anos', 'disciplinas', 'habilidades', 'unidades'));
    }

    // Método para atualizar a questão
    public function update(Request $request, $id)
    {
        $request->validate([
            'ano_id' => 'required',
            'disciplina_id' => 'required',
            'unidade_id' => 'required',

            'habilidade_id' => 'required',
            'enunciado' => 'required',
            'resposta_correta' => 'required',
        ]);

        $questao = Questao::findOrFail($id);
        $questao->update([
            'ano_id' => $request->ano_id,
            'disciplina_id' => $request->disciplina_id,
            'unidade_id' => $request->unidade_id,

            'habilidade_id' => $request->habilidade_id,
            'enunciado' => $request->enunciado,
            'resposta_correta' => $request->resposta_correta,
        ]);

        return redirect()->route('questoes.index')->with('success', 'Questão atualizada com sucesso!');
    }

    // Método para excluir a questão
    public function destroy($id)
    {
        $questao = Questao::findOrFail($id);
        $questao->delete();

        return redirect()->route('questoes.index')->with('success', 'Questão excluída com sucesso!');
    }
}
