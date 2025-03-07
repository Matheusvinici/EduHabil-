<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prova</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            margin-bottom: 20px;
        }
        .header h1, .header h2 {
            margin: 0;
        }
        .footer {
            text-align: center;
            padding: 10px;
            margin-top: 30px;
        }
        .questao {
            margin-bottom: 30px; /* Espaçamento entre questões */
        }
        .alternativa {
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Cabeçalho -->
        <div class="header text-center">
            <h1>{{ $user->escola }}</h1> <!-- Nome da escola -->
            <h2>Atividade Avaliativa: {{ $prova->disciplina->nome }}</h2> <!-- Nome da disciplina -->
            <p><strong>Unidade:</strong> {{ $prova->unidade->nome }}</p> <!-- Unidade trabalhada -->
            <p><strong>Habilidade:</strong> {{ $prova->habilidade->descricao }}</p> <!-- Habilidade -->
            <p><strong>Ano:</strong> {{ now()->year }}</p> <!-- Ano atual -->
        </div>

        <!-- Questões -->
        <div class="questoes">
            <h3 class="mb-4">Questões</h3>
            @foreach ($prova->questoes as $questao)
                <div class="questao">
                    <strong>{{ $loop->iteration }}.</strong> {{ $questao->enunciado }}

                    <!-- Alternativas -->
                    <div class="alternativas mt-2">
                        <p class="alternativa">A) {{ $questao->alternativa_a }}</p>
                        <p class="alternativa">B) {{ $questao->alternativa_b }}</p>
                        <p class="alternativa">C) {{ $questao->alternativa_c }}</p>
                        <p class="alternativa">D) {{ $questao->alternativa_d }}</p>
                    </div>

                    <!-- Espaço para resposta -->
                    <div class="resposta">
                        <p>Resposta: ____________________________________________</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Rodapé -->
        <div class="footer">
            <p>Prova gerada pelo Sistema de Gestão de Provas</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
