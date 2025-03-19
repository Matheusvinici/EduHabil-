<?php

namespace App\Http\Controllers;

use App\Models\Caracteristica;
use App\Models\Deficiencia;
use Illuminate\Http\Request;

class CaracteristicaController extends Controller
{
    // Exibir lista de características
    public function index()
    {
        $caracteristicas = Caracteristica::with('deficiencia')->get();
        return view('caracteristicas.index', compact('caracteristicas'));
    }

    // Exibir formulário de criação
    public function create()
    {
        $deficiencias = Deficiencia::all();
        return view('caracteristicas.create', compact('deficiencias'));
    }

    // Salvar nova característica
    public function store(Request $request)
    {
        $request->validate([
            'deficiencia_id' => 'required|exists:deficiencias,id',
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        Caracteristica::create($request->all());
        return redirect()->route('caracteristicas.index')->with('success', 'Característica criada com sucesso!');
    }

    // Exibir detalhes de uma característica
    public function show(Caracteristica $caracteristica)
    {
        return view('caracteristicas.show', compact('caracteristica'));
    }

    // Exibir formulário de edição
    public function edit(Caracteristica $caracteristica)
    {
        $deficiencias = Deficiencia::all();
        return view('caracteristicas.edit', compact('caracteristica', 'deficiencias'));
    }

    // Atualizar característica
    public function update(Request $request, Caracteristica $caracteristica)
    {
        $request->validate([
            'deficiencia_id' => 'required|exists:deficiencias,id',
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        $caracteristica->update($request->all());
        return redirect()->route('caracteristicas.index')->with('success', 'Característica atualizada com sucesso!');
    }

    // Excluir característica
    public function destroy(Caracteristica $caracteristica)
    {
        $caracteristica->delete();
        return redirect()->route('caracteristicas.index')->with('success', 'Característica excluída com sucesso!');
    }
}