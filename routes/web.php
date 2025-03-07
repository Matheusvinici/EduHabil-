<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    
    UserController,
    RespostaController,
    ProfileController,
    HomeController,
    AnoEscolarController,
    DisciplinaController,
    HabilidadeController,
   QuestaoController,
   ProvaController,
};
use Barryvdh\DomPDF\Facade as PDF;

// Rota inicial
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Rota para a página inicial autenticada
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    // Páginas estáticas
    Route::view('about', 'about')->name('about');

    // Gestão de Usuários
    Route::resource('anos', AnoEscolarController::class);
    Route::resource('disciplinas', DisciplinaController::class);
    Route::resource('habilidades', HabilidadeController::class);
    Route::resource('questoes', QuestaoController::class);
   // Rotas para Respostas
Route::get('/respostas', [RespostaController::class, 'index'])->name('respostas.index');
Route::get('/respostas/{prova}/create', [RespostaController::class, 'create'])->name('respostas.create');
Route::post('/respostas/{prova}', [RespostaController::class, 'store'])->name('respostas.store');
Route::get('/respostas/{prova}', [RespostaController::class, 'show'])->name('respostas.show');

    Route::resource('provas', ProvaController::class);
    Route::resource('provas', ProvaController::class);
    Route::get('provas/{prova}/pdf', [ProvaController::class, 'gerarPDF'])->name('provas.gerarPDF');


    // Rota para gerar o PDF da prova
    

// Atualizar dados do usuário
    Route::put('user/{id}', [UserController::class, 'update'])->name('admin.user.update');
    Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('admin.user.edit');

    Route::get('users', [UserController::class, 'index'])->name('users.index');

    

    // Teste de geração de PDF
    Route::get('/teste-pdf', function () {
        $data = ['mensagem' => 'Teste de geração de PDF!'];
        $pdf = PDF::loadView('inscricoes.pdf', $data);
        return $pdf->download('teste.pdf');
    });

   
    // Perfil do Usuário
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
});
