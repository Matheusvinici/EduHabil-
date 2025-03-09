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
        $request->validate([
            'user_type' => 'required|in:aluno,professor,admin', // Adicione 'admin' aqui
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Lógica para alunos
        if ($request->user_type === 'aluno') {
            $user = User::where('email', $request->email)
                        ->where('codigo_acesso', $request->password)
                        ->where('role', 'aluno')
                        ->first();

            if ($user) {
                Auth::login($user); // Autentica o aluno
                $request->session()->regenerate();
                return redirect()->route('aluno.dashboard'); // Redireciona para o dashboard do aluno
            }

            return back()->withErrors([
                'email' => 'Credenciais inválidas para aluno.',
            ]);
        }

        // Lógica para professores e administradores
        $guard = $request->user_type === 'professor' ? 'web' : 'web'; // Use o guard 'web' para ambos
        if (Auth::guard($guard)->attempt($request->only('email', 'password'))) {
            $user = Auth::guard($guard)->user();

            // Verifica se o usuário tem o papel correto
            if (($request->user_type === 'professor' && $user->role === 'professor') ||
                ($request->user_type === 'admin' && $user->role === 'admin')) {
                $request->session()->regenerate();
                return redirect()->route('home'); // Redireciona para a página inicial
            }

            // Se o papel não corresponder, faz logout e exibe erro
            Auth::guard($guard)->logout();
            return back()->withErrors([
                'email' => 'Este usuário não tem permissão para acessar esta área.',
            ]);
        }

        // Se a autenticação falhar, exibe uma mensagem de erro
        return back()->withErrors([
            'email' => 'Credenciais inválidas. Verifique seu e-mail e senha.',
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
        // Determina o guard com base no tipo de usuário autenticado
        $user = Auth::user();

        if ($user->role === 'aluno') {
            $guard = 'aluno';
        } else {
            $guard = 'web';
        }

        // Faz o logout do usuário
        Auth::guard($guard)->logout();

        // Invalida a sessão e regenera o token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redireciona para a página inicial
        return redirect('/');
    }
}