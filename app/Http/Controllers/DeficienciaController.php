<?php

namespace App\Http\Controllers;

use App\Models\Deficiencia;
use Illuminate\Http\Request;

class DeficienciaController extends Controller
{
    public function index()
    {
        $deficiencias = Deficiencia::paginate(10); // 10 deficiências por página
        return view('deficiencias.index', compact('deficiencias'));
    }

    // Exibir formulário de criação
    public function create()
    {
        return view('deficiencias.create');
    }

    // Salvar nova deficiência
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        Deficiencia::create($request->all());
        return redirect()->route('deficiencias.index')->with('success', 'Deficiência criada com sucesso!');
    }

    // Exibir detalhes de uma deficiência
    public function show(Deficiencia $deficiencia)
    {
        return view('deficiencias.show', compact('deficiencia'));
    }

    // Exibir formulário de edição
    public function edit(Deficiencia $deficiencia)
    {
        return view('deficiencias.edit', compact('deficiencia'));
    }

    // Atualizar deficiência
    public function update(Request $request, Deficiencia $deficiencia)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        $deficiencia->update($request->all());
        return redirect()->route('deficiencias.index')->with('success', 'Deficiência atualizada com sucesso!');
    }

    // Excluir deficiência
    public function destroy(Deficiencia $deficiencia)
    {
        $deficiencia->delete();
        return redirect()->route('deficiencias.index')->with('success', 'Deficiência excluída com sucesso!');
    }
    public function caracteristicas(Deficiencia $deficiencia)
{
    return response()->json($deficiencia->caracteristicas);
}
}