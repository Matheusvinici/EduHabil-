<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Verifica o papel do usuário autenticado
        $user = Auth::user();

        if ($user->role === 'aluno') {
            return redirect()->route('aluno.dashboard'); // Redireciona para o dashboard do aluno
        } elseif ($user->role === 'professor') {
            return redirect()->route('professor.dashboard'); // Redireciona para o dashboard do professor
        } elseif ($user->role === 'admin') {
            return redirect()->route('admin.dashboard'); // Redireciona para o dashboard do admin
        }

        // Caso o papel não seja reconhecido, exibe a página inicial padrão
        return view('home');
    }
}