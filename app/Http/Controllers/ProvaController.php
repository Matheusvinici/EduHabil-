<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prova;
use App\Models\Ano;
use App\Models\Disciplina;
use App\Models\Unidade;
use App\Models\Habilidade;
use App\Models\Questao;
use Illuminate\Support\Facades\Auth;

class ProvaController extends Controller
{
    /**
     * Exibe o formulário de criação de provas.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        // Verifica se o usuário está autenticado
        if (!$user) {
            abort(403, 'Usuário não autenticado.');
        }

        // Verifica o papel do usuário
        if ($user->role === 'admin') {
            // Admin pode selecionar qualquer escola
            $anos = Ano::all();
            $disciplinas = Disciplina::all();
            $unidades = Unidade::all();
            $habilidades = Habilidade::all();
        } elseif ($user->role === 'professor') {
            // Professor só pode criar provas para a escola à qual está vinculado
            if (is_null($user->escola_id)) {
                abort(403, 'Você não está vinculado a uma escola. Contate o administrador.');
            }

            // Busca todos os anos (não filtra por escola, pois os anos são globais)
            $anos = Ano::all();

            // Busca todas as disciplinas (não filtra por escola, pois as disciplinas são globais)
            $disciplinas = Disciplina::all();

            // Busca todas as unidades (não filtra por escola, pois as unidades são globais)
            $unidades = Unidade::all();

            // Busca todas as habilidades (não filtra por escola, pois as habilidades são globais)
            $habilidades = Habilidade::all();
        } else {
            // Outros papéis (se houver) não têm permissão
            abort(403, 'Acesso não autorizado.');
        }

        return view('provas.create', [
            'anos' => $anos,
            'disciplinas' => $disciplinas,
            'unidades' => $unidades,
            'habilidades' => $habilidades,
        ]);
    }

    /**
     * Armazena uma nova prova no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    $request->validate([
        'ano_id' => 'required|exists:anos,id',
        'disciplina_id' => 'required|exists:disciplinas,id',
        'unidade_id' => 'required|exists:unidades,id',
        'habilidade_id' => 'required|exists:habilidades,id',
        'nome' => 'required|string|max:255',
        'data' => 'required|date',
        'observacoes' => 'nullable|string',
    ]);

    // Cria a prova
    $prova = Prova::create([
        'user_id' => auth()->id(), // Professor que criou a prova
        'ano_id' => $request->ano_id,
        'disciplina_id' => $request->disciplina_id,
        'unidade_id' => $request->unidade_id,
        'habilidade_id' => $request->habilidade_id,
        'nome' => $request->nome,
        'data' => $request->data,
        'observacoes' => $request->observacoes,
    ]);

    // Seleciona 10 questões aleatórias com base nos critérios
    $questoes = Questao::where('ano_id', $request->ano_id)
        ->where('disciplina_id', $request->disciplina_id)
        ->where('unidade_id', $request->unidade_id)
        ->where('habilidade_id', $request->habilidade_id)
        ->inRandomOrder() // Ordena as questões aleatoriamente
        ->limit(10) // Limita a 10 questões
        ->get();

    // Verifica se há questões suficientes
    if ($questoes->count() < 10) {
        return redirect()->back()->with('error', 'Não há questões suficientes para criar a prova.');
    }

    // Vincula as questões à prova
    $prova->questoes()->attach($questoes->pluck('id'));

    return redirect()->route('provas.index')->with('success', 'Prova criada com sucesso!');
}
    /**
     * Exibe a listagem de provas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin pode ver todas as provas
            $provas = Prova::all();
        } elseif ($user->role === 'professor') {
            // Professor só pode ver as provas que ele criou
            $provas = Prova::where('user_id', $user->id)->get();
        } else {
            // Outros papéis (se houver) não têm permissão
            abort(403, 'Acesso não autorizado.');
        }

        return view('provas.index', compact('provas'));
    }

    /**
     * Exibe os detalhes de uma prova específica.
     *
     * @param  \App\Models\Prova  $prova
     * @return \Illuminate\Http\Response
     */
    public function show(Prova $prova)
    {
        $user = Auth::user();

        // Verifica se o usuário tem permissão para acessar a prova
        if ($user->role === 'professor' && $prova->user_id !== $user->id) {
            abort(403, 'Acesso não autorizado.');
        }

        // Carrega as questões associadas à prova
        $prova->load('questoes');

        return view('provas.show', compact('prova'));
    }

    /**
     * Exibe o formulário de edição de uma prova.
     *
     * @param  \App\Models\Prova  $prova
     * @return \Illuminate\Http\Response
     */
    public function edit(Prova $prova)
    {
        $user = Auth::user();

        // Verifica se o usuário tem permissão para editar a prova
        if ($user->role === 'professor' && $prova->user_id !== $user->id) {
            abort(403, 'Acesso não autorizado.');
        }

        // Carrega os dados necessários para o formulário de edição
        $anos = Ano::all();
        $disciplinas = Disciplina::all();
        $unidades = Unidade::all();
        $habilidades = Habilidade::all();

        return view('provas.edit', compact('prova', 'anos', 'disciplinas', 'unidades', 'habilidades'));
    }

    /**
     * Atualiza uma prova no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Prova  $prova
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Prova $prova)
    {
        $user = Auth::user();

        // Verifica se o usuário tem permissão para editar a prova
        if ($user->role === 'professor' && $prova->user_id !== $user->id) {
            abort(403, 'Acesso não autorizado.');
        }

        $request->validate([
            'ano_id' => 'required|exists:anos,id',
            'disciplina_id' => 'required|exists:disciplinas,id',
            'unidade_id' => 'required|exists:unidades,id',
            'habilidade_id' => 'required|exists:habilidades,id',
            'nome' => 'required|string|max:255',
            'data' => 'required|date',
            'observacoes' => 'nullable|string',
        ]);

        // Atualiza a prova
        $prova->update([
            'ano_id' => $request->ano_id,
            'disciplina_id' => $request->disciplina_id,
            'unidade_id' => $request->unidade_id,
            'habilidade_id' => $request->habilidade_id,
            'nome' => $request->nome,
            'data' => $request->data,
            'observacoes' => $request->observacoes,
        ]);

        return redirect()->route('provas.index')->with('success', 'Prova atualizada com sucesso!');
    }

    /**
     * Gera o PDF de uma prova.
     *
     * @param  \App\Models\Prova  $prova
     * @return \Illuminate\Http\Response
     */
    public function gerarPDF(Prova $prova)
    {
        $user = Auth::user();

        // Verifica se o usuário tem permissão para acessar a prova
        if ($user->role === 'professor' && $prova->user_id !== $user->id) {
            abort(403, 'Acesso não autorizado.');
        }

        // Carrega as questões associadas à prova
        $prova->load('questoes');

        // Gera o PDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('provas.pdf', [
            'prova' => $prova,
            'user' => $user,
        ]);

        return $pdf->download('prova_' . $prova->id . '.pdf');
    }
}