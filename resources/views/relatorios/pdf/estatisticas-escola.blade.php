<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estatísticas por Escola - {{ $simulado->nome }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-success { color: green; }
        .text-danger { color: red; }
    </style>
</head>
<body>
    <h1>Estatísticas por Escola - {{ $simulado->nome }}</h1>
    
    <table>
        <thead>
            <tr>
                <th>Escola</th>
                <th class="text-center">Alunos Ativos</th>
                <th class="text-center">Responderam</th>
                <th class="text-center">Faltosos</th>
                <th class="text-center">Média Ponderada</th>
                <th class="text-center">Projeção TRI</th>
                <th class="text-center">Meta Atingida</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($estatisticasPorEscola as $escola)
            <tr>
                <td>{{ $escola['nome'] }}</td>
                <td class="text-center">{{ $escola['alunos_ativos'] }}</td>
                <td class="text-center">{{ $escola['alunos_responderam'] }}</td>
                <td class="text-center">{{ $escola['alunos_ativos'] - $escola['alunos_responderam'] }}</td>
                <td class="text-center">{{ number_format($escola['media_ponderada'], 2) }}</td>
                <td class="text-center">{{ number_format($escola['projecao_tri'], 2) }}</td>
                <td class="text-center {{ $escola['atingiu_meta'] ? 'text-success' : 'text-danger' }}">
                    {{ $escola['atingiu_meta'] ? 'Sim' : 'Não' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>