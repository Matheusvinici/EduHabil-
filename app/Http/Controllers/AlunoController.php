<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Escola;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AlunoController extends Controller
{
    /**
     * Exibe a listagem de alunos.
     *
     * @return \Illuminate\Http\Response
     */

     public function dashboard()
     {
         return view('aluno.dashboard'); // Certifique-se de que a view `aluno.dashboard` existe
     }
    public function index()
    {
        // Lista apenas os alunos vinculados à escola do professor logado
        $alunos = User::where('escola_id', auth()->user()->escola_id)
                      ->where('role', 'aluno')
                      ->get();
        return view('alunos.index', compact('alunos'));
    }

    /**
     * Exibe o formulário de criação de alunos.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('alunos.create');
    }

    /**
     * Armazena um novo aluno no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'quantidade_alunos' => 'required|integer|min:1',
        ]);

        $turma = $request->input('turma'); // Nome da turma
        $quantidadeAlunos = $request->input('quantidade_alunos');

        // Gera códigos de acesso para os alunos
        $alunos = [];
        for ($i = 1; $i <= $quantidadeAlunos; $i++) {
            $codigoAcesso = Str::random(8); // Gera um código aleatório
            $alunos[] = [
                'name' => "Aluno {$i} - {$turma}",
                'codigo_acesso' => $codigoAcesso,
                'escola_id' => auth()->user()->escola_id, // Vincula à escola do professor
                'role' => 'aluno',
                'password' => Hash::make($codigoAcesso), // Usa o código como senha
            ];
        }

        // Insere os alunos no banco de dados
        User::insert($alunos);

        return redirect()->route('alunos.index')
                         ->with('success', 'Alunos cadastrados com sucesso!');
    }

    /**
     * Exibe os detalhes de um aluno.
     *
     * @param  \App\Models\User  $aluno
     * @return \Illuminate\Http\Response
     */
    public function show(User $aluno)
    {
        return view('alunos.show', compact('aluno'));
    }

    /**
     * Exibe o formulário de edição de um aluno.
     *
     * @param  \App\Models\User  $aluno
     * @return \Illuminate\Http\Response
     */
    public function edit(User $aluno)
    {
        return view('alunos.edit', compact('aluno'));
    }

    /**
     * Atualiza um aluno no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $aluno
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $aluno)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'codigo_acesso' => 'required|string|max:255|unique:users,codigo_acesso,' . $aluno->id,
        ]);

        $aluno->update([
            'name' => $request->input('name'),
            'codigo_acesso' => $request->input('codigo_acesso'),
        ]);

        return redirect()->route('alunos.index')
                         ->with('success', 'Aluno atualizado com sucesso!');
    }

    /**
     * Remove um aluno do banco de dados.
     *
     * @param  \App\Models\User  $aluno
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $aluno)
    {
        $aluno->delete();
        return redirect()->route('alunos.index')
                         ->with('success', 'Aluno excluído com sucesso!');
    }
}