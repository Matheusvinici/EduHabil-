<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserEscola;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Escola;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB; 


class UserController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');
        $role_filter = $request->query('role');
        $escola_filter = $request->query('escola_id');
    
        // Carrega os relacionamentos necessários
        $query = User::with(['escolas', 'turma.escola'])->latest('created_at');
    
        // Filtros específicos por tipo de usuário
        if ($user->role === 'inclusiva') {
            $query->where('role', 'aee');
        } elseif ($user->role === 'coordenador') {
            $query->whereHas('escolas', function($q) use ($user) {
                $q->where('escolas.id', $user->escola_id);
            });
        }
    
        // Filtro de busca por nome
        if ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        }
    
        // Filtro por role
        if ($role_filter) {
            $query->where('role', $role_filter);
        }
    
        // Filtro por escola - considera tanto o relacionamento direto quanto através da turma
        if ($escola_filter) {
            $query->where(function($q) use ($escola_filter) {
                $q->whereHas('escolas', function($q) use ($escola_filter) {
                    $q->where('escolas.id', $escola_filter);
                })
                ->orWhereHas('turma', function($q) use ($escola_filter) {
                    $q->where('escola_id', $escola_filter);
                });
            });
        }
    
        $users = $query->paginate(10);
        $escolas = Escola::all();
    
        return view('users.index', compact('users', 'escolas'));
    }
        public function create()
        {
            $escolas = Escola::all();
            $roles = ['admin', 'tutor', 'professor', 'gestor', 'aluno', 'aee', 'inclusiva', 'coordenador', 'aplicador'];
            return view('users.create', compact('escolas', 'roles'));
        }

        public function createLote()
        {
            $escolas = Escola::all();
            $rolesPermitidas = ['professor', 'aee', 'coordenador', 'gestor'];
            return view('users.create-lote', compact('escolas', 'rolesPermitidas'));
        }

       // No controller
        public function store(Request $request)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'role' => 'required|in:admin,tutor,professor,aluno,aee,inclusiva,coordenador,aplicador',
                'escolas' => 'required_if:role,professor,aee,coordenador,gestor|array',
                'escolas.*' => 'exists:escolas,id',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Verificar se usuário já existe
            $user = User::where('email', $request->email)->first();

            if ($user) {
                // Usuário existe, apenas vincular às escolas
                $escolasParaVincular = array_diff($request->escolas, $user->escolas->pluck('id')->toArray());
                
                if (!empty($escolasParaVincular)) {
                    $user->escolas()->attach($escolasParaVincular);
                    return redirect()->route('users.index')
                        ->with('success', 'Usuário já existente foi vinculado às novas escolas!');
                }
                
                return redirect()->route('users.edit', $user->id)
                    ->with('info', 'Usuário já existe e já está vinculado a todas as escolas selecionadas.');
            }

            // Criar novo usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            // Vincular às escolas se necessário
            if (in_array($request->role, ['professor', 'aee', 'coordenador', 'gestor'])) {
                $user->escolas()->attach($request->escolas);
            }

            return redirect()->route('users.index')
                ->with('success', 'Usuário criado com sucesso!');
        }
        public function storeLote(Request $request)
        {
            $request->validate([
                'role' => 'required|in:professor,aee,coordenador,gestor',
                'escola_id' => 'required|exists:escolas,id',
                'usuarios_excel' => 'required|array',
                'usuarios_excel.*.name' => 'required|string',
                'usuarios_excel.*.email' => 'nullable|email',
            ]);
        
            DB::beginTransaction();
            try {
                $escolaId = $request->input('escola_id');
                $role = $request->input('role');
                $escola = Escola::findOrFail($escolaId);
                $usuarios = $request->input('usuarios_excel');
        
                $emails = array_column(array_filter($usuarios, fn($u) => !empty($u['email'])), 'email');
                $usuariosExistentes = User::whereIn('email', $emails)->get()->keyBy('email');
        
                $resultado = [
                    'novos' => 0,
                    'vinculados' => 0,
                    'ignorados' => 0
                ];
        
                foreach ($usuarios as $usuario) {
                    if (!empty($usuario['email']) && isset($usuariosExistentes[$usuario['email']])) {
                        $user = $usuariosExistentes[$usuario['email']];
                        if (!$user->escolas()->where('escola_id', $escolaId)->exists()) {
                            $user->escolas()->attach($escolaId);
                            $resultado['vinculados']++;
                        } else {
                            $resultado['ignorados']++;
                        }
                    } else {
                        $email = $usuario['email'] ?? $this->gerarEmailAutomatico($usuario['name'], $escolaId);
                        
                        $newUser = User::create([
                            'name' => $usuario['name'],
                            'email' => $email,
                            'password' => Hash::make($email),
                            'role' => $role,
                            'escola_id' => $escolaId,
                        ]);
                        $newUser->escolas()->attach($escolaId);
                        $resultado['novos']++;
                    }
                }
        
                DB::commit();
        
                return redirect()->route('users.index')
                    ->with('success', $this->gerarMensagemSucesso($resultado, $escola->nome));
        
            } catch (\Exception $e) {
                DB::rollBack();
                return back()
                    ->withErrors(['error' => 'Erro no cadastro em lote: ' . $e->getMessage()])
                    ->withInput();
            }
        }
        
        protected function gerarEmailAutomatico($nome, $escolaId)
        {
            $username = Str::slug($nome);
            return "{$username}@escola{$escolaId}.edu";
        }
        
        protected function gerarMensagemSucesso($resultado, $escolaNome)
        {
            $mensagem = "Cadastro em lote concluído: ";
            $mensagem .= "{$resultado['novos']} novos usuários, ";
            $mensagem .= "{$resultado['vinculados']} vinculados à escola {$escolaNome}";
            
            if ($resultado['ignorados'] > 0) {
                $mensagem .= ", {$resultado['ignorados']} já estavam cadastrados";
            }
            
            return $mensagem;
        }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $escolas = Escola::all();
        $roles = ['admin', 'tutor', 'professor', 'aluno', 'aee', 'inclusiva', 'coordenador', 'aplicador'];
        return view('users.edit', compact('user', 'escolas', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,professor,aee,inclusiva,coordenador,gestor,tutor,aplicador',
            'escolas' => 'nullable|array',
            'escolas.*' => 'exists:escolas,id',
            'password' => 'nullable|min:8|confirmed'
        ]);
    
        DB::beginTransaction();
        try {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->role = $request->role;
            
            // Atualiza escola principal apenas para perfis que usam escola_id
            if (in_array($request->role, ['professor', 'aee', 'coordenador', 'gestor'])) {
                $user->escola_id = $request->escola_id;
            } else {
                $user->escola_id = null;
            }
    
            // Atualiza senha apenas se for fornecida
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
    
            $user->save();
    
            // Sincroniza escolas vinculadas
            if (in_array($request->role, ['professor', 'aee', 'coordenador', 'gestor'])) {
                $user->escolas()->sync($request->escolas ?? []);
            } else {
                $user->escolas()->detach();
            }
    
            DB::commit();
    
            return redirect()->route('users.index')
                ->with('success', 'Usuário atualizado com sucesso!');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao atualizar usuário: ' . $e->getMessage()]);
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuário excluído com sucesso!');
    }

    public function generatePdf(Request $request)
{
    // Obtenha todos os parâmetros de filtro da requisição
    $filters = $request->all();

    // Recrie a query exatamente como no index
    $query = User::with(['escolas', 'turma.escola']);

    // Aplique os mesmos filtros do index
    if (auth()->user()->role === 'inclusiva') {
        $query->where('role', 'aee');
    } elseif (auth()->user()->role === 'coordenador') {
        $query->whereHas('escolas', function($q) {
            $q->where('escolas.id', auth()->user()->escola_id);
        });
    }

    if (!empty($filters['search'])) {
        $query->where('name', 'like', '%' . $filters['search'] . '%');
    }

    if (!empty($filters['role'])) {
        $query->where('role', $filters['role']);
    }

    if (!empty($filters['escola_id'])) {
        $query->where(function($q) use ($filters) {
            $q->whereHas('escolas', function($q) use ($filters) {
                $q->where('escolas.id', $filters['escola_id']);
            })
            ->orWhereHas('turma', function($q) use ($filters) {
                $q->where('escola_id', $filters['escola_id']);
            });
        });
    }

    // Obtenha os resultados
    $users = $query->get();

    // Prepare dados adicionais para a view
    $escola = !empty($filters['escola_id']) ? Escola::find($filters['escola_id']) : null;

    // Gere o PDF
    $pdf = PDF::loadView('users.pdf', [
        'users' => $users,
        'escola' => $escola,
        'search' => $filters['search'] ?? null
    ]);

    return $pdf->download('relatorio_usuarios_'.now()->format('Ymd_His').'.pdf');
}
}
