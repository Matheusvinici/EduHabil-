<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prova;
use App\Models\Ano;
use App\Models\Disciplina;
use App\Models\Habilidade;
use App\Models\Questao;
use App\Models\Escola;
use Barryvdh\DomPDF\Facade\Pdf; // Importação correta do facade
use Illuminate\Support\Facades\Auth;

class ProvaController extends Controller
{
    /**
     * Redireciona para o método correto com base no perfil do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
    
        // Redireciona para o método correto com base no perfil do usuário
        switch ($user->role) {
            case 'admin':
                return redirect()->route('provas.admin.index');
            case 'professor':
                return redirect()->route('provas.professor.index');
            case 'coordenador':
                return redirect()->route('provas.coordenador.index');
            case 'aee':
                return redirect()->route('provas.aee.index');
            case 'inclusiva':
                return redirect()->route('provas.inclusiva.index');
            default:
                abort(403, 'Acesso não autorizado.');
        }
    }

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

            // Busca todas as habilidades (não filtra por escola, pois as habilidades são globais)
            $habilidades = Habilidade::all();
        } else {
            // Outros papéis (se houver) não têm permissão
            abort(403, 'Acesso não autorizado.');
        }

        return view('provas.create', [
            'anos' => $anos,
            'disciplinas' => $disciplinas,
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
            'habilidade_id' => 'required|exists:habilidades,id',
            'nome' => 'required|string|max:255',
            'data' => 'required|date',
            'observacoes' => 'nullable|string',
        ]);

        // Obtém o usuário logado
        $user = Auth::user();

        // Verifica se o professor está vinculado a uma escola
        if (is_null($user->escola_id)) {
            return redirect()->back()->with('error', 'Você não está vinculado a uma escola. Contate o administrador.');
        }

        // Cria a prova
        $prova = Prova::create([
            'user_id' => $user->id, // ID do professor que criou a prova
            'escola_id' => $user->escola_id, // Escola do professor
            'ano_id' => $request->ano_id,
            'disciplina_id' => $request->disciplina_id,
            'habilidade_id' => $request->habilidade_id,
            'nome' => $request->nome,
            'data' => $request->data,
            'observacoes' => $request->observacoes,
        ]);

        // Seleciona 10 questões aleatórias com base nos critérios
        $questoes = Questao::where('ano_id', $request->ano_id)
            ->where('disciplina_id', $request->disciplina_id)
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
     * Exibe a listagem de provas para o perfil do admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function indexAdmin(Request $request)
{
    // Filtro por escola
    $escolaId = $request->query('escola_id');

    // Verifica se a escola existe
    if ($escolaId && !Escola::find($escolaId)) {
        abort(404, 'Escola não encontrada.');
    }

    // Total de provas (com filtro)
    $totalProvas = Prova::when($escolaId, function ($query, $escolaId) {
        return $query->where('escola_id', $escolaId);
    })->count();

    // Total de professores que geraram provas (com filtro)
    $totalProfessores = Prova::when($escolaId, function ($query, $escolaId) {
        return $query->where('escola_id', $escolaId);
    })->distinct('user_id')->count('user_id');

    // Total de escolas
    $totalEscolas = Escola::count();

    // Escolas que já geraram provas
    $escolasComProvas = Escola::whereHas('provas')->get();

    // Escolas que ainda não geraram provas
    $escolasSemProvas = Escola::whereDoesntHave('provas')->get();
    $totalEscolasSemProvas = $escolasSemProvas->count();

    // Lista de provas (com filtro e ordenação pelas mais recentes)
    $provas = Prova::when($escolaId, function ($query, $escolaId) {
        return $query->where('escola_id', $escolaId);
    })
    ->orderBy('created_at', 'desc') // Ordena pelas mais recentes
    ->with(['escola', 'ano', 'disciplina', 'professor'])
    ->paginate(5);

    // Lista de escolas para o filtro
    $escolas = Escola::all();

    return view('provas.admin.index', compact(
        'provas',
        'escolas',
        'escolasComProvas', 
        'totalProvas',
        'totalProfessores',
        'totalEscolas',
        'totalEscolasSemProvas',
        'escolaId'
    ));
}
    /**
     * Gera o PDF das escolas que não geraram provas.
     */
    public function pdfEscolasSemProvas()
    {
        // Escolas que ainda não geraram provas
        $escolasComProvas = Prova::distinct('escola_id')->pluck('escola_id');
        $escolasSemProvas = Escola::whereNotIn('id', $escolasComProvas)->get();

        // Gera o PDF
        $pdf = Pdf::loadView('provas.admin.pdf.escolas_sem_provas', compact('escolasSemProvas'));
        return $pdf->download('escolas_sem_provas.pdf');
    }

    /**
     * Gera o PDF das escolas que já geraram provas.
     */
    public function pdfEscolasComProvas()
    {
        // Escolas que já geraram provas
        $escolasComProvas = Prova::distinct('escola_id')->pluck('escola_id');
        $escolasComProvas = Escola::whereIn('id', $escolasComProvas)->get();

        // Gera o PDF
        $pdf = Pdf::loadView('provas.admin.pdf.escolas_com_provas', compact('escolasComProvas'));
        return $pdf->download('escolas_com_provas.pdf');
    }

    /**
     * Exibe a listagem de provas para o perfil do professor.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexProfessor()
    {
        $user = Auth::user();
        $provas = Prova::where('user_id', $user->id)->with(['escola', 'ano', 'disciplina'])->get();
        return view('provas.professor.index', compact('provas'));
    }

    /**
     * Exibe a listagem de provas para o perfil do coordenador.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexCoordenador(Request $request)
    {
        $user = Auth::user();
    
        // Filtro por nome do professor
        $professorNome = $request->query('professor_nome');
    
        // Query base
        $provas = Prova::where('escola_id', $user->escola_id)
            ->with(['escola', 'ano', 'disciplina', 'professor'])
            ->orderBy('created_at', 'desc'); // Ordena do mais recente para o mais antigo
    
        // Aplica o filtro de nome do professor, se fornecido
        if ($professorNome) {
            $provas->whereHas('professor', function ($query) use ($professorNome) {
                $query->where('name', 'like', '%' . $professorNome . '%');
            });
        }
    
        // Paginação com 5 registros por página
        $provas = $provas->paginate(5);
    
        return view('provas.coordenador.index', compact('provas', 'professorNome'));
    }
    /**
     * Exibe a listagem de provas para o perfil do AEE.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAEE()
    {
        $user = Auth::user();
        $provas = Prova::where('escola_id', $user->escola_id)->with(['escola', 'ano', 'disciplina', 'professor'])->get();
        return view('provas.aee.index', compact('provas'));
    }

    /**
     * Exibe a listagem de provas para o perfil inclusiva.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexInclusiva()
    {
        $user = Auth::user();
        $provas = Prova::where('escola_id', $user->escola_id)->with(['escola', 'ano', 'disciplina', 'professor'])->get();
        return view('provas.inclusiva.index', compact('provas'));
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
        $pdf = Pdf::loadView('provas.pdf', [
            'prova' => $prova,
            'user' => $user,
        ]);

        return $pdf->download('prova_' . $prova->id . '.pdf');
    }
}