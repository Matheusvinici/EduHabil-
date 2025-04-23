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
        $user = Auth::user();
    
        if ($user->role === 'aluno') {
            return redirect()->route('aluno.dashboard');
        } elseif ($user->role === 'professor') {
            return redirect()->route('professor.dashboard');
        } elseif ($user->role === 'coordenador') {
            return redirect()->route('coordenador.dashboard');
        } elseif ($user->role === 'gestor') {
            return redirect()->route('gestor.dashboard');
        } elseif ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
    
        return view('home');
    }
}