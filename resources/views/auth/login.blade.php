<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - EduHabil+</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #224abe;
            --accent-color: #1cc88a;
            --dark-color: #5a5c69;
        }
        
        body {
            background-color: #f8f9fc;
            overflow-x: hidden;
        }
        
        .gradient-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }
        
        .login-card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .login-card .card-body {
            padding: 3rem 2rem;
        }
        
        @media (min-width: 768px) {
            .login-card .card-body {
                padding: 4rem 3rem;
            }
        }
        
        .form-control {
            border-radius: 0.5rem;
            padding: 1rem;
            border: 1px solid #d1d3e2;
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }
        
        .form-control:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-login {
            background-color: var(--primary-color);
            border: none;
            border-radius: 0.5rem;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
            margin-top: 0.5rem;
        }
        
        .btn-login:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-register {
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            border-radius: 0.5rem;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
            background-color: transparent;
            margin-top: 1rem;
        }
        
        .btn-register:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .logo {
            max-height: 90px;
            margin-bottom: 2rem;
            width: auto;
        }
        
        @media (min-width: 768px) {
            .logo {
                max-height: 110px;
                margin-bottom: 2.5rem;
            }
        }
        
        .feature-side {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        @media (min-width: 992px) {
            .feature-side {
                padding: 4rem;
            }
        }
        
        .feature-item {
            margin-bottom: 2rem;
            display: flex;
            align-items: flex-start;
        }
        
        .feature-icon {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            margin-right: 1.2rem;
            margin-top: 0.3rem;
        }
        
        .text-small {
            font-size: 0.9rem;
            line-height: 1.6;
        }
        
        .divider {
            height: 2px;
            background-color: rgba(255, 255, 255, 0.15);
            margin: 2rem 0;
        }
        
        .form-footer {
            margin-top: 2rem;
            text-align: center;
        }
        
        .remember-me {
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
        
        .form-title {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .feature-title {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        
        .feature-subtitle {
            font-size: 1.1rem;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="container-fluid px-4">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="login-card card o-hidden border-0">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <!-- Coluna do Formulário -->
                                <div class="col-lg-6">
                                    <div class="p-4 p-md-5">
                                        <div class="text-center">
                                            <img src="https://www.juazeiro.ba.gov.br/wp-content/uploads/2021/11/horizontalazul.png" 
                                                alt="Logo Juazeiro" 
                                                class="logo img-fluid">
                                            <h1 class="form-title">Bem-vindo ao EduHabil+</h1>
                                        </div>
                                        
                                        @if($errors->any())
                                            <div class="alert alert-danger">
                                                <ul class="mb-0">
                                                    @foreach($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        
                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf
                                            
                                            <div class="form-group">
                                                <input type="email" name="email" id="email" 
                                                    class="form-control"
                                                    placeholder="Digite seu e-mail" 
                                                    value="{{ old('email') }}" 
                                                    required autofocus>
                                            </div>
                                            
                                            <div class="form-group">
                                                <input type="password" name="password" id="password" 
                                                    class="form-control"
                                                    placeholder="Senha" 
                                                    required>
                                            </div>
                                            
                                            <div class="remember-me">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                                    <label class="form-check-label" for="remember">
                                                        Lembrar-me
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-login text-white">
                                                <i class="fas fa-sign-in-alt me-2 text-white"></i> Entrar
                                            </button>
                                        </form>
                                        
                                        <div class="form-footer">
                                            <div class="mb-3">
                                                <a class="text-muted" href="{{ route('password.request') }}">Esqueceu a senha?</a>
                                            </div>
                                            
                                          
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Coluna de Recursos (oculta em mobile) -->
                                <div class="col-lg-6 d-none d-lg-flex feature-side">
                                    <div>
                                        <h2 class="feature-title">EduHabil+</h2>
                                        <p class="feature-subtitle">Sistema pedagógico completo para gestão de provas, atividades pedagógicas e direcionamentos educacionais para o Atendimento Educacional Especializado (AEE).</p>
                                        
                                        <div class="feature-item">
                                            <div class="feature-icon">
                                                <i class="fas fa-chalkboard-teacher"></i>
                                            </div>
                                            <div>
                                                <h5 class="h5">Gestão Pedagógica Integrada</h5>
                                                <p class="text-small">Ferramentas completas para planejamento, execução e acompanhamento de atividades educacionais.</p>
                                            </div>
                                        </div>
                                        
                                        <div class="feature-item">
                                            <div class="feature-icon">
                                                <i class="fas fa-user-graduate"></i>
                                            </div>
                                            <div>
                                                <h5 class="h5">Acompanhamento Individual</h5>
                                                <p class="text-small">Registro detalhado e monitoramento personalizado do desenvolvimento de cada aluno.</p>
                                            </div>
                                        </div>
                                        
                                        <div class="feature-item">
                                            <div class="feature-icon">
                                                <i class="fas fa-chart-pie"></i>
                                            </div>
                                            <div>
                                                <h5 class="h5">Relatórios e Análises</h5>
                                                <p class="text-small">Dados pedagógicos organizados e indicadores para tomada de decisão informada.</p>
                                            </div>
                                        </div>
                                        
                                        <div class="divider"></div>
                                        
                                        <p class="text-small mb-0">
                                            <i class="fas fa-info-circle me-2"></i> 
                                            Dúvidas ou suporte técnico: suporte@eduhabil.com.br
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Font Awesome JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>