<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use App\Models\User;
use App\Models\Escola;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class TurmaController extends Controller
{
    /**
     * Exibe a listagem de turmas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin pode ver todas as turmas
            $turmas = Turma::with(['escola', 'professor'])->get();
        } elseif ($user->role === 'professor') {
            // Professor só pode ver as turmas que ele cadastrou
            $turmas = Turma::where('professor_id', $user->id)->get();
        } else {
            // Outros papéis (se houver) não têm permissão
            abort(403, 'Acesso não autorizado.');
        }

        return view('turmas.index', compact('turmas'));
    }

    /**
     * Exibe o formulário de criação de turmas.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
{
    $user = Auth::user();

    // Verifica se o usuário está autenticado
    if (!$user) {
        abort(403, 'Usuário não autenticado.');
    }

    // Verifica o papel do usuário
    if ($user->role === 'admin') {
        // Admin pode selecionar qualquer escola
        $escolas = Escola::all();
    } elseif ($user->role === 'professor') {
        // Verifica se o professor está vinculado a uma escola
        if (is_null($user->escola_id)) {
            abort(403, 'Você não está vinculado a uma escola. Contate o administrador.');
        }

        // Verifica se a escola existe
        $escola = Escola::find($user->escola_id);
        if (is_null($escola)) {
            abort(404, 'Escola não encontrada. Contate o administrador.');
        }

        // Professor só pode criar turmas para a escola à qual está vinculado
        $escolas = Escola::where('id', $user->escola_id)->get();
    } else {
        // Outros papéis (se houver) não têm permissão
        abort(403, 'Acesso não autorizado.');
    }

    return view('turmas.create', compact('escolas'));
}

    /**
     * Armazena uma nova turma no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome_turma' => 'required|string|max:255',
            'quantidade_alunos' => 'required|integer|min:1',
        ]);
    
        $user = Auth::user();
    
        // Gera um código único para a turma
        $codigoTurma = Str::random(8);
    
        // Cria a turma
        $turma = Turma::create([
            'nome_turma' => $request->input('nome_turma'),
            'quantidade_alunos' => $request->input('quantidade_alunos'),
            'escola_id' => $user->escola_id, // Vincula à escola do professor logado
            'professor_id' => $user->id, // Vincula ao professor logado
            'codigo_turma' => $codigoTurma, // Define o código da turma
        ]);
    
        // Gera códigos de acesso para os alunos
        $alunos = [];
        for ($i = 1; $i <= $request->input('quantidade_alunos'); $i++) {
            $codigoAcesso = Str::random(8); // Gera um código aleatório
            $email = "{$codigoAcesso}@juazeiro.ba.gov.br"; // E-mail único
            $alunos[] = [
                'name' => "Aluno {$i} - {$turma->nome_turma}",
                'email' => $email,
                'codigo_acesso' => $codigoAcesso,
                'escola_id' => $user->escola_id, // Vincula à escola do professor
                'turma_id' => $turma->id, // Vincula à turma
                'role' => 'aluno',
                'password' => Hash::make($codigoAcesso), // Senha é o código de acesso
            ];
        }
    
        // Insere os alunos no banco de dados
        User::insert($alunos);
    
       
    
        return redirect()->route('turmas.index')
                         ->with('success', 'Turma e alunos cadastrados com sucesso!');
    }

    /**
     * Exibe os detalhes de uma turma.
     *
     * @param  \App\Models\Turma  $turma
     * @return \Illuminate\Http\Response
     */
    public function show(Turma $turma)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin pode ver qualquer turma
        } elseif ($user->role === 'professor') {
            // Professor só pode ver as turmas que ele cadastrou
            if ($turma->professor_id !== $user->id) {
                abort(403, 'Acesso não autorizado.');
            }
        } else {
            // Outros papéis (se houver) não têm permissão
            abort(403, 'Acesso não autorizado.');
        }

        // Lista os alunos da turma
        $alunos = User::where('turma_id', $turma->id)->get();
        return view('turmas.show', compact('turma', 'alunos'));
    }

    /**
     * Exibe o formulário de edição de uma turma.
     *
     * @param  \App\Models\Turma  $turma
     * @return \Illuminate\Http\Response
     */
    public function edit(Turma $turma)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin pode editar qualquer turma
        } elseif ($user->role === 'professor') {
            // Professor só pode editar as turmas que ele cadastrou
            if ($turma->professor_id !== $user->id) {
                abort(403, 'Acesso não autorizado.');
            }
        } else {
            // Outros papéis (se houver) não têm permissão
            abort(403, 'Acesso não autorizado.');
        }

        return view('turmas.edit', compact('turma'));
    }

    /**
     * Atualiza uma turma no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Turma  $turma
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Turma $turma)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin pode editar qualquer turma
        } elseif ($user->role === 'professor') {
            // Professor só pode editar as turmas que ele cadastrou
            if ($turma->professor_id !== $user->id) {
                abort(403, 'Acesso não autorizado.');
            }
        } else {
            // Outros papéis (se houver) não têm permissão
            abort(403, 'Acesso não autorizado.');
        }

        $request->validate([
            'nome_turma' => 'required|string|max:255',
            'quantidade_alunos' => 'required|integer|min:1',
        ]);

        // Atualiza a turma
        $turma->update([
            'nome_turma' => $request->input('nome_turma'),
            'quantidade_alunos' => $request->input('quantidade_alunos'),
        ]);

        return redirect()->route('turmas.index')
                         ->with('success', 'Turma atualizada com sucesso!');
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
    public function gerarCodigosAdicionais(Request $request, Turma $turma)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin pode gerar códigos para qualquer turma
        } elseif ($user->role === 'professor') {
            // Professor só pode gerar códigos para as turmas que ele cadastrou
            if ($turma->professor_id !== $user->id) {
                abort(403, 'Acesso não autorizado.');
            }
        } else {
            // Outros papéis (se houver) não têm permissão
            abort(403, 'Acesso não autorizado.');
        }

        $request->validate([
            'quantidade_adicionais' => 'required|integer|min:1',
        ]);

        $quantidadeAdicionais = $request->input('quantidade_adicionais');

        // Gera códigos de acesso para os novos alunos
        $alunos = [];
        for ($i = 1; $i <= $quantidadeAdicionais; $i++) {
            $codigoAcesso = Str::random(8); // Gera um código aleatório
            $email = "{$codigoAcesso}@juazeiro.ba.gov.br"; // E-mail único
            $alunos[] = [
                'name' => "Aluno Adicional {$i} - {$turma->nome_turma}",
                'email' => $email,
                'codigo_acesso' => $codigoAcesso,
                'escola_id' => $user->escola_id, // Vincula à escola do professor
                'turma_id' => $turma->id, // Vincula à turma
                'role' => 'aluno',
                'password' => Hash::make($codigoAcesso), // Senha é o código de acesso
            ];
        }

        // Insere os novos alunos no banco de dados
        User::insert($alunos);

        // Atualiza a quantidade de alunos na turma
        $turma->quantidade_alunos += $quantidadeAdicionais;
        $turma->save();

        return redirect()->route('turmas.show', $turma->id)
                         ->with('success', "{$quantidadeAdicionais} códigos adicionais gerados com sucesso!");
    }
}