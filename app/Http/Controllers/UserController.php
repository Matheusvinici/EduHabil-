<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Escola;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');

        // Filtra os usuários com base no papel do usuário logado
        if ($user->role === 'inclusiva') {
            // Diretor inclusivo vê todos os professores do AEE
            $users = User::where('role', 'aee')->paginate(10);
        } elseif ($user->role === 'coordenador') {
            // Coordenador vê apenas os usuários da sua escola
            $users = User::where('escola_id', $user->escola_id)->paginate(10);
        } else {
            // Admin vê todos os usuários
            $users = User::paginate(10);
        }

        // Filtra por pesquisa, se houver
        if ($search) {
            $users = User::where('name', 'like', '%' . $search . '%')->paginate(10);
        }

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $escolas = Escola::all(); // Busca todas as escolas
        return view('users.create', compact('escolas'));
    }

    public function store(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,professor,aluno,aee,inclusiva,coordenador',
            'cpf' => 'nullable|string|max:14',
            'codigo_acesso' => 'nullable|string|max:10',
            'escola_id' => 'nullable|exists:escolas,id',
        ]);

        // Validação condicional para escola_id
        if (in_array($request->role, ['coordenador', 'professor'])) {
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

    public function edit($id)
    {
        $user = User::findOrFail($id); // Encontra o usuário pelo ID
        $escolas = Escola::all(); // Busca todas as escolas
        return view('users.edit', compact('user', 'escolas'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        // Validação dos dados
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,professor,aluno,aee,inclusiva,coordenador',
            'escola_id' => 'nullable|exists:escolas,id',
        ]);
    
        // Atualiza os dados do usuário
        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'escola_id' => $request->input('escola_id'), // Pode ser null para inclusiva e admin
        ]);
    
        return redirect()->route('admin.user.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id); // Encontra o usuário pelo ID
        $user->delete(); // Exclui o usuário

        // Redireciona de volta com uma mensagem de sucesso
        return redirect()->route('users.index')->with('success', 'Usuário excluído com sucesso!');
    }
}