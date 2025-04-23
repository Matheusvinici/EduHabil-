<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB; 

use App\Models\AtividadeProfessor;
use App\Models\Atividade;
use App\Models\Ano;
use App\Models\Escola;

use App\Models\Disciplina;
use App\Models\Habilidade;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AtividadeProfessorController extends Controller
{
public function index()
{
    $user = auth()->user();
    
    // Redireciona admin, aplicador e tutor para estatísticas da rede
    if (in_array($user->role, ['admin', 'aplicador', 'tutor'])) {
        return redirect()->route('atividades_professores.estatisticas-rede');
    }
    
    // Redireciona coordenador e gestor para estatísticas da escola
    if (in_array($user->role, ['coordenador', 'gestor'])) {
        // Assumindo que o usuário tem uma escola associada
        $escola = $user->escolas->first(); // ou outra lógica para pegar a escola correta
        if ($escola) {
            return redirect()->route('atividades_professores.estatisticas-escola', $escola);
        }
    }
    
    // Consulta base com eager loading dos relacionamentos
    $query = AtividadeProfessor::with([
        'atividade.disciplina',
        'atividade.ano',
        'professor.escolas'
    ])->orderBy('created_at', 'desc');
    
    // Filtro para professores
    if ($user->role === 'professor') {
        $query->where('professor_id', $user->id);
    } 
    // Filtro para coordenador e gestor (caso não tenha redirecionado acima)
    elseif (in_array($user->role, ['coordenador', 'gestor'])) {
        $query->whereHas('professor.escolas', function($q) use ($user) {
            $q->whereIn('escola_id', $user->escolas->pluck('id'));
        });
    }
    
    $atividadesProfessores = $query->paginate(10);
    
    return view('atividades_professores.index', compact('atividadesProfessores'));
}
public function store(Request $request)
{
    $request->validate([
        'disciplina_id' => 'required|exists:disciplinas,id',
        'ano_id' => 'required|exists:anos,id',
        'habilidade_id' => 'required|exists:habilidades,id',
    ]);

    $atividade = Atividade::where('disciplina_id', $request->disciplina_id)
        ->where('ano_id', $request->ano_id)
        ->where('habilidade_id', $request->habilidade_id)
        ->inRandomOrder()
        ->first();

    if (!$atividade) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Nenhuma atividade encontrada com os filtros selecionados.');
    }

    AtividadeProfessor::create([
        'professor_id' => auth()->id(),
        'atividade_id' => $atividade->id,
    ]);

    return redirect()->route('atividades_professores.index')
        ->with('success', 'Atividade gerada com sucesso!');
}

public function create()
{
    $disciplinas = Disciplina::all();
    $anos = Ano::all();
    $habilidades = Habilidade::all();

    return view('atividades_professores.create', compact('disciplinas', 'anos', 'habilidades'));
}

public function show($id)
{
    $user = auth()->user();
    $atividadeProfessor = AtividadeProfessor::with('atividade')->findOrFail($id);
    
    // Verificação de permissão
    if ($user->role === 'professor' && $atividadeProfessor->professor_id !== $user->id) {
        abort(403, 'Acesso não autorizado');
    }
    
    if (in_array($user->role, ['coordenador', 'gestor']) && 
        $atividadeProfessor->professor->escola_id !== $user->escola_id) {
        abort(403, 'Acesso não autorizado');
    }
    
    return view('atividades_professores.show', compact('atividadeProfessor'));
}

public function destroy($id)
{
    $user = auth()->user();
    $relacao = AtividadeProfessor::findOrFail($id);
    
    // Verificação de permissão
    if ($user->role === 'professor' && $relacao->professor_id !== $user->id) {
        abort(403, 'Acesso não autorizado');
    }
    
    if (in_array($user->role, ['coordenador', 'gestor']) && 
        $relacao->professor->escola_id !== $user->escola_id) {
        abort(403, 'Acesso não autorizado');
    }
                
    $relacao->delete();
    
    return redirect()->route('atividades_professores.index')
            ->with('success', 'Atividade removida com sucesso!');
}

public function downloadPdf($id)
{
    $user = auth()->user();
    $atividadeProfessor = AtividadeProfessor::with([
        'atividade',
        'atividade.disciplina',
        'atividade.ano',
        'atividade.habilidade',
        'professor'
    ])->findOrFail($id);
    
    // Verificação de permissão
    if ($user->role === 'professor' && $atividadeProfessor->professor_id !== $user->id) {
        abort(403, 'Acesso não autorizado');
    }
    
    if (in_array($user->role, ['coordenador', 'gestor']) && 
        $atividadeProfessor->professor->escola_id !== $user->escola_id) {
        abort(403, 'Acesso não autorizado');
    }

    $data = [
        'atividade' => $atividadeProfessor,
        'data_emissao' => now()->format('d/m/Y H:i'),
        'titulo' => 'Atividade Pedagógica'
    ];

    $pdf = Pdf::loadView('atividades_professores.pdf', $data);
    $filename = 'atividade_' . $atividadeProfessor->id . '_' . now()->format('Ymd') . '.pdf';

    return $pdf->download($filename);
}

public function estatisticasRede()
{
    $user = auth()->user();

    // Verificação de acesso direto (sem Policy)
    if (!in_array($user->role, ['admin', 'aplicador', 'tutor'])) {
        abort(403, 'Esta ação não é autorizada.');
    }

    // 1. Total de atividades na rede
    $totalAtividades = AtividadeProfessor::count();

    // 2. Atividades por escola (com nome correto da tabela)
    $atividadesPorEscola = DB::table('atividades_professores')
    ->select(
        'escolas.id as escola_id', // Adicionando o ID explicitamente
        'escolas.nome', 
        DB::raw('count(*) as total')
    )
    ->join('user_escola', 'atividades_professores.professor_id', '=', 'user_escola.user_id')
    ->join('escolas', 'user_escola.escola_id', '=', 'escolas.id')
    ->groupBy('escolas.id', 'escolas.nome')
    ->orderBy('total', 'desc')
    ->get();

    // 3. Top 5 habilidades mais geradas (removendo 'habilidades.codigo')
    $topHabilidades = DB::table('atividades_professores')
        ->select(
            'habilidades.descricao',
            DB::raw('count(*) as total')
        )
        ->join('atividades', 'atividades_professores.atividade_id', '=', 'atividades.id')
        ->join('habilidades', 'atividades.habilidade_id', '=', 'habilidades.id')
        ->groupBy('habilidades.id', 'habilidades.descricao') // Removido 'habilidades.codigo' do GROUP BY
        ->orderBy('total', 'desc')
        ->limit(5)
        ->get();

    // 4. Atividades por ano de ensino
    $atividadesPorAno = DB::table('atividades_professores')
        ->select('anos.nome', DB::raw('count(*) as total'))
        ->join('atividades', 'atividades_professores.atividade_id', '=', 'atividades.id')
        ->join('anos', 'atividades.ano_id', '=', 'anos.id')
        ->groupBy('anos.id', 'anos.nome')
        ->orderBy('total', 'desc')
        ->get();

    // 5. Total de professores que geraram atividades
    $totalProfessores = DB::table('atividades_professores')
        ->distinct('professor_id')
        ->count('professor_id');

    return view('atividades_professores.estatisticas-rede', compact(
        'totalAtividades',
        'atividadesPorEscola',
        'topHabilidades',
        'atividadesPorAno',
        'totalProfessores'
    ));
}

public function estatisticasEscola(Escola $escola)
{
    $user = auth()->user();
    
    // Verificação de acesso
    $allowedRoles = ['admin', 'aplicador', 'tutor', 'coordenador', 'gestor'];
    if (!in_array($user->role, $allowedRoles)) {
        abort(403, 'Esta ação não é autorizada.');
    }
    
    if (in_array($user->role, ['coordenador', 'gestor']) && 
        !DB::table('user_escola')->where('user_id', $user->id)
                                    ->where('escola_id', $escola->id)
                                    ->exists()) {
        abort(403, 'Esta ação não é autorizada.');
    }

    // 1. Atividades da escola paginadas
    $atividadesQuery = DB::table('atividades_professores')
        ->select(
            'atividades_professores.*',
            'disciplinas.nome as disciplina_nome',
            'anos.nome as ano_nome',
            'users.name as professor_name'
        )
        ->join('atividades', 'atividades_professores.atividade_id', '=', 'atividades.id')
        ->join('disciplinas', 'atividades.disciplina_id', '=', 'disciplinas.id')
        ->join('anos', 'atividades.ano_id', '=', 'anos.id')
        ->join('users', 'atividades_professores.professor_id', '=', 'users.id')
        ->join('user_escola', 'users.id', '=', 'user_escola.user_id')
        ->where('user_escola.escola_id', $escola->id)
        ->orderBy('atividades_professores.created_at', 'desc');

    $atividades = $atividadesQuery->paginate(10);

    // 2. Total de atividades na escola
    $totalAtividadesEscola = DB::table('atividades_professores')
        ->join('users', 'atividades_professores.professor_id', '=', 'users.id')
        ->join('user_escola', 'users.id', '=', 'user_escola.user_id')
        ->where('user_escola.escola_id', $escola->id)
        ->count();

    // 3. Top 5 habilidades da escola
    $topHabilidades = DB::table('atividades_professores')
        ->select(
            'habilidades.descricao',
            DB::raw('count(*) as total')
        )
        ->join('atividades', 'atividades_professores.atividade_id', '=', 'atividades.id')
        ->join('habilidades', 'atividades.habilidade_id', '=', 'habilidades.id')
        ->join('users', 'atividades_professores.professor_id', '=', 'users.id')
        ->join('user_escola', 'users.id', '=', 'user_escola.user_id')
        ->where('user_escola.escola_id', $escola->id)
        ->groupBy('habilidades.id', 'habilidades.descricao')
        ->orderBy('total', 'desc')
        ->limit(5)
        ->get();

    // 4. Atividades por ano na escola
    $atividadesPorAno = DB::table('atividades_professores')
        ->select(
            'anos.nome',
            DB::raw('count(*) as total')
        )
        ->join('atividades', 'atividades_professores.atividade_id', '=', 'atividades.id')
        ->join('anos', 'atividades.ano_id', '=', 'anos.id')
        ->join('users', 'atividades_professores.professor_id', '=', 'users.id')
        ->join('user_escola', 'users.id', '=', 'user_escola.user_id')
        ->where('user_escola.escola_id', $escola->id)
        ->groupBy('anos.id', 'anos.nome')
        ->orderBy('total', 'desc')
        ->get();

    // 5. Professores que geraram atividades na escola
    $professoresAtivos = DB::table('atividades_professores')
        ->select(
            'users.name',
            DB::raw('count(*) as total')
        )
        ->join('users', 'atividades_professores.professor_id', '=', 'users.id')
        ->join('user_escola', 'users.id', '=', 'user_escola.user_id')
        ->where('user_escola.escola_id', $escola->id)
        ->groupBy('users.id', 'users.name')
        ->orderBy('total', 'desc')
        ->get();

    return view('atividades_professores.estatisticas-escola', [
        'escola' => $escola,
        'atividades' => $atividades,
        'totalAtividadesEscola' => $totalAtividadesEscola,
        'topHabilidades' => $topHabilidades,
        'atividadesPorAno' => $atividadesPorAno,
        'professoresAtivos' => $professoresAtivos
    ]);
}
}