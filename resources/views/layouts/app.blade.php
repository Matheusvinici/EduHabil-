<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'EduHabil+') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <style>
        /* Cor principal para o sidebar */
        .sidebar-custom {
            background-color: #2B5598 !important;
        }
        
        /* Faixa clara para a área da logo */
        .brand-area {
            background-color: #e9f2ff; /* Azul bem claro */
            padding: 10px 0;
            margin-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        /* Estilo para a logo */
        .brand-logo {
            display: block;
            margin: 0 auto;
            height: 40px; /* Ajuste conforme necessário */
            width: auto;
            padding-bottom: 5px;
        }
        
        /* Ajuste do texto do brand */
        .brand-text {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2B5598 !important; /* Cor azul para contraste */
            display: block;
            text-align: center;
        }
        
        /* Manter ícones brancos no menu */
        .nav-sidebar .nav-link p, 
        .nav-sidebar .nav-link i {
            color: white !important;
        }
        
        /* Hover dos itens do menu */
        .nav-sidebar .nav-item:hover > .nav-link {
            background-color: rgba(255,255,255,0.1);
        }
        
        /* Ajuste do link da brand area */
        .brand-link {
            padding: 0;
            background: transparent !important;
        }
    </style>
    
    @yield('styles')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropdownMenuButton" role="button" data-toggle="dropdown" aria-expanded="false">
                    {{ Auth::user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    <a href="{{ route('profile.show') }}" class="dropdown-item">
                        <i class="fas fa-file mr-2"></i>
                        {{ __('Meu perfil') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="dropdown-item"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            {{ __('Sair') }}
                        </a>
                    </form>
                </div>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

   <!-- Sidebar -->
@auth
    @php
        // Definir $escolaAtual de forma segura
        $escolaAtual = session('escola_selecionada') ? App\Models\Escola::find(session('escola_selecionada')) : null;
        $usuarioPodeSelecionarEscola = in_array(auth()->user()->role, ['professor', 'coordenador', 'gestor']);
    @endphp

    @if($usuarioPodeSelecionarEscola)
        @if($escolaAtual)
            <aside class="main-sidebar elevation-4 sidebar-custom">
                <!-- Brand Logo Area -->
                <div class="brand-area">
                    <a href="/" class="brand-link text-center">
                        <img src="{{ asset('images/logoprefeitura.png') }}" alt="Logo Prefeitura" class="brand-logo">
                        <span class="brand-text">EduHabil+</span>
                    </a>
                    <div style="padding: 10px" class="school-info">
                        <small class="text-muted">Escola selecionada:</small>
                        <h6>{{ $escolaAtual->nome }}</h6>
                       
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <div class="sidebar">
                    @include('layouts.navigation')
                </div>
            </aside>
        @endif
    @else
        <!-- Outros perfis (admin, aluno, etc) -->
        <aside class="main-sidebar elevation-4 sidebar-custom">
            <div class="brand-area">
                <a href="/" class="brand-link text-center">
                    <img src="{{ asset('images/logoprefeitura.png') }}" alt="Logo Prefeitura" class="brand-logo">
                    <span class="brand-text">EduHabil+</span>
                </a>
            </div>
            <div class="sidebar">
                @include('layouts.navigation')
            </div>
        </aside>
    @endif
@endauth
    <!-- Content Wrapper -->
    <div class="content-wrapper">
        @yield('content')
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">
            SEDUC-Juazeiro-BA
        </div>
        <strong>Copyright &copy; 2025 <a href="https://www.juazeiro.ba.gov.br/">Prefeitura Municipal de Juazeiro-BA</a>.</strong> Todos os direitos reservados.
    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- XLSX -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
    // Inicializa os tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    function confirmDelete(cursoId) {
        Swal.fire({
            title: 'Você tem certeza?',
            text: "Esta ação não poderá ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + cursoId).submit();
            }
        });
    }
</script>

@yield('scripts')
</body>
</html>