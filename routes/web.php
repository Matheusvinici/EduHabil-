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
    AvaliacaoController,
    SimuladoController,
    PerguntaController,
    RespostaSimuladoController

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
    Route::get('respostas/aluno/index', [RespostaController::class, 'alunoIndex'])->name('respostas.aluno.index');

    Route::resource('respostas_simulados', RespostaSimuladoController::class);
    Route::get('/respostas_simulados/aluno/index', [RespostaSimuladoController::class, 'alunoIndex'])->name('respostas_simulados.aluno.index');
    Route::get('/respostas_simulados/create/{simulado}', [RespostaSimuladoController::class, 'create'])->name('respostas_simulados.create');
    Route::post('/respostas_simulados/store/{simulado}', [RespostaSimuladoController::class, 'store'])->name('respostas_simulados.store');    Route::get('/respostas_simulados/show/{simulado}', [RespostaSimuladoController::class, 'show'])->name('respostas_simulados.show');
});

// Rotas para professores
Route::middleware('auth')->group(function () {
    Route::get('/professor/dashboard', [ProfessorController::class, 'dashboard'])->name('professor.dashboard');
    Route::get('respostas/professor/estatisticas', [RespostaController::class, 'professorEstatisticas'])->name('respostas.professor.estatisticas');
    Route::get('respostas/professor/index', [RespostaController::class, 'professorEstatisticas'])->name('respostas.professor.index');

    Route::get('respostas/professor/estatisticas/pdf', [RespostaController::class, 'gerarPdfEstatisticas'])->name('respostas.professor.estatisticas.pdf');
    Route::get('/provas/professor/index', [ProvaController::class, 'indexProfessor'])->name('provas.professor.index');
    Route::resource('atividades_professores', AtividadeProfessorController::class);
    Route::get('atividades_professores/{id}/download', [AtividadeProfessorController::class, 'downloadPdf'])->name('atividades_professores.download');
    Route::get('/turmas/professor/index', [TurmaController::class, 'indexProfessor'])->name('turmas.professor.index');
    Route::get('/respostas_simulados/professor/index', [RespostaSimuladoController::class, 'estatisticasProfessor'])->name('respostas_simulados.professor.index');

    Route::get('/respostas_simulados/professor/{simulado}/{aluno}', [RespostaSimuladoController::class, 'showProfessor'])->name('respostas_simulados.professor.show');
    Route::get('/respostas_simulados/professor/estatisticas', [RespostaSimuladoController::class, 'estatisticasProfessor'])->name('respostas_simulados.professor.estatisticas');


    // Rotas para provas
    Route::resource('provas', ProvaController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::get('provas/{prova}/pdf', [ProvaController::class, 'gerarPDF'])->name('provas.gerarPDF');

    // Rotas para respostas
    Route::get('respostas/professor/index', [RespostaController::class, 'professorIndex'])->name('respostas.professor.index');
    Route::get('/respostas/professor/{prova}/{aluno}', [RespostaController::class, 'professorShow'])
    ->name('respostas.professor.show');
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
    Route::get('/turmas/aee/index', [TurmaController::class, 'indexAEE'])->name('turmas.aee.index');

    Route::get('/provas/aee', [ProvaController::class, 'indexAEE'])->name('provas.aee');
});

// Rotas para diretoria inclusiva
Route::middleware('auth')->group(function () {
    Route::get('/inclusiva/dashboard', [InclusivaController::class, 'index'])->name('inclusiva.dashboard');
    Route::resource('caracteristicas', CaracteristicaController::class);
    Route::get('/provas/inclusiva', [ProvaController::class, 'indexInclusiva'])->name('provas.inclusiva');
    Route::resource('deficiencias', DeficienciaController::class);
});

// Rotas para coordenadores
Route::middleware('auth')->group(function () {
    Route::get('/coordenador/dashboard', [CoordenadorController::class, 'dashboard'])->name('coordenador.dashboard');
    Route::get('/provas/coordenador/index', [ProvaController::class, 'indexCoordenador'])->name('provas.coordenador.index');
    Route::get('/turmas/coordenador/index', [TurmaController::class, 'indexCoordenador'])->name('turmas.coordenador.index');
    Route::get('/respostas/coordenador/estatisticas', [RespostaController::class, 'indexCoordenador'])->name('respostas.coordenador.estatisticas');
    Route::get('/respostas_simulados/coordenador/index', [RespostaSimuladoController::class, 'indexCoordenador'])->name('respostas_simulados.coordenador.index');

    // Rota para gerar PDF das estatísticas do coordenador
    Route::get('/respostas/coordenador/pdf', [RespostaController::class, 'pdfCoordenadorEstatisticas'])->name('respostas.coordenador.index.pdf');
});


// Rotas para administradores
Route::middleware('auth')->group(function () {
    Route::get('/user/create', [UserController::class, 'create'])->name('admin.user.create');
    Route::get('/provas', [ProvaController::class, 'index'])->name('provas.index');
    Route::get('/provas/admin/index', [ProvaController::class, 'indexAdmin'])->name('provas.admin.index');
    Route::get('/turmas/admin/index', [TurmaController::class, 'indexAdmin'])->name('turmas.admin.index');
    Route::get('/provas/admin/pdf/escolas/sem/provas', [ProvaController::class, 'pdfEscolasSemProvas'])->name('provas.admin.pdf.escolas.sem.provas');
    Route::get('/provas/admin/pdf/escolas/com/provas', [ProvaController::class, 'pdfEscolasComProvas'])->name('provas.admin.pdf.escolas.com.provas');
    Route::get('/respostas_simulados/admin/estatisticas', [RespostaSimuladoController::class, 'estatisticasAdmin'])->name('respostas_simulados.admin.estatisticas');
    // Rotas para simulados
        Route::resource('simulados', SimuladoController::class);
        Route::get('simulados/{simulado}/gerar-pdf', [SimuladoController::class, 'gerarPdf'])->name('simulados.gerarPdf');
        Route::get('/simulados/{simulado}/gerar-pdf-braille', [SimuladoController::class, 'gerarPdfBraille'])->name('simulados.gerar-pdf-braille');
        Route::get('/simulados/{simulado}/baixa-visao', [SimuladoController::class, 'gerarPdfBaixaVisao'])
        ->name('simulados.baixa-visao');

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
    Route::delete('/adaptacoes/{adaptacao}', [AdaptacaoController::class, 'destroy'])
    ->name('adaptacoes.destroy');
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
    Route::resource('perguntas', PerguntaController::class);
    Route::post('/perguntas/save-content', [PerguntaController::class, 'saveContent'])->name('perguntas.saveContent');

    Route::resource('users', UserController::class);
    Route::get('users/pdf/{role?}/{escola_id?}', [UserController::class, 'generatePdf'])->name('users.pdf');


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
