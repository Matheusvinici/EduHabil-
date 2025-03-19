<!DOCTYPE html>
<html>
<head>
    <title>Atividade Gerada</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #333; }
        .info { margin-bottom: 20px; }
        .info p { margin: 5px 0; }
        .list { margin-left: 20px; }
    </style>
</head>
<body>
    <h1>Atividade Gerada</h1>

    <!-- Informações do Recurso -->
    <div class="info">
        <p><strong>Recurso:</strong> {{ $adaptacao->recurso->nome }}</p>
        <p><strong>Descrição:</strong> {{ $adaptacao->recurso->descricao }}</p>
        <p><strong>Como Trabalhar:</strong> {{ $adaptacao->recurso->como_trabalhar }}</p>
        <p><strong>Direcionamentos:</strong> {{ $adaptacao->recurso->direcionamentos }}</p>
    </div>

    <!-- Lista de Deficiências -->
    <div class="info">
        <p><strong>Deficiências:</strong></p>
        <ul class="list">
            @foreach ($adaptacao->deficiencias as $deficiencia)
                <li>{{ $deficiencia->nome }}</li>
            @endforeach
        </ul>
    </div>

    <!-- Lista de Características -->
    <div class="info">
        <p><strong>Características:</strong></p>
        <ul class="list">
            @foreach ($adaptacao->caracteristicas as $caracteristica)
                <li>{{ $caracteristica->nome }}</li>
            @endforeach
        </ul>
    </div>
</body>
</html>