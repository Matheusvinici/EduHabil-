<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <!-- Painel Inicial - Acesso para todos -->
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
        <!-- Simulados -->
        @php
            $showSimulados = Auth::check() && in_array(Auth::user()->role, ['inclusiva', 'admin']);
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
                    <li class="nav-item">
                        <a href="{{ route('perguntas.index') }}" class="nav-link text-white">
                            <i class="far fa-circle nav-icon text-white"></i>
                            <p>Perguntas</p>
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        <!-- Avaliações e Provas -->
        @php
            $showAvaliacoes = false;
            $showGerarProvas = Auth::check() && in_array(Auth::user()->role, ['professor', 'coordenador', 'admin', 'inclusiva', 'aee']);
            $showAvaliacao = Auth::check() && in_array(Auth::user()->role, ['professor', 'coordenador', 'admin', 'inclusiva', 'aee', 'aluno']);
            $showBancoQuestoes = Auth::check() && in_array(Auth::user()->role, ['admin']);
            $showAvaliacoes = $showGerarProvas || $showAvaliacao || $showBancoQuestoes;
        @endphp
        @if($showAvaliacoes)
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link text-white">
                    <i class="nav-icon fas fa-file-alt text-white"></i>
                    <p>Avaliações e Provas<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @if($showGerarProvas)
                        <li class="nav-item">
                            <a href="{{ route('provas.index') }}" class="nav-link text-white">
                                <i class="far fa-circle nav-icon text-white"></i>
                                <p>Gerar Provas</p>
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

        <!-- Planejamento do Professor -->
        @php
            $showPlanejamento = false;
            $showAtividadesEducativas = Auth::check() && in_array(Auth::user()->role, ['professor', 'coordenador', 'admin', 'inclusiva', 'aee']);
            $showMinhasTurmas = Auth::check() && in_array(Auth::user()->role, ['professor', 'coordenador', 'admin', 'inclusiva', 'aee']);
            $showCadastrarAtividades = Auth::check() && in_array(Auth::user()->role, ['admin']);
            $showPlanejamento = $showAtividadesEducativas || $showMinhasTurmas || $showCadastrarAtividades;
        @endphp
        @if($showPlanejamento)
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link text-white">
                    <i class="nav-icon fas fa-tasks text-white"></i>
                    <p>Planejamento do Professor<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @if($showAtividadesEducativas)
                        <li class="nav-item">
                            <a href="{{ route('atividades_professores.index') }}" class="nav-link text-white">
                                <i class="far fa-circle nav-icon text-white"></i>
                                <p>Atividades Educativas</p>
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

        <!-- Gestão Pedagógica -->
        @if(Auth::check() && in_array(Auth::user()->role, ['admin']))
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

        <!-- Inclusão e Acessibilidade -->
        @php
            $showInclusao = false;
            $showAtividadesAdaptadas = Auth::check() && in_array(Auth::user()->role, ['aee', 'coordenador', 'professor', 'inclusiva', 'admin']);
            $showPerfisAprendizagem = Auth::check() && in_array(Auth::user()->role, ['inclusiva', 'admin']);
            $showTiposDeficiencias = Auth::check() && in_array(Auth::user()->role, ['inclusiva', 'admin']);
            $showCadastrarRecursos = Auth::check() && in_array(Auth::user()->role, ['inclusiva', 'admin']);
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

        <!-- Administração -->
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

    </ul>
</nav>