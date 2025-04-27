<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Escola;

class EscolaSelecionada
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
    
        if (!$user) {
            return redirect()->route('login');
        }
    
        // Perfis que NÃO precisam selecionar escola
        $perfisSemEscola = ['admin', 'tutor', 'aplicador', 'inclusiva'];
        if (in_array($user->role, $perfisSemEscola)) {
            view()->share('escolaAtual', null);
            view()->share('usuarioPodeSelecionarEscola', false);
            return $next($request);
        }
    
        // Determinar a chave de sessão correta baseada no perfil
        $sessionKey = 'escola_selecionada';
        if ($user->isAee()) {
            $sessionKey = 'escola_selecionada_aee';
        }
    
        // Perfis que PRECISAM selecionar escola
        $perfisComEscola = ['professor', 'coordenador', 'gestor', 'aee'];
        $rotaSelecao = null;
        $usuarioPodeSelecionarEscola = true;
    
        if ($user->role === 'coordenador') {
            $rotaSelecao = 'coordenador.selecionar.escola';
        } elseif ($user->role === 'professor') {
            $rotaSelecao = 'professor.selecionar.escola';
        } elseif ($user->role === 'gestor') {
            $rotaSelecao = 'gestor.selecionar.escola';
        } elseif ($user->role === 'aee') {
            $rotaSelecao = 'aee.selecionar.escola';
        }
    
        if (in_array($user->role, $perfisComEscola)) {
            if (!session($sessionKey) || !$user->escolas->contains(session($sessionKey))) {
                $rotasPermitidas = [$rotaSelecao, $user->role.'.definir.escola', 'logout', 'profile.edit'];
                if (!in_array($request->route()->getName(), $rotasPermitidas)) {
                    return redirect()->route($rotaSelecao)
                        ->with('info', 'Selecione uma escola para continuar');
                }
                view()->share('escolaAtual', null);
            } else {
                view()->share('escolaAtual', Escola::find(session($sessionKey)));
            }
        } else {
            view()->share('escolaAtual', null);
            view()->share('usuarioPodeSelecionarEscola', false);
        }
    
        view()->share('usuarioPodeSelecionarEscola', $usuarioPodeSelecionarEscola);
        return $next($request);
    }
}