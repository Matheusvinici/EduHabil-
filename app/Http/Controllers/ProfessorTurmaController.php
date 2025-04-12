<?php
    namespace App\Http\Controllers;

    use App\Models\Escola;
    use App\Models\Turma;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    
    class ProfessorTurmaController extends Controller
    {
        /**
         * Mostra o formulário inicial para selecionar a escola
         */
        public function selectEscola()
        {
            if (!in_array(Auth::user()->role, ['admin', 'aplicador'])) {
                abort(403, 'Acesso não autorizado.');
            }
    
            $escolas = Escola::all();
            return view('professor_turma.select_escola', compact('escolas'));
        }
    
        /**
         * Mostra o formulário completo de vinculação
         */
        public function create(Request $request)
        {
            // Verifica permissões
            if (!in_array(Auth::user()->role, ['admin', 'aplicador'])) {
                abort(403, 'Acesso não autorizado.');
            }
        
            // Validação
            $request->validate(['escola_id' => 'required|exists:escolas,id']);
            $escola = Escola::findOrFail($request->escola_id);
        
            // Obtém todas as turmas da escola com seus professores
            $turmasComProfessores = Turma::with('professores')
                ->where('escola_id', $escola->id)
                ->get();
        
            // Lista de professores já vinculados a turmas
            $professoresVinculados = collect();
            foreach ($turmasComProfessores as $turma) {
                $professoresVinculados = $professoresVinculados->merge($turma->professores);
            }
            $professoresVinculadosIds = $professoresVinculados->pluck('id')->unique();
        
            // Obtém professores disponíveis (não vinculados e que pertencem à escola)
            $professoresDisponiveis = User::whereHas('escolas', function($q) use ($escola) {
                $q->where('escola_id', $escola->id);
            })
            ->where('role', 'professor')
            ->whereNotIn('id', $professoresVinculadosIds)
            ->orderBy('name')
            ->get();
        
            // Lista de turmas que já possuem professores
            $turmasVinculadasIds = $turmasComProfessores
                ->filter(function($turma) {
                    return $turma->professores->isNotEmpty();
                })
                ->pluck('id');
        
            // Obtém turmas disponíveis (sem professor)
            $turmasDisponiveis = Turma::where('escola_id', $escola->id)
                ->whereNotIn('id', $turmasVinculadasIds)
                ->orderBy('nome_turma')
                ->get();
        
            // Prepara o resumo das vinculações existentes
            $vinculacoesExistentes = collect();
            foreach ($turmasComProfessores as $turma) {
                foreach ($turma->professores as $professor) {
                    $vinculacoesExistentes->push([
                        'turma' => $turma->nome_turma,
                        'professor' => $professor->name
                    ]);
                }
            }
        
            return view('professor_turma.create', [
                'escola' => $escola,
                'turmasDisponiveis' => $turmasDisponiveis,
                'professoresDisponiveis' => $professoresDisponiveis,
                'vinculacoesExistentes' => $vinculacoesExistentes
            ]);
        }
    
        /**
         * Processa o formulário de vinculação
         */
        public function store(Request $request)
        {
            $request->validate([
                'escola_id' => 'required|exists:escolas,id',
                'professor_id' => 'required|exists:users,id,role,professor',
                'turmas' => 'required|array',
                'turmas.*' => 'exists:turmas,id,escola_id,'.$request->escola_id
            ]);
        
            $professor = User::findOrFail($request->professor_id);
            $turmasConflitantes = [];
        
            foreach ($request->turmas as $turmaId) {
                $jaVinculada = \DB::table('professor_turma')
                                  ->where('turma_id', $turmaId)
                                  ->exists();
        
                if ($jaVinculada) {
                    $turma = \App\Models\Turma::find($turmaId);
                    $turmasConflitantes[] = $turma->nome_turma;
                }
            }
        
            if (count($turmasConflitantes)) {
                return redirect()->back()
                                 ->withInput()
                                 ->with('error', 'As seguintes turmas já estão vinculadas a outro professor: ' . implode(', ', $turmasConflitantes));
            }
        
            // Vincula professor às turmas
            foreach ($request->turmas as $turmaId) {
                \DB::table('professor_turma')->insert([
                    'professor_id' => $professor->id,
                    'turma_id' => $turmaId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        
            return redirect()->route('professor-turma.index')
                             ->with('success', 'Vinculações realizadas com sucesso!');
        }
        
    
        /**
         * Lista todas as vinculações existentes
         */
        public function index(Request $request)
        {
            if (!in_array(Auth::user()->role, ['admin', 'aplicador'])) {
                abort(403, 'Acesso não autorizado.');
            }
        
            $query = \DB::table('professor_turma')
                ->join('users', 'professor_turma.professor_id', '=', 'users.id')
                ->join('turmas', 'professor_turma.turma_id', '=', 'turmas.id')
                ->join('escolas', 'turmas.escola_id', '=', 'escolas.id')
                ->select(
                    'professor_turma.professor_id',
                    'professor_turma.turma_id',
                    'users.name as professor',
                    'turmas.nome_turma',
                    'escolas.nome as escola',
                    'professor_turma.created_at as vinculado_em'
                );
        
            // Filtros
            if ($request->filled('escola_id')) {
                $query->where('escolas.id', $request->escola_id);
            }
        
            if ($request->filled('turma_id')) {
                $query->where('turmas.id', $request->turma_id);
            }
        
            $vinculacoes = $query
                ->orderBy('escolas.nome')
                ->orderBy('turmas.nome_turma')
                ->get();
        
            $escolas = \App\Models\Escola::orderBy('nome')->get();
            $turmas = \App\Models\Turma::orderBy('nome_turma')->get();
        
            return view('professor_turma.index', compact('vinculacoes', 'escolas', 'turmas'));
        }
        
        
        

        public function edit($professor_id, $turma_id)
        {
            $vinculo = \DB::table('professor_turma as pt')
                ->join('users as u', 'pt.professor_id', '=', 'u.id')
                ->join('turmas as t', 'pt.turma_id', '=', 't.id')
                ->join('escolas as e', 't.escola_id', '=', 'e.id')
                ->where('pt.professor_id', $professor_id)
                ->where('pt.turma_id', $turma_id)
                ->select('pt.*', 'e.nome as escola_nome', 't.escola_id')
                ->first();
        
            if (!$vinculo) {
                return redirect()->route('professor-turma.index')->with('error', 'Vinculação não encontrada.');
            }
        
            $turmas = \App\Models\Turma::where('escola_id', $vinculo->escola_id)->get();
            $professores = \App\Models\User::where('escola_id', $vinculo->escola_id)->where('role', 'professor')->get();
        
            return view('professor_turma.edit', compact('vinculo', 'turmas', 'professores'));
        }
        
        public function update(Request $request, $professor_id, $turma_id)
{
    $request->validate([
        'professor_id' => 'required|exists:users,id',
        'turma_id' => 'required|exists:turmas,id',
    ]);

    // Verifica se já existe vínculo com outro professor na mesma turma
    $existe = \DB::table('professor_turma')
        ->where('turma_id', $request->turma_id)
        ->where('professor_id', '!=', $professor_id)
        ->exists();

    if ($existe) {
        return redirect()->back()->with('error', 'Essa turma já possui um professor vinculado.');
    }

    // Deleta o vínculo antigo
    \DB::table('professor_turma')
        ->where('professor_id', $professor_id)
        ->where('turma_id', $turma_id)
        ->delete();

    // Insere o novo vínculo
    \DB::table('professor_turma')->insert([
        'professor_id' => $request->professor_id,
        'turma_id' => $request->turma_id,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return redirect()->route('professor-turma.index')->with('success', 'Vinculação atualizada com sucesso!');
}

        public function destroy($professor_id, $turma_id)
        {
            \DB::table('professor_turma')
                ->where('professor_id', $professor_id)
                ->where('turma_id', $turma_id)
                ->delete();
        
            return redirect()->route('professor-turma.index')->with('success', 'Vinculação excluída com sucesso!');
        }
        

    }