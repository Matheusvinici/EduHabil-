<?php

namespace App\Http\Controllers;

use App\Models\AvaliacaoTutoria;
use App\Models\Escola;
use App\Models\User;
use Illuminate\Http\Request;

class AvaliacaoTutoriaController extends Controller
{
    // Listar avaliações
    public function index()
    {
        $avaliacoes = AvaliacaoTutoria::with(['tutor', 'escola'])->get();
        return view('avaliacoes.index', compact('avaliacoes'));
    }

    // Formulário de criação
    public function create()
    {
        $tutores = User::where('role', 'tutor')->get();
        $escolas = Escola::all();
        return view('avaliacoes.create', compact('tutores', 'escolas'));
    }

    // Salvar avaliação
    public function store(Request $request)
    {
        $request->validate([
            'tutor_id' => 'required|exists:users,id',
            'escola_id' => 'required|exists:escolas,id',
            'data_visita' => 'required|date',
        ]);

        AvaliacaoTutoria::create($request->all());
        return redirect()->route('avaliacoes.index')->with('success', 'Avaliação registrada!');
    }

    // Formulário de edição
    public function edit(AvaliacaoTutoria $avaliacao)
    {
        $tutores = User::where('role', 'tutor')->get();
        $escolas = Escola::all();
        return view('avaliacoes.edit', compact('avaliacao', 'tutores', 'escolas'));
    }

    // Atualizar avaliação
    public function update(Request $request, AvaliacaoTutoria $avaliacao)
    {
        $request->validate([
            'tutor_id' => 'required|exists:users,id',
            'escola_id' => 'required|exists:escolas,id',
            'data_visita' => 'required|date',
        ]);

        $avaliacao->update($request->all());
        return redirect()->route('avaliacoes.index')->with('success', 'Avaliação atualizada!');
    }

    // Excluir avaliação
    public function destroy(AvaliacaoTutoria $avaliacao)
    {
        $avaliacao->delete();
        return redirect()->route('avaliacoes.index')->with('success', 'Avaliação removida!');
    }
}
