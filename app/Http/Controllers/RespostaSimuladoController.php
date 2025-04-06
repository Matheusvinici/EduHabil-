<?php

namespace App\Http\Controllers;

use App\Exports\EstatisticasExport;  
use App\Exports\EstatisticasProfessorExport;  
use App\Exports\EstatisticasCoordenadorExport;  
use App\Exports\RespostasSimuladoExport;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use PDF;
use Excel;


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
        case 'inclusiva':
            return redirect()->route('respostas_simulados.inclusiva.estatisticas');
        case 'professor':
            return redirect()->route('respostas_simulados.professor.index');
        case 'coordenador':
            return redirect()->route('respostas_simulados.coordenador.index');
        case 'aluno':
            return redirect()->route('respostas_simulados.aluno.index');
        case 'aplicador':
            return redirect()->route('respostas_simulados.aplicador.index');
        default:
            abort(403, 'Acesso não autorizado.');
    }
}

    

    public function detalhesForAplicador($id)
    {
        $aplicador = Auth::user();
        
        // Busca a resposta específica com todas as relações necessárias
        $resposta = RespostaSimulado::with([
            'simulado.perguntas', 
            'user'
        ])->findOrFail($id);
    
        // Verifica permissão
        if ($resposta->aplicador_id !== $aplicador->id) {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Busca TODAS as respostas do aluno para este simulado
        $respostasAluno = RespostaSimulado::where('simulado_id', $resposta->simulado_id)
            ->where('user_id', $resposta->user_id)
            ->get();
    
        // Calcula estatísticas
        $totalQuestoes = $resposta->simulado->perguntas_count;
        $acertos = $respostasAluno->where('correta', true)->count();
        $erros = $totalQuestoes - $acertos;
        $porcentagem = $totalQuestoes > 0 ? round(($acertos / $totalQuestoes) * 100) : 0;
    
        // Prepara detalhes por pergunta
        $detalhesPerguntas = $resposta->simulado->perguntas->map(function ($pergunta) use ($respostasAluno) {
            $respostaAluno = $respostasAluno->where('pergunta_id', $pergunta->id)->first();
            
            return [
                'enunciado' => $pergunta->enunciado,
                'resposta_aluno' => $respostaAluno->resposta ?? 'Não respondida',
                'correta' => $respostaAluno->correta ?? false,
                'alternativa_correta' => $pergunta->alternativa_correta
            ];
        });
    
        return view('respostas_simulados.aplicador.detalhes', [
            'simulado' => $resposta->simulado,
            'aluno' => $resposta->user,
            'data_aplicacao' => $resposta->created_at,
            'totalQuestoes' => $totalQuestoes,
            'acertos' => $acertos,
            'erros' => $erros,
            'porcentagem' => $porcentagem,
            'detalhesPerguntas' => $detalhesPerguntas,
            'raca' => $resposta->raca
        ]);
    }
    
   
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

    public function selectForAplicador()
{
    $user = Auth::user();
    
    // Obtém todas as escolas onde o aplicador criou turmas
    $escolas = Escola::whereHas('turmas', function($query) use ($user) {
        $query->where('aplicador_id', $user->id);
    })->get();

    // Se não tiver escolas vinculadas, mostra mensagem
    if ($escolas->isEmpty()) {
        return view('respostas_simulados.aplicador.select', [
            'escolas' => collect(),
            'simulados' => collect(),
            'escolaSelecionada' => null
        ]);
    }

    // Verifica se há escola na sessão ou pega a primeira
    $escolaId = session('escola_aplicador_id') ?? $escolas->first()->id;
    $escolaSelecionada = Escola::find($escolaId);

    // Obtém simulados paginados
    $simulados = Simulado::withCount('perguntas')
                ->paginate(6);

    return view('respostas_simulados.aplicador.select', [
        'escolas' => $escolas,
        'simulados' => $simulados,
        'escolaSelecionada' => $escolaSelecionada
    ]);
}

public function selectEscola(Request $request)
{
    $request->validate(['escola_id' => 'required|exists:escolas,id']);
    
    // Armazena a escola selecionada na sessão
    session(['escola_aplicador_id' => $request->escola_id]);
    
    return redirect()->route('respostas_simulados.aplicador.select');
}

public function createForAplicador(Simulado $simulado)
{
    $user = Auth::user();
    $escolaId = session('escola_aplicador_id');
    
    if (!$escolaId) {
        return redirect()->route('respostas_simulados.aplicador.select')
               ->with('error', 'Selecione uma escola primeiro');
    }

    // Limpa a sessão se for um novo acesso ou se foi solicitado reset
    if (!request()->has('keep_session') || request()->has('reset')) {
        session()->forget([
            'aluno_selecionado',
            'aluno_id',
            'aluno_nome',
            'aluno_turma',
            'turma_id',
            'raca'
        ]);
    }

    // Verifica se o simulado tem perguntas
    if ($simulado->perguntas->isEmpty()) {
        return redirect()->route('respostas_simulados.aplicador.select')
               ->with('error', 'Este simulado não possui perguntas cadastradas.');
    }

    // Obtém apenas turmas da escola selecionada
    $turmas = Turma::where('escola_id', $escolaId)
                ->where('aplicador_id', $user->id)
                ->orderBy('nome_turma')
                ->get();

    // Obtém alunos que já responderam
    $alunosRespondidos = RespostaSimulado::where('simulado_id', $simulado->id)
        ->where('aplicador_id', $user->id)
        ->pluck('user_id')
        ->toArray();

    return view('respostas_simulados.aplicador.create', [
        'simulado' => $simulado,
        'turmas' => $turmas,
        'alunosRespondidos' => $alunosRespondidos
    ]);
}

            public function alunosPendentes(Simulado $simulado)
            {
                $user = Auth::user();
                $escolaId = session('escola_aplicador_id');
                
                if (!$escolaId) {
                    return redirect()->route('respostas_simulados.aplicador.select')
                        ->with('error', 'Selecione uma escola primeiro');
                }

                // Alunos que já responderam
                $respondidos = RespostaSimulado::where('simulado_id', $simulado->id)
                    ->where('aplicador_id', $user->id)
                    ->with('user')
                    ->get()
                    ->groupBy('user_id');

                // Todos alunos das turmas do aplicador na escola selecionada
                $alunos = User::whereHas('turma', function($query) use ($user, $escolaId) {
                        $query->where('escola_id', $escolaId)
                            ->where('aplicador_id', $user->id);
                    })
                    ->where('role', 'aluno')
                    ->with('turma')
                    ->get();

                // Separa em respondidos e pendentes
                $alunosRespondidos = collect();
                $alunosPendentes = collect();

                foreach ($alunos as $aluno) {
                    if (isset($respondidos[$aluno->id])) {
                        $alunosRespondidos->push([
                            'id' => $aluno->id,
                            'nome' => $aluno->name,
                            'turma' => $aluno->turma->nome_turma,
                            'data' => $respondidos[$aluno->id]->first()->created_at->format('d/m/Y H:i')
                        ]);
                    } else {
                        $alunosPendentes->push([
                            'id' => $aluno->id,
                            'nome' => $aluno->name,
                            'turma' => $aluno->turma->nome_turma
                        ]);
                    }
                }

                // Paginação manual
                $perPage = 10;
                $page = request()->get('page', 1);
                $offset = ($page - 1) * $perPage;

                $alunosPendentesPaginated = new LengthAwarePaginator(
                    $alunosPendentes->slice($offset, $perPage),
                    $alunosPendentes->count(),
                    $perPage,
                    $page,
                    ['path' => request()->url()]
                );

                $alunosRespondidosPaginated = new LengthAwarePaginator(
                    $alunosRespondidos->slice($offset, $perPage),
                    $alunosRespondidos->count(),
                    $perPage,
                    $page,
                    ['path' => request()->url()]
                );

                return view('respostas_simulados.aplicador.alunos_pendentes', [
                    'simulado' => $simulado,
                    'alunosPendentes' => $alunosPendentesPaginated,
                    'alunosRespondidos' => $alunosRespondidosPaginated
                ]);
            }

    public function getAlunosPorTurma($turmaId)
    {
        try {
            \Log::info("Requisição para turma ID: $turmaId");
            
            $alunos = User::where('turma_id', $turmaId)
                        ->where('role', 'aluno')
                        ->select('id', 'name')
                        ->get();
    
            if($alunos->isEmpty()) {
                return response()->json([
                    ['id' => 0, 'name' => 'Nenhum aluno nesta turma']
                ]);
            }
    
            return response()->json($alunos);
    
        } catch (\Exception $e) {
            \Log::error("Erro ao buscar alunos: " . $e->getMessage());
            return response()->json([
                ['id' => 0, 'name' => 'Erro ao carregar alunos']
            ]);
        }
    }
    
    public function selecionarAluno(Request $request, Simulado $simulado)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'aluno_id' => 'required|exists:users,id',
            'raca' => 'required'
        ]);
    
        $turma = Turma::find($request->turma_id);
        if (!$turma) {
            return back()->withErrors('Turma não encontrada.');
        }
    
        $aluno = User::where('id', $request->aluno_id)
                   ->where('role', 'aluno')
                   ->first();
        
        if (!$aluno) {
            return back()->withErrors('Aluno não encontrado.');
        }
    
        if ($aluno->turma_id != $turma->id) {
            return back()->withErrors('O aluno selecionado não pertence à turma escolhida.');
        }

        // Verifica se o aluno já respondeu este simulado
        $respostaExistente = RespostaSimulado::where([
            'simulado_id' => $simulado->id,
            'user_id' => $aluno->id,
            'aplicador_id' => Auth::id()
        ])->exists();

        if ($respostaExistente) {
            return back()->withErrors('Este aluno já respondeu este simulado.');
        }
    
        // Armazena os dados na sessão
        session([
            'aluno_selecionado' => true,
            'aluno_id' => $aluno->id,
            'aluno_nome' => $aluno->name,
            'aluno_turma' => $turma->nome_turma,
            'turma_id' => $turma->id,
            'raca' => $request->raca,
        ]);
    
        return redirect()->route('respostas_simulados.aplicador.create', [
            'simulado' => $simulado->id,
            'keep_session' => true
        ]);
    }
    
   
   
    public function indexForAplicador()
    {
        $aplicador = Auth::user();
        
        // Obtém todas as respostas registradas pelo aplicador
        $respostas = RespostaSimulado::where('aplicador_id', $aplicador->id)
            ->with(['simulado.perguntas', 'user']) // Carrega relacionamentos necessários
            ->get();
        
        // Agrupa por simulado e aluno
        $agrupadas = $respostas->groupBy(['simulado_id', 'user_id']);
        
        $estatisticas = collect();
        
        foreach ($agrupadas as $simuladoId => $alunos) {
            foreach ($alunos as $alunoId => $respostasAluno) {
                $primeiraResposta = $respostasAluno->first();
                $simulado = $primeiraResposta->simulado;
                $aluno = $primeiraResposta->user;
                
                $totalQuestoes = $simulado->perguntas->count();
                $acertos = $respostasAluno->where('correta', true)->count();
                $porcentagem = $totalQuestoes > 0 ? ($acertos / $totalQuestoes) * 100 : 0;
                
                $estatisticas->push([
                    'aluno' => $aluno->name,
                    'simulado' => $simulado->nome,
                    'total_questoes' => $totalQuestoes,
                    'acertos' => $acertos,
                    'porcentagem' => $porcentagem,
                    'media' => $totalQuestoes > 0 ? ($acertos / $totalQuestoes) * 10 : 0,
                    'data' => $primeiraResposta->created_at,
                    'desempenho_class' => $porcentagem >= 70 ? 'success' : 
                                         ($porcentagem >= 50 ? 'warning' : 'danger')
                ]);
            }
        }
        
        return view('respostas_simulados.aplicador.index', [
            'estatisticas' => $estatisticas->sortByDesc('data')
        ]);
    }
    public function storeForAplicador(Request $request, Simulado $simulado)
    {
        $request->validate([
            'aluno_id' => 'required|exists:users,id',
            'turma_id' => 'required|exists:turmas,id',
            'raca' => 'required|string',
            'respostas' => 'required|array'
        ]);
    
        $aplicador = Auth::user();
        $aluno = User::findOrFail($request->aluno_id);
        $turma = Turma::findOrFail($request->turma_id);
    
        // Verifica se o aluno pertence à turma
        if ($aluno->turma_id != $turma->id) {
            return back()->with('error', 'Aluno não pertence à turma selecionada');
        }
    
        // Verifica se o aplicador tem vínculo com a turma
        if ($turma->aplicador_id != $aplicador->id) {
            return back()->with('error', 'Você não tem permissão para aplicar nesta turma');
        }
        
    
        // Carrega todas as perguntas com as respostas corretas
        $perguntas = $simulado->perguntas()->pluck('resposta_correta', 'perguntas.id');
    
        // Verifica se todas as perguntas foram respondidas
        $perguntasNaoRespondidas = $perguntas->keys()->diff(array_keys($request->respostas));
        if ($perguntasNaoRespondidas->isNotEmpty()) {
            return back()->with('error', 'Todas as questões devem ser respondidas');
        }
    
        DB::beginTransaction();
        try {
            foreach ($request->respostas as $perguntaId => $respostaAluno) {
                RespostaSimulado::create([
                    'simulado_id' => $simulado->id,
                    'pergunta_id' => $perguntaId,
                    'user_id' => $aluno->id,
                    'aplicador_id' => $aplicador->id,
                    'escola_id' => $turma->escola_id,
                    'resposta' => $respostaAluno,
                    'correta' => $perguntas[$perguntaId] === $respostaAluno,
                    'raca' => $request->raca
                ]);
            }
    
            DB::commit();
    
            return redirect()
                ->route('respostas_simulados.aplicador.index', ['escola_id' => $turma->escola_id])
                ->with('success', 'Simulado aplicado com sucesso!');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao salvar respostas: ' . $e->getMessage());
        }
    }
    

    /**
     * Verifica se a resposta do aluno está correta
     */
    private function verificarRespostaSimples($perguntaId, $resposta)
    {
        $pergunta = Pergunta::find($perguntaId);
        return $pergunta ? $resposta === $pergunta->alternativa_correta : false;
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
        
        // Verifica se é aluno ou aplicador/professor
        if (!in_array($user->role, ['aluno', 'aplicador', 'professor'])) {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Se for aluno, verifica se já respondeu
        if ($user->role === 'aluno') {
            if (RespostaSimulado::where('simulado_id', $simulado->id)
                ->where('user_id', $user->id)
                ->exists()) {
                return redirect()->route('respostas_simulados.aluno.index')
                    ->with('error', 'Você já respondeu este simulado.');
            }
            
            return view('respostas_simulados.aluno.create', [
                'simulado' => $simulado,
                'perguntas' => $simulado->perguntas()->with('habilidade')->get(),
                'tempo_limite' => $simulado->tempo_limite
            ]);
        }
        
        // Se for aplicador/professor
        $turmas = $user->turmas; // Assumindo que há relação entre User e Turma
        
        return view('respostas_simulados.aplicador.create', [
            'simulado' => $simulado,
            'perguntas' => $simulado->perguntas()->with('habilidade')->get(),
            'tempo_limite' => $simulado->tempo_limite,
            'turmas' => $turmas
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
        'raca' => 'required|string|in:Branca,Preta,Parda,Amarela,Indígena,Prefiro não informar',
        'tempo_resposta' => 'required|integer|min:1'
    ]);

    // Verifica se todas as perguntas foram respondidas
    if (count($validated['respostas']) !== $simulado->perguntas->count()) {
        return back()->with('error', 'Você deve responder todas as questões do simulado.');
    }

    DB::beginTransaction();

    try {
        // Obtém a turma do aluno
        $turma = $user->turma;
        
        if (!$turma) {
            throw new \Exception('Aluno não está vinculado a nenhuma turma.');
        }

        // Obtém o aplicador responsável pela turma
        $aplicador = $turma->aplicador; // Assumindo que existe relação turma->aplicador

        foreach ($validated['respostas'] as $pergunta_id => $resposta) {
            $pergunta = Pergunta::findOrFail($pergunta_id);
            
            RespostaSimulado::create([
                'user_id' => $user->id,
                'turma_id' => $turma->id,
                'aplicador_id' => $aplicador->id, // ID do aplicador que criou a turma
                'escola_id' => $turma->escola_id,
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
        return back()->with('error', 'Ocorreu um erro ao salvar suas respostas: ' . $e->getMessage());
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
   
    protected function calcularEstatisticas($respostas, $turmasIds)
    {
        // Agrupa por aluno
        $agrupadas = $respostas->groupBy('user_id');
        
        $estatisticasPorAluno = [];
        $mediaTurmaPorSimulado = [];
        $estatisticasPorHabilidade = [];
        
        foreach ($agrupadas as $alunoId => $respostasAluno) {
            $aluno = $respostasAluno->first()->user;
            
            foreach ($respostasAluno->groupBy('simulado_id') as $simuladoId => $respostasSimulado) {
                $simulado = $respostasSimulado->first()->simulado;
                
                $totalQuestoes = $simulado->perguntas->count();
                $acertos = $respostasSimulado->where('correta', true)->count();
                $porcentagem = $totalQuestoes > 0 ? ($acertos / $totalQuestoes) * 100 : 0;
                
                $estatisticasPorAluno[] = [
                    'aluno' => $aluno->name,
                    'turma' => $aluno->turma->nome,
                    'simulado' => $simulado->nome,
                    'total_questoes' => $totalQuestoes,
                    'acertos' => $acertos,
                    'porcentagem' => $porcentagem,
                    'media' => $totalQuestoes > 0 ? ($acertos / $totalQuestoes) * 10 : 0,
                    'data' => $respostasSimulado->first()->created_at,
                    'desempenho_class' => $porcentagem >= 70 ? 'success' : ($porcentagem >= 50 ? 'warning' : 'danger')
                ];
                
                // Calcula média por simulado
                if (!isset($mediaTurmaPorSimulado[$simuladoId])) {
                    $mediaTurmaPorSimulado[$simuladoId] = [
                        'simulado' => $simulado->nome,
                        'total_respostas' => 0,
                        'acertos' => 0
                    ];
                }
                $mediaTurmaPorSimulado[$simuladoId]['total_respostas'] += $totalQuestoes;
                $mediaTurmaPorSimulado[$simuladoId]['acertos'] += $acertos;
            }
            
            // Calcula por habilidade
            foreach ($respostasAluno as $resposta) {
                $habilidadeId = $resposta->pergunta->habilidade_id;
                $habilidadeNome = $resposta->pergunta->habilidade->descricao;
                
                if (!isset($estatisticasPorHabilidade[$habilidadeId])) {
                    $estatisticasPorHabilidade[$habilidadeId] = [
                        'habilidade' => $habilidadeNome,
                        'total_respostas' => 0,
                        'acertos' => 0
                    ];
                }
                
                $estatisticasPorHabilidade[$habilidadeId]['total_respostas']++;
                if ($resposta->correta) {
                    $estatisticasPorHabilidade[$habilidadeId]['acertos']++;
                }
            }
        }
        
        // Calcula médias finais
        foreach ($mediaTurmaPorSimulado as &$simulado) {
            $simulado['media_turma'] = $simulado['total_respostas'] > 0 
                ? ($simulado['acertos'] / $simulado['total_respostas']) * 10 
                : 0;
        }
        
        foreach ($estatisticasPorHabilidade as &$habilidade) {
            $habilidade['porcentagem_acertos'] = $habilidade['total_respostas'] > 0
                ? ($habilidade['acertos'] / $habilidade['total_respostas']) * 100
                : 0;
        }
        
        return [
            'estatisticasPorAluno' => $estatisticasPorAluno,
            'mediaTurmaPorSimulado' => array_values($mediaTurmaPorSimulado),
            'estatisticasPorHabilidade' => array_values($estatisticasPorHabilidade)
        ];
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
        $professor = Auth::user();
        
        if ($professor->role !== 'professor') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Inicializa todas as coleções que serão usadas
        $estatisticas = collect();
        $estatisticasHabilidades = collect();
        $mediasTurma = collect();
        $alunosSemResposta = collect();
    
        // Obter turmas do professor
        $turmas = $professor->turmasLecionadas()
            ->with(['alunos' => function($query) {
                $query->where('role', 'aluno');
            }])
            ->get();
    
        // Se não houver turmas vinculadas
        if ($turmas->isEmpty()) {
            return view('respostas_simulados.professor.index', [
                'turmas' => collect(),
                'estatisticas' => $estatisticas,
                'habilidades' => Habilidade::all(),
                'filtros' => $request->all(),
                'alunosSemResposta' => $alunosSemResposta,
                'estatisticasHabilidades' => $estatisticasHabilidades,
                'mediasTurma' => $mediasTurma,
                'graficosData' => [
                    'desempenho' => [
                        'otimo' => 0,
                        'regular' => 0,
                        'ruim' => 0
                    ],
                    'habilidades' => [
                        'labels' => [],
                        'valores' => []
                    ]
                ],
                'totalAlunosTurma' => 0,
                'alunosComDeficiencia' => 0,
                'turmaSelecionada' => null,
                'mensagemSemTurma' => 'Você não está vinculado a nenhuma turma. Por favor, entre em contato com a secretaria de educação para ser vinculado a uma turma.'
            ]);
        }
    
        // Aplicar filtros
        $turmaId = $request->turma_id ?? $turmas->first()->id;
        $habilidadeId = $request->habilidade_id;
        $simuladoId = $request->simulado_id;
        
        $turmaSelecionada = $turmas->firstWhere('id', $turmaId);
        $alunosTurma = $turmaSelecionada->alunos;
    
        // Query base para respostas
        $respostasQuery = RespostaSimulado::with([
                'simulado.perguntas.habilidade',
                'user', 
                'pergunta.habilidade'
            ])
            ->whereIn('user_id', $alunosTurma->pluck('id'));
    
        if ($habilidadeId) {
            $respostasQuery->whereHas('pergunta', function($q) use ($habilidadeId) {
                $q->where('habilidade_id', $habilidadeId);
            });
        }
    
        if ($simuladoId) {
            $respostasQuery->where('simulado_id', $simuladoId);
        }
    
        $respostas = $respostasQuery->get();
    
        // Estatísticas gerais
        $agrupadas = $respostas->groupBy(['simulado_id', 'user_id']);
    
        foreach ($agrupadas as $simuladoId => $alunos) {
            foreach ($alunos as $alunoId => $respostasAluno) {
                $primeiraResposta = $respostasAluno->first();
                $simulado = $primeiraResposta->simulado;
                $aluno = $primeiraResposta->user;
                
                $totalQuestoes = $simulado->perguntas->count();
                $acertos = $respostasAluno->where('correta', true)->count();
                $porcentagem = $totalQuestoes > 0 ? ($acertos / $totalQuestoes) * 100 : 0;
                
                $estatisticas->push([
                    'aluno_id' => $aluno->id,
                    'aluno' => $aluno->name,
                    'turma' => $turmaSelecionada->nome_turma,
                    'simulado_id' => $simulado->id,
                    'simulado' => $simulado->nome,
                    'total_questoes' => $totalQuestoes,
                    'acertos' => $acertos,
                    'porcentagem' => $porcentagem,
                    'media' => $totalQuestoes > 0 ? ($acertos / $totalQuestoes) * 10 : 0,
                    'data' => $primeiraResposta->created_at,
                    'deficiencia' => $aluno->deficiencia,
                    'desempenho_class' => $porcentagem >= 70 ? 'success' : 
                                         ($porcentagem >= 50 ? 'warning' : 'danger')
                ]);
            }
        }
    
        // Estatísticas por habilidade
        if (!$habilidadeId) {
            $habilidadesRespondidas = $respostas->pluck('pergunta.habilidade')
                ->unique()
                ->filter();
    
            foreach ($habilidadesRespondidas as $habilidade) {
                if (!$habilidade) continue;
                
                $respostasHabilidade = $respostas->filter(function($resposta) use ($habilidade) {
                    return optional($resposta->pergunta)->habilidade_id == $habilidade->id;
                });
    
                $totalRespostas = $respostasHabilidade->count();
                $acertos = $respostasHabilidade->where('correta', true)->count();
                $porcentagem = $totalRespostas > 0 ? ($acertos / $totalRespostas) * 100 : 0;
    
                $estatisticasHabilidades->push([
                    'habilidade_id' => $habilidade->id,
                    'habilidade' => $habilidade->descricao,
                    'total_respostas' => $totalRespostas,
                    'acertos' => $acertos,
                    'porcentagem' => $porcentagem,
                    'media' => $totalRespostas > 0 ? ($acertos / $totalRespostas) * 10 : 0,
                    'desempenho_class' => $porcentagem >= 70 ? 'success' : 
                                         ($porcentagem >= 50 ? 'warning' : 'danger')
                ]);
            }
        }
    
        // Médias por turma/simulado
        if (!$simuladoId) {
            $agrupadasPorSimulado = $estatisticas->groupBy('simulado_id');
            
            foreach ($agrupadasPorSimulado as $simuladoId => $dados) {
                $primeiroItem = $dados->first();
                
                $mediasTurma->push([
                    'simulado_id' => $simuladoId,
                    'simulado' => $primeiroItem['simulado'],
                    'quantidade_alunos' => $dados->count(),
                    'media_porcentagem' => $dados->avg('porcentagem'),
                    'media_nota' => $dados->avg('media')
                ]);
            }
        }
    
        // Alunos sem resposta
        $alunosComResposta = $estatisticas->pluck('aluno_id')->unique();
        $alunosSemResposta = $alunosTurma->whereNotIn('id', $alunosComResposta);
        $alunosComDeficiencia = $alunosTurma->where('deficiencia', true)->count();
    
        // Dados para os novos gráficos
        $graficosData = [
            'desempenho' => [
                'otimo' => $estatisticas->where('porcentagem', '>=', 70)->count(),
                'regular' => $estatisticas->whereBetween('porcentagem', [50, 69])->count(),
                'ruim' => $estatisticas->where('porcentagem', '<', 50)->count()
            ],
            'habilidades' => [
                'labels' => $estatisticasHabilidades->pluck('habilidade')->toArray(),
                'valores' => $estatisticasHabilidades->pluck('porcentagem')->map(function($item) {
                    return (float) number_format($item, 2);
                })->toArray()
            ]
        ];
    
        // Paginação
        $perPage = 15;
        $estatisticas = $this->paginateCollection($estatisticas, $perPage, 'resultados_page');
        $estatisticasHabilidades = $this->paginateCollection($estatisticasHabilidades, $perPage, 'habilidades_page');
        $alunosSemResposta = $this->paginateCollection($alunosSemResposta, $perPage, 'alunos_sem_resposta_page');
        $mediasTurma = $this->paginateCollection($mediasTurma, $perPage, 'medias_page');
    
        // Obter lista de simulados para o filtro
        $simulados = Simulado::whereHas('respostas', function($q) use ($alunosTurma) {
            $q->whereIn('user_id', $alunosTurma->pluck('id'));
        })->get();
    
        return view('respostas_simulados.professor.index', [
            'turmas' => $turmas,
            'simulados' => $simulados,
            'estatisticas' => $estatisticas,
            'habilidades' => Habilidade::all(),
            'filtros' => $request->all(),
            'alunosSemResposta' => $alunosSemResposta,
            'estatisticasHabilidades' => $estatisticasHabilidades,
            'mediasTurma' => $mediasTurma,
            'graficosData' => $graficosData,
            'totalAlunosTurma' => $alunosTurma->count(),
            'alunosComDeficiencia' => $alunosComDeficiencia,
            'turmaSelecionada' => $turmaSelecionada,
            'mensagemSemTurma' => null
        ]);
    }
    
    // Método auxiliar para paginar coleções
    private function paginateCollection($items, $perPage, $pageName = 'page')
    {
        $page = LengthAwarePaginator::resolveCurrentPage($pageName);
        $sliced = $items->slice(($page - 1) * $perPage, $perPage);
        return new LengthAwarePaginator(
            $sliced,
            $items->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => $pageName]
        );
    }
    
    // Método auxiliar para gerar cores dos gráficos
    private function generateChartColors($count)
    {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = '#' . substr(md5(rand()), 0, 6);
        }
        return $colors;
    }
    
  
    
    public function exportarPdf(Request $request)
    {
        // Obter os dados da estatística
        $response = $this->estatisticasProfessor($request);
        
        // Se a resposta for uma view, pegue os dados
        if ($response instanceof \Illuminate\View\View) {
            $viewData = $response->getData();
        } 
        // Se for uma resposta JSON, decodifique
        elseif ($response instanceof \Illuminate\Http\JsonResponse) {
            $viewData = $response->getData();
        } else {
            $viewData = (object)[];
        }
    
        // Converter para array se for objeto
        $viewData = (array)$viewData;
    
        // Garantir que as chaves existam
        $viewData['estatisticas'] = $viewData['estatisticas'] ?? [];
        $viewData['alunosSemResposta'] = $viewData['alunosSemResposta'] ?? [];
        $viewData['turmaSelecionada'] = $viewData['turmaSelecionada'] ?? null;
        $viewData['filtros'] = $request->all();
    
        // Debug - verifique os dados antes de gerar o PDF
        \Log::info('Dados para o PDF:', $viewData);
    
        // Gerar o PDF
        $pdf = \PDF::loadView('respostas_simulados.professor.pdf', $viewData);
        
        return $pdf->download('relatorio-'.now()->format('YmdHis').'.pdf');
    }
    
    public function exportarExcel(Request $request)
    {
        // Obter os dados
        $response = $this->estatisticasProfessor($request);
        $data = $response->getData();
        
        // Verificar e garantir a estrutura dos dados
        $exportData = [
            'estatisticas' => $data->estatisticas ?? [],
            'alunosSemResposta' => $data->alunosSemResposta ?? [],
            'totalAlunosTurma' => $data->totalAlunosTurma ?? 0,
            'alunosComDeficiencia' => $data->alunosComDeficiencia ?? 0
        ];
    
        return Excel::download(
            new RespostasSimuladoExport(
                $exportData,
                $data->turmaSelecionada ?? null,
                $request->all()
            ),
            'relatorio-desempenho-'.now()->format('Y-m-d_H-i').'.xlsx'
        );
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

     public function estatisticasInclusiva(Request $request)
     {
         $user = Auth::user();
     
         if ($user->role !== 'admin') {
             abort(403, 'Acesso não autorizado.');
         }
     
         // Filtros
         $filtros = [
             'escola_id' => $request->input('escola_id'),
             'ano_id' => $request->input('ano_id'),
             'simulado_id' => $request->input('simulado_id'),
             'habilidade_id' => $request->input('habilidade_id'),
             'deficiencia' => $request->input('deficiencia')
         ];
     
         // Busca todas as escolas, anos, simulados e habilidades para os filtros
         $escolas = Escola::all();
         $anos = Ano::all();
         $simulados = Simulado::all();
         $habilidades = Habilidade::all();
     
         // Query base para usuários com filtros de escola
         $baseUserQuery = User::query()
             ->when($filtros['escola_id'], function ($query) use ($filtros) {
                 return $query->where('escola_id', $filtros['escola_id']);
             });
     
         // Dados gerais COM FILTROS
         $totalSimulados = Simulado::when($filtros['ano_id'], function($query) use ($filtros) {
                 return $query->where('ano_id', $filtros['ano_id']);
             })->count();
     
         $totalProfessores = (clone $baseUserQuery)->where('role', 'professor')->count();
         $totalAlunos = (clone $baseUserQuery)->where('role', 'aluno')->count();
         
         $totalAlunosComDeficiencia = (clone $baseUserQuery)
             ->where('role', 'aluno')
             ->when($filtros['deficiencia'] && $filtros['deficiencia'] !== 'ND', function($query) use ($filtros) {
                 return $query->where('deficiencia', $filtros['deficiencia']);
             })
             ->when($filtros['deficiencia'] === 'ND', function($query) {
                 return $query->whereNull('deficiencia');
             })
             ->when(!$filtros['deficiencia'], function($query) {
                 return $query->whereNotNull('deficiencia');
             })
             ->count();
     
         // Query base para estatísticas de respostas (com correção para coluna ambígua)
         $baseQuery = RespostaSimulado::query()
             ->when($filtros['escola_id'], function ($query) use ($filtros) {
                 return $query->where('respostas_simulados.escola_id', $filtros['escola_id']);
             })
             ->when($filtros['simulado_id'], function ($query) use ($filtros) {
                 return $query->where('simulado_id', $filtros['simulado_id']);
             })
             ->when($filtros['ano_id'], function ($query) use ($filtros) {
                 return $query->whereHas('simulado', function ($q) use ($filtros) {
                     $q->where('ano_id', $filtros['ano_id']);
                 });
             })
             ->when($filtros['habilidade_id'], function ($query) use ($filtros) {
                 return $query->whereHas('pergunta', function ($q) use ($filtros) {
                     $q->where('habilidade_id', $filtros['habilidade_id']);
                 });
             })
             ->when($filtros['deficiencia'], function ($query) use ($filtros) {
                 if ($filtros['deficiencia'] === 'ND') {
                     return $query->whereHas('user', function($q) {
                         $q->whereNull('deficiencia');
                     });
                 }
                 return $query->whereHas('user', function($q) use ($filtros) {
                     $q->where('deficiencia', $filtros['deficiencia']);
                 });
             });
     
         // Totais que responderam
         $totalRespostas = $baseQuery->count();
         
         $professoresResponderam = (clone $baseUserQuery)
             ->where('role', 'professor')
             ->whereHas('respostasSimulado', function($q) use ($baseQuery) {
                 $q->whereIn('id', $baseQuery->select('id')->getQuery());
             })->count();
     
         $alunosResponderam = (clone $baseUserQuery)
             ->where('role', 'aluno')
             ->whereHas('respostasSimulado', function($q) use ($baseQuery) {
                 $q->whereIn('id', $baseQuery->select('id')->getQuery());
             })->count();
     
         // Estatísticas por escola (com correção para coluna ambígua)
         $estatisticasPorEscola = $baseQuery->clone()
             ->select(
                 DB::raw('escolas.nome as escola'),
                 DB::raw('COUNT(*) as total_respostas'),
                 DB::raw('SUM(correta) as acertos')
             )
             ->join('escolas', 'respostas_simulados.escola_id', '=', 'escolas.id')
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
     
         // Estatísticas por raça (corrigido para usar a coluna da tabela respostas_simulados)
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
     
         // Estatísticas por deficiência (com correção para coluna ambígua)
         $estatisticasPorDeficiencia = $baseQuery->clone()
             ->select(
                 DB::raw('COALESCE(users.deficiencia, "ND") as deficiencia'),
                 DB::raw('COUNT(*) as total_respostas'),
                 DB::raw('SUM(correta) as acertos')
             )
             ->join('users', 'respostas_simulados.user_id', '=', 'users.id')
             ->groupBy('users.deficiencia')
             ->orderBy('users.deficiencia')
             ->get()
             ->map(function ($item) {
                 return [
                     'deficiencia' => $item->deficiencia,
                     'total_respostas' => $item->total_respostas,
                     'acertos' => $item->acertos,
                     'porcentagem_acertos' => $item->total_respostas > 0 ? ($item->acertos / $item->total_respostas) * 100 : 0,
                     'media_final' => $item->total_respostas > 0 ? ($item->acertos / $item->total_respostas) * 10 : 0
                 ];
             })->toArray();
     
         // Alunos por deficiência (quantidade)
         $alunosPorDeficiencia = (clone $baseUserQuery)
             ->where('role', 'aluno')
             ->select('deficiencia', DB::raw('count(*) as total'))
             ->groupBy('deficiencia')
             ->orderBy('deficiencia')
             ->get()
             ->map(function ($item) {
                 return [
                     'deficiencia' => $item->deficiencia ?? 'ND',
                     'total' => $item->total
                 ];
             })->toArray();
     
         // Médias gerais com filtros
         $mediaGeral1a5 = (clone $baseQuery)
             ->whereHas('simulado', function ($q) {
                 $q->whereIn('ano_id', range(1, 5));
             })
             ->avg('correta') * 10;
     
         $mediaGeral6a9 = (clone $baseQuery)
             ->whereHas('simulado', function ($q) {
                 $q->whereIn('ano_id', range(6, 9));
             })
             ->avg('correta') * 10;
     
         return view('respostas_simulados.inclusiva.estatisticas', compact(
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
             'totalAlunosComDeficiencia',
             'alunosPorDeficiencia',
             'estatisticasPorDeficiencia',
             'escolas',
             'anos',
             'simulados',
             'habilidades',
             'filtros',
             'request'
         ));
     }
    public function estatisticasAdmin(Request $request)
    {
        $user = Auth::user();
    
        if ($user->role !== 'admin') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Filtros
        $filtros = [
            'escola_id' => $request->input('escola_id'),
            'ano_id' => $request->input('ano_id'),
            'simulado_id' => $request->input('simulado_id'),
            'habilidade_id' => $request->input('habilidade_id'),
            'deficiencia' => $request->input('deficiencia')
        ];
    
        // Busca todas as escolas, anos, simulados e habilidades para os filtros
        $escolas = Escola::all();
        $anos = Ano::all();
        $simulados = Simulado::all();
        $habilidades = Habilidade::all();
    
        // Query base para usuários com filtros de escola
        $baseUserQuery = User::query()
            ->when($filtros['escola_id'], function ($query) use ($filtros) {
                return $query->where('escola_id', $filtros['escola_id']);
            });
    
        // Dados gerais COM FILTROS
        $totalSimulados = Simulado::when($filtros['ano_id'], function($query) use ($filtros) {
                return $query->where('ano_id', $filtros['ano_id']);
            })->count();
    
        $totalProfessores = (clone $baseUserQuery)->where('role', 'professor')->count();
        $totalAlunos = (clone $baseUserQuery)->where('role', 'aluno')->count();
        
        $totalAlunosComDeficiencia = (clone $baseUserQuery)
            ->where('role', 'aluno')
            ->when($filtros['deficiencia'] && $filtros['deficiencia'] !== 'ND', function($query) use ($filtros) {
                return $query->where('deficiencia', $filtros['deficiencia']);
            })
            ->when($filtros['deficiencia'] === 'ND', function($query) {
                return $query->whereNull('deficiencia');
            })
            ->when(!$filtros['deficiencia'], function($query) {
                return $query->whereNotNull('deficiencia');
            })
            ->count();
    
        // Query base para estatísticas de respostas (com correção para coluna ambígua)
        $baseQuery = RespostaSimulado::query()
            ->when($filtros['escola_id'], function ($query) use ($filtros) {
                return $query->where('respostas_simulados.escola_id', $filtros['escola_id']);
            })
            ->when($filtros['simulado_id'], function ($query) use ($filtros) {
                return $query->where('simulado_id', $filtros['simulado_id']);
            })
            ->when($filtros['ano_id'], function ($query) use ($filtros) {
                return $query->whereHas('simulado', function ($q) use ($filtros) {
                    $q->where('ano_id', $filtros['ano_id']);
                });
            })
            ->when($filtros['habilidade_id'], function ($query) use ($filtros) {
                return $query->whereHas('pergunta', function ($q) use ($filtros) {
                    $q->where('habilidade_id', $filtros['habilidade_id']);
                });
            })
            ->when($filtros['deficiencia'], function ($query) use ($filtros) {
                if ($filtros['deficiencia'] === 'ND') {
                    return $query->whereHas('user', function($q) {
                        $q->whereNull('deficiencia');
                    });
                }
                return $query->whereHas('user', function($q) use ($filtros) {
                    $q->where('deficiencia', $filtros['deficiencia']);
                });
            });
    
        // Totais que responderam
        $totalRespostas = $baseQuery->count();
        
        $professoresResponderam = (clone $baseUserQuery)
            ->where('role', 'professor')
            ->whereHas('respostasSimulado', function($q) use ($baseQuery) {
                $q->whereIn('id', $baseQuery->select('id')->getQuery());
            })->count();
    
        $alunosResponderam = (clone $baseUserQuery)
            ->where('role', 'aluno')
            ->whereHas('respostasSimulado', function($q) use ($baseQuery) {
                $q->whereIn('id', $baseQuery->select('id')->getQuery());
            })->count();
    
        // Estatísticas por escola (com correção para coluna ambígua)
        $estatisticasPorEscola = $baseQuery->clone()
            ->select(
                DB::raw('escolas.nome as escola'),
                DB::raw('COUNT(*) as total_respostas'),
                DB::raw('SUM(correta) as acertos')
            )
            ->join('escolas', 'respostas_simulados.escola_id', '=', 'escolas.id')
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
    
        // Estatísticas por raça (corrigido para usar a coluna da tabela respostas_simulados)
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
    
        // Estatísticas por deficiência (com correção para coluna ambígua)
        $estatisticasPorDeficiencia = $baseQuery->clone()
            ->select(
                DB::raw('COALESCE(users.deficiencia, "ND") as deficiencia'),
                DB::raw('COUNT(*) as total_respostas'),
                DB::raw('SUM(correta) as acertos')
            )
            ->join('users', 'respostas_simulados.user_id', '=', 'users.id')
            ->groupBy('users.deficiencia')
            ->orderBy('users.deficiencia')
            ->get()
            ->map(function ($item) {
                return [
                    'deficiencia' => $item->deficiencia,
                    'total_respostas' => $item->total_respostas,
                    'acertos' => $item->acertos,
                    'porcentagem_acertos' => $item->total_respostas > 0 ? ($item->acertos / $item->total_respostas) * 100 : 0,
                    'media_final' => $item->total_respostas > 0 ? ($item->acertos / $item->total_respostas) * 10 : 0
                ];
            })->toArray();
    
        // Alunos por deficiência (quantidade)
        $alunosPorDeficiencia = (clone $baseUserQuery)
            ->where('role', 'aluno')
            ->select('deficiencia', DB::raw('count(*) as total'))
            ->groupBy('deficiencia')
            ->orderBy('deficiencia')
            ->get()
            ->map(function ($item) {
                return [
                    'deficiencia' => $item->deficiencia ?? 'ND',
                    'total' => $item->total
                ];
            })->toArray();
    
        // Médias gerais com filtros
        $mediaGeral1a5 = (clone $baseQuery)
            ->whereHas('simulado', function ($q) {
                $q->whereIn('ano_id', range(1, 5));
            })
            ->avg('correta') * 10;
    
        $mediaGeral6a9 = (clone $baseQuery)
            ->whereHas('simulado', function ($q) {
                $q->whereIn('ano_id', range(6, 9));
            })
            ->avg('correta') * 10;
    
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
            'totalAlunosComDeficiencia',
            'alunosPorDeficiencia',
            'estatisticasPorDeficiencia',
            'escolas',
            'anos',
            'simulados',
            'habilidades',
            'filtros',
            'request'
        ));
    }
    public function exportAdminPdf(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Acesso não autorizado.');
        }
    
        // Obter os mesmos dados que são usados na view normal
        $data = [
            'totalSimulados' => Simulado::count(),
            'totalProfessores' => User::where('role', 'professor')->count(),
            'totalAlunos' => User::where('role', 'aluno')->count(),
            'totalRespostas' => RespostaSimulado::count(),
            'professoresResponderam' => User::where('role', 'professor')->whereHas('respostasSimulado')->count(),
            'alunosResponderam' => User::where('role', 'aluno')->whereHas('respostasSimulado')->count(),
            'mediaGeral1a5' => RespostaSimulado::whereHas('simulado', fn($q) => $q->whereIn('ano_id', range(1, 5)))->avg('correta') * 10,
            'mediaGeral6a9' => RespostaSimulado::whereHas('simulado', fn($q) => $q->whereIn('ano_id', range(6, 9)))->avg('correta') * 10,
            'estatisticasPorEscola' => $this->getEstatisticasPorEscola($request),
            'estatisticasPorAno' => $this->getEstatisticasPorAno($request),
            'estatisticasPorHabilidade' => $this->getEstatisticasPorHabilidade($request),
            'estatisticasPorRaca' => $this->getEstatisticasPorRaca($request),
            'escolas' => Escola::all(),
            'anos' => Ano::all(),
            'simulados' => Simulado::all(),
            'habilidades' => Habilidade::all(),
            'request' => $request,
            'filtros' => [
                'simulado' => $request->simulado_id ? Simulado::find($request->simulado_id)?->nome : null,
                'ano' => $request->ano_id ? Ano::find($request->ano_id)?->nome : null,
                'escola' => $request->escola_id ? Escola::find($request->escola_id)?->nome : null,
                'habilidade' => $request->habilidade_id ? Habilidade::find($request->habilidade_id)?->descricao : null,
            ]
        ];
    
        $pdf = \PDF::loadView('respostas_simulados.admin.pdf', $data);
        
        $filename = 'estatisticas_';
        if ($request->simulado_id) $filename .= 'simulado_'.$request->simulado_id.'_';
        if ($request->ano_id) $filename .= 'ano_'.$request->ano_id.'_';
        if ($request->escola_id) $filename .= 'escola_'.$request->escola_id.'_';
        if ($request->habilidade_id) $filename .= 'habilidade_'.$request->habilidade_id.'_';
        $filename .= now()->format('Ymd_His').'.pdf';
        
        return $pdf->download($filename);
    }
    
    // Métodos auxiliares para cada tipo de estatística
    private function getEstatisticasPorEscola(Request $request)
    {
        return RespostaSimulado::query()
            ->when($request->escola_id, fn($q, $escolaId) => $q->whereHas('user', fn($q) => $q->where('escola_id', $escolaId)))
            ->when($request->simulado_id, fn($q, $simuladoId) => $q->where('simulado_id', $simuladoId))
            ->when($request->ano_id, fn($q, $anoId) => $q->whereHas('simulado', fn($q) => $q->where('ano_id', $anoId)))
            ->when($request->habilidade_id, fn($q, $habilidadeId) => $q->whereHas('pergunta', fn($q) => $q->where('habilidade_id', $habilidadeId)))
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
    }
    
    private function getEstatisticasPorRaca(Request $request)
    {
        return RespostaSimulado::query()
            ->when($request->escola_id, fn($q, $escolaId) => $q->whereHas('user', fn($q) => $q->where('escola_id', $escolaId)))
            ->when($request->simulado_id, fn($q, $simuladoId) => $q->where('simulado_id', $simuladoId))
            ->when($request->ano_id, fn($q, $anoId) => $q->whereHas('simulado', fn($q) => $q->where('ano_id', $anoId)))
            ->when($request->habilidade_id, fn($q, $habilidadeId) => $q->whereHas('pergunta', fn($q) => $q->where('habilidade_id', $habilidadeId)))
            ->select(
                DB::raw('COALESCE(respostas_simulados.raca, "Não informado") as raca'),
                DB::raw('COUNT(*) as total_respostas'),
                DB::raw('SUM(correta) as acertos')
            )
            ->groupBy(DB::raw('COALESCE(respostas_simulados.raca, "Não informado")'))
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
    }

    private function getEstatisticasPorAno(Request $request)
    {
        return RespostaSimulado::query()
            ->when($request->escola_id, fn($q, $escolaId) => $q->whereHas('user', fn($q) => $q->where('escola_id', $escolaId)))
            ->when($request->simulado_id, fn($q, $simuladoId) => $q->where('simulado_id', $simuladoId))
            ->when($request->ano_id, fn($q, $anoId) => $q->whereHas('simulado', fn($q) => $q->where('ano_id', $anoId)))
            ->when($request->habilidade_id, fn($q, $habilidadeId) => $q->whereHas('pergunta', fn($q) => $q->where('habilidade_id', $habilidadeId)))
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
    }
    
    private function getEstatisticasPorHabilidade(Request $request)
    {
        return RespostaSimulado::query()
            ->when($request->escola_id, fn($q, $escolaId) => $q->whereHas('user', fn($q) => $q->where('escola_id', $escolaId)))
            ->when($request->simulado_id, fn($q, $simuladoId) => $q->where('simulado_id', $simuladoId))
            ->when($request->ano_id, fn($q, $anoId) => $q->whereHas('simulado', fn($q) => $q->where('ano_id', $anoId)))
            ->when($request->habilidade_id, fn($q, $habilidadeId) => $q->whereHas('pergunta', fn($q) => $q->where('habilidade_id', $habilidadeId)))
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
    }
    
    // Implemente métodos similares para getEstatisticasPorAno e getEstatisticasPorHabilidade
    
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
    
        $estatisticasPorAno = DB::table('anos')
        ->leftJoin('simulados', 'simulados.ano_id', '=', 'anos.id')
        ->leftJoin('respostas_simulados', 'respostas_simulados.simulado_id', '=', 'simulados.id')
        ->select(
            'anos.nome as ano',
            DB::raw('COUNT(respostas_simulados.id) as total_respostas'),
            DB::raw('SUM(respostas_simulados.correta) as acertos')
        )
        ->groupBy('anos.id', 'anos.nome')
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