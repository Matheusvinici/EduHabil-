<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EscolaSelecionada
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if ($user && $user->isProfessor() && !session('escola_selecionada')) {
            // Se tentar acessar qualquer rota exceto as de seleção
            if (!$request->is('professor/selecionar*') && !$request->is('professor/definir*')) {
                return redirect()->route('selecionar.escola');
            }
        }
        
        return $next($request);
    }
}