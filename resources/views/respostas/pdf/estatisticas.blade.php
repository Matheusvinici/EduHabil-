<!DOCTYPE html>
<html>
<head>
    <title>Estatísticas</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Estatísticas</h1>
    <table>
        <thead>
            <tr>
                <th>Prova</th>
                <th>Aluno</th>
                <th>Acertos</th>
                <th>Total de Questões</th>
                <th>% de Acertos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($estatisticas as $estatistica)
                <tr>
                    <td>{{ $estatistica['prova'] }}</td>
                    <td>{{ $estatistica['aluno'] }}</td>
                    <td>{{ $estatistica['acertos'] }}</td>
                    <td>{{ $estatistica['total_questoes'] }}</td>
                    <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>