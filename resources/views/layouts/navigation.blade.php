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
                $showGerarProvas = Auth::check() && in_array(Auth::user()->role, ['professor', 'coordenador', 'admin', 'inclusiva', 'aee', 'aplicador']);
                $showAvaliacao = Auth::check() && in_array(Auth::user()->role, ['admin', 'inclusiva', 'aee', 'aluno', 'aplicador']);
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
                                                <a href="{{ route('respostas.index') }}" class="nav-link text-white">
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
                $showAtividadesEducativas = Auth::check() && in_array(Auth::user()->role, ['professor', 'coordenador', 'admin', 'aplicador']);
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
                                                        <p>Cadastrar Atividades Educativas</p>
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
                $showAtividadesAdaptadas = Auth::check() && in_array(Auth::user()->role, ['aee', 'coordenador', 'professor', 'inclusiva', 'admin', 'aplicador']);
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
                <li class="nav-item has-treeview">
                        <a href="#" class="nav-link text-white">
                                <i class="nav-icon fas fa-chalkboard-teacher text-white"></i>
                                <p>Tutoria<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                                <li class="nav-item">
                                        <a href="{{ route('tutoria_criterios.index') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Critérios de Avaliação</p>
                                        </a>
                                </li>
                                <li class="nav-item">
                                        <a href="{{ route('tutoria_avaliacoes.index') }}" class="nav-link text-white">
                                                <i class="far fa-circle nav-icon text-white"></i>
                                                <p>Avaliação</p>
                                        </a>
                                </li>
                        </ul>
                </li>
        @endif

          @if(Auth::check() && Auth::user()->role === 'admin')
                <li class="nav-item has-treeview">
                        <a href="#" class="nav-link text-white">
                                <i class="nav-icon fas fa-chart-bar text-white"></i>
                                <p>Relatórios<i class="right fas fa-angle-left"></i></p>
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
    </ul>
</nav>