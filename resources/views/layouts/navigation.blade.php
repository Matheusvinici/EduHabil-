<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <!-- Painel Inicial - Acesso para todos -->
        <li class="nav-item">
            <a href="{{ route('home') }}" class="nav-link text-white">
                <i class="nav-icon fas fa-tachometer-alt text-white"></i>
                <p>Início</p>
            </a>
        </li>

        <!-- Avaliação - Acesso para alunos, professores e coordenadores -->
        @if(Auth::check() && in_array(Auth::user()->role, ['aluno', 'professor', 'coordenador', 'admin']))
            <li class="nav-item">
                <a href="{{ route('respostas.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-check-circle text-white"></i>
                    <p>Minhas Avaliações</p>
                </a>
            </li>
        @endif

        <!-- Avaliação - Acesso para alunos, professores e coordenadores -->
        @if(Auth::check() && in_array(Auth::user()->role, ['aluno', 'professor', 'coordenador', 'admin']))
            <li class="nav-item">
                <a href="{{ route('simulados.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-check-circle text-white"></i>
                    <p>Simulados</p>
                </a>
            </li>
        @endif

        <!-- Provas e Turmas - Acesso para professores, coordenadores e admin -->
        @if(Auth::check() && in_array(Auth::user()->role, ['professor', 'coordenador', 'admin']))
            <li class="nav-item">
                <a href="{{ route('provas.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-file-alt text-white"></i>
                    <p>Gerenciar Provas</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('turmas.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-users text-white"></i>
                    <p>Minhas Turmas</p>
                </a>
            </li>
        @endif

        <!-- Atividades - Acesso para professores e admin -->
        @if(Auth::check() && in_array(Auth::user()->role, ['professor', 'admin']))
            <li class="nav-item">
                <a href="{{ route('atividades_professores.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-tasks text-white"></i>
                    <p>Atividades Pedagógicas</p>
                </a>
            </li>
        @endif

        <!-- Adaptações - Acesso para professores do AEE e admin -->
        @if(Auth::check() && in_array(Auth::user()->role, ['aee', 'admin']))
            <li class="nav-item">
                <a href="{{ route('adaptacoes.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-cogs text-white"></i>
                    <p>Atividades Adaptadas</p>
                </a>
            </li>
        @endif

        <!-- Características e Deficiências - Acesso para diretoria inclusiva e admin -->
        @if(Auth::check() && in_array(Auth::user()->role, ['inclusiva', 'admin']))
            <li class="nav-item">
                <a href="{{ route('caracteristicas.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-list text-white"></i>
                    <p>Perfis de Aprendizagem</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('deficiencias.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-wheelchair text-white"></i>
                    <p>Tipos de Deficiências</p>
                </a>
            </li>
        @endif

        <!-- Admin - Acesso total -->
        @if(Auth::check() && Auth::user()->role === 'admin')
            <li class="nav-item">
                <a href="{{ route('escolas.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-school text-white"></i>
                    <p>Gerenciar Escolas</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-users-cog text-white"></i>
                    <p>Gerenciar Perfil</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('disciplinas.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-book text-white"></i>
                    <p>Disciplinas</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('habilidades.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-list-alt text-white"></i>
                    <p>Habilidades e Competências</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('questoes.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-question-circle text-white"></i>
                    <p>Banco de Questões</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('anos.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-calendar-alt text-white"></i>
                    <p>Anos Letivos</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('recursos.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-toolbox text-white"></i>
                    <p>Recursos Educacionais</p>
                </a>
            </li>
        @endif

    </ul>
</nav>
