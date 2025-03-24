<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RespostaSimulado;
use App\Models\Simulado;
use App\Models\Pergunta;
use App\Models\User;
use App\Models\Habilidade;
use App\Models\Ano;
use App\Models\Turma;

use Illuminate\Support\Facades\DB; 

use App\Models\Escola;
use Illuminate\Support\Facades\Auth;

class RespostaSimuladoController extends Controller
{
    /**
     * Redireciona para o método correto com base no perfil do usuário.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('respostas_simulados.admin.estatisticas');
            case 'professor':
                return redirect()->route('respostas_simulados.professor.index');
            case 'coordenador':
                return redirect()->route('respostas_simulados.coordenador.index');
            case 'aluno':
                return redirect()->route('respostas_simulados.aluno.index');
            default:
                abort(403, 'Acesso não autorizado.');
        }
    }

    /**
     * Exibe a lista de simulados disponíveis para o aluno responder.
     */
    public function alunoIndex()
    {
        $user = Auth::user();
    
        if ($user->role !== 'aluno') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Aluno vê todos os simulados disponíveis
        $simulados = Simulado::withCount('perguntas')->get();
    
        return view('respostas_simulados.aluno.index', compact('simulados'));
    }

    public function indexCoordenador(Request $request)
    {
        $user = Auth::user();
    
        // Verifica se o usuário é um coordenador
        if ($user->role !== 'coordenador') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Filtros
        $anoId = $request->input('ano_id');
        $simuladoId = $request->input('simulado_id');
        $habilidadeId = $request->input('habilidade_id');
        $turmaId = $request->input('turma_id');
    
        // Busca os alunos e professores da escola do coordenador
        $alunosIds = User::where('escola_id', $user->escola_id)
            ->where('role', 'aluno')
            ->pluck('id');
    
        $professoresIds = User::where('escola_id', $user->escola_id)
            ->where('role', 'professor')
            ->pluck('id');
    
        // Dados gerais
        $totalAlunos = $alunosIds->count();
        $totalProfessores = $professoresIds->count();
        $totalRespostas = RespostaSimulado::whereIn('user_id', $alunosIds)->count();
    
        // Média por 1º ao 5º ano
        $media1a5 = RespostaSimulado::whereIn('user_id', $alunosIds)
            ->whereHas('simulado', function ($q) {
                $q->whereIn('ano_id', range(1, 5)); // Filtra por anos de 1º ao 5º
            })
            ->avg('correta') * 10;
    
        // Média por 6º ao 9º ano
        $media6a9 = RespostaSimulado::whereIn('user_id', $alunosIds)
            ->whereHas('simulado', function ($q) {
                $q->whereIn('ano_id', range(6, 9)); // Filtra por anos de 6º ao 9º
            })
            ->avg('correta') * 10;
    
        // Estatísticas por turma
        $estatisticasPorTurma = [];
        $respostasPorTurma = RespostaSimulado::whereIn('user_id', $alunosIds)
            ->when($turmaId, function ($query, $turmaId) {
                return $query->whereHas('user', function ($q) use ($turmaId) {
                    $q->where('turma_id', $turmaId);
                });
            })
            ->when($simuladoId, function ($query, $simuladoId) {
                return $query->where('simulado_id', $simuladoId);
            })
            ->when($anoId, function ($query, $anoId) {
                return $query->whereHas('simulado', function ($q) use ($anoId) {
                    $q->where('ano_id', $anoId);
                });
            })
            ->get()
            ->groupBy('user.turma_id');
    
        foreach ($respostasPorTurma as $turmaId => $respostas) {
            $turma = Turma::find($turmaId);
            $totalRespostasTurma = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalRespostasTurma > 0) ? ($acertos / $totalRespostasTurma) * 100 : 0;
            $mediaFinal = ($totalRespostasTurma > 0) ? ($acertos / $totalRespostasTurma) * 10 : 0;
    
            $estatisticasPorTurma[] = [
                'turma' => $turma->nome_turma, // Usa o nome da turma
                'total_respostas' => $totalRespostasTurma,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
                'media_final' => $mediaFinal,
            ];
        }
    
        // Média geral da escola
        $totalRespostasEscola = RespostaSimulado::whereIn('user_id', $alunosIds)->count();
        $acertosEscola = RespostaSimulado::whereIn('user_id', $alunosIds)->where('correta', true)->count();
        $mediaGeralEscola = ($totalRespostasEscola > 0) ? ($acertosEscola / $totalRespostasEscola) * 10 : 0;
    
        // Estatísticas por habilidade
        $estatisticasPorHabilidade = [];
        $respostasPorHabilidade = RespostaSimulado::whereIn('user_id', $alunosIds)
            ->when($habilidadeId, function ($query, $habilidadeId) {
                return $query->whereHas('pergunta', function ($q) use ($habilidadeId) {
                    $q->where('habilidade_id', $habilidadeId);
                });
            })
            ->when($simuladoId, function ($query, $simuladoId) {
                return $query->where('simulado_id', $simuladoId);
            })
            ->when($anoId, function ($query, $anoId) {
                return $query->whereHas('simulado', function ($q) use ($anoId) {
                    $q->where('ano_id', $anoId);
                });
            })
            ->get()
            ->groupBy('pergunta.habilidade_id');
    
        foreach ($respostasPorHabilidade as $habilidadeId => $respostas) {
            $habilidade = Habilidade::find($habilidadeId);
            $totalRespostasHabilidade = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalRespostasHabilidade > 0) ? ($acertos / $totalRespostasHabilidade) * 100 : 0;
    
            $estatisticasPorHabilidade[] = [
                'habilidade' => $habilidade->descricao,
                'total_respostas' => $totalRespostasHabilidade,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
            ];
        }
    
        // Percentual de acerto por questão
        $questoes = DB::table('respostas_simulados')
            ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
            ->select('perguntas.id', DB::raw('AVG(respostas_simulados.correta) * 100 as percentual_acerto'))
            ->whereIn('respostas_simulados.user_id', $alunosIds)
            ->when($simuladoId, function ($query, $simuladoId) {
                return $query->where('respostas_simulados.simulado_id', $simuladoId);
            })
            ->groupBy('perguntas.id')
            ->get();
    
        // Média por simulado
        $mediasPorSimulado = DB::table('respostas_simulados')
            ->join('simulados', 'respostas_simulados.simulado_id', '=', 'simulados.id')
            ->select('simulados.nome', DB::raw('AVG(respostas_simulados.correta) * 10 as media'))
            ->whereIn('respostas_simulados.user_id', $alunosIds)
            ->groupBy('simulados.nome')
            ->get();
    
        // Busca todos os anos, simulados, turmas e habilidades para os filtros
        $anos = Ano::all();
        $simulados = Simulado::all();
        $turmas = Turma::whereIn('professor_id', $professoresIds)->get(); // Turmas dos professores da escola
        $habilidades = Habilidade::all();
    
        return view('respostas_simulados.coordenador.index', compact(
            'totalAlunos',
            'totalProfessores',
            'totalRespostas',
            'media1a5',
            'media6a9',
            'estatisticasPorTurma',
            'mediaGeralEscola',
            'estatisticasPorHabilidade',
            'questoes',
            'mediasPorSimulado',
            'anos',
            'simulados',
            'turmas',
            'habilidades',
            'request'
        ));
    }
    /**
     * Exibe o formulário para responder um simulado específico.
     */
    public function create(Simulado $simulado)
    {
        $user = Auth::user();
    
        if ($user->role !== 'aluno') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Carrega as perguntas do simulado
        $perguntas = $simulado->perguntas;
    
        return view('respostas_simulados.create', compact('simulado', 'perguntas'));
    }

    /**
     * Salva as respostas do aluno.
     */
    public function store(Request $request, Simulado $simulado)
    {
        $user = Auth::user();
    
        if ($user->role !== 'aluno') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Validação das respostas
        $request->validate([
            'respostas' => 'required|array',
            'respostas.*' => 'required|in:A,B,C,D'
        ]);
    
        // Salva cada resposta do aluno
        foreach ($request->respostas as $pergunta_id => $resposta) {
            $pergunta = Pergunta::find($pergunta_id);
            $correta = ($pergunta->resposta_correta == $resposta);
    
            RespostaSimulado::create([
                'user_id' => $user->id,
                'professor_id' => $user->turma->professor_id, // Professor da turma do aluno
                'escola_id' => $user->escola_id, // Escola do aluno
                'simulado_id' => $simulado->id,
                'pergunta_id' => $pergunta_id,
                'resposta' => $resposta,
                'correta' => $correta,
            ]);
        }
    
        return redirect()->route('respostas_simulados.aluno.index')->with('success', 'Simulado finalizado!');
    }

    /**
     * Exibe os detalhes de um simulado respondido (para o aluno).
     */
    public function show(Simulado $simulado)
    {
        $user = Auth::user();

        if ($user->role !== 'aluno') {
            abort(403, 'Acesso não autorizado.');
        }

        // Busca as respostas do aluno para o simulado
        $respostas = RespostaSimulado::where('simulado_id', $simulado->id)
            ->where('user_id', $user->id)
            ->with('pergunta')
            ->get();

        // Calcula o total de acertos
        $acertos = $respostas->where('correta', true)->count();
        $total = $respostas->count();

        return view('respostas_simulados.show', compact('simulado', 'respostas', 'acertos', 'total'));
    }

    /**
     * Exibe a lista de respostas dos alunos (para o professor/admin).
     */
    public function professorIndex(Request $request)
{
    $user = Auth::user();

    // Verifica se o usuário é um professor
    if ($user->role !== 'professor') {
        abort(403, 'Acesso não autorizado.');
    }

    // Filtro pelo nome do simulado
    $simuladoNome = $request->query('simulado_nome');

    // Busca as turmas criadas pelo professor
    $turmasIds = Turma::where('professor_id', $user->id)->pluck('id');

    // Busca os IDs dos alunos das turmas criadas pelo professor
    $alunosIds = User::whereIn('turma_id', $turmasIds)->where('role', 'aluno')->pluck('id');

    // Busca os simulados
    $simulados = Simulado::when($simuladoNome, function ($query, $simuladoNome) {
        return $query->where('nome', 'like', '%' . $simuladoNome . '%');
    })
    ->with(['respostas' => function ($query) use ($alunosIds) {
        $query->whereIn('user_id', $alunosIds); // Filtra respostas dos alunos das turmas do professor
    }, 'respostas.user', 'respostas.pergunta']) // Carrega as respostas, alunos e perguntas
    ->get();

    // Calcula as estatísticas por aluno
    $estatisticasPorAluno = [];
    foreach ($simulados as $simulado) {
        foreach ($simulado->respostas as $resposta) {
            $alunoId = $resposta->user_id;
            $alunoNome = $resposta->user->name;

            if (!isset($estatisticasPorAluno[$alunoId])) {
                $estatisticasPorAluno[$alunoId] = [
                    'aluno' => $alunoNome,
                    'total_respostas' => 0,
                    'acertos' => 0,
                    'porcentagem_acertos' => 0,
                    'media_final' => 0,
                ];
            }

            $estatisticasPorAluno[$alunoId]['total_respostas']++;
            if ($resposta->correta) {
                $estatisticasPorAluno[$alunoId]['acertos']++;
            }
        }
    }

    // Calcula a porcentagem de acertos e a média final para cada aluno
    foreach ($estatisticasPorAluno as &$estatistica) {
        $estatistica['porcentagem_acertos'] = ($estatistica['total_respostas'] > 0)
            ? ($estatistica['acertos'] / $estatistica['total_respostas']) * 100
            : 0;

        $estatistica['media_final'] = ($estatistica['total_respostas'] > 0)
            ? ($estatistica['acertos'] / $estatistica['total_respostas']) * 10
            : 0;
    }

    // Calcula a média da turma por simulado
    $mediaTurmaPorSimulado = [];
    foreach ($simulados as $simulado) {
        $totalRespostas = $simulado->respostas->count();
        $totalAcertos = $simulado->respostas->where('correta', true)->count();

        $mediaTurmaPorSimulado[] = [
            'simulado' => $simulado->nome,
            'media_turma' => ($totalRespostas > 0) ? ($totalAcertos / $totalRespostas) * 10 : 0,
        ];
    }

    // Calcula as estatísticas por habilidade
    $estatisticasPorHabilidade = [];
    foreach ($simulados as $simulado) {
        foreach ($simulado->respostas as $resposta) {
            $habilidadeId = $resposta->pergunta->habilidade_id;
            $habilidadeNome = $resposta->pergunta->habilidade->descricao;

            if (!isset($estatisticasPorHabilidade[$habilidadeId])) {
                $estatisticasPorHabilidade[$habilidadeId] = [
                    'habilidade' => $habilidadeNome,
                    'total_respostas' => 0,
                    'acertos' => 0,
                    'porcentagem_acertos' => 0,
                ];
            }

            $estatisticasPorHabilidade[$habilidadeId]['total_respostas']++;
            if ($resposta->correta) {
                $estatisticasPorHabilidade[$habilidadeId]['acertos']++;
            }
        }
    }

    // Calcula a porcentagem de acertos por habilidade
    foreach ($estatisticasPorHabilidade as &$estatistica) {
        $estatistica['porcentagem_acertos'] = ($estatistica['total_respostas'] > 0)
            ? ($estatistica['acertos'] / $estatistica['total_respostas']) * 100
            : 0;
    }

    return view('respostas_simulados.professor.index', compact(
        'simulados',
        'simuladoNome',
        'estatisticasPorAluno',
        'mediaTurmaPorSimulado',
        'estatisticasPorHabilidade'
    ));
}
    /**
     * Exibe os detalhes das respostas de um simulado específico (para o professor).
     */
    public function showProfessor(Simulado $simulado, User $aluno)
    {
        $user = Auth::user();

        if ($user->role !== 'professor') {
            abort(403, 'Acesso não autorizado.');
        }

        // Verifica se o simulado pertence ao professor
        if ($simulado->user_id !== $user->id) {
            abort(403, 'Acesso não autorizado.');
        }

        // Busca as respostas do aluno para o simulado
        $respostas = RespostaSimulado::where('simulado_id', $simulado->id)
            ->where('user_id', $aluno->id)
            ->with('pergunta')
            ->get();

        // Calcula o total de acertos
        $acertos = $respostas->where('correta', true)->count();
        $total = $respostas->count();

        return view('respostas_simulados.professor.show', compact('simulado', 'aluno', 'respostas', 'acertos', 'total'));
    }

    /**
     * Exibe as estatísticas do professor.
     */
    public function estatisticasProfessor(Request $request)
    {
        $user = Auth::user();
    
        // Verifica se o usuário é um professor
        if ($user->role !== 'professor') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Filtro pelo nome do simulado
        $simuladoNome = $request->query('simulado_nome');
    
        // Busca as turmas criadas pelo professor
        $turmasIds = Turma::where('professor_id', $user->id)->pluck('id');
    
        // Busca os IDs dos alunos das turmas criadas pelo professor
        $alunosIds = User::whereIn('turma_id', $turmasIds)->where('role', 'aluno')->pluck('id');
    
        // Busca todos os simulados (não filtramos por escola, pois o simulado é global)
        $simulados = Simulado::when($simuladoNome, function ($query, $simuladoNome) {
            return $query->where('nome', 'like', '%' . $simuladoNome . '%');
        })
        ->with(['respostas' => function ($query) use ($alunosIds) {
            $query->whereIn('user_id', $alunosIds); // Filtra respostas dos alunos das turmas do professor
        }, 'respostas.user', 'respostas.pergunta']) // Carrega as respostas, alunos e perguntas
        ->get();
    
        // Busca os anos para o filtro
        $anos = Ano::all(); // Certifique-se de que o modelo Ano existe e está configurado corretamente
    
        // Busca as habilidades para o filtro
        $habilidades = Habilidade::all(); // Certifique-se de que o modelo Habilidade existe e está configurado corretamente
    
        // Calcula o total de alunos das turmas do professor
        $totalAlunos = User::whereIn('turma_id', $turmasIds)->where('role', 'aluno')->count();
    
        // Calcula o total de respostas dos alunos das turmas do professor
        $totalRespostas = RespostaSimulado::whereIn('user_id', $alunosIds)->count();
    
        // Calcula as estatísticas por aluno
        $estatisticasPorAluno = [];
        foreach ($simulados as $simulado) {
            foreach ($simulado->respostas as $resposta) {
                $alunoId = $resposta->user_id;
                $alunoNome = $resposta->user->name;
    
                if (!isset($estatisticasPorAluno[$alunoId])) {
                    $estatisticasPorAluno[$alunoId] = [
                        'aluno' => $alunoNome,
                        'total_respostas' => 0,
                        'acertos' => 0,
                        'porcentagem_acertos' => 0,
                        'media_final' => 0,
                    ];
                }
    
                $estatisticasPorAluno[$alunoId]['total_respostas']++;
                if ($resposta->correta) {
                    $estatisticasPorAluno[$alunoId]['acertos']++;
                }
            }
        }
    
        // Calcula a porcentagem de acertos e a média final para cada aluno
        foreach ($estatisticasPorAluno as &$estatistica) {
            $estatistica['porcentagem_acertos'] = ($estatistica['total_respostas'] > 0)
                ? ($estatistica['acertos'] / $estatistica['total_respostas']) * 100
                : 0;
    
            $estatistica['media_final'] = ($estatistica['total_respostas'] > 0)
                ? ($estatistica['acertos'] / $estatistica['total_respostas']) * 10
                : 0;
        }
    
        // Calcula a média da turma por simulado
        $mediaTurmaPorSimulado = [];
        foreach ($simulados as $simulado) {
            $totalRespostasSimulado = $simulado->respostas->count();
            $totalAcertosSimulado = $simulado->respostas->where('correta', true)->count();
    
            $mediaTurmaPorSimulado[] = [
                'simulado' => $simulado->nome,
                'media_turma' => ($totalRespostasSimulado > 0) ? ($totalAcertosSimulado / $totalRespostasSimulado) * 10 : 0,
            ];
        }
    
        // Calcula as estatísticas por habilidade
        $estatisticasPorHabilidade = [];
        foreach ($simulados as $simulado) {
            foreach ($simulado->respostas as $resposta) {
                $habilidadeId = $resposta->pergunta->habilidade_id;
                $habilidadeNome = $resposta->pergunta->habilidade->descricao;
    
                if (!isset($estatisticasPorHabilidade[$habilidadeId])) {
                    $estatisticasPorHabilidade[$habilidadeId] = [
                        'habilidade' => $habilidadeNome,
                        'total_respostas' => 0,
                        'acertos' => 0,
                        'porcentagem_acertos' => 0,
                    ];
                }
    
                $estatisticasPorHabilidade[$habilidadeId]['total_respostas']++;
                if ($resposta->correta) {
                    $estatisticasPorHabilidade[$habilidadeId]['acertos']++;
                }
            }
        }
    
        // Calcula a porcentagem de acertos por habilidade
        foreach ($estatisticasPorHabilidade as &$estatistica) {
            $estatistica['porcentagem_acertos'] = ($estatistica['total_respostas'] > 0)
                ? ($estatistica['acertos'] / $estatistica['total_respostas']) * 100
                : 0;
        }
    
        return view('respostas_simulados.professor.index', compact(
            'simulados',
            'simuladoNome',
            'estatisticasPorAluno',
            'mediaTurmaPorSimulado',
            'estatisticasPorHabilidade',
            'request', // Passa a variável $request para a view
            'anos', // Passa a variável $anos para a view
            'habilidades', // Passa a variável $habilidades para a view
            'totalAlunos', // Passa a variável $totalAlunos para a view
            'totalRespostas' // Passa a variável $totalRespostas para a view
        ));
    }
    /**
     * Exibe as estatísticas gerais (para o admin).
     */
    public function estatisticasAdmin(Request $request)
    {
        $user = Auth::user();
    
        if ($user->role !== 'admin') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Filtros
        $escolaId = $request->input('escola_id');
        $anoId = $request->input('ano_id');
        $simuladoId = $request->input('simulado_id');
        $habilidadeId = $request->input('habilidade_id');
    
        // Busca todas as escolas, anos, simulados e habilidades para os filtros
        $escolas = Escola::all();
        $anos = Ano::all();
        $simulados = Simulado::all();
        $habilidades = Habilidade::all();
    
        // Dados gerais
        $totalSimulados = Simulado::count();
        $totalProfessores = User::where('role', 'professor')->count();
        $totalAlunos = User::where('role', 'aluno')->count();
        $totalRespostas = RespostaSimulado::count();
    
        // Total de professores e alunos que responderam o simulado
        $professoresResponderam = User::where('role', 'professor')
            ->whereHas('respostasSimulado')
            ->count();
        $alunosResponderam = User::where('role', 'aluno')
            ->whereHas('respostasSimulado')
            ->count();
    
        // Estatísticas por escola
        $estatisticasPorEscola = [];
        $respostasPorEscola = RespostaSimulado::when($escolaId, function ($query, $escolaId) {
            return $query->whereHas('user', function ($q) use ($escolaId) {
                $q->where('escola_id', $escolaId);
            });
        })
        ->when($simuladoId, function ($query, $simuladoId) {
            return $query->where('simulado_id', $simuladoId);
        })
        ->when($anoId, function ($query, $anoId) {
            return $query->whereHas('simulado', function ($q) use ($anoId) {
                $q->where('ano_id', $anoId);
            });
        })
        ->get()
        ->groupBy('user.escola_id');
    
        foreach ($respostasPorEscola as $escolaId => $respostas) {
            $escola = Escola::find($escolaId);
            $totalRespostasEscola = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalRespostasEscola > 0) ? ($acertos / $totalRespostasEscola) * 100 : 0;
            $mediaFinal = ($totalRespostasEscola > 0) ? ($acertos / $totalRespostasEscola) * 10 : 0;
    
            $estatisticasPorEscola[] = [
                'escola' => $escola->nome,
                'total_respostas' => $totalRespostasEscola,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
                'media_final' => $mediaFinal,
            ];
        }
    
        // Estatísticas por ano
        $estatisticasPorAno = [];
        $respostasPorAno = RespostaSimulado::when($anoId, function ($query, $anoId) {
            return $query->whereHas('simulado', function ($q) use ($anoId) {
                $q->where('ano_id', $anoId);
            });
        })
        ->when($simuladoId, function ($query, $simuladoId) {
            return $query->where('simulado_id', $simuladoId);
        })
        ->get()
        ->groupBy('simulado.ano_id');
    
        foreach ($respostasPorAno as $anoId => $respostas) {
            $ano = Ano::find($anoId);
            $totalRespostasAno = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalRespostasAno > 0) ? ($acertos / $totalRespostasAno) * 100 : 0;
            $mediaFinal = ($totalRespostasAno > 0) ? ($acertos / $totalRespostasAno) * 10 : 0;
    
            $estatisticasPorAno[] = [
                'ano' => $ano->nome,
                'total_respostas' => $totalRespostasAno,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
                'media_final' => $mediaFinal,
            ];
        }
    
        // Média geral para 1º ao 5º ano e 6º ao 9º ano
        $mediaGeral1a5 = RespostaSimulado::whereHas('simulado', function ($q) {
            $q->whereIn('ano_id', range(1, 5));
        })->avg('correta') * 10;
    
        $mediaGeral6a9 = RespostaSimulado::whereHas('simulado', function ($q) {
            $q->whereIn('ano_id', range(6, 9));
        })->avg('correta') * 10;
    
        // Estatísticas por habilidade
        $estatisticasPorHabilidade = [];
        $respostasPorHabilidade = RespostaSimulado::when($habilidadeId, function ($query, $habilidadeId) {
            return $query->whereHas('pergunta', function ($q) use ($habilidadeId) {
                $q->where('habilidade_id', $habilidadeId);
            });
        })
        ->when($simuladoId, function ($query, $simuladoId) {
            return $query->where('simulado_id', $simuladoId);
        })
        ->when($anoId, function ($query, $anoId) {
            return $query->whereHas('simulado', function ($q) use ($anoId) {
                $q->where('ano_id', $anoId);
            });
        })
        ->get()
        ->groupBy('pergunta.habilidade_id');
    
        foreach ($respostasPorHabilidade as $habilidadeId => $respostas) {
            $habilidade = Habilidade::find($habilidadeId);
            $totalRespostasHabilidade = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalRespostasHabilidade > 0) ? ($acertos / $totalRespostasHabilidade) * 100 : 0;
    
            $estatisticasPorHabilidade[] = [
                'habilidade' => $habilidade->descricao,
                'total_respostas' => $totalRespostasHabilidade,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
            ];
        }
    
        return view('respostas_simulados.admin.estatisticas', compact(
            'totalSimulados',
            'totalProfessores',
            'totalAlunos',
            'totalRespostas',
            'professoresResponderam',
            'alunosResponderam',
            'estatisticasPorEscola',
            'estatisticasPorAno',
            'estatisticasPorHabilidade',
            'mediaGeral1a5',
            'mediaGeral6a9',
            'escolas',
            'anos',
            'simulados',
            'habilidades',
            'request'
        ));
    }

    
    
}