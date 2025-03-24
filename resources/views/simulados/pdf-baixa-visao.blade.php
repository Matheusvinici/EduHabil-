<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulado - {{ $simulado->nome }} (Baixa Visão)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 18pt; /* Fonte ampliada */
            line-height: 1.6; /* Espaçamento maior */
            color: #000000; /* Contraste máximo */
            background-color: #ffffff;
        }
        h1 {
            text-align: center;
            color: #333;
            font-size: 24pt; /* Título maior */
        }
        .pergunta {
            margin-bottom: 30px; /* Espaço maior entre perguntas */
            page-break-inside: avoid;
        }
        .enunciado {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .alternativas {
            margin-left: 30px; /* Margem maior */
        }
        .imagem {
            max-width: 700px; /* Imagem maior que no convencional */
            height: auto;
            margin: 15px 0; /* Espaçamento maior */
            border: 2px solid #000; /* Borda para melhor visualização */
        }
        .resposta-correta {
            margin-top: 15px;
            font-weight: bold;
            padding: 5px;
            background-color: #f0f0f0; /* Fundo para destacar */
        }
    </style>
</head>
<body>
    <h1>Simulado: {{ $simulado->nome }}</h1>
    <p><strong>Descrição:</strong> {{ $simulado->descricao ?? 'N/A' }}</p>

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
            
       
        </div>
    @endforeach
</body>
</html>