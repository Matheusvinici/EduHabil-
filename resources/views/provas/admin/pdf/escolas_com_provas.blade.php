<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolas com Provas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="text-center my-4">Escolas com Provas</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Escola</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($escolasComProvas as $escola)
                    <tr>
                        <td>{{ $escola->nome }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>