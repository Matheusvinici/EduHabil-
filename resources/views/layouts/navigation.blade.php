<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <li class="nav-item">
            <a href="{{ route('home') }}" class="nav-link text-white">
                <i class="nav-icon fas fa-tachometer-alt text-white"></i>
                <p>Início</p>
            </a>
        </li>
        <li class="nav-item">
                        <a href="{{ route('respostas_simulados.index') }}" class="nav-link text-white">
                <i class="far fa-circle nav-icon text-white"></i>
                <p>Avaliação Simulado</p>
            </a>
                        </li>
       @php
                        $showSimulados = Auth::check() && in_array(Auth::user()->role, ['inclusiva', 'admin']);
                        $canSeePerguntas = Auth::check() && Auth::user()->role === 'admin';
                @endphp

                @if($showSimulados)
                        <li class="nav-item has-treeview">
                                <a href="#" class="nav-link text-white">
                                        <i class="nav-icon fas fa-check-circle text-white"></i>
                                        <p>Simulados<i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                                <a href="{{ route('simulados.index') }}" class="nav-link text-white">
                                                        <i class="far fa-circle nav-icon text-white"></i>
                                                        <p>Simulados</p>
                                                </a>
                                        </li>
                                        @if($canSeePerguntas)
                                        <li class="nav-item">
                                                <a href="{{ route('perguntas.index') }}" class="nav-link text-white">
                                                        <i class="far fa-circle nav-icon text-white"></i>
                                                        <p>Perguntas</p>
                                                </a>
                                        </li>
                                        @endif
                                </ul>
                        </li>
                @endif

        @php
                $showAvaliacoes = false;
                $showGerarProvas = Auth::check() && in_array(Auth::user()->role, ['professor', 'coordenador', 'gestor', 'aee']);
                $showAvaliacao = Auth::check() && in_array(Auth::user()->role, ['admin', 'inclusiva', 'aee', 'aplicador']);
                $showBancoQuestoes = Auth::check() && in_array(Auth::user()->role, ['admin', 'inclusiva', 'aplicador']);
                $showAvaliacoes = $showGerarProvas || $showAvaliacao || $showBancoQuestoes;
        @endphp
        @if($showAvaliacoes)
                <li class="nav-item has-treeview">
                        <a href="#" class="nav-link text-white">
                                <i class="nav-icon fas fa-file-alt text-white"></i>
                                <p>Avaliações e Atividades<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                                @if($showGerarProvas)
                                        <li class="nav-item">
                                                <a href="{{ route('provas.index') }}" class="nav-link text-white">
                                                        <i class="far fa-circle nav-icon text-white"></i>
                                                        <p>Gerar Atividades</p>
                                                </a>
                                        </li>
                                @endif
                                @if($showAvaliacao)
                                <li class="nav-item">
                                        <a href="{{ route('direcionar.avaliacao') }}" class="nav-link text-white">
                                        <i class="far fa-circle nav-icon text-white"></i>
                                        <p>Avaliação</p>
                                        </a>
                                </li>
                                @endif
                                @if($showBancoQuestoes)
                                        <li class="nav-item">
                                                <a href="{{ route('questoes.index') }}" class="nav-link text-white">
                                                        <i class="far fa-circle nav-icon text-white"></i>
                                                        <p>Banco de Questões</p>
                                                </a>
                                        </li>
                                @endif
                        </ul>
                </li>
        @endif

        @php
                $showPlanejamento = false;
                $showAtividadesEducativas = Auth::check() && in_array(Auth::user()->role, ['professor', 'coordenador', 'gestor', 'admin', 'aplicador']);
                $showMinhasTurmas = Auth::check() && in_array(Auth::user()->role, ['admin', 'inclusiva', 'aplicador']);
                $showCadastrarAtividades = Auth::check() && in_array(Auth::user()->role, ['admin','aplicador']);
                $showPlanejamento = $showAtividadesEducativas || $showMinhasTurmas || $showCadastrarAtividades;
        @endphp
        @if($showPlanejamento)
                <li class="nav-item has-treeview">
                        <a href="#" class="nav-link text-white">
                                <i class="nav-icon fas fa-tasks text-white"></i>
                                <p>Sequência Didática<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                                @if($showAtividadesEducativas)
                                        <li class="nav-item">
                                                <a href="{{ route('atividades_professores.index') }}" class="nav-link text-white">
                                                        <i class="far fa-circle nav-icon text-white"></i>
                                                        <p>Gerar Sequência Didática</p>
                                                </a>
                                        </li>
                                @endif
                                @if($showMinhasTurmas)
                                        <li class="nav-item">
                                                <a href="{{ route('turmas.index') }}" class="nav-link text-white">
                                                        <i class="far fa-circle nav-icon text-white"></i>
                                                        <p>Minhas Turmas</p>
                                                </a>
                                        </li>
                                @endif
                                @if($showCadastrarAtividades)
                                        <li class="nav-item">
                                                <a href="{{ route('atividades.index') }}" class="nav-link text-white">
                                                        <i class="far fa-circle nav-icon text-white"></i>
                                                        <p>Cadastrar Sequência Didática</p>
                                                </a>
                                        </li>
                                @endif
                        </ul>
                </li>
        @endif

        @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'aplicador']))
                <li class="nav-item has-treeview">
                        <a href="#" class="nav-link text-white">
                                <i class="nav-icon fas fa-book text-white"></i>
                                <p>Gestão Pedagógica<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                                <li class="nav-item">
                                        <a href="{{ route('disciplinas.index') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Disciplinas</p>
                                        </a>
                                </li>
                                <li class="nav-item">
                                        <a href="{{ route('habilidades.index') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Habilidades e Competências</p>
                                        </a>
                                </li>
                                <li class="nav-item">
                                        <a href="{{ route('anos.index') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Anos Letivos</p>
                                        </a>
                                </li>
                        </ul>
                </li>
        @endif

        @php
                $showInclusao = false;
                $showAtividadesAdaptadas = Auth::check() && in_array(Auth::user()->role, ['aee', 'coordenador', 'gestor', 'inclusiva', 'admin', 'aplicador']);
                $showPerfisAprendizagem = Auth::check() && in_array(Auth::user()->role, ['inclusiva', 'admin', 'aplicador']);
                $showTiposDeficiencias = Auth::check() && in_array(Auth::user()->role, ['inclusiva', 'admin', 'aplicador']);
                $showCadastrarRecursos = Auth::check() && in_array(Auth::user()->role, ['inclusiva', 'admin','aplicador']);
                $showInclusao = $showAtividadesAdaptadas || $showPerfisAprendizagem || $showTiposDeficiencias || $showCadastrarRecursos;
        @endphp
        @if($showInclusao)
                <li class="nav-item has-treeview">
                        <a href="#" class="nav-link text-white">
                                <i class="nav-icon fas fa-universal-access text-white"></i>
                                <p>Inclusão e Acessibilidade<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                                @if($showAtividadesAdaptadas)
                                        <li class="nav-item">
                                                <a href="{{ route('adaptacoes.index') }}" class="nav-link text-white">
                                                        <i class="far fa-circle nav-icon text-white"></i>
                                                        <p>Atividades Adaptadas</p>
                                                </a>
                                        </li>
                                @endif
                                @if($showPerfisAprendizagem)
                                        <li class="nav-item">
                                                <a href="{{ route('caracteristicas.index') }}" class="nav-link text-white">
                                                        <i class="far fa-circle nav-icon text-white"></i>
                                                        <p>Perfis de Aprendizagem</p>
                                                </a>
                                        </li>
                                @endif
                                @if($showTiposDeficiencias)
                                        <li class="nav-item">
                                                <a href="{{ route('deficiencias.index') }}" class="nav-link text-white">
                                                        <i class="far fa-circle nav-icon text-white"></i>
                                                        <p>Tipos de Deficiências</p>
                                                </a>
                                        </li>
                                @endif
                                @if($showCadastrarRecursos)
                                        <li class="nav-item">
                                                <a href="{{ route('recursos.index') }}" class="nav-link text-white">
                                                        <i class="far fa-circle nav-icon text-white"></i>
                                                        <p>Cadastrar Recursos</p>
                                                </a>
                                        </li>
                                @endif
                        </ul>
                </li>
        @endif
        @php
                $showVinculacoes = Auth::check() && in_array(Auth::user()->role, ['aplicador', 'admin']);
        @endphp
        @if($showVinculacoes)
                <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('professor-turma.index') }}">
                                <i class="fas fa-link text-white"></i> Vincular Professor-Turma
                        </a>
                </li>
        @endif


        @if(Auth::check() && Auth::user()->role === 'admin')
                <li class="nav-item has-treeview">
                        <a href="#" class="nav-link text-white">
                                <i class="nav-icon fas fa-cogs text-white"></i>
                                <p>Administração<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                                <li class="nav-item">
                                        <a href="{{ route('escolas.index') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Gerenciar Escolas</p>
                                        </a>
                                </li>
                                <li class="nav-item">
                                        <a href="{{ route('users.index') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Gerenciar Usuários</p>
                                        </a>
                                </li>
                        </ul>
                </li>
        @endif

          @if(Auth::check() && Auth::user()->role === 'admin')
          <li class="nav-item has-treeview menu-open">
    <a href="#" class="nav-link bg-primary-light">
        <i class="nav-icon fas fa-chalkboard-teacher text-white"></i>
        <p class="text-white font-weight-bold">
            Tutoria
            <i class="right fas fa-angle-down"></i>
        </p>
    </a>
    <ul class="nav nav-treeview" style="background-color: rgba(30, 136, 229, 0.1);">
        <!-- Dashboard -->
        <li class="nav-item">
            <a href="{{ route('tutoria.dashboard') }}" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt text-primary"></i>
                <p class="text-dark">
                    Dashboard
                    <span class="right badge badge-primary">Novo</span>
                </p>
            </a>
        </li>
        
        <!-- Critérios -->
        <li class="nav-item">
            <a href="{{ route('tutoria_criterios.index') }}" class="nav-link">
                <i class="nav-icon fas fa-list-check text-info"></i>
                <p class="text-dark">Critérios de Avaliação</p>
            </a>
        </li>
        
        <!-- Avaliação -->
        <li class="nav-item">
            <a href="{{ route('tutoria_avaliacoes.index') }}" class="nav-link">
                <i class="nav-icon fas fa-clipboard-list text-success"></i>
                <p class="text-dark">
                    Avaliações
                    <span class="right badge badge-success">+</span>
                </p>
            </a>
        </li>
        
        <!-- Acompanhamento -->
        <li class="nav-item">
            <a href="{{ route('tutoria.acompanhamento.index') }}" class="nav-link">
                <i class="nav-icon fas fa-hands-helping text-warning"></i>
                <p class="text-dark">
                    Acompanhamento
                    <span class="right badge badge-danger">!</span>
                </p>
            </a>
        </li>
        
        <!-- Quadrantes -->
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-chart-pie text-secondary"></i>
                <p class="text-dark">
                    Quadrantes
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview" style="margin-left: 15px;">
                <li class="nav-item">
                    <a href="{{ route('tutoria.quadrante', ['quadrante' => 'vermelho']) }}" class="nav-link">
                        <i class="fas fa-square text-danger nav-icon"></i>
                        <p>Prioridade Máxima</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tutoria.quadrante', ['quadrante' => 'amarelo']) }}" class="nav-link">
                        <i class="fas fa-square text-warning nav-icon"></i>
                        <p>Prioridade Média</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tutoria.quadrante', ['quadrante' => 'verde']) }}" class="nav-link">
                        <i class="fas fa-square text-success nav-icon"></i>
                        <p>Bom Desempenho</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tutoria.quadrante', ['quadrante' => 'azul']) }}" class="nav-link">
                        <i class="fas fa-square text-primary nav-icon"></i>
                        <p>Excelência</p>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</li>
        @endif

          @if(Auth::check() && Auth::user()->role === 'admin')
                <li class="nav-item has-treeview">
                        <a href="#" class="nav-link text-white">
                                <i class="nav-icon fas fa-chart-bar text-white"></i>
                                <p>Relatórios Simulados<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                                <li class="nav-item">
                                        <a href="{{ route('relatorios.rede-municipal') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Estatisticas da Rede Municipal</p>
                                        </a>
                                </li>
                                <li class="nav-item">
                                        <a href="{{ route('relatorios.estatisticas-ano') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Estatísticas por Ano</p>
                                        </a>
                                </li>
                                <li class="nav-item">
                                        <a href="{{ route('relatorios.estatisticas-escola') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Estatísticas por Escola</p>
                                        </a>
                                </li>
                                <li class="nav-item">
                                        <a href="{{ route('relatorios.estatisticas-questoes') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Estatísticas por Questão</p>
                                        </a>
                                </li>
                                <li class="nav-item">
                                        <a href="{{ route('relatorios.habilidades') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Estatísticas por Habilidade</p>
                                        </a>
                                </li>
                                <li class="nav-item">
                                        <a href="{{ route('relatorios.raca') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Estatísticas por Raça</p>
                                        </a>
                                </li>
                                <li class="nav-item">
                                        <a href="{{ route('relatorios.deficiencias') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Estatísticas de Alunos com Deficiências</p>
                                        </a>
                                </li>
                        </ul>
                </li>
        @endif
        @if(Auth::check() && Auth::user()->role === 'admin')
                <li class="nav-item has-treeview">
                        <a href="#" class="nav-link text-white">
                                <i class="nav-icon fas fa-chart-bar text-white"></i>
                                <p>Relatórios Tutoria <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                             
                              
                                <li class="nav-item">
                                        <a href="{{ route('relatorios.estatisticas-escola') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Estatísticas por Escola</p>
                                        </a>
                                </li>
                               
                                
                                
                              
                        </ul>
                </li>
        @endif
        @if(Auth::check() && Auth::user()->role === 'admin')
                <li class="nav-item has-treeview">
                        <a href="#" class="nav-link text-white">
                                <i class="nav-icon fas fa-chart-bar text-white"></i>
                                <p>Tutoria|Simulados <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                             
                              
                                <li class="nav-item">
                                        <a href="{{ route('relatorios.estatisticas-escola') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Estatísticas Comparativa</p>
                                        </a>
                                </li>
                               
                                
                                
                              
                        </ul>
                </li>
        @endif
    </ul>
</nav>