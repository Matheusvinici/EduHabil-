<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulado - {{ $simulado->nome }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .pergunta {
            margin-bottom: 20px;
            page-break-inside: avoid; /* Evita que as perguntas sejam cortadas entre páginas */
        }
        .enunciado {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .alternativas {
            margin-left: 20px;
        }
        .imagem {
            max-width: 150px; /* Tamanho pequeno para a imagem */
            height: auto;
            margin-top: 10px;
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
            @if ($pergunta->imagem)
                <div class="imagem">
                    <img src="{{ storage_path('app/public/' . $pergunta->imagem) }}" alt="Imagem da pergunta" class="imagem">
                </div>
            @endif
            <div class="alternativas">
                <p>A) {{ $pergunta->alternativa_a }}</p>
                <p>B) {{ $pergunta->alternativa_b }}</p>
                <p>C) {{ $pergunta->alternativa_c }}</p>
                <p>D) {{ $pergunta->alternativa_d }}</p>
            </div>
            <div class="resposta-correta">
                <strong>Resposta Correta:</strong> {{ $pergunta->resposta_correta }}
            </div>
        </div>
    @endforeach
</body>
</html>