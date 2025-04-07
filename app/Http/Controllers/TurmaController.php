<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use App\Models\User;
use App\Models\Escola;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;


class TurmaController extends Controller
{
    /**
     * Exibe a listagem de turmas.
     *
     * @return \Illuminate\Http\Response
     */
                public function index(Request $request)
                {
                    $user = Auth::user();

                    // Redireciona para o método correto com base no perfil do usuário
                    switch ($user->role) {
                        case 'admin':
                            return redirect()->route('turmas.admin.index');
                        case 'inclusiva':
                                return redirect()->route('turmas.admin.index');
                        case 'coordenador':
                            return redirect()->route('turmas.coordenador.index');
                        case 'aee':
                            return redirect()->route('turmas.aee.index');
                        case 'professor':
                            return redirect()->route('turmas.professor.index');
                        case 'aplicador':
                            return redirect()->route('turmas.aplicador.index');
                
                        default:
                            abort(403, 'Acesso não autorizado.');
                    }
                }

                public function indexAdmin(Request $request)
                {
                // Quantitativo de turmas por escola
                $escolas = Escola::withCount('turmas')->paginate(10);

                return view('turmas.admin.index', compact('escolas'));
                }
               
                public function indexCoordenador(Request $request)
                {
                $user = Auth::user();
                $escolaId = $user->escola_id;

                // Filtro por nome da turma
                $nomeTurma = $request->query('nome_turma');

                // Query base
                $turmas = Turma::where('escola_id', $escolaId)
                    ->when($nomeTurma, function ($query, $nomeTurma) {
                        return $query->where('nome_turma', 'like', '%' . $nomeTurma . '%');
                    })
                    ->with(['escola', 'professor'])
                    ->paginate(5);

                return view('turmas.coordenador.index', compact('turmas', 'nomeTurma'));
                }

            public function indexAEE(Request $request)
            {
                $user = Auth::user();
                $escolaId = $user->escola_id;

                // Filtro por nome da turma
                $nomeTurma = $request->query('nome_turma');

                // Query base
                $turmas = Turma::where('escola_id', $escolaId)
                    ->when($nomeTurma, function ($query, $nomeTurma) {
                        return $query->where('nome_turma', 'like', '%' . $nomeTurma . '%');
                    })
                    ->with(['escola', 'professor'])
                    ->paginate(5);

                return view('turmas.aee.index', compact('turmas', 'nomeTurma'));
            }

            public function indexProfessor(Request $request)
            {
                $user = Auth::user();

                // Filtro por nome da turma
                $nomeTurma = $request->query('nome_turma');

                // Query base: apenas turmas cadastradas pelo professor
                $turmas = Turma::where('professor_id', $user->id)
                    ->when($nomeTurma, function ($query, $nomeTurma) {
                        return $query->where('nome_turma', 'like', '%' . $nomeTurma . '%');
                    })
                    ->with(['escola', 'professor'])
                    ->paginate(5);

                return view('turmas.professor.index', compact('turmas', 'nomeTurma'));
            }

            public function indexAplicador(Request $request)
            {
                $user = Auth::user();

                // Filtros
                $nomeTurma = $request->query('nome_turma');
                $nomeEscola = $request->query('nome_escola');

                // Query base: apenas turmas cadastradas pelo professor
                $turmas = Turma::where('aplicador_id', $user->id)
                    ->when($nomeTurma, function ($query, $nomeTurma) {
                        return $query->where('nome_turma', 'like', '%' . $nomeTurma . '%');
                    })
                    ->when($nomeEscola, function ($query, $nomeEscola) {
                        return $query->whereHas('escola', function($q) use ($nomeEscola) {
                            $q->where('nome', 'like', '%' . $nomeEscola . '%');
                        });
                    })
                    ->with(['escola', 'aplicador'])
                    ->withCount('alunos')
                    ->paginate(10); // Paginação de 10 itens por página

                return view('turmas.aplicador.index', compact('turmas', 'nomeTurma', 'nomeEscola'));
            }
            public function gerarPdf($id)
                {
                    $turma = Turma::with(['escola', 'alunos'])->findOrFail($id);
                    
                    $pdf = Pdf::loadView('turmas.pdf', compact('turma'));
                    
                    return $pdf->download('turma_' . $turma->nome_turma . '.pdf');
                }


            public function create()
            {
                $user = Auth::user();
            
                if (!$user) {
                    abort(403, 'Usuário não autenticado.');
                }
            
                // Verifica os papéis permitidos (admin ou aplicador)
                if (!in_array($user->role, ['admin', 'aplicador'])) {
                    abort(403, 'Acesso não autorizado.');
                }
            
                // Para admin e aplicador, mostramos todas as escolas
                $escolas = Escola::all();
            
                return view('turmas.create', compact('escolas'));
            }
            public function addAlunosForm(Turma $turma)
            {
                $user = Auth::user();
                
                // Verifica permissões
                if ($user->role === 'admin') {
                    // Admin pode acessar qualquer turma
                } elseif ($user->role === 'aplicador') {
                    if ($turma->aplicador_id !== $user->id) {
                        abort(403, 'Acesso não autorizado.');
                    }
                } else {
                    abort(403, 'Acesso não autorizado.');
                }
            
                return view('turmas.add-alunos', compact('turma'));
            }

                    public function addAlunos(Request $request, Turma $turma)
                    {
                        $user = Auth::user();
                        
                        // Verifica permissões
                        if ($user->role === 'admin') {
                            // Admin pode acessar qualquer turma
                        } elseif ($user->role === 'aplicador') {
                            if ($turma->aplicador_id !== $user->id) {
                                abort(403, 'Acesso não autorizado.');
                            }
                        } else {
                            abort(403, 'Acesso não autorizado.');
                        }

                        $request->validate([
                            'alunos' => 'required|array|min:1',
                            'alunos.*' => 'required|string|max:255',
                        ]);

                        // Gera e cadastra os novos alunos
                        $novosAlunos = [];
                        foreach ($request->input('alunos') as $nomeAluno) {
                            $codigoAcesso = Str::random(8);
                            $email = "{$codigoAcesso}@juazeiro.ba.gov.br";
                            
                            $novosAlunos[] = [
                                'name' => $nomeAluno,
                                'email' => $email,
                                'codigo_acesso' => $codigoAcesso,
                                'escola_id' => $turma->escola_id,
                                'turma_id' => $turma->id,
                                'role' => 'aluno',
                                'password' => Hash::make($codigoAcesso),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        // Insere os novos alunos
                        User::insert($novosAlunos);

                        return redirect()->route('turmas.show', $turma->id)
                                        ->with('success', count($novosAlunos) . ' alunos foram adicionados à turma com sucesso!');
                    }
            public function store(Request $request)
                    {
                        $request->validate([
                            'nome_turma' => 'required|string|max:255',
                            'alunos' => 'required|array|min:1',
                            'alunos.*' => 'required|string|max:255',
                            'escola_id' => 'required|exists:escolas,id',
                        ]);

                        $user = Auth::user();

                        // Gera um código único para a turma
                        $codigoTurma = Str::random(8);

                        // Cria a turma
                        $turma = Turma::create([
                            'nome_turma' => $request->input('nome_turma'),
                            'quantidade_alunos' => count($request->input('alunos')),
                            'escola_id' => $request->input('escola_id'),
                            'aplicador_id' => $user->id,
                            'codigo_turma' => $codigoTurma,
                        ]);  
                        
                        // Gera códigos de acesso para os alunos
                        $alunos = [];
                        foreach ($request->input('alunos') as $nomeAluno) {
                            $codigoAcesso = Str::random(6);
                            $email = "{$codigoAcesso}@juazeiro.ba.gov.br";
                    
                            $alunos[] = [
                                'name' => $nomeAluno,
                                'email' => $email,
                                'codigo_acesso' => $codigoAcesso,
                                'escola_id' => $request->input('escola_id'),
                                'turma_id' => $turma->id,
                                'role' => 'aluno',
                                'password' => Hash::make($codigoAcesso),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    
                        User::insert($alunos);
                    
        return redirect()->route('turmas.index')
              ->with('success', 'Turma e alunos cadastrados com sucesso!');
                }
                    
            
                public function show(Turma $turma)
                {
                    $user = Auth::user();
                
                    // Verificação mais robusta do usuário
                    if (!$user) {
                        return redirect()->route('login')->with('error', 'Faça login para acessar esta página.');
                    }
                
                    // Verifica se o usuário tem role definido
                    if (empty($user->role)) {
                        abort(403, 'Seu perfil não possui permissões definidas.');
                    }
                
                    // Lógica de permissões melhorada
                    if ($user->role === 'admin') {
                        // Admin tem acesso livre
                    } 
                    elseif ($user->role === 'aplicador') {
                        // Verifica se o aplicador é o dono da turma OU se está associado à turma
                        if ($turma->aplicador_id != $user->id) {
                            abort(403, 'Você só pode visualizar turmas que criou.');
                        }
                    }
                    else {
                        abort(403, 'Seu perfil não tem permissão para visualizar turmas.');
                    }
                
                    // Carrega os alunos com tratamento de exceção
                    try {
                        $alunos = User::where('turma_id', $turma->id)->get();
                    } catch (\Exception $e) {
                        report($e);
                        $alunos = collect(); // Retorna coleção vazia em caso de erro
                    }
                
                    return view('turmas.show', compact('turma', 'alunos'));
                }
    
    /**
     * Remove uma turma do banco de dados.
     *
     * @param  \App\Models\Turma  $turma
     * @return \Illuminate\Http\Response
     */
    public function destroy(Turma $turma)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin pode excluir qualquer turma
        } elseif ($user->role === 'professor') {
            // Professor só pode excluir as turmas que ele cadastrou
            if ($turma->professor_id !== $user->id) {
                abort(403, 'Acesso não autorizado.');
            }
        } else {
            // Outros papéis (se houver) não têm permissão
            abort(403, 'Acesso não autorizado.');
        }

        // Remove os alunos da turma
        User::where('turma_id', $turma->id)->delete();

        // Remove a turma
        $turma->delete();

        return redirect()->route('turmas.index')
                         ->with('success', 'Turma e alunos excluídos com sucesso!');
    }

    /**
     * Gera códigos de acesso adicionais para uma turma.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Turma  $turma
     * @return \Illuminate\Http\Response
     */
    

    public function edit($turma_id, $aluno_id)
    {
        $turma = Turma::findOrFail($turma_id);
        $aluno = User::findOrFail($aluno_id); // Obtendo o aluno corretamente
        $user = auth()->user(); // Usuário autenticado
    
        return view('turmas.edit', compact('aluno', 'user', 'turma'));
    }
    
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'deficiencia' => 'nullable|string|max:255',
        ]);
    
        $aluno = User::findOrFail($id);
        
        $aluno->update([
            'name' => $request->name,
            'deficiencia' => $request->deficiencia,
        ]);
    
        return redirect()->route('turmas.index')->with('success', 'Aluno atualizado com sucesso!');
    }
    public function updateTurma(Request $request, Turma $turma)
    {

        $request->validate([
            'nome_turma' => 'required|string|max:255',
        ]);
    
        // Verifica permissões
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            // Admin pode editar qualquer turma
        } elseif ($user->role === 'aplicador') {
            // Aplicador só pode editar suas próprias turmas
            if ($turma->aplicador_id !== $user->id) {
                abort(403, 'Você só pode editar turmas que criou.');
            }
        } else {
            abort(403, 'Acesso não autorizado.');
        }
    
        $turma->update([
            'nome_turma' => $request->nome_turma,
        ]);
    
        return redirect()->route('turmas.index')->with('success', 'Nome da turma atualizado com sucesso!');
    }
    

    
}
