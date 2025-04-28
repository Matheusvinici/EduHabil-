<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 15px;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
        }
        
        .logo {
            height: 80px;
            margin-right: 15px;
        }
        
        .header-text {
            text-align: center;
            flex-grow: 1;
        }
        
        .header-text h1 {
            color: #0066cc;
            font-size: 22px;
            margin: 0;
        }
        
        .header-text h2 {
            font-size: 18px;
            margin: 5px 0 0 0;
            color: #555;
        }
        
        .municipio {
            font-size: 14px;
            color: #555;
            font-style: italic;
        }
        
        .sistema {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
            text-align: right;
        }
        
        .info-prova {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        
        .info-prova p {
            margin: 5px 0;
            min-width: 200px;
        }
        
        .info-prova strong {
            color: #0066cc;
        }
        
        .questoes {
            margin-top: 25px;
        }
        
        .questoes h3 {
            color: #0066cc;
            font-size: 18px;
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        
        .questao {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .questao:last-child {
            border-bottom: none;
        }
        
        .questao strong {
            color: #0066cc;
        }
        
        .alternativas {
            margin-left: 20px;
            margin-top: 10px;
        }
        
        .alternativa {
            margin-bottom: 5px;
        }
        
        .resposta {
            margin-top: 15px;
            padding: 8px 0;
            border-top: 1px dashed #ccc;
            display: {{ $mostrar_gabarito ? 'block' : 'none' }};
        }
        
        .resposta p {
            margin: 0;
            font-weight: bold;
            color: #0066cc;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        
        .gerador {
            font-size: 11px;
            color: #555;
            text-align: right;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Cabeçalho -->
        <div class="header">
            <div class="logo-container">
                <img src="{{ public_path('images/logoprefeitura.png') }}" class="logo" alt="Logo da Prefeitura">
                <div>
                    <h2>Secretaria Municipal de Educação</h2>
                    <p class="municipio">Prefeitura de Juazeiro-BA, presente no futuro da gente.</p>
                </div>
            </div>
            <div class="header-text">
                <h1>{{ $prova->escola->nome }}</h1>
                <h2>Atividade Avaliativa: {{ $prova->disciplina->nome }}</h2>
            </div>
            <div class="sistema">
                <p>Gerado pelo sistema EduHabil+</p>
                <p>{{ $data_emissao }}</p>
            </div>
        </div>

        <!-- Informações da Prova -->
        <div class="info-prova">
            <p><strong>Professor:</strong> {{ $professor_gerador }}</p>
            <p><strong>Habilidade:</strong> {{ $prova->habilidade->descricao }}</p>
            <p><strong>Ano:</strong> {{ $prova->ano->nome }}</p>
            <p><strong>Data da Prova:</strong> ______/________/_______</p>
            <p><strong>Turma:</strong> ___________________________________</p>
            <p><strong>Aluno(a):</strong> _________________________________</p>
        </div>

        <!-- Questões -->
        <div class="questoes">
            <h3>Questões</h3>
            @foreach ($prova->questoes as $questao)
                <div class="questao">
                    <strong>{{ $loop->iteration }}.</strong> {{ $questao->enunciado }}

                    <!-- Alternativas -->
                    <div class="alternativas">
                        <p class="alternativa"><strong>A)</strong> {{ $questao->alternativa_a }}</p>
                        <p class="alternativa"><strong>B)</strong> {{ $questao->alternativa_b }}</p>
                        <p class="alternativa"><strong>C)</strong> {{ $questao->alternativa_c }}</p>
                        <p class="alternativa"><strong>D)</strong> {{ $questao->alternativa_d }}</p>
                    </div>
                    <div class="resposta">
                        <p>Resposta correta: {{ strtoupper($questao->resposta_correta) }}</p>
                    </div>
                   
                </div>
            @endforeach
        </div>

         <!-- Seção do Gabarito Fixo -->
    <div class="gabarito-section">
        <h2 class="gabarito-title">GABARITO PARA RESPOSTAS</h2>
        
        <img src="{{ public_path('images/gabarito-padrao.png') }}" class="gabarito-image" alt="Gabarito Padrão">
        
        <div class="gabarito-instructions">
            <h4>INSTRUÇÕES:</h4>
            <ol>
                <li>Preencha completamente o círculo correspondente à alternativa escolhida</li>
                <li>Use caneta esferográfica azul ou preta</li>
                <li>Não rabisque ou faça marcas fora dos círculos</li>
                <li>Preencha apenas uma alternativa por questão</li>
            </ol>
        </div>
    </div>

    <div class="footer">
        <p>EduHabil+ - Sistema de Gestão Educacional</p>
        <p>Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
    </div>
    </div>
</body>
</html>