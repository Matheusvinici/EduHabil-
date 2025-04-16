<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use App\Models\TutoriaAvaliacao;
use Illuminate\Http\Request;

class RelatorioTutoriaController extends Controller
{
    public function index()
    {
        $escolas = Escola::with(['avaliacoes' => function($query) {
            $query->orderBy('data_visita', 'desc');
        }])->get();

        return view('tutoria.relatorios.index', compact('escolas'));
    }

    public function gerarRelatorio(Request $request)
    {
        $request->validate([
            'escola_id' => 'required|exists:escolas,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after:data_inicio'
        ]);

        $dados = TutoriaAvaliacao::with(['criterios', 'acompanhamentos'])
                    ->where('escola_id', $request->escola_id)
                    ->whereBetween('data_visita', [$request->data_inicio, $request->data_fim])
                    ->get()
                    ->map(function($avaliacao) {
                        return [
                            'data' => $avaliacao->data_visita->format('d/m/Y'),
                            'media' => $avaliacao->criterios->avg('pivot.nota'),
                            'pontos_criticos' => $avaliacao->acompanhamentos->count(),
                            'concluidos' => $avaliacao->acompanhamentos->where('status', 'concluido')->count()
                        ];
                    });

        return response()->json($dados);
    }
}