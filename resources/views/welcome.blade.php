<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema de Geração de Atividades Automatizadas">
    <title>Sistema de Geração de Atividades - Juazeiro-BA</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }
      .b-example-divider {
        height: 3rem;
        background-color: #e9ecef;
        border: 0;
        margin: 3rem 0;
      }
      .logo-custom {
        width: 100%; /* Define a largura da logo como metade do contêiner */
        height: auto; /* Mantém a proporção da imagem */
      }
      section {
        background-color: #F1F3F9;
        font-family: Calibri, sans-serif;
      }
    </style>
  </head>
  <body>

    <main>
      <section>
        <div class="container col-xxl-8 px-4 py-5">
          <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
            <div class="col-10 col-sm-8 col-lg-6">
                <img src="https://www.juazeiro.ba.gov.br/wp-content/uploads/2021/11/horizontalazul.png" 
                    alt="Logo Juazeiro" 
                    loading="lazy" 
                    class="logo-custom">
            </div>
            <div id="objetivo" class="col-lg-6">
              <h1 class="display-5 fw-bold lh-1 mb-3">EduHabil+</h1>
              <p class="lead">Facilitando a criação e gestão de atividades pedagógicas na Rede Municipal de Ensino de Juazeiro-BA</p>
              <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 me-md-2">Entrar</a>
                <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">Cadastre-se</a>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-dark text-secondary px-4 py-5 text-center">
          <div class="py-5">
            <h3 class="display-8 fw-bold text-white">EduHabil+</h3>
            <div class="col-lg-6 mx-auto">
              <p class="fs-5 mb-4">Desenvolvido pelo CETEJ</p>
              <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
              <a href="{{ route('login') }}" class="btn btn-outline-info btn-lg px-4 me-md-2">Entrar</a>
              <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">Cadastre-se</a>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

  </body>
</html>
