<!DOCTYPE html>
<html>
<head>
    <title>{{ $atividadeProfessor->atividade->titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            font-size: 24px;
            color: #333;
        }
        p {
            font-size: 14px;
            color: #555;
        }
        .section {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>{{ $atividadeProfessor->atividade->titulo }}</h1>
    <div class="section">
        <p><strong>Disciplina:</strong> {{ $atividadeProfessor->atividade->disciplina->nome }}</p>
        <p><strong>Ano:</strong> {{ $atividadeProfessor->atividade->ano->nome }}</p>
        <p><strong>Habilidade:</strong> {{ $atividadeProfessor->atividade->habilidade->descricao }}</p>
    </div>
    <div class="section">
        <p><strong>Objetivo:</strong> {{ $atividadeProfessor->atividade->objetivo }}</p>
    </div>
    <div class="section">
        <p><strong>Etapas da Aula:</strong> {{ $atividadeProfessor->atividade->metodologia }}</p>
    </div>
    <div class="section">
        <p><strong>Materiais Necessários:</strong> {{ $atividadeProfessor->atividade->materiais }}</p>
    </div>
    <div class="section">
        <p><strong>Atividade Proposta:</strong> {{ $atividadeProfessor->atividade->resultados_esperados }}</p>
    </div>
</body>
</html>