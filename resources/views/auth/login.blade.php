<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EduHabil+</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .gradient-custom-2 {
            /* fallback for old browsers */
            background: #fccb90;

            /* Chrome 10-25, Safari 5.1-6 */
            background: -webkit-linear-gradient(to right,rgb(36, 36, 238),rgb(60, 39, 154),rgb(14, 23, 143),rgb(95, 69, 180));

            /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
            background: linear-gradient(to right,rgb(18, 41, 123),rgb(59, 54, 216),rgb(54, 146, 221),rgb(69, 84, 180));
        }

        @media (min-width: 768px) {
            .gradient-form {
                height: 100vh !important;
            }
        }

        @media (min-width: 769px) {
            .gradient-custom-2 {
                border-top-right-radius: .3rem;
                border-bottom-right-radius: .3rem;
            }
        }

        .logo-custom {
            width: 100%;
            max-width: 300px; /* Ajuste o tamanho do logo conforme necessário */
        }
    </style>
</head>
<body class="bg-light">
    <section class="h-100 gradient-form" style="background-color: #eee;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-10">
                    <div class="card rounded-3 text-black">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="card-body p-md-5 mx-md-4">
                                    <div class="text-center">
                                        <img src="https://www.juazeiro.ba.gov.br/wp-content/uploads/2021/11/horizontalazul.png" 
                                            alt="Logo Juazeiro" 
                                            loading="lazy" 
                                            class="logo-custom">
                                        <h4 class="mt-1 mb-5 pb-1">EduHabil+</h4>
                                    </div>

                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf <!-- Token CSRF -->
                                        <p>Por favor, faça login para acessar o sistema.</p>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <input type="email" name="email" id="email" class="form-control" placeholder="Digite seu e-mail" required />
                                            <label class="form-label" for="email">E-mail:</label>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <input type="password" name="password" id="password" class="form-control" placeholder="Digite sua senha" required />
                                            <label class="form-label" for="password">Senha:</label>
                                        </div>

                                        <div class="text-center pt-1 mb-5 pb-1">
                                            <button data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit">Entrar</button>
                                            <a class="text-muted" href="#!">Esqueceu a senha?</a>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">Não tem uma conta?</p>
                                            <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-outline-danger">Criar nova conta</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h4 class="mb-4">EduHabil+</h4>
                                    <p class="small mb-0">
                                        EduHabil+ é um sistema pedagógico desenvolvido para a gestão de provas, atividades pedagógicas e direcionamentos educacionais, especialmente adaptado para professores do Atendimento Educacional Especializado (AEE). Simplifique sua rotina e potencialize o aprendizado dos seus alunos com ferramentas intuitivas e eficientes.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS (opcional, se precisar de funcionalidades JS do Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>