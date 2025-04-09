<?php

namespace App\Http\Controllers;

use App\Models\CriterioAvaliacao;
use Illuminate\Http\Request;

class CriterioAvaliacaoController extends Controller
{
    // Listar critérios
    public function index()
    {
        $criterios = CriterioAvaliacao::all();
        return view('criterios.index', compact('criterios'));
    }

    // Formulário de criação
    public function create()
    {
    $tutores = User::where('role', 'tutor')->get();
    $escolas = Escola::all();
    $criterios = CriterioAvaliacao::all(); // traz os critérios

    return view('avaliacoes.create', compact('tutores', 'escolas', 'criterios'));
}

    // Salvar critério
    public function store(Request $request)
    {
        $request->validate([
            'categoria' => 'required|string|max:50',
            'descricao' => 'required|string|max:100',
        ]);

        CriterioAvaliacao::create($request->all());
        return redirect()->route('criterios.index')->with('success', 'Critério criado!');
    }

    // Formulário de edição
    public function edit(CriterioAvaliacao $criterio)
    {
        return view('criterios.edit', compact('criterio'));
    }

    // Atualizar critério
    public function update(Request $request, CriterioAvaliacao $criterio)
    {
        $request->validate([
            'categoria' => 'required|string|max:50',
            'descricao' => 'required|string|max:100',
        ]);

        $criterio->update($request->all());
        return redirect()->route('criterios.index')->with('success', 'Critério atualizado!');
    }

    // Excluir critério
    public function destroy(CriterioAvaliacao $criterio)
    {
        $criterio->delete();
        return redirect()->route('criterios.index')->with('success', 'Critério removido!');
    }
}
