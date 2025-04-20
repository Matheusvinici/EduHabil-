<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulado - {{ $simulado->nome }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.3;
            font-size: 11px;
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
            text-align: right;
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
        }
        .main-title {
            color: #0066cc;
            font-size: 16px;
            margin: 10px 0;
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .student-form table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-form td {
            padding: 5px;
        }
        .form-label {
            display: block;
            font-weight: bold;
            color: #0066cc;
            font-size: 10px;
            margin-bottom: 2px;
        }
        .form-input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #999;
            padding: 3px 0;
            font-size: 11px;
        }
        .questions-container {
            column-count: 1;
        }
        .question {
            margin-bottom: 12px;
            page-break-inside: avoid;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px;
            background-color: #f9f9f9;
        }
        .question-text {
            font-weight: bold;
            margin-bottom: 6px;
            font-size: 12px;
        }
        .options {
            margin-left: 8px;
        }
        .option {
            margin: 3px 0;
            font-size: 11px;
        }
        .image-container {
            text-align: center;
            margin: 6px 0;
        }
        .question-image {
            max-width: 100%;
            max-height: 350px;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .correct-answer {
            margin-top: 6px;
            padding: 4px;
            background-color: #f8f9fa;
            border-left: 3px solid #0066cc;
            font-size: 11px;
        }
        .skills {
            background-color: #f0f7ff;
            border: 1px solid #cce5ff;
            border-radius: 4px;
            padding: 8px 10px;
            margin: 12px 0;
            font-size: 10px;
        }
        .skills-title {
            color: #0066cc;
            margin: 0 0 5px 0;
            font-size: 12px;
            border-bottom: 1px solid #cce5ff;
            padding-bottom: 3px;
        }
        .footer {
            text-align: center;
            margin-top: 12px;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 4px;
        }
        .questions-count {
            text-align: right;
            margin: 5px 0;
            font-size: 11px;
            font-weight: bold;
        }
        .gabarito-section {
            page-break-before: always;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #0066cc;
        }
        .gabarito-title {
            color: #0066cc;
            text-align: center;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .gabarito-instructions {
            background-color: #f0f7ff;
            border: 1px solid #cce5ff;
            border-radius: 4px;
            padding: 10px;
            margin-top: 15px;
            font-size: 11px;
        }
        .gabarito-image {
            max-width: 100%;
            border: 1px solid #ddd;
            margin: 0 auto;
            display: block;
        }
        @media print {
            body {
                font-size: 10pt;
            }
            .question {
                margin-bottom: 10pt;
            }
            .question-image {
                max-height: 180pt;
            }
            @page {
                margin: 1cm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('images/logoprefeitura.png') }}" class="logo" alt="Prefeitura de Juazeiro-BA">
            <div>
                <h2>Secretaria Municipal de Educação</h2>
                <p class="municipio">Prefeitura de Juazeiro-BA, presente no futuro da gente.</p>
            </div>
        </div>
        <div class="header-text">
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
                        <img src="{{ storage_path('app/public/' . $pergunta->imagem) }}" class="question-image" alt="Imagem da pergunta {{ $loop->iteration }}">
                    </div>
                @endif
                <div class="options">
                    <p class="option">A) {{ $pergunta->alternativa_a }}</p>
                    <p class="option">B) {{ $pergunta->alternativa_b }}</p>
                    <p class="option">C) {{ $pergunta->alternativa_c }}</p>
                    <p class="option">D) {{ $pergunta->alternativa_d }}</p>
                </div>
                <div class="correct-answer">
                    <strong>Resposta:</strong> {{ $pergunta->resposta_correta }}
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
</body>
</html>