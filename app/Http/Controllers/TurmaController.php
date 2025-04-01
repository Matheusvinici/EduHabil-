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
    public function index(Request $request)
    {
        $user = Auth::user();

        // Redireciona para o método correto com base no perfil do usuário
        switch ($user->role) {
            case 'admin':
                return redirect()->route('turmas.admin.index');
            case 'coordenador':
                return redirect()->route('turmas.coordenador.index');
            case 'aee':
                return redirect()->route('turmas.aee.index');
            case 'professor':
                return redirect()->route('turmas.professor.index');
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
        'alunos' => 'required|array|min:1',
        'alunos.*' => 'required|string|max:255',
    ]);

    $user = Auth::user();

    // Gera um código único para a turma
    $codigoTurma = Str::random(8);

    // Cria a turma
    $turma = Turma::create([
        'nome_turma' => $request->input('nome_turma'),
        'quantidade_alunos' => count($request->input('alunos')), // Conta a quantidade de alunos
        'escola_id' => $user->escola_id,
        'professor_id' => $user->id,
        'codigo_turma' => $codigoTurma,
    ]);

    // Gera códigos de acesso para os alunos
    $alunos = [];
    foreach ($request->input('alunos') as $nomeAluno) {
        $codigoAcesso = Str::random(8); // Gera um código aleatório
        $email = "{$codigoAcesso}@juazeiro.ba.gov.br"; // E-mail único
        $alunos[] = [
            'name' => $nomeAluno, // Usa o nome real do aluno
            'email' => $email,
            'codigo_acesso' => $codigoAcesso,
            'escola_id' => $user->escola_id,
            'turma_id' => $turma->id,
            'role' => 'aluno',
            'password' => Hash::make($codigoAcesso),
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
    
        // Verificação de permissões
        if ($user->role === 'admin') {
            // Admin pode adicionar alunos a qualquer turma
        } elseif ($user->role === 'professor') {
            if ($turma->professor_id !== $user->id) {
                abort(403, 'Acesso não autorizado.');
            }
        } else {
            abort(403, 'Acesso não autorizado.');
        }
    
        $request->validate([
            'alunos' => 'required|array|min:1',
            'alunos.*' => 'required|string|max:255',
        ]);
    
        $alunos = [];
        foreach ($request->input('alunos') as $nomeAluno) {
            $codigoAcesso = Str::random(8);
            $email = "{$codigoAcesso}@juazeiro.ba.gov.br";
            $alunos[] = [
                'name' => $nomeAluno,
                'email' => $email,
                'codigo_acesso' => $codigoAcesso,
                'escola_id' => $user->escola_id,
                'turma_id' => $turma->id,
                'role' => 'aluno',
                'password' => Hash::make($codigoAcesso),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    
        User::insert($alunos);
        $turma->increment('quantidade_alunos', count($alunos));
    
        return redirect()->route('turmas.show', $turma->id)
                       ->with('success', count($alunos) . ' alunos adicionados com sucesso!');
    }

}
