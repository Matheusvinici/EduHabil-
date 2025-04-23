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
            $rotaSelecao = 'aee.selecionar.escola'; // Assumindo que você terá uma rota de seleção para AEE
        }

        if (in_array($user->role, $perfisComEscola)) {
            if (!session('escola_selecionada') || !$user->escolas->contains(session('escola_selecionada'))) {
                $rotasPermitidas = [$rotaSelecao, 'definir.escola', 'logout', 'profile.edit'];
                if (!in_array($request->route()->getName(), $rotasPermitidas)) {
                    return redirect()->route($rotaSelecao)
                        ->with('info', 'Selecione uma escola para continuar');
                }
                view()->share('escolaAtual', null);
            } else {
                view()->share('escolaAtual', Escola::find(session('escola_selecionada')));
            }
        } else {
            // Se o perfil não está em nenhum dos grupos acima (por segurança)
            view()->share('escolaAtual', null);
            view()->share('usuarioPodeSelecionarEscola', false);
            // Opcional: Redirecionar ou abortar se um role inesperado for encontrado
            // return abort(403, 'Acesso não permitido para este perfil.');
        }

        view()->share('usuarioPodeSelecionarEscola', $usuarioPodeSelecionarEscola);
        return $next($request);
    }
}