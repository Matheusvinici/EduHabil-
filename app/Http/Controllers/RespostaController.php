<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resposta;
use App\Models\Prova;
use App\Models\Questao;
use App\Models\Escola;
use App\Models\User;
use App\Models\Habilidade;
use App\Models\Ano;
use Illuminate\Support\Facades\Auth;
use PDF;

class RespostaController extends Controller
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
                return redirect()->route('respostas.admin.estatisticas');
            case 'professor':
                return redirect()->route('respostas.professor.index');
            case 'coordenador':
                return redirect()->route('respostas.coordenador.estatisticas');
            case 'aee':
                return redirect()->route('respostas.aee.index');
            case 'inclusiva':
                return redirect()->route('respostas.inclusiva.index');
            case 'aluno':
                return redirect()->route('respostas.aluno.index');
            default:
                abort(403, 'Acesso não autorizado.');
        }
    }

    /**
     * Exibe a lista de provas disponíveis para o aluno responder.
     *
     * @return \Illuminate\Http\Response
     */
    public function alunoIndex()
    {
        $user = Auth::user();

        if ($user->role !== 'aluno') {
            abort(403, 'Acesso não autorizado.');
        }

        // Aluno só vê as provas criadas pelo professor da sua turma
        $professorId = $user->turma->professor_id; // Pega o professor_id da turma do aluno
        $provas = Prova::where('user_id', $professorId)->withCount('questoes')->get();

        return view('respostas.aluno.index', compact('provas'));
    }

    /**
     * Exibe o formulário para responder uma prova específica.
     *
     * @param  \App\Models\Prova  $prova
     * @return \Illuminate\Http\Response
     */
    public function create(Prova $prova)
    {
        // Carrega as questões da prova
        $questoes = $prova->questoes;

        return view('respostas.create', compact('prova', 'questoes'));
    }

    /**
     * Salva as respostas do aluno.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Prova  $prova
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Prova $prova)
    {
        // Validação das respostas
        $request->validate([
            'respostas' => 'required|array',
            'respostas.*' => 'required|in:A,B,C,D'
        ]);

        // Salva cada resposta do aluno
        foreach ($request->respostas as $questao_id => $resposta) {
            $questao = Questao::find($questao_id);
            $correta = ($questao->resposta_correta == $resposta);

            Resposta::create([
                'user_id' => auth()->id(),
                'prova_id' => $prova->id,
                'questao_id' => $questao_id,
                'resposta' => $resposta,
                'correta' => $correta,
            ]);
        }

        return redirect()->route('respostas.index')->with('success', 'Prova finalizada!');
    }

    /**
     * Exibe os detalhes de uma prova respondida (para o aluno).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $prova = Prova::with('questoes')->findOrFail($id);
        return view('provas.show', compact('prova'));
    }

    /**
     * Exibe a lista de respostas dos alunos cadastrados pelo professor.
     *
     * @return \Illuminate\Http\Response
     */
    public function professorIndex(Request $request)
{
    $user = Auth::user();

    if ($user->role !== 'professor') {
        abort(403, 'Acesso não autorizado.');
    }

    // Filtro pelo nome da prova
    $provaNome = $request->query('prova_nome');

    // Busca as provas criadas pelo professor
    $provas = Prova::where('user_id', $user->id)
        ->when($provaNome, function ($query, $provaNome) {
            return $query->where('nome', 'like', '%' . $provaNome . '%');
        })
        ->with(['respostas.user', 'respostas.questao']) // Carrega as respostas, alunos e questões
        ->get();

    // Calcula a porcentagem de acertos por prova
    $porcentagemAcertosPorProva = [];
    foreach ($provas as $prova) {
        $totalRespostas = $prova->respostas->count();
        $totalAcertos = $prova->respostas->where('correta', true)->count();
        $porcentagemAcertos = $totalRespostas > 0 ? ($totalAcertos / $totalRespostas) * 100 : 0;

        $porcentagemAcertosPorProva[$prova->id] = [
            'nome' => $prova->nome,
            'porcentagem' => $porcentagemAcertos,
        ];
    }

    return view('respostas.professor.index', compact('provas', 'provaNome', 'porcentagemAcertosPorProva'));
}

    public function professorShow(Prova $prova, User $aluno)
    {
        $user = Auth::user();
    
        if ($user->role !== 'professor') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Verifica se a prova pertence ao professor
        if ($prova->user_id !== $user->id) {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Busca as respostas do aluno para a prova
        $respostas = Resposta::where('prova_id', $prova->id)
            ->where('user_id', $aluno->id)
            ->with('questao')
            ->get();
    
        // Calcula o total de acertos
        $acertos = $respostas->where('correta', true)->count();
        $total = $respostas->count();
    
        return view('provas.show', compact('prova', 'aluno', 'respostas', 'acertos', 'total'));
    }
    /**
     * Exibe os detalhes das respostas de uma prova específica (para o professor).
     *
     * @param  \App\Models\Prova  $prova
     * @return \Illuminate\Http\Response
     */
    public function showProfessor(Prova $prova)
    {
        $user = Auth::user();

        if ($user->role !== 'professor') {
            abort(403, 'Acesso não autorizado.');
        }

        // Busca as respostas dos alunos cadastrados pelo professor para a prova específica
        $respostas = $prova->respostas()->whereHas('user', function ($query) use ($user) {
            $query->where('professor_id', $user->id);
        })->get();

        return view('respostas.professor.show', compact('prova', 'respostas'));
    }

    /**
     * Exibe as estatísticas do professor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function estatisticasProfessor(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'professor') {
            abort(403, 'Acesso não autorizado.');
        }

        // Filtros
        $provaId = $request->input('prova_id');
        $habilidadeId = $request->input('habilidade_id');
        $anoId = $request->input('ano_id');

        // Busca as provas criadas pelo professor
        $provas = Prova::where('user_id', $user->id)->get();

        // Busca as habilidades, anos para os filtros
        $habilidades = Habilidade::all();
        $anos = Ano::all();

        // Filtra as respostas dos alunos
        $respostas = Resposta::whereHas('prova', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->when($provaId, function ($query, $provaId) {
            return $query->where('prova_id', $provaId);
        })
        ->when($habilidadeId, function ($query, $habilidadeId) {
            return $query->whereHas('questao', function ($q) use ($habilidadeId) {
                $q->where('habilidade_id', $habilidadeId);
            });
        })
        ->when($anoId, function ($query, $anoId) {
            return $query->whereHas('prova', function ($q) use ($anoId) {
                $q->where('ano_id', $anoId);
            });
        })
        ->with(['user', 'prova', 'questao'])
        ->get();

        // Calcula as estatísticas
        $estatisticas = [];
        foreach ($respostas->groupBy('prova_id') as $provaId => $respostasProva) {
            $prova = $respostasProva->first()->prova;
            $totalQuestoes = $prova->questoes->count();
            $alunos = $respostasProva->groupBy('user_id');

            foreach ($alunos as $userId => $respostasAluno) {
                $aluno = $respostasAluno->first()->user;
                $acertos = $respostasAluno->where('correta', true)->count();
                $porcentagemAcertos = ($acertos / $totalQuestoes) * 100;

                $estatisticas[] = [
                    'prova' => $prova->nome,
                    'aluno' => $aluno->name,
                    'acertos' => $acertos,
                    'total_questoes' => $totalQuestoes,
                    'porcentagem_acertos' => $porcentagemAcertos,
                ];
            }
        }

        return view('respostas.professor.estatisticas', compact('estatisticas', 'provas', 'habilidades', 'anos'));
    }

    /**
     * Exibe as estatísticas gerais (para o admin).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function estatisticasAdmin(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Acesso não autorizado.');
        }

        // Filtros
        $escolaId = $request->input('escola_id');
        $habilidadeId = $request->input('habilidade_id');
        $anoId = $request->input('ano_id');

        // Busca todas as escolas, habilidades, anos para os filtros
        $escolas = Escola::all();
        $habilidades = Habilidade::all();
        $anos = Ano::all();

        // Dados gerais
        $totalProvas = Prova::count();
        $totalProfessores = User::where('role', 'professor')->count();
        $totalQuestoesRespondidas = Resposta::count();

        // Estatísticas por escola
        $estatisticasPorEscola = [];
        $respostasPorEscola = Resposta::when($escolaId, function ($query, $escolaId) {
            return $query->whereHas('user', function ($q) use ($escolaId) {
                $q->where('escola_id', $escolaId);
            });
        })
        ->get()
        ->groupBy('user.escola_id');

        foreach ($respostasPorEscola as $escolaId => $respostas) {
            $escola = Escola::find($escolaId);
            $totalQuestoes = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalQuestoes > 0) ? ($acertos / $totalQuestoes) * 100 : 0;

            $estatisticasPorEscola[] = [
                'escola' => $escola->nome,
                'total_questoes' => $totalQuestoes,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
            ];
        }

        // Estatísticas por habilidade
        $estatisticasPorHabilidade = [];
        $respostasPorHabilidade = Resposta::when($habilidadeId, function ($query, $habilidadeId) {
            return $query->whereHas('questao', function ($q) use ($habilidadeId) {
                $q->where('habilidade_id', $habilidadeId);
            });
        })
        ->get()
        ->groupBy('questao.habilidade_id');

        foreach ($respostasPorHabilidade as $habilidadeId => $respostas) {
            $habilidade = Habilidade::find($habilidadeId);
            $totalQuestoes = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalQuestoes > 0) ? ($acertos / $totalQuestoes) * 100 : 0;

            $estatisticasPorHabilidade[] = [
                'habilidade' => $habilidade->descricao,
                'total_questoes' => $totalQuestoes,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
            ];
        }

        return view('respostas.admin.estatisticas', compact(
            'totalProvas',
            'totalProfessores',
            'totalQuestoesRespondidas',
            'estatisticasPorEscola',
            'estatisticasPorHabilidade',
            'escolas',
            'habilidades',
            'anos',
            'request'
        ));
    }

    /**
     * Exibe a lista de respostas (para o coordenador).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function indexCoordenador(Request $request)
{
    $user = Auth::user();
    $escolaId = $user->escola_id;

    // Filtro por habilidade
    $habilidadeId = $request->query('habilidade_id');

    // Dados gerais da escola
    $escola = Escola::findOrFail($escolaId);
    $totalProvas = Prova::where('escola_id', $escolaId)->count();
    $totalProfessores = User::where('escola_id', $escolaId)->where('role', 'professor')->count();
    $totalQuestoesRespondidas = Resposta::whereHas('prova', function ($query) use ($escolaId) {
        $query->where('escola_id', $escolaId);
    })->count();

    // Estatísticas da escola
    $estatisticasEscola = [
        'total_questoes' => Resposta::whereHas('prova', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->count(),
        'acertos' => Resposta::whereHas('prova', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->where('correta', true)->count(),
        'porcentagem_acertos' => Resposta::whereHas('prova', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->avg('correta') * 100,
    ];

    // Estatísticas por habilidade
    $estatisticasPorHabilidade = Habilidade::withCount(['respostas' => function ($query) use ($escolaId) {
        $query->whereHas('prova', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        });
    }])->get()->map(function ($habilidade) use ($escolaId) {
        $acertos = $habilidade->respostas()
            ->whereHas('prova', function ($query) use ($escolaId) {
                $query->where('escola_id', $escolaId);
            })
            ->where('correta', true)
            ->count();

        return [
            'habilidade' => $habilidade->descricao,
            'total_questoes' => $habilidade->respostas_count,
            'acertos' => $acertos,
            'porcentagem_acertos' => $habilidade->respostas_count > 0 ? ($acertos / $habilidade->respostas_count) * 100 : 0,
        ];
    });

    // Lista de habilidades para o filtro
    $habilidades = Habilidade::all();

    return view('respostas.coordenador.estatisticas', compact(
        'escola',
        'totalProvas',
        'totalProfessores',
        'totalQuestoesRespondidas',
        'estatisticasEscola',
        'estatisticasPorHabilidade',
        'habilidades',
        'request'
    ));
}

    /**
     * Gera o PDF das estatísticas.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function gerarPdfEstatisticas(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Acesso não autorizado.');
        }

        // Aplica os mesmos filtros do método estatisticasAdmin
        $escolaId = $request->input('escola_id');
        $habilidadeId = $request->input('habilidade_id');
        $anoId = $request->input('ano_id');

        // Dados gerais
        $totalProvas = Prova::count();
        $totalProfessores = User::where('role', 'professor')->count();
        $totalQuestoesRespondidas = Resposta::count();

        // Estatísticas por escola
        $estatisticasPorEscola = [];
        $respostasPorEscola = Resposta::when($escolaId, function ($query, $escolaId) {
            return $query->whereHas('user', function ($q) use ($escolaId) {
                $q->where('escola_id', $escolaId);
            });
        })
        ->get()
        ->groupBy('user.escola_id');

        foreach ($respostasPorEscola as $escolaId => $respostas) {
            $escola = Escola::find($escolaId);
            $totalQuestoes = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalQuestoes > 0) ? ($acertos / $totalQuestoes) * 100 : 0;

            $estatisticasPorEscola[] = [
                'escola' => $escola->nome,
                'total_questoes' => $totalQuestoes,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
            ];
        }

        // Estatísticas por habilidade
        $estatisticasPorHabilidade = [];
        $respostasPorHabilidade = Resposta::when($habilidadeId, function ($query, $habilidadeId) {
            return $query->whereHas('questao', function ($q) use ($habilidadeId) {
                $q->where('habilidade_id', $habilidadeId);
            });
        })
        ->get()
        ->groupBy('questao.habilidade_id');

        foreach ($respostasPorHabilidade as $habilidadeId => $respostas) {
            $habilidade = Habilidade::find($habilidadeId);
            $totalQuestoes = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalQuestoes > 0) ? ($acertos / $totalQuestoes) * 100 : 0;

            $estatisticasPorHabilidade[] = [
                'habilidade' => $habilidade->descricao,
                'total_questoes' => $totalQuestoes,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
            ];
        }

        // Gera o PDF
        $pdf = PDF::loadView('respostas.pdf.estatisticas', compact(
            'totalProvas',
            'totalProfessores',
            'totalQuestoesRespondidas',
            'estatisticasPorEscola',
            'estatisticasPorHabilidade'
        ));

        return $pdf->download('estatisticas.pdf');
    }

    /**
     * Exibe a lista de todas as respostas (para o admin).
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAdmin()
    {
        $respostas = Resposta::all();
        return view('respostas.admin.index', compact('respostas'));
    }

    /**
     * Exibe os detalhes das respostas de uma prova específica (para o admin).
     *
     * @param  \App\Models\Prova  $prova
     * @return \Illuminate\Http\Response
     */
    public function showAdmin(Prova $prova)
    {
        $respostas = $prova->respostas;
        return view('respostas.admin.show', compact('prova', 'respostas'));
    }
    public function adminEstatisticas(Request $request) // Renomeado para adminEstatisticas
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Acesso não autorizado.');
        }

        // Filtros
        $escolaId = $request->input('escola_id');
        $habilidadeId = $request->input('habilidade_id');
        $anoId = $request->input('ano_id');

        // Busca todas as escolas, habilidades, anos para os filtros
        $escolas = Escola::all();
        $habilidades = Habilidade::all();
        $anos = Ano::all();

        // Dados gerais
        $totalProvas = Prova::count();
        $totalProfessores = User::where('role', 'professor')->count();
        $totalQuestoesRespondidas = Resposta::count();

        // Estatísticas por escola
        $estatisticasPorEscola = [];
        $respostasPorEscola = Resposta::when($escolaId, function ($query, $escolaId) {
            return $query->whereHas('user', function ($q) use ($escolaId) {
                $q->where('escola_id', $escolaId);
            });
        })
        ->get()
        ->groupBy('user.escola_id');

        foreach ($respostasPorEscola as $escolaId => $respostas) {
            $escola = Escola::find($escolaId);
            $totalQuestoes = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalQuestoes > 0) ? ($acertos / $totalQuestoes) * 100 : 0;

            $estatisticasPorEscola[] = [
                'escola' => $escola->nome,
                'total_questoes' => $totalQuestoes,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
            ];
        }

        // Estatísticas por habilidade
        $estatisticasPorHabilidade = [];
        $respostasPorHabilidade = Resposta::when($habilidadeId, function ($query, $habilidadeId) {
            return $query->whereHas('questao', function ($q) use ($habilidadeId) {
                $q->where('habilidade_id', $habilidadeId);
            });
        })
        ->get()
        ->groupBy('questao.habilidade_id');

        foreach ($respostasPorHabilidade as $habilidadeId => $respostas) {
            $habilidade = Habilidade::find($habilidadeId);
            $totalQuestoes = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalQuestoes > 0) ? ($acertos / $totalQuestoes) * 100 : 0;

            $estatisticasPorHabilidade[] = [
                'habilidade' => $habilidade->descricao,
                'total_questoes' => $totalQuestoes,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
            ];
        }

        return view('respostas.admin.estatisticas', compact(
            'totalProvas',
            'totalProfessores',
            'totalQuestoesRespondidas',
            'estatisticasPorEscola',
            'estatisticasPorHabilidade',
            'escolas',
            'habilidades',
            'anos',
            'request'
        ));
    }
}