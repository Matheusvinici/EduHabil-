<?php

namespace App\Http\Controllers;

use App\Exports\EstatisticasExport;  
use App\Exports\EstatisticasProfessorExport;  
use App\Exports\EstatisticasCoordenadorExport;  


use Maatwebsite\Excel\Facades\Excel;

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

    public function exportCoordenadorExcel(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'coordenador') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Obter os dados
        $data = $this->getEstatisticasCoordenadorData($request);
        $tipo = 'geral';
    
        return Excel::download(
            new EstatisticasCoordenadorExport($data, $tipo), 
            'estatisticas_coordenador_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
    public function exportCoordenadorPdf(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'coordenador') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Obter os dados filtrados
        $data = $this->getEstatisticasCoordenadorData($request);
        
        // Gerar o PDF
        $pdf = \PDF::loadView('respostas_simulados.coordenador.pdf', $data);
        
        // Nome do arquivo
        $filename = 'estatisticas_coordenador_'.now()->format('Ymd_His').'.pdf';
        
        return $pdf->download($filename);
    }
    
    private function getEstatisticasCoordenadorData(Request $request)
    {
        $user = Auth::user();
        
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
        
        // Query base para respostas
        $respostasQuery = RespostaSimulado::whereIn('user_id', $alunosIds)
            ->with(['user', 'pergunta.habilidade', 'simulado']);
    
        // Aplica filtros
        if ($request->filled('simulado_id')) {
            $respostasQuery->where('simulado_id', $request->simulado_id);
        }
        
        if ($request->filled('ano_id')) {
            $respostasQuery->whereHas('simulado', function($q) use ($request) {
                $q->where('ano_id', $request->ano_id);
            });
        }
        
        if ($request->filled('turma_id')) {
            $respostasQuery->whereHas('user', function($q) use ($request) {
                $q->where('turma_id', $request->turma_id);
            });
        }
        
        if ($request->filled('habilidade_id')) {
            $respostasQuery->whereHas('pergunta', function($q) use ($request) {
                $q->where('habilidade_id', $request->habilidade_id);
            });
        }
    
        $respostas = $respostasQuery->get();
        $totalRespostas = $respostas->count();
    
        // Médias por faixa de ano
        $media1a5 = RespostaSimulado::whereIn('user_id', $alunosIds)
            ->whereHas('simulado', function($q) {
                $q->whereIn('ano_id', range(1, 5));
            })
            ->when($request->simulado_id, function($q) use ($request) {
                $q->where('simulado_id', $request->simulado_id);
            })
            ->avg('correta') * 10;
    
        $media6a9 = RespostaSimulado::whereIn('user_id', $alunosIds)
            ->whereHas('simulado', function($q) {
                $q->whereIn('ano_id', range(6, 9));
            })
            ->when($request->simulado_id, function($q) use ($request) {
                $q->where('simulado_id', $request->simulado_id);
            })
            ->avg('correta') * 10;
    
        // Média geral da escola
        $mediaGeralEscola = $respostas->avg('correta') * 10;
    
        // Estatísticas por turma
        $estatisticasPorTurma = [];
        $respostasPorTurma = $respostas->groupBy('user.turma_id');
        
        foreach ($respostasPorTurma as $turmaId => $respostasTurma) {
            $turma = Turma::find($turmaId);
            $totalRespostasTurma = $respostasTurma->count();
            $acertos = $respostasTurma->where('correta', true)->count();
            $porcentagemAcertos = ($totalRespostasTurma > 0) ? ($acertos / $totalRespostasTurma) * 100 : 0;
            $mediaFinal = ($totalRespostasTurma > 0) ? ($acertos / $totalRespostasTurma) * 10 : 0;
    
            $estatisticasPorTurma[] = [
                'turma' => $turma->nome_turma,
                'professor' => $turma->professor->name ?? 'N/A',
                'total_respostas' => $totalRespostasTurma,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
                'media_final' => $mediaFinal,
            ];
        }
    
        // Estatísticas por habilidade
        $estatisticasPorHabilidade = [];
        $respostasPorHabilidade = $respostas->groupBy('pergunta.habilidade_id');
        
        foreach ($respostasPorHabilidade as $habilidadeId => $respostasHabilidade) {
            $habilidade = Habilidade::find($habilidadeId);
            $totalRespostasHabilidade = $respostasHabilidade->count();
            $acertos = $respostasHabilidade->where('correta', true)->count();
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
            ->when($request->simulado_id, function($q) use ($request) {
                return $q->where('respostas_simulados.simulado_id', $request->simulado_id);
            })
            ->groupBy('perguntas.id')
            ->get();
    
        // Média por simulado
        $mediasPorSimulado = DB::table('respostas_simulados')
            ->join('simulados', 'respostas_simulados.simulado_id', '=', 'simulados.id')
            ->select('simulados.nome', DB::raw('AVG(respostas_simulados.correta) * 10 as media'))
            ->whereIn('respostas_simulados.user_id', $alunosIds)
            ->when($request->ano_id, function($q) use ($request) {
                return $q->where('simulados.ano_id', $request->ano_id);
            })
            ->groupBy('simulados.nome')
            ->get();
    
        return [
            'totalAlunos' => $totalAlunos,
            'totalProfessores' => $totalProfessores,
            'totalRespostas' => $totalRespostas,
            'media1a5' => $media1a5,
            'media6a9' => $media6a9,
            'mediaGeralEscola' => $mediaGeralEscola,
            'estatisticasPorTurma' => $estatisticasPorTurma,
            'estatisticasPorHabilidade' => $estatisticasPorHabilidade,
            'questoes' => $questoes,
            'mediasPorSimulado' => $mediasPorSimulado,
            'simulados' => Simulado::all(),
            'anos' => Ano::all(),
            'turmas' => Turma::whereIn('professor_id', $professoresIds)->get(),
            'habilidades' => Habilidade::all(),
            'request' => $request
        ];
    }
    
    public function detalhesTurma(Request $request, $turmaId)
    {
        $user = Auth::user();
        
        if ($user->role !== 'coordenador') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Busca a turma
        $turma = Turma::findOrFail($turmaId);
        
        // Verifica se a turma pertence à escola do coordenador
        if ($turma->escola_id !== $user->escola_id) {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Busca os alunos da turma
        $alunosIds = User::where('turma_id', $turmaId)
                        ->where('role', 'aluno')
                        ->pluck('id');
    
        // Estatísticas por aluno
        $estatisticasPorAluno = [];
        $respostasPorAluno = RespostaSimulado::whereIn('user_id', $alunosIds)
            ->when($request->simulado_id, function ($query, $simuladoId) {
                return $query->where('simulado_id', $simuladoId);
            })
            ->when($request->ano_id, function ($query, $anoId) {
                return $query->whereHas('simulado', function ($q) use ($anoId) {
                    $q->where('ano_id', $anoId);
                });
            })
            ->when($request->habilidade_id, function ($query, $habilidadeId) {
                return $query->whereHas('pergunta', function ($q) use ($habilidadeId) {
                    $q->where('habilidade_id', $habilidadeId);
                });
            })
            ->get()
            ->groupBy('user_id');
    
        foreach ($respostasPorAluno as $alunoId => $respostas) {
            $aluno = User::find($alunoId);
            $totalRespostas = $respostas->count();
            $acertos = $respostas->where('correta', true)->count();
            $porcentagemAcertos = ($totalRespostas > 0) ? ($acertos / $totalRespostas) * 100 : 0;
            $mediaFinal = ($totalRespostas > 0) ? ($acertos / $totalRespostas) * 10 : 0;
    
            $estatisticasPorAluno[] = [
                'aluno' => $aluno->name,
                'total_respostas' => $totalRespostas,
                'acertos' => $acertos,
                'porcentagem_acertos' => $porcentagemAcertos,
                'media_final' => $mediaFinal,
            ];
        }
    
        // Média geral da turma
        $totalRespostasTurma = RespostaSimulado::whereIn('user_id', $alunosIds)
            ->when($request->simulado_id, function ($query, $simuladoId) {
                return $query->where('simulado_id', $simuladoId);
            })
            ->when($request->ano_id, function ($query, $anoId) {
                return $query->whereHas('simulado', function ($q) use ($anoId) {
                    $q->where('ano_id', $anoId);
                });
            })
            ->count();
        
        $acertosTurma = RespostaSimulado::whereIn('user_id', $alunosIds)
            ->where('correta', true)
            ->when($request->simulado_id, function ($query, $simuladoId) {
                return $query->where('simulado_id', $simuladoId);
            })
            ->when($request->ano_id, function ($query, $anoId) {
                return $query->whereHas('simulado', function ($q) use ($anoId) {
                    $q->where('ano_id', $anoId);
                });
            })
            ->count();
        
        $mediaGeralTurma = ($totalRespostasTurma > 0) ? ($acertosTurma / $totalRespostasTurma) * 10 : 0;
    
        return view('respostas_simulados.coordenador.detalhes-turma', compact(
            'turma',
            'estatisticasPorAluno',
            'mediaGeralTurma',
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
    
        // Verifica se o aluno já respondeu este simulado
        if (RespostaSimulado::where('simulado_id', $simulado->id)
            ->where('user_id', $user->id)
            ->exists()) {
            return redirect()->route('respostas_simulados.aluno.index')
                ->with('error', 'Você já respondeu este simulado.');
        }
    
        // Carrega as perguntas com suas habilidades relacionadas
        $perguntas = $simulado->perguntas()->with('habilidade')->get();
    
        return view('respostas_simulados.create', [
            'simulado' => $simulado,
            'perguntas' => $perguntas,
            'tempo_limite' => $simulado->tempo_limite // Passa o tempo limite para a view
        ]);
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
    
        // Verifica se o aluno já respondeu este simulado
        if (RespostaSimulado::where('simulado_id', $simulado->id)
            ->where('user_id', $user->id)
            ->exists()) {
            return redirect()->route('respostas_simulados.aluno.index')
                ->with('error', 'Você já respondeu este simulado.');
        }
    
        // Validação dos dados
        $validated = $request->validate([
            'respostas' => 'required|array',
            'respostas.*' => 'required|in:A,B,C,D',
            'raca' => 'nullable|string|in:Branca,Preta,Parda,Amarela,Indígena,Prefiro não informar',
            'tempo_resposta' => 'required|integer|min:1'
        ]);
    
        // Verifica se todas as perguntas foram respondidas
        if (count($validated['respostas']) !== $simulado->perguntas->count()) {
            return back()->with('error', 'Você deve responder todas as questões do simulado.');
        }
    
        // Verifica se o tempo limite foi excedido
        if ($simulado->tempo_limite && ($validated['tempo_resposta'] > ($simulado->tempo_limite * 60))) {
            return back()->with('error', 'Tempo limite excedido. Suas respostas não foram salvas.');
        }
    
        // Inicia uma transação para garantir a integridade dos dados
        DB::beginTransaction();
    
        try {
            // Salva cada resposta do aluno
            foreach ($validated['respostas'] as $pergunta_id => $resposta) {
                $pergunta = Pergunta::findOrFail($pergunta_id);
                
                RespostaSimulado::create([
                    'user_id' => $user->id,
                    'professor_id' => $user->turma->professor_id,
                    'escola_id' => $user->escola_id,
                    'simulado_id' => $simulado->id,
                    'pergunta_id' => $pergunta_id,
                    'resposta' => $resposta,
                    'correta' => $resposta === $pergunta->resposta_correta,
                    'raca' => $validated['raca'],
                    'tempo_resposta' => $validated['tempo_resposta'],
                ]);
            }
    
            DB::commit();
    
            return redirect()->route('respostas_simulados.aluno.index')
                ->with('success', 'Simulado finalizado com sucesso!');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro ao salvar suas respostas. Por favor, tente novamente.');
        }
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
        
        if ($user->role !== 'professor') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Busca as turmas do professor
        $turmasIds = Turma::where('professor_id', $user->id)->pluck('id');
        
        // Busca os IDs dos alunos das turmas do professor
        $alunosIds = User::whereIn('turma_id', $turmasIds)
                        ->where('role', 'aluno')
                        ->pluck('id');
        
        // Total de alunos
        $totalAlunos = $alunosIds->count();
        
        // Query base para respostas
        $respostasQuery = RespostaSimulado::query()
            ->whereIn('user_id', $alunosIds)
            ->with(['user', 'pergunta.habilidade', 'simulado']);
        
        // Aplica filtros
        if ($request->filled('simulado_id')) {
            $respostasQuery->where('simulado_id', $request->simulado_id);
        }
        
        if ($request->filled('ano_id')) {
            $respostasQuery->whereHas('simulado', function($q) use ($request) {
                $q->where('ano_id', $request->ano_id);
            });
        }
        
        if ($request->filled('habilidade_id')) {
            $respostasQuery->whereHas('pergunta', function($q) use ($request) {
                $q->where('habilidade_id', $request->habilidade_id);
            });
        }
        
        $respostas = $respostasQuery->get();
        $totalRespostas = $respostas->count();
        
        // Estatísticas por aluno
        $estatisticasPorAluno = [];
        foreach ($respostas->groupBy('user_id') as $alunoId => $respostasAluno) {
            $aluno = $respostasAluno->first()->user;
            $total = $respostasAluno->count();
            $acertos = $respostasAluno->where('correta', true)->count();
            
            $estatisticasPorAluno[] = [
                'aluno' => $aluno->name,
                'total_respostas' => $total,
                'acertos' => $acertos,
                'porcentagem_acertos' => $total > 0 ? ($acertos / $total) * 100 : 0,
                'media_final' => $total > 0 ? ($acertos / $total) * 10 : 0
            ];
        }
        
        // Média por simulado
        $mediaTurmaPorSimulado = [];
        foreach ($respostas->groupBy('simulado_id') as $simuladoId => $respostasSimulado) {
            $simulado = $respostasSimulado->first()->simulado;
            $total = $respostasSimulado->count();
            $acertos = $respostasSimulado->where('correta', true)->count();
            
            $mediaTurmaPorSimulado[] = [
                'simulado' => $simulado->nome,
                'media_turma' => $total > 0 ? ($acertos / $total) * 10 : 0
            ];
        }
        
        // Estatísticas por habilidade
        $estatisticasPorHabilidade = [];
        foreach ($respostas->groupBy('pergunta.habilidade_id') as $habilidadeId => $respostasHabilidade) {
            $habilidade = $respostasHabilidade->first()->pergunta->habilidade;
            $total = $respostasHabilidade->count();
            $acertos = $respostasHabilidade->where('correta', true)->count();
            
            $estatisticasPorHabilidade[] = [
                'habilidade' => $habilidade->descricao,
                'total_respostas' => $total,
                'acertos' => $acertos,
                'porcentagem_acertos' => $total > 0 ? ($acertos / $total) * 100 : 0
            ];
        }
        
        // Obter dados para os selects
        $simulados = Simulado::all();
        $anos = Ano::all();
        $habilidades = Habilidade::all();
        
        return view('respostas_simulados.professor.index', compact(
            'totalAlunos',
            'totalRespostas',
            'estatisticasPorAluno',
            'mediaTurmaPorSimulado',
            'estatisticasPorHabilidade',
            'simulados',
            'anos',
            'habilidades',
            'request',
            'user'
        ));
    }

    public function detalhesEscola(Request $request, $escolaId)
{
    $user = Auth::user();
    
    if ($user->role !== 'admin') {
        abort(403, 'Acesso não autorizado.');
    }

    // Busca a escola
    $escola = Escola::findOrFail($escolaId);

    // Busca os alunos da escola
    $alunosIds = User::where('escola_id', $escolaId)
                    ->where('role', 'aluno')
                    ->pluck('id');

    // Estatísticas por aluno
    $estatisticasPorAluno = [];
    $respostasPorAluno = RespostaSimulado::whereIn('user_id', $alunosIds)
        ->when($request->simulado_id, function ($query, $simuladoId) {
            return $query->where('simulado_id', $simuladoId);
        })
        ->when($request->ano_id, function ($query, $anoId) {
            return $query->whereHas('simulado', function ($q) use ($anoId) {
                $q->where('ano_id', $anoId);
            });
        })
        ->when($request->habilidade_id, function ($query, $habilidadeId) {
            return $query->whereHas('pergunta', function ($q) use ($habilidadeId) {
                $q->where('habilidade_id', $habilidadeId);
            });
        })
        ->get()
        ->groupBy('user_id');

    foreach ($respostasPorAluno as $alunoId => $respostas) {
        $aluno = User::find($alunoId);
        $totalRespostas = $respostas->count();
        $acertos = $respostas->where('correta', true)->count();
        $porcentagemAcertos = ($totalRespostas > 0) ? ($acertos / $totalRespostas) * 100 : 0;
        $mediaFinal = ($totalRespostas > 0) ? ($acertos / $totalRespostas) * 10 : 0;

        $estatisticasPorAluno[] = [
            'aluno' => $aluno->name,
            'turma' => $aluno->turma ? $aluno->turma->nome_turma : 'N/A',
            'total_respostas' => $totalRespostas,
            'acertos' => $acertos,
            'porcentagem_acertos' => $porcentagemAcertos,
            'media_final' => $mediaFinal,
        ];
    }

    // Média geral da escola
    $totalRespostasEscola = RespostaSimulado::whereIn('user_id', $alunosIds)
        ->when($request->simulado_id, function ($query, $simuladoId) {
            return $query->where('simulado_id', $simuladoId);
        })
        ->when($request->ano_id, function ($query, $anoId) {
            return $query->whereHas('simulado', function ($q) use ($anoId) {
                $q->where('ano_id', $anoId);
            });
        })
        ->count();
    
    $acertosEscola = RespostaSimulado::whereIn('user_id', $alunosIds)
        ->where('correta', true)
        ->when($request->simulado_id, function ($query, $simuladoId) {
            return $query->where('simulado_id', $simuladoId);
        })
        ->when($request->ano_id, function ($query, $anoId) {
            return $query->whereHas('simulado', function ($q) use ($anoId) {
                $q->where('ano_id', $anoId);
            });
        })
        ->count();
    
    $mediaGeralEscola = ($totalRespostasEscola > 0) ? ($acertosEscola / $totalRespostasEscola) * 10 : 0;

    return view('respostas_simulados.admin.detalhes-escola', compact(
        'escola',
        'estatisticasPorAluno',
        'mediaGeralEscola',
        'request'
    ));
}


    public function exportProfessorPdf(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'professor') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Obter os dados filtrados
        $data = $this->getEstatisticasProfessorData($request);
        
        // Verificar e garantir que todas as variáveis necessárias existam
        if (!isset($data['totalAlunos'])) {
            $data['totalAlunos'] = 0;
        }
        if (!isset($data['totalRespostas'])) {
            $data['totalRespostas'] = 0;
        }
        if (!isset($data['estatisticasPorAluno'])) {
            $data['estatisticasPorAluno'] = [];
        }
        if (!isset($data['mediaTurmaPorSimulado'])) {
            $data['mediaTurmaPorSimulado'] = [];
        }
        if (!isset($data['estatisticasPorHabilidade'])) {
            $data['estatisticasPorHabilidade'] = [];
        }
    
        // Gerar o PDF
        $pdf = \PDF::loadView('respostas_simulados.professor.pdf', $data);
        
        // Nome do arquivo
        $filename = 'estatisticas_alunos_'.now()->format('Ymd_His').'.pdf';
        
        return $pdf->download($filename);
    }
    
    private function getEstatisticasProfessorData(Request $request)
    {
        $user = Auth::user();
        
        // Busca as turmas do professor
        $turmasIds = Turma::where('professor_id', $user->id)->pluck('id');
        
        // Busca os IDs dos alunos das turmas do professor
        $alunosIds = User::whereIn('turma_id', $turmasIds)
                        ->where('role', 'aluno')
                        ->pluck('id');
        
        // Total de alunos
        $totalAlunos = $alunosIds->count();
        
        // Query base para respostas
        $respostasQuery = RespostaSimulado::query()
            ->whereIn('user_id', $alunosIds)
            ->with(['user', 'pergunta.habilidade', 'simulado']);
        
        // Aplica filtros
        if ($request->filled('simulado_id')) {
            $respostasQuery->where('simulado_id', $request->simulado_id);
        }
        
        if ($request->filled('ano_id')) {
            $respostasQuery->whereHas('simulado', function($q) use ($request) {
                $q->where('ano_id', $request->ano_id);
            });
        }
        
        if ($request->filled('habilidade_id')) {
            $respostasQuery->whereHas('pergunta', function($q) use ($request) {
                $q->where('habilidade_id', $request->habilidade_id);
            });
        }
        
        $respostas = $respostasQuery->get();
        $totalRespostas = $respostas->count();
        
        // Estatísticas por aluno
        $estatisticasPorAluno = [];
        foreach ($respostas->groupBy('user_id') as $alunoId => $respostasAluno) {
            $aluno = $respostasAluno->first()->user;
            $total = $respostasAluno->count();
            $acertos = $respostasAluno->where('correta', true)->count();
            
            $estatisticasPorAluno[] = [
                'aluno' => $aluno->name,
                'total_respostas' => $total,
                'acertos' => $acertos,
                'porcentagem_acertos' => $total > 0 ? ($acertos / $total) * 100 : 0,
                'media_final' => $total > 0 ? ($acertos / $total) * 10 : 0
            ];
        }
        
        // Média por simulado
        $mediaPorSimulado = [];
        foreach ($respostas->groupBy('simulado_id') as $simuladoId => $respostasSimulado) {
            $simulado = $respostasSimulado->first()->simulado;
            $total = $respostasSimulado->count();
            $acertos = $respostasSimulado->where('correta', true)->count();
            
            $mediaPorSimulado[] = [
                'simulado' => $simulado->nome,
                'media_turma' => $total > 0 ? ($acertos / $total) * 10 : 0
            ];
        }
        
        // Estatísticas por habilidade
        $estatisticasPorHabilidade = [];
        foreach ($respostas->groupBy('pergunta.habilidade_id') as $habilidadeId => $respostasHabilidade) {
            $habilidade = $respostasHabilidade->first()->pergunta->habilidade;
            $total = $respostasHabilidade->count();
            $acertos = $respostasHabilidade->where('correta', true)->count();
            
            $estatisticasPorHabilidade[] = [
                'habilidade' => $habilidade->descricao,
                'total_respostas' => $total,
                'acertos' => $acertos,
                'porcentagem_acertos' => $total > 0 ? ($acertos / $total) * 100 : 0
            ];
        }
        
        return [
            'totalAlunos' => $totalAlunos,
            'totalRespostas' => $totalRespostas,
            'estatisticasPorAluno' => $estatisticasPorAluno,
            'mediaTurmaPorSimulado' => $mediaPorSimulado,
            'estatisticasPorHabilidade' => $estatisticasPorHabilidade,
            'request' => $request,
            'simulados' => Simulado::all(),
            'anos' => Ano::all(),
            'habilidades' => Habilidade::all(),
            'professor' => $user
        ];
    }
    
    public function exportProfessorExcel(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'professor') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Obter os dados
        $data = $this->getEstatisticasProfessorData($request);
        
        return Excel::download(
            new EstatisticasProfessorExport($data), 
            'estatisticas_alunos_'.now()->format('Ymd_His').'.xlsx'
        );
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
    
        // Inicializa as variáveis de estatísticas
        $estatisticasPorEscola = [];
        $estatisticasPorAno = [];
        $estatisticasPorHabilidade = [];
        $estatisticasPorRaca = [];
        $mediaGeral1a5 = 0;
        $mediaGeral6a9 = 0;
    
        // Query base para estatísticas
        $baseQuery = RespostaSimulado::query()
            ->when($escolaId, function ($query, $escolaId) {
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
            ->when($habilidadeId, function ($query, $habilidadeId) {
                return $query->whereHas('pergunta', function ($q) use ($habilidadeId) {
                    $q->where('habilidade_id', $habilidadeId);
                });
            });
    
        // Estatísticas por escola
        $estatisticasPorEscola = $baseQuery->clone()
            ->select(
                DB::raw('escolas.nome as escola'),
                DB::raw('COUNT(*) as total_respostas'),
                DB::raw('SUM(correta) as acertos')
            )
            ->join('users', 'respostas_simulados.user_id', '=', 'users.id')
            ->join('escolas', 'users.escola_id', '=', 'escolas.id')
            ->groupBy('escolas.nome')
            ->get()
            ->map(function ($item) {
                return [
                    'escola' => $item->escola,
                    'total_respostas' => $item->total_respostas,
                    'acertos' => $item->acertos,
                    'porcentagem_acertos' => $item->total_respostas > 0 ? ($item->acertos / $item->total_respostas) * 100 : 0,
                    'media_final' => $item->total_respostas > 0 ? ($item->acertos / $item->total_respostas) * 10 : 0
                ];
            })->toArray();
    
        // Estatísticas por ano
        $estatisticasPorAno = $baseQuery->clone()
            ->select(
                DB::raw('anos.nome as ano'),
                DB::raw('COUNT(*) as total_respostas'),
                DB::raw('SUM(correta) as acertos')
            )
            ->join('simulados', 'respostas_simulados.simulado_id', '=', 'simulados.id')
            ->join('anos', 'simulados.ano_id', '=', 'anos.id')
            ->groupBy('anos.nome')
            ->get()
            ->map(function ($item) {
                return [
                    'ano' => $item->ano,
                    'total_respostas' => $item->total_respostas,
                    'acertos' => $item->acertos,
                    'porcentagem_acertos' => $item->total_respostas > 0 ? ($item->acertos / $item->total_respostas) * 100 : 0,
                    'media_final' => $item->total_respostas > 0 ? ($item->acertos / $item->total_respostas) * 10 : 0
                ];
            })->toArray();
    
        // Estatísticas por habilidade
        $estatisticasPorHabilidade = $baseQuery->clone()
            ->select(
                DB::raw('habilidades.descricao as habilidade'),
                DB::raw('COUNT(*) as total_respostas'),
                DB::raw('SUM(correta) as acertos')
            )
            ->join('perguntas', 'respostas_simulados.pergunta_id', '=', 'perguntas.id')
            ->join('habilidades', 'perguntas.habilidade_id', '=', 'habilidades.id')
            ->groupBy('habilidades.descricao')
            ->get()
            ->map(function ($item) {
                return [
                    'habilidade' => $item->habilidade,
                    'total_respostas' => $item->total_respostas,
                    'acertos' => $item->acertos,
                    'porcentagem_acertos' => $item->total_respostas > 0 ? ($item->acertos / $item->total_respostas) * 100 : 0
                ];
            })->toArray();
    
        // Estatísticas por raça
        $estatisticasPorRaca = $baseQuery->clone()
            ->select(
                DB::raw('COALESCE(raca, "Não informado") as raca'),
                DB::raw('COUNT(*) as total_respostas'),
                DB::raw('SUM(correta) as acertos')
            )
            ->groupBy(DB::raw('COALESCE(raca, "Não informado")'))
            ->orderBy('raca')
            ->get()
            ->map(function ($item) {
                return [
                    'raca' => $item->raca,
                    'total_respostas' => $item->total_respostas,
                    'acertos' => $item->acertos,
                    'porcentagem_acertos' => $item->total_respostas > 0 ? ($item->acertos / $item->total_respostas) * 100 : 0,
                    'media_final' => $item->total_respostas > 0 ? ($item->acertos / $item->total_respostas) * 10 : 0
                ];
            })->toArray();
    
        // Médias gerais
        $mediaGeral1a5 = RespostaSimulado::whereHas('simulado', function ($q) {
                $q->whereIn('ano_id', range(1, 5));
            })->avg('correta') * 10;
    
        $mediaGeral6a9 = RespostaSimulado::whereHas('simulado', function ($q) {
                $q->whereIn('ano_id', range(6, 9));
            })->avg('correta') * 10;
    
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
            'estatisticasPorRaca',
            'mediaGeral1a5',
            'mediaGeral6a9',
            'escolas',
            'anos',
            'simulados',
            'habilidades',
            'request'
        ));
    }
    public function exportAdminPdf(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Obter os dados filtrados
        $data = $this->getEstatisticasData($request);
        
        // Adicionar informações de filtro
        $data['tipo'] = 'geral';
        $data['filtros'] = [
            'simulado' => $request->simulado_id ? Simulado::find($request->simulado_id)?->nome : null,
            'ano' => $request->ano_id ? Ano::find($request->ano_id)?->nome : null,
            'escola' => $request->escola_id ? Escola::find($request->escola_id)?->nome : null,
            'habilidade' => $request->habilidade_id ? Habilidade::find($request->habilidade_id)?->descricao : null,
        ];
    
        // Gerar o PDF
        $pdf = \PDF::loadView('respostas_simulados.admin.pdf', $data);
        
        // Nome do arquivo com filtros aplicados
        $filename = 'estatisticas_';
        if ($request->simulado_id) $filename .= 'simulado_'.$request->simulado_id.'_';
        if ($request->ano_id) $filename .= 'ano_'.$request->ano_id.'_';
        if ($request->escola_id) $filename .= 'escola_'.$request->escola_id.'_';
        if ($request->habilidade_id) $filename .= 'habilidade_'.$request->habilidade_id.'_';
        $filename .= now()->format('Ymd_His').'.pdf';
        
        return $pdf->download($filename);
    }
    
    public function exportAdminExcel(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Obter os dados
        $data = $this->getEstatisticasData($request);
        $tipo = 'geral';
    
        // Gerar o Excel usando o nome correto da classe
        return Excel::download(
            new EstatisticasExport($data, $tipo), 
            'estatisticas_simulados_' . now()->format('Ymd_His') . '.xlsx'
        );
    } 
    private function getEstatisticasData(Request $request)
    {
        // Filtros
        $escolaId = $request->input('escola_id');
        $anoId = $request->input('ano_id');
        $simuladoId = $request->input('simulado_id');
        $habilidadeId = $request->input('habilidade_id');
    
        // Buscar todas as escolas, anos, simulados e habilidades para os filtros
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
    
        return [
            'totalSimulados' => $totalSimulados,
            'totalProfessores' => $totalProfessores,
            'totalAlunos' => $totalAlunos,
            'totalRespostas' => $totalRespostas,
            'professoresResponderam' => $professoresResponderam,
            'alunosResponderam' => $alunosResponderam,
            'estatisticasPorEscola' => $estatisticasPorEscola,
            'estatisticasPorAno' => $estatisticasPorAno,
            'estatisticasPorHabilidade' => $estatisticasPorHabilidade,
            'mediaGeral1a5' => $mediaGeral1a5,
            'mediaGeral6a9' => $mediaGeral6a9,
            'escolas' => $escolas,
            'anos' => $anos,
            'simulados' => $simulados,
            'habilidades' => $habilidades,
            'request' => $request
        ];
    }
    
}