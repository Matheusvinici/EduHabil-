<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'escola.selecionada' => \App\Http\Middleware\EscolaSelecionada::class,
            'role' => \App\Http\Middleware\CheckRole::class, // Adicione esta linha
        ]);

        // Outros middlewares globais podem ser adicionados aqui
        // $middleware->append([]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Configurações de exceções podem ser adicionadas aqui
    })
    ->create();