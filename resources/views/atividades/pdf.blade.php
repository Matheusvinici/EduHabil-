<!DOCTYPE html>
<html>
<head>
    <title>{{ $atividade->titulo }}</title>
</head>
<body>
    <h1>{{ $atividade->titulo }}</h1>
    <p><strong>Disciplina:</strong> {{ $atividade->disciplina->nome }}</p>
    <p><strong>Ano:</strong> {{ $atividade->ano->nome }}</p>
    <p><strong>Habilidade:</strong> {{ $atividade->habilidade->nome }}</p>
    <p><strong>Objetivo:</strong> {{ $atividade->objetivo }}</p>
    <p><strong>Metodologia:</strong> {{ $atividade->metodologia }}</p>
    <p><strong>Materiais Necessários:</strong> {{ $atividade->materiais }}</p>
    <p><strong>Resultados Esperados:</strong> {{ $atividade->resultados_esperados }}</p>
</body>
</html>