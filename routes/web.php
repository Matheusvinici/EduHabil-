<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TutoriaCriterioController;
use App\Services\GabaritoProcessor;

use App\Http\Middleware\CheckEscolaSelecionada;
use App\Http\Middleware\CheckRole; // Importe seu middleware de role
use App\Http\Controllers\TutoriaAvaliacaoController;
use App\Http\Controllers\{
    UserController,
    TutoriaAcompanhamentoController,
    GabaritoController,
    GestorController,
    ProfessorTurmaController,
    NotaAvaliacaoController,
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
    RelatorioController,
    RespostaSimuladoController

};
use Barryvdh\DomPDF\Facade as PDF;

// Rota inicial
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/teste-ocr', function () {
    $imagePath = storage_path('app/public/teste.png'); // Coloque uma imagem com texto aqui
    $processor = new GabaritoProcessor();
    $texto = $processor->processar($imagePath);
    
    return response()->json(['texto_extraido' => $texto]);
});
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

Route::middleware(['auth', 'role:coordenador'])->group(function () {
    // Rotas de seleção (sem verificação de escola)
    Route::get('/coordenador/selecionar-escola', [CoordenadorController::class, 'selecionarEscola'])
         ->name('selecionar.escola');
         
    Route::post('/coordenador/definir-escola', [CoordenadorController::class, 'definirEscola'])
         ->name('definir.escola');
         
    // Demais rotas (com verificação de escola)
    Route::middleware('escola.selecionada')->group(function () {
        Route::get('/coordenador/dashboard', [CoordenadorController::class, 'dashboard'])
             ->name('coordenador.dashboard');
        
    });
});
Route::middleware(['auth', 'role:gestor'])->group(function () {
    // Rotas de seleção de escola (sem verificação de escola selecionada)
    Route::get('/gestor/selecionar-escola', [GestorController::class, 'selecionarEscola'])
    ->name('gestor.selecionar.escola');
        
    Route::post('/gestor/definir-escola', [GestorController::class, 'definirEscola'])
         ->name('gestor.definir-escola');
         
    // Rotas protegidas (requer escola selecionada)
    Route::middleware('escola.selecionada')->group(function () {
        Route::get('/gestor/dashboard', [GestorController::class, 'dashboard'])
             ->name('gestor.dashboard');
        
        // Adicione aqui outras rotas específicas do gestor
        Route::get('/gestor/relatorios', [GestorController::class, 'relatorios'])
             ->name('gestor.relatorios');
             
        Route::get('/gestor/estatisticas', [GestorController::class, 'estatisticas'])
             ->name('gestor.estatisticas');
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
Route::middleware(['auth', 'role:professor'])->group(function () {
    // Seleção de escola
    Route::get('/professor/index', [ProvaController::class, 'index'])->name('provas.professor.index');

    Route::get('/professor/selecionar-escola', [ProfessorController::class, 'selecionarEscola'])
         ->name('professor.selecionar.escola');
         
    Route::post('/professor/definir-escola', [ProfessorController::class, 'definirEscola'])
         ->name('professor.definir.escola');
    
    // Trocar escola
    Route::get('/professor/trocar-escola', [ProfessorController::class, 'trocarEscola'])
         ->name('professor.trocar.escola');
         
    // Rotas que requerem escola selecionada
    Route::middleware('escola.selecionada')->group(function () {
        Route::get('/professor/dashboard', [ProfessorController::class, 'dashboard'])
             ->name('professor.dashboard');
             
        Route::get('/professor/provas', [ProfessorController::class, 'indexProvas'])
             ->name('professor.provas.index');
        
        // Adicione outras rotas protegidas aqui
    });
});
// Rotas para coordenadores
Route::middleware(['auth', 'role:coordenador'])->group(function () {
    // Seleção de escola
    Route::get('/coordenador/selecionar-escola', [CoordenadorController::class, 'selecionarEscola'])
         ->name('coordenador.selecionar.escola');
         
    Route::post('/coordenador/definir-escola', [CoordenadorController::class, 'definirEscola'])
         ->name('coordenador.definir.escola');
         Route::get('/provas/professor/index', [ProvaController::class, 'indexProfessor'])
         ->name('provas.professor.index');

    
    // Trocar escola
    Route::get('/coordenador/trocar-escola', [CoordenadorController::class, 'trocarEscola'])
         ->name('coordenador.trocar.escola');
         
    // Rotas que requerem escola selecionada
    Route::middleware('escola.selecionada')->group(function () {
        Route::get('/coordenador/dashboard', [CoordenadorController::class, 'dashboard'])
             ->name('coordenador.dashboard');
             
        Route::get('/coordenador/estatisticas', [CoordenadorController::class, 'estatisticas'])
             ->name('coordenador.estatisticas');
             
        Route::get('/coordenador/professores', [CoordenadorController::class, 'listarProfessores'])
             ->name('coordenador.professores');
        
        // Adicione outras rotas específicas do coordenador aqui
    });
});
Route::middleware(['auth', 'role:coordenador,gestor,aee'])->group(function() {
    Route::get('/coordenador/estatisticas-escola/{escola}', [ProvaController::class, 'estatisticasEscola'])
         ->name('provas.coordenador.estatisticas-escola');
});

// Remova a outra definição duplicada das rotas de professor    // Rotas de respostas
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
        Route::get('/avaliacao', [ProvaController::class, 'direcionarAvaliacao'])
        ->name('direcionar.avaliacao');
        Route::get('/avaliacao', [ProvaController::class, 'direcionarAvaliacao'])
        ->name('direcionar.avaliacao');
        Route::get('/avaliacao/estatisticas/rede', [ProvaController::class, 'estatisticasRede'])
        ->name('provas.estatisticas-rede');
       
        Route::resource('/', ProvaController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        Route::get('/professor/index', [ProvaController::class, 'indexProfessor'])
            ->name('provas.professor.index');
        Route::get('/{prova}/pdf', [ProvaController::class, 'gerarPDF'])
            ->name('provas.gerarPDF');
    });

// Rotas de atividades
Route::resource('atividades_professores', AtividadeProfessorController::class);

Route::get('/atividades_professores/estatisticas-escola/{escola}', [AtividadeProfessorController::class, 'estatisticasEscola'])
    ->name('atividades_professores.estatisticas-escola');

Route::get('/atividades/estatisticas-rede', [AtividadeProfessorController::class, 'estatisticasRede'])
    ->name('atividades_professores.estatisticas-rede');

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

    Route::get('/', [TurmaController::class, 'index'])->name('turmas.index');
    Route::get('/create', [TurmaController::class, 'create'])->name('turmas.create');
    Route::get('/create-lote', [TurmaController::class, 'createLote'])->name('turmas.create-lote');
    Route::post('/', [TurmaController::class, 'store'])->name('turmas.store');
    Route::post('/store-lote', [TurmaController::class, 'storeLote'])->name('turmas.store-lote');
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
        Route::post('/respostas-simulados/aplicador/{simulado}/clear-session', [RespostaSimuladoController::class, 'clearSession'])
        ->name('respostas_simulados.aplicador.clear_session');
        Route::post('/verificar-resposta', [RespostaSimuladoController::class, 'verificarResposta'])
     ->name('respostas_simulados.verificar');
     Route::post('/respostas_simulados/aplicador/select-escola', [RespostaSimuladoController::class, 'selectEscola'])
    ->name('respostas_simulados.aplicador.select_escola');

Route::get('/respostas_simulados/aplicador/{simulado}/alunos-pendentes', [RespostaSimuladoController::class, 'alunosPendentes'])
    ->name('respostas_simulados.aplicador.alunos_pendentes');

Route::get('/respostas_simulados/aplicador/create/{simulado}/{aluno_id}', [RespostaSimuladoController::class, 'createForAluno'])
    ->name('respostas_simulados.aplicador.create_aluno');
            Route::get('/{simulado}/camera', [GabaritoController::class, 'showCameraForm'])
            ->name('respostas_simulados.aplicador.camera');
            
            Route::get('/aplicador/simulados/{simulado}/alunos', [GabaritoController::class, 'getAlunosPorTurma'])
    ->name('respostas_simulados.aplicador.alunos');

        // Processar seleção do aluno (POST)
        Route::post('/{simulado}/selecionar-aluno', [GabaritoController::class, 'selecionarAluno'])
        ->name('respostas_simulados.aplicador.selecionar-aluno')
        ->whereNumber('simulado');
        
        // Processar imagem do gabarito (POST)
        Route::post('/{simulado}/processar-gabarito', [GabaritoController::class, 'processImage'])
            ->name('respostas_simulados.aplicador.processar-gabarito');

        // Exibir confirmação (GET)
        Route::get('/{simulado}/confirmacao', [GabaritoController::class, 'showConfirmacao'])
            ->name('respostas_simulados.aplicador.confirmacao');

        // Salvar respostas (POST)
        Route::post('/{simulado}/salvar-gabarito', [GabaritoController::class, 'salvarRespostas'])
            ->name('respostas_simulados.aplicador.salvar-gabarito');

             // Retorna alunos por turma (AJAX)
    Route::get('/alunos-por-turma', [GabaritoController::class, 'getAlunosPorTurma'])
    ->name('gabarito.alunos-por-turma');

        // Nova rota para exibir correção (GET)
        Route::get('/{simulado}/correcao', [GabaritoController::class, 'showCorrecao'])
            ->name('respostas_simulados.aplicador.correcao');
   // Rota para finalizar o simulado (POST)
   Route::post('/simulados/{simulado}/finalizar', [RespostaSimuladoController::class, 'finalizarSimulado'])
        ->name('respostas_simulados.aplicador.finalizar');

        Route::get('/aplicador/index', [TurmaController::class, 'indexAplicador'])
        ->name('turmas.aplicador.index');
        Route::get('/respostas-simulados/aplicador/alunos', [RespostaSimuladoController::class, 'getAlunosPorTurma'])
        ->name('respostas_simulados.aplicador.alunos');
        Route::get('{simulado}/{aluno}', [RespostaSimuladoController::class, 'showForAplicador'])
        ->name('respostas_simulados.aplicador.show');
        
   Route::get('{simulado}/{aluno}/edit', [RespostaSimuladoController::class, 'editForAplicador'])
        ->name('respostas_simulados.aplicador.edit');
        
   Route::put('{simulado}/{aluno}', [RespostaSimuladoController::class, 'updateForAplicador'])
        ->name('respostas_simulados.aplicador.update');
        
   Route::delete('{simulado}/{aluno}', [RespostaSimuladoController::class, 'destroyForAplicador'])
        ->name('respostas_simulados.aplicador.destroy');    
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



    Route::get('/respostas-simulados/coordenador/export/{type}', [RespostaSimuladoController::class, 'exportCoordenadorPdf'])
    ->name('respostas_simulados.coordenador.export');

    Route::get('/respostas-simulados/coordenador/turma/{id}', [RespostaSimuladoController::class, 'detalhesTurma'])
    ->name('respostas_simulados.coordenador.turma');



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
    Route::get('/admin/graficos', [RespostaSimuladoController::class, 'graficos'])->name('estatisticas.graficos');


    Route::get('/user/create', [UserController::class, 'create'])->name('admin.user.create');
    Route::get('/provas', [ProvaController::class, 'index'])->name('provas.index');
    Route::get('/provas/estatisticas-rede', [ProvaController::class, 'estatisticasRede'])
->name('provas.admin.estatisticas-rede');

    Route::get('/estatisticas/escola/{id}', [ProvaController::class, 'estatisticasEscola'])
    ->name('provas.estatisticas-escola');

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

    //Tutoria
    // Rotas de Tutoria - Grupo Principal
Route::prefix('tutoria')->group(function () {
    
    // Dashboard e Quadrantes
    Route::get('/dashboard', [TutoriaAvaliacaoController::class, 'dashboard'])
        ->name('tutoria.dashboard');
    Route::get('/quadrante/{quadrante}', [TutoriaAvaliacaoController::class, 'quadrante'])
        ->name('tutoria.quadrante');

    // Rotas de Avaliações
    Route::prefix('avaliacoes')->group(function () {
        Route::get('/', [TutoriaAvaliacaoController::class, 'index'])
            ->name('tutoria.avaliacoes.index');
        Route::get('/create', [TutoriaAvaliacaoController::class, 'create'])
            ->name('tutoria.avaliacoes.create');
        Route::post('/', [TutoriaAvaliacaoController::class, 'store'])
            ->name('tutoria.avaliacoes.store');
        Route::get('/{tutoria_avaliacao}/edit', [TutoriaAvaliacaoController::class, 'edit'])
            ->name('tutoria.avaliacoes.edit');
        Route::put('/{tutoria_avaliacao}', [TutoriaAvaliacaoController::class, 'update'])
            ->name('tutoria.avaliacoes.update');
        Route::delete('/{tutoria_avaliacao}', [TutoriaAvaliacaoController::class, 'destroy'])
            ->name('tutoria.avaliacoes.destroy');
    });

    // Rotas de Acompanhamento
   // Rotas de Tutoria - Grupo Principal
Route::prefix('tutoria')->group(function () {
    
    // Dashboard e Quadrantes
    Route::get('/dashboard', [TutoriaAvaliacaoController::class, 'dashboard'])
        ->name('tutoria.dashboard');
        
    Route::get('/quadrante/{quadrante}', [TutoriaAvaliacaoController::class, 'quadrante'])
        ->name('tutoria.quadrante');

    // Rotas de Avaliações
    Route::prefix('avaliacoes')->group(function () {
        Route::get('/', [TutoriaAvaliacaoController::class, 'index'])
            ->name('tutoria.avaliacoes.index');
            Route::get('/tutoria_avaliacoes/create', [TutoriaAvaliacaoController::class, 'create'])
    ->name('tutoria_avaliacoes.create');
    Route::get('/tutoria_avaliacoes/{tutoria_avaliacao}/edit', [TutoriaAvaliacaoController::class, 'edit'])
    ->name('tutoria_avaliacoes.edit');
    Route::delete('/tutoria_avaliacoes/{tutoria_avaliacao}', [TutoriaAvaliacaoController::class, 'destroy'])
    ->name('tutoria_avaliacoes.destroy');
Route::post('/tutoria_avaliacoes', [TutoriaAvaliacaoController::class, 'store'])
->name('tutoria_avaliacoes.store');
        Route::get('/create', [TutoriaAvaliacaoController::class, 'create'])
            ->name('tutoria.avaliacoes.create');
            Route::get('/tutoria_avaliacoes', [TutoriaAvaliacaoController::class, 'index'])
    ->name('tutoria_avaliacoes.index');

        Route::post('/', [TutoriaAvaliacaoController::class, 'store'])
            ->name('tutoria.avaliacoes.store');
        Route::get('/{tutoria_avaliacao}/edit', [TutoriaAvaliacaoController::class, 'edit'])
            ->name('tutoria.avaliacoes.edit');
            
        Route::put('/{tutoria_avaliacao}', [TutoriaAvaliacaoController::class, 'update'])
            ->name('tutoria.avaliacoes.update');
        Route::delete('/{tutoria_avaliacao}', [TutoriaAvaliacaoController::class, 'destroy'])
            ->name('tutoria.avaliacoes.destroy');
    });

    // Rotas de Acompanhamento
    Route::prefix('acompanhamento')->group(function () {
        Route::get('/', [TutoriaAcompanhamentoController::class, 'index'])
            ->name('tutoria.acompanhamento.index');
        Route::get('/escola/{escola}', [TutoriaAcompanhamentoController::class, 'escola'])
            ->name('tutoria.acompanhamento.escola');
        Route::get('/create/{avaliacao}', [TutoriaAcompanhamentoController::class, 'createFromEvaluation'])
            ->name('tutoria.acompanhamento.createFromEvaluation');
        Route::post('/', [TutoriaAcompanhamentoController::class, 'store'])
            ->name('tutoria.acompanhamento.store');
        Route::get('/{acompanhamento}/edit', [TutoriaAcompanhamentoController::class, 'edit'])
            ->name('tutoria.acompanhamento.edit');
        Route::put('/{acompanhamento}', [TutoriaAcompanhamentoController::class, 'update'])
            ->name('tutoria.acompanhamento.update');
        Route::delete('/{acompanhamento}', [TutoriaAcompanhamentoController::class, 'destroy'])
            ->name('tutoria.acompanhamento.destroy');
    });

    // Rotas de Critérios (Atualizadas)
    Route::prefix('criterios')->group(function () {
        Route::get('/', [TutoriaCriterioController::class, 'index'])
            ->name('tutoria.criterios.index');  // Nome correto da rota
            Route::get('/', [TutoriaAcompanhamentoController::class, 'index'])
    ->name('tutoria.acompanhamento');
  
        Route::get('/create', [TutoriaCriterioController::class, 'create'])
            ->name('tutoria.criterios.create');
            Route::get('/tutoria_criterios/create', [TutoriaCriterioController::class, 'create'])
    ->name('tutoria_criterios.create');
    Route::get('/tutoria_criterios/{tutoria_criterio}/edit', [TutoriaCriterioController::class, 'edit'])
    ->name('tutoria_criterios.edit');
        Route::post('/', [TutoriaCriterioController::class, 'store'])
            ->name('tutoria.criterios.store');
        Route::put('/{tutoria_criterio}', [TutoriaCriterioController::class, 'update'])
            ->name('tutoria.criterios.update');
            Route::delete('/tutoria_criterios/{tutoria_criterio}', [TutoriaCriterioController::class, 'destroy'])
            ->name('tutoria_criterios.destroy');
    });
});
    // Rotas de Critérios (se necessário)
    Route::prefix('criterios')->group(function () {
        Route::get('/', [TutoriaCriterioController::class, 'index'])
            ->name('tutoria.criterios.index');
            Route::get('/tutoria_criterios', [TutoriaCriterioController::class, 'index'])
    ->name('tutoria_criterios.index');

        Route::get('/create', [TutoriaCriterioController::class, 'create'])
            ->name('tutoria.criterios.create');
        Route::post('/', [TutoriaCriterioController::class, 'store'])
            ->name('tutoria.criterios.store');
        Route::get('/{tutoria_criterio}/edit', [TutoriaCriterioController::class, 'edit'])
            ->name('tutoria.criterios.edit');
        Route::put('/{tutoria_criterio}', [TutoriaCriterioController::class, 'update'])
            ->name('tutoria.criterios.update');
        Route::delete('/{tutoria_criterio}', [TutoriaCriterioController::class, 'destroy'])
            ->name('tutoria.criterios.destroy');
    });
});

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

    Route::middleware(['auth'])->group(function () {
        // Rotas de usuários
         // Cadastro em lote
         Route::get('users/create-lote', [UserController::class, 'createLote'])->name('users.create-lote');
         Route::post('users/store-lote', [UserController::class, 'storeLote'])->name('users.store-lote');
           
        Route::get('/users/pdf', [UserController::class, 'generatePdf'])->name('users.pdf');
        Route::resource('users', UserController::class);
    });
Route::prefix('relatorios')->group(function() {
    Route::get('/rede-municipal', [RelatorioController::class, 'estatisticasRede'])->name('relatorios.rede-municipal');
    Route::get('/relatorios/escolas-quadrante', [RelatorioController::class, 'escolasQuadrante'])
    ->name('relatorios.escolas-quadrante');

    Route::get('/rede-municipal/pdf', [RelatorioController::class, 'exportarPdf'])
    ->name('relatorios.rede-municipal.pdf');
    Route::post('/relatorios/exportar-excel', [RelatorioController::class, 'exportarExcel'])
    ->name('relatorios.exportar-excel');
    Route::get('/relatorios/estatisticas-escola', [RelatorioController::class, 'estatisticasEscola'])->name('relatorios.estatisticas-escola');
    Route::get('/relatorios/exportar-escola-pdf', [RelatorioController::class, 'exportarEscolaPdf'])->name('relatorios.exportar-escola-pdf');
    Route::get('/relatorios/exportar-escola-excel', [RelatorioController::class, 'exportarEscolaExcel'])->name('relatorios.exportar-escola-excel');
    Route::get('/relatorios/por-ano', [RelatorioController::class, 'estatisticasAnoEnsino'])->name('relatorios.estatisticas-ano');
    Route::get('/relatorios/exportar-ano-excel', [RelatorioController::class, 'exportarAnoExcel'])->name('relatorios.exportar-ano-excel');
    Route::get('/relatorios/exportar-ano-pdf', [RelatorioController::class, 'exportarAnoPdf'])->name('relatorios.exportar-ano-pdf');
    Route::get('questoes', [RelatorioController::class, 'estatisticasQuestoes'])->name('relatorios.estatisticas-questoes');
    Route::get('questoes/pdf', [RelatorioController::class, 'exportPdf'])->name('relatorios.exportar-questoes-pdf');
    Route::get('questoes/excel', [RelatorioController::class, 'exportExcel'])->name('relatorios.exportar-questoes-excel');
    Route::get('/relatorios/habilidades', [RelatorioController::class, 'estatisticasHabilidade'])->name('relatorios.habilidades');
    Route::get('/relatorios/habilidades/pdf', [RelatorioController::class, 'pdfHabilidade'])->name('relatorios.habilidades.pdf');
    Route::get('/relatorios/habilidades/excel', [RelatorioController::class, 'excelHabilidade'])->name('relatorios.habilidades.excel');
    Route::get('raca', [RelatorioController::class, 'estatisticasRaca'])->name('relatorios.raca');
    Route::get('raca/pdf', [RelatorioController::class, 'pdfRaca'])->name('relatorios.raca.pdf');
    Route::get('raca/excel', [RelatorioController::class, 'excelRaca'])->name('relatorios.raca.excel');
    Route::get('/relatorios/deficiencias', [RelatorioController::class, 'estatisticasDeficiencia'])->name('relatorios.deficiencias');
        Route::get('/relatorios/deficiencias/excel', [RelatorioController::class, 'exportDeficienciaExcel'])->name('relatorios.deficiencias.excel');
    Route::get('/relatorios/deficiencias/pdf', [RelatorioController::class, 'exportDeficienciaPdf'])->name('relatorios.deficiencias.pdf');

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