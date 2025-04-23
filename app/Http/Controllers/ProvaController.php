<?php

namespace App\Http\Controllers;

use App\Models\Prova;
use App\Models\Questao;
use App\Models\Ano;
use App\Models\Disciplina;
use App\Models\Habilidade;
use App\Models\Escola;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ProvaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('escola.selecionada')->only(['create', 'store']);
    }

    public function index()
    {
        $user = Auth::user();
        
        switch ($user->role) {
            case 'admin':
            case 'aplicador':
            case 'tutor':
                return redirect()->route('provas.admin.estatisticas-rede');
                
            case 'coordenador':
            case 'gestor':
            case 'aee':
                // Obter a escola selecionada ou primeira escola vinculada
                $escolaId = $this->getEscolaId();
                
                if (!$escolaId) {
                    // Redireciona para seleção de escola se não houver escola definida
                    return redirect()->route('coordenador.selecionar.escola')
                           ->with('warning', 'Por favor, selecione uma escola.');
                }
                
                // Redireciona para as estatísticas da escola específica
                return redirect()->route('provas.coordenador.estatisticas-escola', ['escola' => $escolaId]);
                
            case 'professor':
                $escolaId = $this->getEscolaId();
                $provas = Prova::with(['ano', 'disciplina', 'habilidade', 'professor', 'escola'])
                    ->where('user_id', $user->id)
                    ->when($escolaId, function($query) use ($escolaId) {
                        $query->where('escola_id', $escolaId);
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                
                return view('provas.professor.index', compact('provas'));
                
            default:
                abort(403, 'Acesso não autorizado para este perfil.');
        }
    } 
    public function indexProfessor(Request $request)
    {
        $user = Auth::user();
        $professorId = $request->professor_id;
    
        // Se for um coordenador/gestor visualizando provas de outro professor
        if ($professorId && in_array($user->role, ['coordenador', 'gestor', 'aee'])) {
            $professor = User::where('role', 'professor')
                           ->findOrFail($professorId);
            
            // Verifica se o professor pertence à mesma escola
            $escolaId = $this->getEscolaId();
            if (!$professor->escolas->contains($escolaId)) {
                abort(403, 'Este professor não pertence à sua escola.');
            }
    
            $provas = Prova::where('user_id', $professorId)
                         ->with(['ano', 'disciplina', 'habilidade', 'escola'])
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);
    
            return view('provas.professor.index', [
                'provas' => $provas,
                'professor' => $professor,
                'isCoordenadorView' => true
            ]);
        }
    
        // Se for o professor visualizando suas próprias provas
        if ($user->role === 'professor') {
            $escolaId = $this->getEscolaId();
            $provas = Prova::where('user_id', $user->id)
                         ->when($escolaId, function($query) use ($escolaId) {
                             $query->where('escola_id', $escolaId);
                         })
                         ->with(['ano', 'disciplina', 'habilidade', 'escola'])
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);
    
            return view('provas.professor.index', [
                'provas' => $provas,
                'professor' => $user,
                'isCoordenadorView' => false
            ]);
        }
    
        abort(403, 'Acesso não autorizado.');
    }
    public function create()
    {
        $user = Auth::user();
        
        // Verifica se tem escola vinculada
        if (!$user->escolas()->exists()) {
            return redirect()->route('profile.edit')
                   ->with('error', 'Você precisa estar vinculado a uma escola para criar provas.');
        }

        $disciplinas = Disciplina::all();
        $anos = Ano::all();
        $habilidades = Habilidade::all();

        return view('provas.create', compact('disciplinas', 'anos', 'habilidades'));
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

        $user = Auth::user();
        
        // Obtém a escola ID (da sessão ou única vinculada)
        $escolaId = $this->getEscolaId();
        
        if (!$escolaId) {
            return redirect()->route('selecionar.escola')
                   ->with('error', 'Selecione uma escola para criar a prova.');
        }

        // Cria a prova
        $prova = Prova::create([
            'user_id' => $user->id,
            'escola_id' => $escolaId,
            'ano_id' => $request->ano_id,
            'disciplina_id' => $request->disciplina_id,
            'habilidade_id' => $request->habilidade_id,
            'nome' => $request->nome,
            'data' => $request->data,
            'observacoes' => $request->observacoes,
        ]);

        // Seleciona questões aleatórias
        $questoes = Questao::where('ano_id', $request->ano_id)
            ->where('disciplina_id', $request->disciplina_id)
            ->where('habilidade_id', $request->habilidade_id)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        if ($questoes->count() < 10) {
            $prova->delete();
            return back()->withInput()
                   ->with('error', 'Não há questões suficientes para criar a prova (mínimo 10).');
        }

        // Vincula as questões à prova
        $prova->questoes()->attach($questoes->pluck('id'));

        return redirect()->route('provas.index')
               ->with('success', 'Prova criada com sucesso!');
    }

    public function show($id)
    {
        $user = Auth::user();
        $prova = Prova::with(['questoes', 'ano', 'disciplina', 'habilidade', 'professor', 'escola'])
                     ->findOrFail($id);
        
        // Verificação de permissão
        if ($user->role === 'professor' && $prova->user_id !== $user->id) {
            abort(403, 'Acesso não autorizado');
        }
        
        if (in_array($user->role, ['coordenador', 'gestor', 'aee'])) {
            if (!$user->escolas->contains($prova->escola_id)) {
                abort(403, 'Acesso não autorizado');
            }
        }
        
        return view('provas.show', compact('prova'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $prova = Prova::findOrFail($id);
        
        // Verificação de permissão
        if ($user->role === 'professor' && $prova->user_id !== $user->id) {
            abort(403, 'Acesso não autorizado');
        }
        
        if (in_array($user->role, ['coordenador', 'gestor', 'aee'])) {
            if (!$user->escolas->contains($prova->escola_id)) {
                abort(403, 'Acesso não autorizado');
            }
        }
                  
        $prova->delete();
        
        return redirect()->route('provas.index')
               ->with('success', 'Prova removida com sucesso!');
    }

    public function downloadPdf($id)
    {
        $user = Auth::user();
        $prova = Prova::with([
            'questoes',
            'ano',
            'disciplina',
            'habilidade',
            'professor', // Usando o relacionamento professor em vez de user
            'escola'
        ])->findOrFail($id);
        
        // Verificação de permissão
        if ($user->role === 'professor' && $prova->user_id !== $user->id) {
            abort(403, 'Acesso não autorizado');
        }
        
        if (in_array($user->role, ['coordenador', 'gestor', 'aee'])) {
            if (!$user->escolas->contains($prova->escola_id)) {
                abort(403, 'Acesso não autorizado');
            }
        }
    
        $data = [
            'prova' => $prova,
            'data_emissao' => now()->format('d/m/Y H:i'),
            'titulo' => 'Prova Pedagógica'
        ];
    
        $pdf = Pdf::loadView('provas.pdf', $data);
        $filename = 'prova_' . $prova->id . '_' . now()->format('Ymd') . '.pdf';
    
        return $pdf->download($filename);
    }
    public function direcionarAvaliacao()
    {
        $user = Auth::user();
    
        if (in_array($user->role, ['admin', 'aplicador', 'tutor'])) {
            return redirect()->route('provas.admin.estatisticas-rede');
        } elseif ($user->role === 'coordenador') {
            $escolas = $user->escolas;
            if ($escolas->count() === 1) {
                return redirect()->route('avaliacao.estatisticas.coordenador', ['escola' => $escolas->first()->id]);
            } else {
                return redirect()->route('avaliacao.selecionar-escola'); // Redireciona para a seleção se tiver múltiplas ou nenhuma escola
            }
        } elseif ($user->role === 'gestor') {
            $escolas = $user->escolas;
            if ($escolas->count() === 1) {
                return redirect()->route('avaliacao.estatisticas.gestor', ['escola' => $escolas->first()->id]);
            } else {
                return redirect()->route('avaliacao.selecionar-escola'); // Redireciona para a seleção se tiver múltiplas ou nenhuma escola
            }
        } elseif ($user->role === 'aee') {
            $escolas = $user->escolas;
            if ($escolas->count() === 1) {
                return redirect()->route('avaliacao.estatisticas.aee', ['escola' => $escolas->first()->id]);
            } else {
                return redirect()->route('avaliacao.selecionar-escola'); // Redireciona para a seleção se tiver múltiplas ou nenhuma escola
            }
        } elseif ($user->role === 'professor') {
            return view('provas.professor.estatisticas'); // Assumindo que você tem esta view
        } else {
            abort(403, 'Acesso não autorizado para visualizar as estatísticas.');
        }
    }
    public function estatisticasRede()
    {
        $user = Auth::user();
    
        if (!in_array($user->role, ['admin', 'aplicador', 'tutor'])) {
            abort(403, 'Esta ação não é autorizada.');
        }
    
        // ID da escola selecionada no filtro
        $escolaId = request('escola_id');
    
        // 1. Total de provas na rede ou por escola
        $totalProvas = Prova::when($escolaId, function ($query) use ($escolaId) {
            $query->whereHas('user.escolas', function ($q) use ($escolaId) {
                $q->where('escolas.id', $escolaId);
            });
        })->count();
    
        // 2. Provas por escola (esse bloco é geral, não depende do filtro)
        $provasPorEscola = DB::table('provas')
        ->select(
            'escolas.id as escola_id',
            'escolas.nome', 
            DB::raw('count(provas.id) as total')
        )
        ->rightJoin('escolas', function($join) {
            $join->on('provas.escola_id', '=', 'escolas.id')
                 ->orWhereNull('provas.escola_id');
        })
        ->groupBy('escolas.id', 'escolas.nome')
        ->orderBy('total', 'desc')
        ->get();
        
        // 3. Top 5 habilidades mais usadas
        $topHabilidades = DB::table('provas')
            ->select(
                'habilidades.descricao',
                DB::raw('count(provas.habilidade_id) as total')
            )
            ->join('habilidades', 'provas.habilidade_id', '=', 'habilidades.id')
            ->when($escolaId, function ($query) use ($escolaId) {
                $query->join('users', 'provas.user_id', '=', 'users.id')
                    ->join('user_escola', 'users.id', '=', 'user_escola.user_id')
                    ->where('user_escola.escola_id', $escolaId);
            })
            ->groupBy('habilidades.id', 'habilidades.descricao')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    
        // 4. Provas por ano de ensino
        $provasPorAno = DB::table('provas')
            ->select('anos.nome', DB::raw('count(provas.id) as total'))
            ->join('anos', 'provas.ano_id', '=', 'anos.id')
            ->when($escolaId, function ($query) use ($escolaId) {
                $query->join('users', 'provas.user_id', '=', 'users.id')
                    ->join('user_escola', 'users.id', '=', 'user_escola.user_id')
                    ->where('user_escola.escola_id', $escolaId);
            })
            ->groupBy('anos.id', 'anos.nome')
            ->orderBy('total', 'desc')
            ->get();
    
        // 5. Total de professores que criaram provas
        $totalProfessores = DB::table('provas')
            ->select('provas.user_id')
            ->when($escolaId, function ($query) use ($escolaId) {
                $query->join('users', 'provas.user_id', '=', 'users.id')
                    ->join('user_escola', 'users.id', '=', 'user_escola.user_id')
                    ->where('user_escola.escola_id', $escolaId);
            })
            ->distinct()
            ->count();
    
        // 6. Top 5 disciplinas
        $topDisciplinas = DB::table('provas')
            ->select(
                'disciplinas.nome',
                DB::raw('count(provas.disciplina_id) as total')
            )
            ->join('disciplinas', 'provas.disciplina_id', '=', 'disciplinas.id')
            ->when($escolaId, function ($query) use ($escolaId) {
                $query->join('users', 'provas.user_id', '=', 'users.id')
                    ->join('user_escola', 'users.id', '=', 'user_escola.user_id')
                    ->where('user_escola.escola_id', $escolaId);
            })
            ->groupBy('disciplinas.id', 'disciplinas.nome')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    
        // 7. Lista de escolas para o filtro
        $escolas = Escola::orderBy('nome')->get();
    
        return view('provas.admin.estatisticas-rede', compact(
            'totalProvas',
            'provasPorEscola',
            'topHabilidades',
            'provasPorAno',
            'totalProfessores',
            'escolas',
            'escolaId',
            'topDisciplinas' // Adicionado aqui
        ));
    }

    public function estatisticasEscola(Escola $escola)
    {
        $user = Auth::user();
        
        $allowedRoles = ['admin', 'aplicador', 'tutor', 'coordenador', 'gestor', 'aee'];
        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Esta ação não é autorizada.');
        }
        
        if (in_array($user->role, ['coordenador', 'gestor', 'aee'])) {
            if (!$user->escolas->contains($escola->id)) {
                abort(403, 'Esta ação não é autorizada.');
            }
        }
    
        // 1. Provas da escola paginadas
        $provasQuery = DB::table('provas')
            ->select(
                'provas.*',
                'disciplinas.nome as disciplina_nome',
                'anos.nome as ano_nome',
                'users.name as professor_name'
            )
            ->join('disciplinas', 'provas.disciplina_id', '=', 'disciplinas.id')
            ->join('anos', 'provas.ano_id', '=', 'anos.id')
            ->join('users', 'provas.user_id', '=', 'users.id')
            ->where('provas.escola_id', $escola->id)
            ->orderBy('provas.created_at', 'desc');
    
        $provas = $provasQuery->paginate(10);
    
        // 2. Total de provas na escola
        $totalProvas = Prova::where('escola_id', $escola->id)->count();
    
        // 3. Top 5 habilidades da escola
        $topHabilidades = DB::table('provas')
            ->select(
                'habilidades.descricao',
                DB::raw('count(*) as total')
            )
            ->join('habilidades', 'provas.habilidade_id', '=', 'habilidades.id')
            ->where('provas.escola_id', $escola->id)
            ->groupBy('habilidades.id', 'habilidades.descricao')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    
        // 4. Provas por ano na escola
        $provasPorAno = DB::table('provas')
            ->select(
                'anos.nome',
                DB::raw('count(*) as total')
            )
            ->join('anos', 'provas.ano_id', '=', 'anos.id')
            ->where('provas.escola_id', $escola->id)
            ->groupBy('anos.id', 'anos.nome')
            ->orderBy('total', 'desc')
            ->get();
    
        // 5. Professores que criaram provas na escola
        $professores = DB::table('provas')
            ->select(
                'users.id',
                'users.name',
                DB::raw('count(*) as provas_count')
            )
            ->join('users', 'provas.user_id', '=', 'users.id')
            ->where('provas.escola_id', $escola->id)
            ->groupBy('users.id', 'users.name')
            ->orderBy('provas_count', 'desc')
            ->get();
    
        // 6. Top disciplinas na escola
        $topDisciplinas = DB::table('provas')
            ->select(
                'disciplinas.nome',
                DB::raw('count(*) as total')
            )
            ->join('disciplinas', 'provas.disciplina_id', '=', 'disciplinas.id')
            ->where('provas.escola_id', $escola->id)
            ->groupBy('disciplinas.id', 'disciplinas.nome')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    
        // 7. Provas recentes (últimas 5)
        $provasRecentes = Prova::with(['ano', 'disciplina', 'professor'])
            ->where('escola_id', $escola->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    
        return view('provas.coordenador.estatisticas-escola', [
            'escola' => $escola,
            'provas' => $provas,
            'totalProvas' => $totalProvas, // Renomeado para corresponder à view
            'topHabilidades' => $topHabilidades,
            'provasPorAno' => $provasPorAno,
            'professores' => $professores, // Renomeado para corresponder à view
            'topDisciplinas' => $topDisciplinas,
            'provasRecentes' => $provasRecentes
        ]);
    }
    /**
     * Obtém o ID da escola atual (da sessão ou única vinculada)
     */
    protected function getEscolaId()
{
    $user = Auth::user();
    
    // 1. Verifica se tem escola na sessão
    if (session('escola_selecionada')) {
        return session('escola_selecionada');
    }
    
    // 2. Se tiver apenas uma escola vinculada
    if ($user->escolas()->count() === 1) {
        return $user->escolas()->first()->id;
    }
    
    // 3. Se não tiver escola definida
    return null;
}
    public function gerarPDF(Prova $prova)
{
    $user = Auth::user();
    
    // Verificação de permissão
    if ($user->role === 'professor' && $prova->professor->id !== $user->id) {
        abort(403, 'Acesso não autorizado');
    }
    
    if (in_array($user->role, ['coordenador', 'gestor', 'aee'])) {
        if (!$user->escolas->contains($prova->escola_id)) {
            abort(403, 'Acesso não autorizado');
        }
    }

    $data = [
        'prova' => $prova->load(['questoes', 'ano', 'disciplina', 'habilidade', 'professor', 'escola']),
        'data_emissao' => now()->format('d/m/Y H:i'),
        'titulo' => 'Prova Pedagógica',
        'mostrar_gabarito' => false,
        'professor_gerador' => $prova->professor->name
    ];

    $pdf = Pdf::loadView('provas.pdf', $data);
    $filename = 'prova_'.$prova->id.'_'.now()->format('Ymd').'.pdf';

    return $pdf->download($filename);
}

public function gerarPDFGabarito(Prova $prova)
{
    $user = Auth::user();
    
    // Verificação de permissão
    if ($user->role === 'professor' && $prova->professor->id !== $user->id) {
        abort(403, 'Acesso não autorizado');
    }
    
    if (in_array($user->role, ['coordenador', 'gestor', 'aee'])) {
        if (!$user->escolas->contains($prova->escola_id)) {
            abort(403, 'Acesso não autorizado');
        }
    }

    $data = [
        'prova' => $prova->load(['questoes', 'ano', 'disciplina', 'habilidade', 'professor', 'escola']),
        'data_emissao' => now()->format('d/m/Y H:i'),
        'titulo' => 'Gabarito da Prova',
        'mostrar_gabarito' => true,
        'professor_gerador' => $prova->professor->name
    ];

    $pdf = Pdf::loadView('provas.pdf', $data);
    $filename = 'gabarito_prova_'.$prova->id.'_'.now()->format('Ymd').'.pdf';

    return $pdf->download($filename);
}
}