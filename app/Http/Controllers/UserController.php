<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Escola;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use PDF;

class UserController extends Controller
{
    
    public function index(Request $request)
{
    $user = Auth::user();
    $search = $request->query('search');
    $role_filter = $request->query('role');
    $escola_filter = $request->query('escola_id');

    // Inicia a query ordenando por created_at decrescente
    $query = User::with('escola')->latest('created_at');

    if ($user->role === 'inclusiva') {
        $query->where('role', 'aee');
    } elseif ($user->role === 'coordenador') {
        $query->where('escola_id', $user->escola_id);
    }

    if ($search) {
        $query->where('name', 'like', '%'.$search.'%');
    }

    if ($role_filter) {
        $query->where('role', $role_filter);
    }

    if ($escola_filter) {
        $query->where('escola_id', $escola_filter);
    }

    $users = $query->paginate(10);
    $escolas = Escola::all();
    $roles = ['admin', 'professor', 'aluno', 'aee', 'inclusiva', 'coordenador', 'aplicador'];

    return view('users.index', compact('users', 'escolas', 'roles'));
}
    public function create()
    {
        $escolas = Escola::all();
        $roles = ['admin', 'professor', 'aluno', 'aee', 'inclusiva', 'coordenador', 'aplicador'];
        return view('users.create', compact('escolas', 'roles'));
    }

    public function store(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,professor,aluno,aee,inclusiva,coordenador,aplicador',
            'cpf' => 'nullable|string|max:14',
            'codigo_acesso' => 'nullable|string|max:10',
            'escola_id' => 'nullable|exists:escolas,id',
        ]);

        // Validação condicional para escola_id
        if (in_array($request->role, ['coordenador', 'professor', 'aplicador'])) {
            $request->validate([
                'escola_id' => 'required|exists:escolas,id',
            ]);
        }

        // Cria o usuário
        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
            'codigo_acesso' => $request->input('codigo_acesso'),
            'escola_id' => $request->input('escola_id'), // Pode ser null para inclusiva e admin
        ]);

        // Redireciona de volta com uma mensagem de sucesso
        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $escolas = Escola::all();
        $roles = ['admin', 'professor', 'aluno', 'aee', 'inclusiva', 'coordenador', 'aplicador'];
        return view('users.edit', compact('user', 'escolas', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,professor,aluno,aee,inclusiva,coordenador, aplicador',
            'cpf' => 'nullable|string|max:14',
            'codigo_acesso' => 'nullable|string|max:10',
            'escola_id' => 'nullable|exists:escolas,id',
        ]);

        if (in_array($request->role, ['coordenador', 'professor'])) {
            $request->validate(['escola_id' => 'required|exists:escolas,id']);
        }

        $data = $request->only(['name', 'email', 'role', 'cpf', 'codigo_acesso', 'escola_id']);
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
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
        $query = User::with('escola');
        
        // Aplique os mesmos filtros do index
        if (auth()->user()->role === 'inclusiva') {
            $query->where('role', 'aee');
        } elseif (auth()->user()->role === 'coordenador') {
            $query->where('escola_id', auth()->user()->escola_id);
        }
        
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
        
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }
        
        if (!empty($filters['escola_id'])) {
            $query->where('escola_id', $filters['escola_id']);
        }
        
        // Obtenha os resultados
        $users = $query->get();
        
        // Prepare dados adicionais para a view
        $escola = !empty($filters['escola_id']) ? Escola::find($filters['escola_id']) : null;
        $role = !empty($filters['role']) ? ucfirst($filters['role']) : null;
        
        // Gere o PDF
        $pdf = PDF::loadView('users.pdf', [
            'users' => $users,
            'escola' => $escola,
            'role' => $role,
            'search' => $filters['search'] ?? null
        ]);
        
        return $pdf->download('relatorio_usuarios_'.now()->format('Ymd_His').'.pdf');
    }
}