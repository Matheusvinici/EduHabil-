<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    ProfessorTurmaController,
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

// Rotas para vinculação professor-turma
Route::prefix('auth')->group(function () {
    // Seleção inicial de escola
    Route::get('/professor-turma/select-escola', [ProfessorTurmaController::class, 'selectEscola'])
         ->name('professor-turma.select-escola');
         
    // Formulário de vinculação
    Route::get('/professor-turma/create', [ProfessorTurmaController::class, 'create'])
         ->name('professor-turma.create');
         
    // Processar vinculação
    Route::post('/professor-turma', [ProfessorTurmaController::class, 'store'])
         ->name('professor-turma.store');
         
    // Listagem de vinculações
    Route::get('/professor-turma', [ProfessorTurmaController::class, 'index'])
         ->name('professor-turma.index');

         Route::get('/professor-turma', [ProfessorTurmaController::class, 'index'])->name('professor-turma.index');
            // Editar
            Route::get('/professor-turma/{professor_id}/{turma_id}/edit', [ProfessorTurmaController::class, 'edit'])->name('professor-turma.edit');

            // Atualizar (corrigido)
            Route::put('/professor-turma/{professor_id}/{turma_id}', [ProfessorTurmaController::class, 'update'])->name('professor-turma.update');
         Route::delete('/professor-turma/{professor_id}/{turma_id}', [ProfessorTurmaController::class, 'destroy'])->name('professor-turma.destroy');
         Route::get('/turmas-por-escola/{escolaId}', function ($escolaId) {
            $turmas = \App\Models\Turma::where('escola_id', $escolaId)->orderBy('nome_turma')->get(['id', 'nome_turma']);
            return response()->json($turmas);
        });
        
});


// Rotas para alunos
Route::middleware('auth')->group(function () {
    Route::get('/aluno/dashboard', [AlunoController::class, 'dashboard'])->name('aluno.dashboard');
    Route::resource('respostas', RespostaController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('respostas/aluno/index', [RespostaController::class, 'alunoIndex'])->name('respostas.aluno.index');

    Route::resource('respostas_simulados', RespostaSimuladoController::class);
    Route::get('/respostas_simulados/aluno/index', [RespostaSimuladoController::class, 'alunoIndex'])->name('respostas_simulados.aluno.index');
    Route::get('/respostas_simulados/create/{simulado}', [RespostaSimuladoController::class, 'create'])->name('respostas_simulados.create');
    Route::post('/respostas_simulados/store/{simulado}', [RespostaSimuladoController::class, 'store'])->name('respostas_simulados.store');  
      Route::get('/respostas_simulados/show/{simulado}', [RespostaSimuladoController::class, 'show'])->name('respostas_simulados.show');

    
});

              // Rotas para professores
    Route::get('/professor/dashboard', [ProfessorController::class, 'dashboard'])->name('professor.dashboard');



    // Rotas de respostas
    Route::prefix('respostas')->group(function () {
        Route::get('/professor/estatisticas', [RespostaController::class, 'professorEstatisticas'])
            ->name('respostas.professor.estatisticas');
        Route::get('/professor/index', [RespostaController::class, 'professorIndex'])
            ->name('respostas.professor.index');
        Route::get('/professor/estatisticas/pdf', [RespostaController::class, 'gerarPdfEstatisticas'])
            ->name('respostas.professor.estatisticas.pdf');
        Route::get('/professor/{prova}/{aluno}', [RespostaController::class, 'professorShow'])
            ->name('respostas.professor.show');
        Route::get('/{prova}/create', [RespostaController::class, 'create'])
            ->name('respostas.create');
        Route::post('/{prova}', [RespostaController::class, 'store'])
            ->name('respostas.store');
    });

    Route::prefix('respostas_simulados')->group(function () {
        // Rota principal (mantida como estava)
        Route::get('respostas_simulados/professor', [RespostaSimuladoController::class, 'estatisticasProfessor'])
            ->name('respostas_simulados.professor.index');
        
        // Rota de visualização específica
        Route::get('/professor/{simulado}/{aluno}', [RespostaSimuladoController::class, 'showProfessor'])
            ->name('respostas_simulados.professor.show');
        
                // Rotas de exportação (também com caminho duplicado)
            Route::get('respostas_simulados/professor/exportar/pdf', [RespostaSimuladoController::class, 'exportarPdf'])
            ->name('respostas_simulados.professor.exportar.pdf');

        Route::get('respostas_simulados/professor/exportar/excel', [RespostaSimuladoController::class, 'exportarExcel'])
            ->name('respostas_simulados.professor.exportar.excel');
        });

    
    // Rotas de provas
    Route::prefix('provas')->group(function () {
        Route::resource('/', ProvaController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        Route::get('/professor/index', [ProvaController::class, 'indexProfessor'])
            ->name('provas.professor.index');
        Route::get('/{prova}/pdf', [ProvaController::class, 'gerarPDF'])
            ->name('provas.gerarPDF');
    });

   

    // Rotas de atividades
    Route::resource('atividades_professores', AtividadeProfessorController::class);
    Route::get('atividades_professores/{id}/download', [AtividadeProfessorController::class, 'downloadPdf'])
        ->name('atividades_professores.download');

    // Rotas de turmas (ATUALIZADO)
    Route::prefix('turmas')->group(function () {
        // Rotas específicas primeiro
        Route::get('/professor/index', [TurmaController::class, 'indexProfessor'])
            ->name('turmas.professor.index');
            Route::get('/professor/show', [TurmaController::class, 'show'])
            ->name('turmas.professor.show');
                    // routes/web.php
            Route::get('/turmas/{turma}/add-alunos', [TurmaController::class, 'addAlunosForm'])
            ->name('turmas.add-alunos-form');

            Route::post('/turmas/{turma}/add-alunos', [TurmaController::class, 'addAlunos'])
            ->name('turmas.add-alunos');
            Route::put('/turmas/{turma}/update-nome', [TurmaController::class, 'updateTurma'])
            ->name('turmas.update-nome');
            Route::get('/turmas/{id}/gerar-pdf', [TurmaController::class, 'gerarPdf'])
            ->name('turmas.gerar-pdf');        

            Route::get('/{turma}/alunos/{aluno}/edit', [TurmaController::class, 'edit'])
            ->name('turmas.alunos.edit');

        // Rota para gerar códigos adicionais
        Route::post('/{turma}/gerar-codigos-adicionais', [TurmaController::class, 'gerarCodigosAdicionais'])
            ->name('turmas.gerar-codigos-adicionais');

        // Resource principal (com exceções se necessário)
        Route::resource('/', TurmaController::class)->names([
            'index' => 'turmas.index',
            'create' => 'turmas.create',
            'store' => 'turmas.store',
            'show' => 'turmas.show',
            'edit' => 'turmas.edit',
            'update' => 'turmas.update',
            'destroy' => 'turmas.destroy'
        ])->parameters(['' => 'turma']);
    });


            // Rotas para aplicador
Route::prefix('aplicador')->group(function() {
    Route::get('/simulados', [RespostaSimuladoController::class, 'indexForAplicador'])
        ->name('respostas_simulados.aplicador.index');
    
        Route::get('/aplicador/respostas-simulados/detalhes/{id}', [RespostaSimuladoController::class, 'detalhesForAplicador'])
    ->name('respostas_simulados.aplicador.detalhes');

    Route::get('/simulados/novo', [RespostaSimuladoController::class, 'selectForAplicador'])
        ->name('respostas_simulados.aplicador.select');

        Route::get('/simulados/{simulado}/aplicar', [RespostaSimuladoController::class, 'createForAplicador'])
        ->name('respostas_simulados.aplicador.create');
        // Processar seleção do aluno
    Route::post('/simulados/{simulado}/selecionar', [RespostaSimuladoController::class, 'selecionarAluno'])
         ->name('respostas_simulados.aplicador.selecionar');
   // Rota para processar a seleção (POST)
   Route::post('/simulados/{simulado}/aplicar', [RespostaSimuladoController::class, 'storeForAplicador'])
        ->name('respostas_simulados.aplicador.store');

        Route::post('/verificar-resposta', [RespostaSimuladoController::class, 'verificarResposta'])
     ->name('respostas_simulados.verificar');
     Route::post('/respostas_simulados/aplicador/select-escola', [RespostaSimuladoController::class, 'selectEscola'])
    ->name('respostas_simulados.aplicador.select_escola');

Route::get('/respostas_simulados/aplicador/{simulado}/alunos-pendentes', [RespostaSimuladoController::class, 'alunosPendentes'])
    ->name('respostas_simulados.aplicador.alunos_pendentes');

Route::get('/respostas_simulados/aplicador/create/{simulado}/{aluno_id}', [RespostaSimuladoController::class, 'createForAluno'])
    ->name('respostas_simulados.aplicador.create_aluno');
        
   // Rota para finalizar o simulado (POST)
   Route::post('/simulados/{simulado}/finalizar', [RespostaSimuladoController::class, 'finalizarSimulado'])
        ->name('respostas_simulados.aplicador.finalizar');

        Route::get('/aplicador/index', [TurmaController::class, 'indexAplicador'])
        ->name('turmas.aplicador.index');
        Route::get('/get-alunos/{turma}', [RespostaSimuladoController::class, 'getAlunosPorTurma'])
        ->name('respostas_simulados.aplicador.alunos');


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

    Route::get('/inclusiva/estatisticas', [RespostaSimuladoController::class, 'estatisticasInclusiva'])
    ->name('respostas_simulados.inclusiva.estatisticas');

});

// Rotas para coordenadores
Route::middleware('auth')->group(function () {

    Route::get('/estatisticas', [RespostaSimuladoController::class, 'indexCoordenador'])
    ->name('respostas_simulados.coordenador.index');

Route::get('/estatisticas/detalhes-turma/{turma_id}', [RespostaSimuladoController::class, 'detalhesTurma'])
    ->name('respostas_simulados.coordenador.detalhes-turma');

Route::get('/estatisticas/export/pdf', [RespostaSimuladoController::class, 'exportCoordenadorPdf'])
    ->name('respostas_simulados.coordenador.export.pdf');

Route::get('/estatisticas/export/excel', [RespostaSimuladoController::class, 'exportCoordenadorExcel'])
    ->name('respostas_simulados.coordenador.export.excel');

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
    Route::get('/admin/estatisticas/export/pdf', [RespostaSimuladoController::class, 'exportAdminPdf'])
    ->name('respostas_simulados.admin.export.pdf');
Route::get('/admin/estatisticas/export/excel', [RespostaSimuladoController::class, 'exportAdminExcel'])
    ->name('respostas_simulados.admin.export.excel');
    Route::get('/respostas-simulados/admin/detalhes-escola/{escola_id}', [RespostaSimuladoController::class, 'detalhesEscola'])
    ->name('respostas_simulados.admin.detalhes-escola');

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
        Route::get('simulados/{simulado}/gerar-pdf-Escola', [SimuladoController::class, 'gerarPdfEscolas'])->name('simulados.gerarPdfEscolas');


        Route::get('/simulados/{simulado}/gerar-pdf-braille', [SimuladoController::class, 'gerarPdfBraille'])->name('simulados.gerar-pdf-braille');

        Route::get('/simulados/{simulado}/baixa-visao', [SimuladoController::class, 'gerarPdfBaixaVisao'])
        ->name('simulados.baixa-visao');
        Route::get('/simulados/{simulado}/baixa-visao-escola', [SimuladoController::class, 'gerarPdfBaixaVisaoEscola'])
        ->name('simulados.baixa-visao-escola');



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
    Route::resource('disciplinas', DisciplinaController::class);
    Route::resource('habilidades', HabilidadeController::class);
    Route::resource('questoes', QuestaoController::class)->parameters([
        'questoes' => 'questao', // Altera o parâmetro {questo} para {questao}
    ]);
    Route::resource('atividades', AtividadeController::class);

    Route::resource('provas', ProvaController::class);
    Route::delete('/{prova}', [ProvaController::class, 'destroy'])
    ->name('provas.destroy');

    Route::resource('alunos', AlunoController::class);
    Route::resource('anos', AnoEscolarController::class);
    Route::resource('perguntas', PerguntaController::class);
    Route::post('/perguntas/save-content', [PerguntaController::class, 'saveContent'])->name('perguntas.saveContent');

    // Rotas para Users (admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/users/pdf', [UserController::class, 'generatePdf'])->name('users.pdf');
    Route::resource('users', UserController::class);
});


    // Geração de PDF
    Route::get('provas/{prova}/pdf', [ProvaController::class, 'gerarPDF'])->name('provas.gerarPDF');
    Route::get('provas/{prova}/pdf-com-gabarito', [ProvaController::class, 'gerarPDFGabarito'])->name('provas.gerarPDFGabarito');

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
