<?php

namespace App\Http\Controllers;

use App\Models\Ano;
use Illuminate\Http\Request;

class AnoEscolarController extends Controller
{
    /**
     * Exibe todos os anos escolares.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Recupera todos os anos registrados
        $anos = Ano::all();
        return view('anos.index', compact('anos')); // Passa os anos para a view index
    }

    /**
     * Exibe o formulário para criar um novo ano escolar.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('anos.create'); // Exibe a tela para criar um novo ano
    }

    /**
     * Armazena um novo ano escolar no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Valida os dados recebidos
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        // Cria o ano escolar
        Ano::create($request->all());

        // Redireciona para a tela de listagem de anos
        return redirect()->route('anos.index')->with('success', 'Ano escolar criado com sucesso!');
    }

    /**
     * Exibe o formulário para editar um ano escolar.
     *
     * @param  \App\Models\Ano  $ano
     * @return \Illuminate\View\View
     */
   
    /**
     * Atualiza um ano escolar no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ano  $ano
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Ano $ano)
    {
        return view('anos.edit', compact('ano'));
    }
    
    public function update(Request $request, Ano $ano)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:anos,nome,'.$ano->id,
        ]);
    
        try {
            $ano->update($request->all());
            
            return redirect()->route('anos.index')
                   ->with('success', 'Ano escolar atualizado com sucesso!');
                   
        } catch (\Exception $e) {
            return redirect()->back()
                   ->with('error', 'Erro ao atualizar ano escolar: ' . $e->getMessage())
                   ->withInput();
        }
    }

    /**
     * Remove um ano escolar do banco de dados.
     *
     * @param  \App\Models\Ano  $ano
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Ano $ano)
    {
        // Deleta o ano escolar
        $ano->delete();

        // Redireciona para a tela de listagem de anos
        return redirect()->route('anos.index')->with('success', 'Ano escolar removido com sucesso!');
    }
}
