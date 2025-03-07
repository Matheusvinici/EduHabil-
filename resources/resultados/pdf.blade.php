<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados da Prova</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Resultados da Prova: {{ $prova->nome }}</h2>
        <p>Data: {{ date('d/m/Y', strtotime($prova->data)) }}</p>
        <p>Professor: {{ $prova->user->name }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Aluno</th>
                <th>Quantidade de Questões</th>
                <th>Acertos</th>
                <th>Nota (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultados as $resultado)
                <tr>
                    <td>{{ $resultado['aluno'] }}</td>
                    <td>10</td>
                    <td>{{ $resultado['acertos'] }}</td>
                    <td>{{ number_format(($resultado['acertos'] / 10) * 100, 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Gerado em {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
