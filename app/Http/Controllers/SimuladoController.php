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
        // Carregar os dados necessários para a view
        $anos = Ano::all();
        $disciplinas = Disciplina::all();
        $habilidades = Habilidade::all();

        return view('simulados.create', compact('anos', 'disciplinas', 'habilidades'));
    }

    public function store(Request $request)
    {

    $request->validate([
        'ano_id' => 'required|exists:anos,id',
        'user_id' => 'required|exists:users,id',
        'disciplina_id' => 'required|exists:disciplinas,id',
        'nome' => 'required|string|max:255',
        'data' => 'required|date',
        'observacoes' => 'nullable|string',
        'disciplinas' => 'required|array|min:1', // Valida que ao menos uma disciplina foi selecionada
        'habilidades' => 'required|array|min:1', // Valida que ao menos uma habilidade foi selecionada
    ]);

    // Criação do simulado
    $simulado = Simulado::create([
        'user_id' => auth()->id(),
        'ano_id' => $request->ano_id,
        'disciplina_id' => $request->disciplina_id,
        'nome' => $request->nome,
        'data' => $request->data,
        'observacoes' => $request->observacoes,
    ]);

    // Associar disciplinas e habilidades ao simulado
    $disciplinas = $request->input('disciplinas');
    $habilidades = $request->input('habilidades');

    // Verificar se há questões para cada disciplina e habilidade
    foreach ($disciplinas as $index => $disciplina_id) {
        $habilidade_id = $habilidades[$index];

        // Buscar questões associadas à disciplina e habilidade
        $questoes = Questao::where('disciplina_id', $disciplina_id)
            ->where('habilidade_id', $habilidade_id)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        if ($questoes->count() < 2) {
            return redirect()->back()->with('error', 'Não há questões suficientes para criar o simulado.');
        }

        // Associar as questões ao simulado
        $simulado->questoes()->attach($questoes->pluck('id'));
    }

    return redirect()->route('simulados.index')->with('success', 'Simulado criado com sucesso!');
    }

    public function destroy(Simulado $simulado)
    {
        // Excluir o simulado
        $simulado->delete();
        return redirect()->route('simulados.index')->with('success', 'Simulado excluído com sucesso!');
    }
}
