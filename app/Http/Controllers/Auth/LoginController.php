<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Exibe o formulário de login.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login'); // Certifique-se de que a view `auth.login` existe
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validação dos dados do formulário
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Busca o usuário pelo email
        $user = User::where('email', $request->email)->first();

        // Verifica se o usuário existe
        if (!$user) {
            return back()->withErrors([
                'email' => 'E-mail não encontrado.',
            ]);
        }

        // Lógica para alunos (usam código de acesso como senha)
        if ($user->role === 'aluno' && $user->codigo_acesso === $request->password) {
            Auth::login($user); // Autentica o aluno
            $request->session()->regenerate();
            return redirect()->route('aluno.dashboard'); // Redireciona para o dashboard do aluno
        }

        // Lógica para outros perfis (professor, admin, etc.)
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            // Redireciona com base no papel do usuário
            switch ($user->role) {
                case 'professor':
                    return redirect()->route('professor.dashboard');
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'coordenador':
                    return redirect()->route('coordenador.dashboard');
                case 'aee':
                    return redirect()->route('aee.dashboard');
                case 'inclusiva':
                    return redirect()->route('inclusiva.dashboard');
                default:
                    return redirect()->route('home');
            }
        }

        // Se a autenticação falhar, exibe uma mensagem de erro
        return back()->withErrors([
            'password' => 'Senha incorreta.',
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Faz o logout do usuário
        Auth::logout();

        // Invalida a sessão e regenera o token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redireciona para a página inicial
        return redirect('/');
    }
}