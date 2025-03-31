<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulado - {{ $simulado->nome }} - Versão Ampliada</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            padding: 20px;
            color: #000; /* Preto para alto contraste */
            line-height: 1.6; /* Aumento do espaçamento entre linhas */
            font-size: 18px; /* Aumento do tamanho da fonte */
            background-color: #fff; /* Branco para alto contraste */
        }
        .header {
            display: flex;
            flex-direction: column; /* Coloca os elementos em coluna para melhor leitura */
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 20px;
        }
        .logo {
            max-width: 200px; /* Aumento do tamanho da logo */
            margin-bottom: 15px;
        }
        .header-text {
            text-align: center;
        }
        .municipio {
            font-size: 20px;
            color: #555;
            font-style: italic;
        }
        .sistema {
            font-size: 16px;
            color: #777;
            margin-top: 10px;
        }
        .main-title {
            color: #0066cc;
            font-size: 24px;
            margin: 20px 0;
            text-align: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .student-form table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .student-form td {
            padding: 10px;
        }
        .form-label {
            display: block;
            font-weight: bold;
            color: #0066cc;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .form-input {
            width: 100%;
            border: none;
            border-bottom: 2px solid #999;
            padding: 8px 0;
            font-size: 18px;
        }
        .questions-container {
            column-count: 1; /* Garante que as perguntas não sejam divididas em colunas */
        }
        .question {
            margin-bottom: 20px;
            page-break-inside: avoid; /* Evita que as perguntas sejam divididas entre páginas */
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background-color: #f9f9f9;
        }
        .question-text {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 20px;
        }
        .options {
            margin-left: 15px;
        }
        .option {
            margin: 8px 0;
            font-size: 18px;
        }
        .image-container {
            text-align: center;
            margin: 10px 0;
        }
        .question-image {
            max-width: 90%; /* Aumento do tamanho da imagem */
            max-height: 500px;
            height: auto;
            border: 2px solid #ddd;
            border-radius: 5px;
        }
        .correct-answer {
            margin-top: 10px;
            padding: 8px;
            background-color: #f8f9fa;
            border-left: 5px solid #0066cc;
            font-size: 18px;
        }
        .skills {
            background-color: #f0f7ff;
            border: 2px solid #cce5ff;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            font-size: 16px;
        }
        .skills-title {
            color: #0066cc;
            margin: 0 0 10px 0;
            font-size: 20px;
            border-bottom: 2px solid #cce5ff;
            padding-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
            color: #777;
            border-top: 2px solid #eee;
            padding-top: 8px;
        }
        .questions-count {
            text-align: right;
            margin: 10px 0;
            font-size: 18px;
            font-weight: bold;
        }
        @media print {
            body {
                font-size: 16pt;
            }
            .question-image {
                max-height: 400pt;
            }
            @page {
                margin: 2cm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logoprefeitura.png') }}" class="logo" alt="Logo da Prefeitura de Juazeiro-BA (Imagem ampliada)">
        <div class="header-text">
            <h2>Secretaria Municipal de Educação</h2>
            <p class="municipio">Prefeitura de Juazeiro-BA, presente no futuro da gente.</p>
            <p class="sistema">Gerado pelo sistema EduHabil+</p>
            <p>{{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
    <h1 class="main-title">{{ $simulado->nome }}</h1>
    <div class="student-form">
        <table>
            <tr>
                <td style="width: 60%;">
                    <label class="form-label">Aluno(a):</label>
                    <div class="form-input" style="margin-left: 10px;"></div>
                </td>
                <td style="width: 20%;">
                    <label class="form-label">Turma:</label>
                    <div class="form-input"></div>
                </td>
                <td style="width: 20%;">
                    <label class="form-label">Ano:</label>
                    <div class="form-input"></div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <label class="form-label">Professor(a):</label>
                    <div class="form-input" style="margin-left: 10px;"></div>
                </td>
            </tr>
        </table>
    </div>
    <p class="questions-count">Total de questões: {{ count($simulado->perguntas) }}</p>
    <div class="questions-container">
        @foreach ($simulado->perguntas as $pergunta)
            <div class="question">
                <div class="question-text">
                    <span style="color: #0066cc;">{{ $loop->iteration }}.</span>
                    {{ $pergunta->enunciado }}
                </div>
                @if ($pergunta->imagem && file_exists(storage_path('app/public/' . $pergunta->imagem)))
                    <div class="image-container">
                        <img src="{{ storage_path('app/public/' . $pergunta->imagem) }}" class="question-image" alt="Imagem da pergunta {{ $loop->iteration }} (Imagem ampliada)">
                    </div>
                @endif
                <div class="options">
                    <p class="option">A) {{ $pergunta->alternativa_a }}</p>
                    <p class="option">B) {{ $pergunta->alternativa_b }}</p>
                    <p class="option">C) {{ $pergunta->alternativa_c }}</p>
                    <p class="option">D) {{ $pergunta->alternativa_d }}</p>
                </div>
               
            </div>
        @endforeach
    </div>
    <div class="skills">
        <h3 class="skills-title">Habilidades Trabalhadas</h3>
        @if(isset($simulado->descricao) && !empty($simulado->descricao))
            <p>{{ $simulado->descricao }}</p>
        @else
            @foreach($simulado->habilidades ?? [] as $habilidade)
                <p>• {{ $habilidade->descricao }}</p>
            @endforeach
            @if(empty($simulado->habilidades) || count($simulado->habilidades) == 0)
                <p>Não há habilidades cadastradas para este simulado.</p>
            @endif
        @endif
    </div>
    <div class="footer">
        <p>EduHabil+ - Sistema de Gestão Educacional</p>
        <p>Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
    </div>
</body>
</html>