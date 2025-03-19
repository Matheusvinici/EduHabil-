<!DOCTYPE html>
<html>
<head>
    <title>Atividade Gerada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1 { color: #0d6efd; margin-bottom: 20px; }
        .card { margin-bottom: 20px; }
        .card-header { background-color: #0d6efd; color: white; }
        .list-group-item { border: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Atividade Gerada</h1>

        <!-- Informações do Recurso -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recurso</h5>
            </div>
            <div class="card-body">
                <p><strong>Nome:</strong> {{ $adaptacao->recurso->nome }}</p>
                <p><strong>Descrição:</strong> {{ $adaptacao->recurso->descricao }}</p>
                <p><strong>Como Trabalhar:</strong> {{ $adaptacao->recurso->como_trabalhar }}</p>
                <p><strong>Direcionamentos:</strong> {{ $adaptacao->recurso->direcionamentos }}</p>
            </div>
        </div>

        <!-- Lista de Deficiências -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Deficiências</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach ($adaptacao->deficiencias as $deficiencia)
                        <li class="list-group-item">{{ $deficiencia->nome }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Lista de Características -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Características</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach ($adaptacao->caracteristicas as $caracteristica)
                        <li class="list-group-item">{{ $caracteristica->nome }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Botão Voltar -->
        <div class="text-center mt-4">
            <a href="{{ route('adaptacoes.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Adicionando Font Awesome para ícones -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</body>
</html>