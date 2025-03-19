<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    AdminController,
    RecursoController,
    AdaptacaoController,
    DeficienciaController,
    CaracteristicaController,
    CoordenadorController,
    ProfessorController,
    RespostaController,
    EscolaController,
    AtividadeController,
    AtividadeProfessorController,
    AlunoController,
    TurmaController,
    ProfileController,
    HomeController,
    AnoEscolarController,
    DisciplinaController,
    HabilidadeController,
    QuestaoController,
    ProvaController,
    AeeController,
    InclusivaController,
    AvaliacaoController // Adicione o controlador de Avaliação
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

    Route::resource('atividades_professores', AtividadeProfessorController::class);
    Route::get('atividades_professores/{id}/download', [AtividadeProfessorController::class, 'downloadPdf'])->name('atividades_professores.download');

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

// Rotas para professores do AEE
Route::middleware('auth')->group(function () {
    Route::get('/aee/dashboard', [AeeController::class, 'index'])->name('aee.dashboard');
    Route::resource('adaptacoes', AdaptacaoController::class);
});

// Rotas para diretoria inclusiva
Route::middleware('auth')->group(function () {
    Route::get('/inclusiva/dashboard', [InclusivaController::class, 'index'])->name('inclusiva.dashboard');
    Route::resource('caracteristicas', CaracteristicaController::class);
    Route::resource('deficiencias', DeficienciaController::class);
});

// Rotas para coordenadores
Route::middleware('auth')->group(function () {
    Route::get('/coordenador/dashboard', [CoordenadorController::class, 'dashboard'])->name('coordenador.dashboard');
});

// Rotas para administradores
Route::middleware('auth')->group(function () {
    Route::get('/user/create', [UserController::class, 'create'])->name('admin.user.create');

    // Rota para processar a criação do usuário
    Route::post('/user', [UserController::class, 'store'])->name('admin.user.store');

    // Outras rotas existentes
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('admin.user.edit');
    Route::put('/user/{id}', [UserController::class, 'update'])->name('admin.user.update');

    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('respostas/admin/estatisticas', [RespostaController::class, 'adminEstatisticas'])->name('respostas.admin.estatisticas');
    Route::get('respostas/admin/estatisticas/pdf', [RespostaController::class, 'gerarPdfEstatisticas'])->name('respostas.admin.estatisticas.pdf');

    Route::resource('deficiencias', DeficienciaController::class);

    // Rotas para Características
    Route::resource('caracteristicas', CaracteristicaController::class);

    // Rotas para Recursos
    Route::resource('recursos', RecursoController::class);

    // Rotas para Adaptações
    Route::resource('adaptacoes', AdaptacaoController::class);
    Route::get('/deficiencias/{deficiencia}/caracteristicas', [DeficienciaController::class, 'caracteristicas']);
    Route::get('/adaptacoes/{adaptacao}/pdf', [AdaptacaoController::class, 'gerarPdf'])->name('adaptacoes.gerarPDF');
    // Rotas para escolas, turmas, disciplinas, habilidades, questões, provas, alunos, anos, etc.
    Route::resource('escolas', EscolaController::class);
    Route::resource('turmas', TurmaController::class);
    Route::resource('disciplinas', DisciplinaController::class);
    Route::resource('habilidades', HabilidadeController::class);
    Route::resource('questoes', QuestaoController::class)->parameters([
        'questoes' => 'questao', // Altera o parâmetro {questo} para {questao}
    ]);
    Route::resource('atividades', AtividadeController::class);

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

    // Rota para avaliação
    Route::get('/avaliacao', [AvaliacaoController::class, 'index'])->name('avaliacao.index');
});