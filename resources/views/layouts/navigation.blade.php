<!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <!-- Painel Inicial - Acesso para todos -->
        <li class="nav-item">
            <a href="{{ route('home') }}" class="nav-link text-white">
                <i class="nav-icon fas fa-tachometer-alt text-white"></i>
                <p>Painel Inicial</p>
            </a>
        </li>

        <!-- Cadastro de Unidades e Habilidades -->
        <li class="nav-item">
            <a href="{{ route('habilidades.index') }}" class="nav-link text-white">
                <i class="nav-icon fas fa-list-alt text-white"></i>
                <p>Registro de Habilidades</p>
            </a>
        </li>

        <!-- Geração de Atividades Padronizadas -->
        <li class="nav-item">
            <a href="{{ route('questoes.index') }}" class="nav-link text-white">
                <i class="nav-icon fas fa-file-alt text-white"></i>
                <p>Questões</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('provas.index') }}" class="nav-link text-white">
                <i class="nav-icon fas fa-file-alt text-white"></i>
                <p>Provas</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('respostas.index') }}" class="nav-link text-white">
                <i class="nav-icon fas fa-file-alt text-white"></i>
                <p>Avaliação</p>
            </a>
        </li>

    </ul>
</nav>
<!-- /.sidebar-menu -->
