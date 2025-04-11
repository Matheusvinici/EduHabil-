<?php

namespace App\Http\Controllers;

use App\Models\TutoriaAvaliacao;
use App\Models\Escola;
use App\Models\User;
use Illuminate\Http\Request;

class TutoriaAvaliacaoController extends Controller
{
    // Listar avaliações
    public function index()
    {
        $tutoria_avaliacoes = AvaliacaoTutoria::with(['tutor', 'escola'])->get();
        return view('tutoria_avaliacoes.index', compact('tutoria_avaliacoes'));
    }

    // Formulário de criação
    public function create()
{
    $tutores = User::where('role', 'tutor')->get();
    $escolas = Escola::all();
    $criterios = \App\Models\TutoriaAvaliacao::all(); // ← adiciona essa linha

    return view('tutoria_avaliacoes.create', compact('tutores', 'escolas', 'criterios'));
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
        return redirect()->route('tutoria_avaliacoes.index')->with('success', 'Avaliação registrada!');
    }

    // Formulário de edição
    public function edit(TutoriaAvaliacao $avaliacao)
    {
        $tutores = User::where('role', 'tutor')->get();
        $escolas = Escola::all();
        return view('tutoria_avaliacoes.edit', compact('avaliacao', 'tutores', 'escolas'));
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
        return redirect()->route('tutoria_avaliacoes.index')->with('success', 'Avaliação atualizada!');
    }

    // Excluir avaliação
    public function destroy(AvaliacaoTutoria $avaliacao)
    {
        $avaliacao->delete();
        return redirect()->route('tutoria_avaliacoes.index')->with('success', 'Avaliação removida!');
    }
}
