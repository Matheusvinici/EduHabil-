<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use Illuminate\Http\Request;

class EscolaController extends Controller
{
    /**
     * Exibe a listagem de escolas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $escolas = Escola::all();
        return view('escolas.index', compact('escolas'));
    }

    /**
     * Exibe o formulário de criação de escolas.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('escolas.create');
    }

    /**
     * Armazena uma nova escola no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'codigo_escola' => 'required|string|unique:escolas,codigo_escola',
        ]);

        Escola::create($request->all());

        return redirect()->route('escolas.index')
                         ->with('success', 'Escola cadastrada com sucesso!');
    }

    /**
     * Exibe os detalhes de uma escola.
     *
     * @param  \App\Models\Escola  $escola
     * @return \Illuminate\Http\Response
     */
    public function show(Escola $escola)
    {
        return view('escolas.show', compact('escola'));
    }

    /**
     * Exibe o formulário de edição de uma escola.
     *
     * @param  \App\Models\Escola  $escola
     * @return \Illuminate\Http\Response
     */
    public function edit(Escola $escola)
    {
        return view('escolas.edit', compact('escola'));
    }

    /**
     * Atualiza uma escola no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Escola  $escola
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Escola $escola)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'codigo_escola' => 'required|string|unique:escolas,codigo_escola,' . $escola->id,
        ]);

        $escola->update($request->all());

        return redirect()->route('escolas.index')
                         ->with('success', 'Escola atualizada com sucesso!');
    }

    /**
     * Remove uma escola do banco de dados.
     *
     * @param  \App\Models\Escola  $escola
     * @return \Illuminate\Http\Response
     */
    public function destroy(Escola $escola)
    {
        $escola->delete();

        return redirect()->route('escolas.index')
                         ->with('success', 'Escola excluída com sucesso!');
    }
}