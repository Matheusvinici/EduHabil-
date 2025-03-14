<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <!-- Painel Inicial - Acesso para todos -->
        <li class="nav-item">
            <a href="{{ route('home') }}" class="nav-link text-white">
                <i class="nav-icon fas fa-tachometer-alt text-white"></i>
                <p>Painel Inicial</p>
            </a>
        </li>

        <!-- Avaliação e Respostas - Acesso para todos -->
        <li class="nav-item">
            <a href="{{ route('respostas.index') }}" class="nav-link text-white">
                <i class="nav-icon fas fa-check-circle text-white"></i>
                <p>Avaliação</p>
            </a>
        </li>

        <!-- Verifica se o usuário é um professor -->
        @if(Auth::check() && Auth::user()->role === 'professor')
            <!-- Provas -->
            <li class="nav-item">
                <a href="{{ route('provas.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-file-alt text-white"></i>
                    <p>Provas</p>
                </a>
            </li>

            <!-- Turmas -->
            <li class="nav-item">
                <a href="{{ route('turmas.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-users text-white"></i>
                    <p>Turmas</p>
                </a>
            </li>

            <!-- Atividades -->
            <li class="nav-item">
                <a href="{{ route('atividades_professores.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-tasks text-white"></i>
                    <p>Atividades</p>
                </a>
            </li>
        @endif

        <!-- Verifica se o usuário é um admin -->
        @if(Auth::check() && Auth::user()->role === 'admin')
            <!-- Cadastro de Escolas -->
            <li class="nav-item">
                <a href="{{ route('escolas.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-school text-white"></i>
                    <p>Escolas</p>
                </a>
            </li>

            <!-- Cadastro de Turmas -->
            <li class="nav-item">
                <a href="{{ route('turmas.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-users text-white"></i>
                    <p>Turmas</p>
                </a>
            </li>

            <!-- Cadastro de Disciplinas -->
            <li class="nav-item">
                <a href="{{ route('disciplinas.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-book text-white"></i>
                    <p>Disciplinas</p>
                </a>
            </li>

            <!-- Cadastro de Habilidades -->
            <li class="nav-item">
                <a href="{{ route('habilidades.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-list-alt text-white"></i>
                    <p>Habilidades</p>
                </a>
            </li>

            <!-- Geração de Questões -->
            <li class="nav-item">
                <a href="{{ route('questoes.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-question-circle text-white"></i>
                    <p>Questões</p>
                </a>
            </li>

            <!-- Geração de Provas -->
            <li class="nav-item">
                <a href="{{ route('provas.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-file-alt text-white"></i>
                    <p>Provas</p>
                </a>
            </li>

            <!-- Gestão de Usuários -->
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-users-cog text-white"></i>
                    <p>Usuários</p>
                </a>
            </li>

            <!-- Cadastro de Atividades -->
            <li class="nav-item">
                <a href="{{ route('atividades.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-tasks text-white"></i>
                    <p>Atividades</p>
                </a>
            </li>
        @endif

    </ul>
</nav>