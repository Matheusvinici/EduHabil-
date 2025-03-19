<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Simulado;
use App\Models\Ano;
use App\Models\Disciplina;
use App\Models\Habilidade;
use App\Models\Questao;
use Illuminate\Support\Facades\Auth;

class SimuladoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $simulados = Simulado::all();
        } elseif ($user->role === 'professor') {
            $simulados = Simulado::where('user_id', $user->id)->get();
        } else {
            abort(403, 'Acesso não autorizado.');
        }

        return view('simulados.index', compact('simulados'));
    }

    public function create()
    {
        $anos = Ano::all();
        $disciplinas = Disciplina::all();
        $habilidades = Habilidade::all();

        return view('simulados.create', compact('anos', 'disciplinas', 'habilidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ano_id' => 'required|exists:anos,id',
            'disciplina_id' => 'required|exists:disciplinas,id',
            'habilidade_id' => 'required|exists:habilidades,id',
            'nome' => 'required|string|max:255',
            'data' => 'required|date',
            'observacoes' => 'nullable|string',
        ]);

        $simulado = Simulado::create([
            'user_id' => auth()->id(),
            'ano_id' => $request->ano_id,
            'disciplina_id' => $request->disciplina_id,
            'habilidade_id' => $request->habilidade_id,
            'nome' => $request->nome,
            'data' => $request->data,
            'observacoes' => $request->observacoes,
        ]);

        $questoes = Questao::where('ano_id', $request->ano_id)
            ->where('disciplina_id', $request->disciplina_id)
            ->where('habilidade_id', $request->habilidade_id)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        if ($questoes->count() < 10) {
            return redirect()->back()->with('error', 'Não há questões suficientes para criar o simulado.');
        }

        $simulado->questoes()->attach($questoes->pluck('id'));

        return redirect()->route('simulados.index')->with('success', 'Simulado criado com sucesso!');
    }

    public function destroy(Simulado $simulado)
    {
        $simulado->delete();
        return redirect()->route('simulados.index')->with('success', 'Simulado excluído com sucesso!');
    }
}
