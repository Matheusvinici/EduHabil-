<?php
namespace App\Http\Controllers;

use App\Models\Adaptacao;
use App\Models\Recurso;
use App\Models\User;
use App\Models\Escola;
use Illuminate\Support\Facades\DB; 
use App\Models\Deficiencia;
use App\Models\Caracteristica;
use Illuminate\Http\Request;
use PDF; // Para gerar PDF


class AdaptacaoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Redirecionamentos
        if (in_array($user->role, ['admin', 'inclusiva'])) {
            return redirect()->route('adaptacoes.estatisticas');
        }
        
        if (in_array($user->role, ['coordenador', 'gestor'])) {
            $escola = $user->escolas()->first();
                
            if ($escola) {
                return redirect()->route('adaptacoes.coordenador.estatisticas', $escola);
            }
            return redirect()->route('selecionar.escola')
                   ->with('warning', 'Por favor, selecione uma escola.');
        }
        
        // Consulta para professores ou outros perfis
        $adaptacoes = Adaptacao::with(['recurso', 'deficiencias', 'caracteristicas'])
            ->where('user_id', $user->id)
            ->paginate(5);
            
        return view('adaptacoes.index', compact('adaptacoes'));
    }
    
    public function estatisticasEscola(Escola $escola)
    {
        $user = auth()->user();
        
        // Verifica se o usuário tem permissão para acessar esta escola
        if (in_array($user->role, ['admin', 'inclusiva'])) {
            // Permite acesso sem restrições
        } elseif (in_array($user->role, ['coordenador', 'gestor', 'aee'])) {
            if (!$user->escolas->contains($escola->id)) {
                abort(403, 'Você só pode visualizar estatísticas da sua própria escola');
            }
        } else {
            abort(403, 'Acesso não autorizado');
        }
        
        // Consulta os dados da escola
        $totalAdaptacoes = $escola->users()
            ->whereHas('adaptacoes')
            ->withCount('adaptacoes')
            ->get()
            ->sum('adaptacoes_count');
        
        $usuariosComAdaptacoes = $escola->users()
            ->whereHas('adaptacoes')
            ->withCount('adaptacoes')
            ->orderByDesc('adaptacoes_count')
            ->get();
    
        return view('adaptacoes.estatisticas_escola', compact(
            'escola',
            'totalAdaptacoes',
            'usuariosComAdaptacoes'
        ));
    }
    public function estatisticasEscolaCoordenador(Escola $escola)
    {
        $user = auth()->user();
        
        // Verifica se o usuário é coordenador/gestor e pertence à escola
        if (!in_array($user->role, ['coordenador', 'gestor']) || !$user->escolas->contains($escola->id)) {
            abort(403, 'Acesso não autorizado');
        }
    
        // Total de adaptações na escola
        $totalAdaptacoes = $escola->users()
            ->whereHas('adaptacoes')
            ->withCount('adaptacoes')
            ->get()
            ->sum('adaptacoes_count');
    
        // Usuários com adaptações (incluindo filtro por role se necessário)
        $usuariosComAdaptacoes = $escola->users()
            ->whereHas('adaptacoes')
            ->withCount('adaptacoes')
            ->orderByDesc('adaptacoes_count')
            ->get();
    
        return view('adaptacoes.estatisticas_coordenador', compact(
            'escola',
            'totalAdaptacoes',
            'usuariosComAdaptacoes'
        ));
    }
    public function estatisticas()
    {
        // Mantenha as consultas que já funcionam
        $topDeficiencias = Deficiencia::withCount('adaptacoes')
            ->orderByDesc('adaptacoes_count')
            ->limit(10)
            ->get();
    
        $topCaracteristicas = Caracteristica::withCount('adaptacoes')
            ->orderByDesc('adaptacoes_count')
            ->limit(10)
            ->get();
    
        // Nova consulta corrigida para escolas
        $escolas = Escola::whereHas('users', function($query) {
                $query->has('adaptacoes');
            })
            ->withCount(['users as adaptacoes_count' => function($query) {
                $query->select(DB::raw('count(adaptacoes.id)'))
                      ->join('adaptacoes', 'users.id', '=', 'adaptacoes.user_id');
            }])
            ->orderByDesc('adaptacoes_count')
            ->paginate(10);
    
        return view('adaptacoes.estatisticas', compact(
            'topDeficiencias',
            'topCaracteristicas',
            'escolas'
        ));
    }
    
    public function adaptacoesProfessor(User $professor)
    {
        // Verifica se o usuário tem adaptações
        if (!$professor->adaptacoes()->exists()) {
            return redirect()->back()->with('error', 'Este usuário não possui adaptações');
        }
    
        $adaptacoes = $professor->adaptacoes()
            ->with(['recurso', 'deficiencias', 'caracteristicas'])
            ->latest()
            ->paginate(10);
    
        return view('adaptacoes.adaptacoes_professor', compact(
            'professor',
            'adaptacoes'
        ));
    }
    public function adaptacoesProfessorCoordenador(User $professor)
    {
        $user = auth()->user();
        
        // Verifica se é coordenador/gestor e se pertence à mesma escola
        if (!in_array($user->role, ['coordenador', 'gestor']) || 
            !$user->escolas->pluck('id')->contains($professor->escolas->first()->id)) {
            abort(403, 'Acesso não autorizado');
        }
    
        $adaptacoes = $professor->adaptacoes()
            ->with(['recurso', 'deficiencias'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    
        return view('adaptacoes.adaptacoes_professor_coordenador', compact(
            'professor',
            'adaptacoes'
        ));
    }
    public function create()
    {
        $recursos = Recurso::all();
        $deficiencias = Deficiencia::all();
        $caracteristicas = Caracteristica::all();
        return view('adaptacoes.create', compact('recursos', 'deficiencias', 'caracteristicas'));
    }

    public function store(Request $request)
{
    $request->validate([
        'deficiencias' => 'required|array|min:1',
        'deficiencias.*' => 'exists:deficiencias,id',
        'caracteristicas' => 'required|array|min:1',
        'caracteristicas.*' => 'exists:caracteristicas,id',
    ]);

    // Busca recursos que correspondam às deficiências e características selecionadas
    $recursos = Recurso::whereHas('deficiencias', function ($query) use ($request) {
        $query->whereIn('deficiencias.id', $request->deficiencias);
    })->whereHas('caracteristicas', function ($query) use ($request) {
        $query->whereIn('caracteristicas.id', $request->caracteristicas);
    })->get();

    // Verifica se há recursos disponíveis
    if ($recursos->isEmpty()) {
        return redirect()->back()
               ->withInput()
               ->with('error', 'Nenhum recurso encontrado para as deficiências e características selecionadas.');
    }

    // Seleciona um recurso aleatório
    $recursoAleatorio = $recursos->random();

    // Cria a adaptação com o usuário logado
    $adaptacao = Adaptacao::create([
        'recurso_id' => $recursoAleatorio->id,
        'user_id' => auth()->id() // Adiciona automaticamente o usuário logado
    ]);

    // Associa as deficiências e características à adaptação
    $adaptacao->deficiencias()->sync($request->deficiencias);
    $adaptacao->caracteristicas()->sync($request->caracteristicas);

    return redirect()->route('adaptacoes.index')
           ->with('success', 'Atividade gerada com sucesso!');
}
    public function show($id)
{
    try {
        $adaptacao = Adaptacao::with([
            'recurso', 
            'deficiencias', 
            'caracteristicas'
        ])->findOrFail($id);

        return view('adaptacoes.show', compact('adaptacao'));

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return redirect()->route('adaptacoes.index')
               ->with('error', 'Adaptação não encontrada.');
    }
}
    // Exibir formulário de edição
    public function edit(Adaptacao $adaptacao)
    {
        $recursos = Recurso::all();
        $deficiencias = Deficiencia::all();
        $caracteristicas = Caracteristica::all();
        return view('adaptacoes.edit', compact('adaptacao', 'recursos', 'deficiencias', 'caracteristicas'));
    }

    // Atualizar adaptação
    public function update(Request $request, Adaptacao $adaptacao)
    {
        $request->validate([
            'professor_id' => 'required|exists:users,id',
            'recurso_id' => 'required|exists:recursos,id',
            'deficiencia_id' => 'required|exists:deficiencias,id',
            'caracteristica_id' => 'required|exists:caracteristicas,id',
        ]);

        $adaptacao->update($request->all());
        return redirect()->route('adaptacoes.index')->with('success', 'Adaptação atualizada com sucesso!');
    }

    public function destroy($id)
    {
        try {
            // Encontra a adaptação apenas pelo ID
            $adaptacao = Adaptacao::findOrFail($id);
            
            // Remove as relações many-to-many
            $adaptacao->deficiencias()->detach();
            $adaptacao->caracteristicas()->detach();
            
            // Exclui a adaptação
            $adaptacao->delete();
            
            return redirect()->route('adaptacoes.index')
                   ->with('success', 'Adaptação excluída com sucesso!');
                   
        } catch (\Exception $e) {
            return redirect()->back()
                   ->with('error', 'Erro ao excluir: ' . $e->getMessage());
        }
    }
    public function gerarPdf(Adaptacao $adaptacao)
    {
        // Carrega os dados da adaptação com os relacionamentos corretos
        $adaptacao->load(['recurso', 'deficiencias', 'caracteristicas']);
    
        // Gera o PDF
        $pdf = Pdf::loadView('adaptacoes.pdf', compact('adaptacao'));
    
        // Retorna o PDF para download ou visualização
        return $pdf->download('atividade_gerada.pdf');

    }
}