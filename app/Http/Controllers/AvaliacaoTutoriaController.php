<?php

namespace App\Http\Controllers;

use App\Models\AvaliacaoTutoria;
use App\Models\AvaliacaoCriterio;
use App\Models\CriterioAvaliacao;
use App\Models\Escola;
use App\Models\User;
use Illuminate\Http\Request;

class AvaliacaoTutoriaController extends Controller
{
    public function index()
    {
        $avaliacoes = AvaliacaoTutoria::with(['tutor', 'escola'])->get();
        return view('avaliacoes.index', compact('avaliacoes'));
    }

    public function create()
    {
        $tutores = User::where('role', 'tutor')->get();
        $escolas = Escola::all();
        $criterios = CriterioAvaliacao::all();

        return view('avaliacoes.create', compact('tutores', 'escolas', 'criterios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tutor_id' => 'required|exists:users,id',
            'escola_id' => 'required|exists:escolas,id',
            'data_visita' => 'required|date',
            'avaliacoes' => 'required|array',
        ]);

        $avaliacao = AvaliacaoTutoria::create([
            'tutor_id' => $request->tutor_id,
            'escola_id' => $request->escola_id,
            'data_visita' => $request->data_visita,
            'observacoes' => $request->observacoes,
        ]);

        foreach ($request->avaliacoes as $criterio_id => $nota) {
            AvaliacaoCriterio::create([
                'avaliacao_tutoria_id' => $avaliacao->id,
                'criterio_avaliacao_id' => $criterio_id,
                'nota' => $nota,
            ]);
        }

        return redirect()->route('avaliacoes.index')->with('success', 'Avaliação registrada com sucesso!');
    }

    public function edit(AvaliacaoTutoria $avaliacao)
    {
        $tutores = User::where('role', 'tutor')->get();
        $escolas = Escola::all();
        return view('avaliacoes.edit', compact('avaliacao', 'tutores', 'escolas'));
    }

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

    public function destroy(AvaliacaoTutoria $avaliacao)
    {
        $avaliacao->delete();
        return redirect()->route('avaliacoes.index')->with('success', 'Avaliação removida!');
    }
}
