<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulado - {{ $simulado->nome }} (Baixa Visão)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 18pt;
            line-height: 1.6;
            color: #000000;
            background-color: #ffffff;
            margin: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px; /* Aumentado para melhor visualização */
            border-bottom: 3px solid #0066cc;
            padding-bottom: 20px; /* Aumentado para melhor visualização */
        }
        .logo-container {
            display: flex;
            align-items: center;
        }
        .logo {
            height: 100px; /* Aumentado para melhor visualização */
            margin-right: 20px; /* Aumentado para melhor visualização */
        }
        .header-text {
            text-align: right;
        }
        .municipio {
            font-size: 20pt; /* Aumentado para melhor visualização */
            color: #555;
            font-style: italic;
        }
        .sistema {
            font-size: 18pt; /* Aumentado para melhor visualização */
            color: #777;
            margin-top: 10px; /* Aumentado para melhor visualização */
        }
        h1 {
            text-align: center;
            color: #333;
            font-size: 28pt;
            margin-bottom: 30px;
        }
        .pergunta {
            margin-bottom: 40px;
            padding: 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        .enunciado {
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 20pt;
        }
        .alternativas {
            margin-left: 40px;
            font-size: 18pt;
        }
        .imagem-container {
            text-align: center;
        }
        .imagem {
            max-width: 80%;
            height: auto;
            margin: 20px 0;
            border: 3px solid #000;
            border-radius: 5px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .resposta-correta {
            margin-top: 20px;
            font-weight: bold;
            padding: 10px;
            background-color: #f0f0f0;
            border-left: 5px solid #0066cc;
            font-size: 18pt;
        }
        .descricao {
            font-size: 18pt;
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px; /* Aumentado para melhor visualização */
            font-size: 18pt; /* Aumentado para melhor visualização */
            color: #777;
            border-top: 2px solid #eee; /* Aumentado para melhor visualização */
            padding-top: 10px; /* Aumentado para melhor visualização */
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
    <h1>Simulado: {{ $simulado->nome }}</h1>
    <p class="descricao"><strong>Descrição:</strong> {{ $simulado->descricao ?? 'N/A' }}</p>

    @foreach ($simulado->perguntas as $pergunta)
        <div class="pergunta">
            <div class="enunciado">
                {{ $loop->iteration }}. {{ $pergunta->enunciado }}
            </div>
            
            @if ($pergunta->imagem && file_exists(storage_path('app/public/' . $pergunta->imagem)))
                <div class="imagem-container">
                    <img src="{{ storage_path('app/public/' . $pergunta->imagem) }}" 
                         alt="Imagem da pergunta {{ $loop->iteration }}"
                         class="imagem">
                </div>
            @endif
            
            <div class="alternativas">
                <p>A) {{ $pergunta->alternativa_a }}</p>
                <p>B) {{ $pergunta->alternativa_b }}</p>
                <p>C) {{ $pergunta->alternativa_c }}</p>
                <p>D) {{ $pergunta->alternativa_d }}</p>
            </div>
            
            <div class="resposta-correta">
                Resposta correta: {{ strtoupper($pergunta->resposta_correta) }}
            </div>
        </div>
    @endforeach

    <div class="footer">
        <p>EduHabil+ - Sistema de Gestão Educacional</p>
        <p>Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
    </div>
</body>
</html>