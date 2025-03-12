<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    AdminController,
    ProfessorController,
    RespostaController,
    EscolaController,
    AlunoController,
    TurmaController,
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

// Rotas para alunos
Route::middleware('auth')->group(function () {
    Route::get('/aluno/dashboard', [AlunoController::class, 'dashboard'])->name('aluno.dashboard');
    Route::resource('respostas', RespostaController::class)->only(['index', 'create', 'store', 'show']);
});

// Rotas para professores
Route::middleware('auth')->group(function () {
    Route::get('/professor/dashboard', [ProfessorController::class, 'dashboard'])->name('professor.dashboard');
    Route::get('respostas/professor/estatisticas', [RespostaController::class, 'professorEstatisticas'])->name('respostas.professor.estatisticas');
    Route::get('respostas/professor/estatisticas/pdf', [RespostaController::class, 'gerarPdfEstatisticas'])->name('respostas.professor.estatisticas.pdf');


    // Rotas para provas
    Route::resource('provas', ProvaController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::get('provas/{prova}/pdf', [ProvaController::class, 'gerarPDF'])->name('provas.gerarPDF');
    

    // Rotas para respostas
    Route::get('respostas/professor', [RespostaController::class, 'professorIndex'])->name('respostas.professor.index');
    Route::get('/respostas/{prova}/create', [RespostaController::class, 'create'])->name('respostas.create');
    Route::post('/respostas/{prova}', [RespostaController::class, 'store'])->name('respostas.store');
    Route::get('respostas/professor/{prova}', [RespostaController::class, 'professorShow'])->name('respostas.professor.show');

    // Rotas para turmas
    Route::resource('turmas', TurmaController::class)->except(['edit', 'update', 'destroy']);
    Route::post('/turmas/{turma}/gerar-codigos-adicionais', [TurmaController::class, 'gerarCodigosAdicionais'])
        ->name('turmas.gerar-codigos-adicionais');
});

// Rotas para administradores
Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('respostas/admin/estatisticas', [RespostaController::class, 'adminEstatisticas'])->name('respostas.admin.estatisticas');
    Route::get('respostas/admin/estatisticas/pdf', [RespostaController::class, 'gerarPdfEstatisticas'])->name('respostas.admin.estatisticas.pdf');


    // Rotas para escolas, turmas, disciplinas, habilidades, questões, provas, alunos, anos, etc.
    Route::resource('escolas', EscolaController::class);
    Route::resource('turmas', TurmaController::class);
    Route::resource('disciplinas', DisciplinaController::class);
    Route::resource('habilidades', HabilidadeController::class);
    Route::resource('questoes', QuestaoController::class)->parameters([
        'questoes' => 'questao', // Altera o parâmetro {questo} para {questao}
    ]);
    Route::resource('provas', ProvaController::class);
    Route::resource('alunos', AlunoController::class);
    Route::resource('anos', AnoEscolarController::class);

    // Geração de PDF
    Route::get('provas/{prova}/pdf', [ProvaController::class, 'gerarPDF'])->name('provas.gerarPDF');

    // Gestão de Usuários
    Route::resource('users', UserController::class)->except(['create', 'store']);
});

// Rotas comuns a todos os usuários autenticados
Route::middleware('auth')->group(function () {
    // Perfil do Usuário
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
});