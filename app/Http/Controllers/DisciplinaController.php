<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use Illuminate\Http\Request;

class DisciplinaController extends Controller
{
    /**
     * Exibe todas as disciplinas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $disciplinas = Disciplina::all();
        return view('disciplinas.index', compact('disciplinas'));
    }

    /**
     * Exibe o formulário para criar uma nova disciplina.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('disciplinas.create');
    }

    /**
     * Armazena uma nova disciplina no banco de dados.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        Disciplina::create($request->all());

        return redirect()->route('disciplinas.index')->with('success', 'Disciplina criada com sucesso!');
    }

    /**
     * Exibe o formulário para editar uma disciplina.
     *
     * @param \App\Models\Disciplina $disciplina
     * @return \Illuminate\View\View
     */
    public function edit(Disciplina $disciplina)
    {
        return view('disciplinas.edit', compact('disciplina'));
    }

    /**
     * Atualiza uma disciplina no banco de dados.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Disciplina $disciplina
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Disciplina $disciplina)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $disciplina->update($request->all());

        return redirect()->route('disciplinas.index')->with('success', 'Disciplina atualizada com sucesso!');
    }

    /**
     * Remove uma disciplina do banco de dados.
     *
     * @param \App\Models\Disciplina $disciplina
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Disciplina $disciplina)
    {
        $disciplina->delete();
        return redirect()->route('disciplinas.index')->with('success', 'Disciplina removida com sucesso!');
    }
}
