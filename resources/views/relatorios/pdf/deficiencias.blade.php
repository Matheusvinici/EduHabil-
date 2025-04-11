<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Desempenho - Alunos com Deficiência</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
            font-size: 12px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 15px;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo {
            height: 60px;
            margin-right: 15px;
        }

        .header-text {
            text-align: right;
        }

        .municipio {
            font-size: 14px;
            color: #555;
            font-style: italic;
        }

        .sistema {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
        }

        h1 {
            color: #0066cc;
            font-size: 22px;
            margin-top: 25px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            text-align: center;
        }

        .filters {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .filters p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #0066cc;
            color: white
            ;
            text-align: left;
            padding: 8px;
            font-weight: bold;
        }

        td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 12px;
            color: white;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-warning {
            background-color: #ffc107;
            color: black;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .summary {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 5px;
        }

        .summary-item {
            display: inline-block;
            margin-right: 30px;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('images/logoprefeitura.png') }}" class="logo" alt="Prefeitura de Juazeiro-BA">
            <div>
                <h2>Secretaria Municipal de Educação</h2>
                <p class="municipio">Prefeitura de Juazeiro-BA, presente no futuro da gente.</p>
            </div>
        </div>
        <div class="header-text">
            <p class="sistema">Gerado pelo sistema EduHabil+</p>
            <p>{{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <h1>Relatório de Desempenho - Alunos com Deficiência</h1>

    <div class="filters">
        <h3>Filtros Aplicados:</h3>
        @if(request('simulado_id'))
            <p><strong>Simulado:</strong> {{ $filtros['simulados']->firstWhere('id', request('simulado_id'))->nome ?? 'Não Especificado' }}</p>
        @else
            <p><strong>Simulado:</strong> Todos</p>
        @endif
        @if(request('deficiencia'))
            <p><strong>Deficiência:</strong> {{ $filtros['deficiencias'][request('deficiencia')] ?? request('deficiencia') ?? 'Não Especificada' }}</p>
        @else
            <p><strong>Deficiência:</strong> Todas</p>
        @endif
        @if(request('escola_id'))
            <p><strong>Escola:</strong> {{ $filtros['escolas']->firstWhere('id', request('escola_id'))->nome ?? 'Não Especificada' }}</p>
        @else
            <p><strong>Escola:</strong> Todas</p>
        @endif
    </div>

    <div class="summary">
        <div class="summary-item"><strong>Total de Alunos:</strong> {{ $totalAlunos }}</div>
        <div class="summary-item"><strong>Responderam:</strong> {{ $totalResponderam }} ({{ $totalAlunos > 0 ? number_format(($totalResponderam/$totalAlunos)*100, 2) : 0 }}%)</div>
        <div class="summary-item"><strong>Média Geral:</strong> {{ number_format($mediaGeral, 2) }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Escola</th>
                <th>Aluno</th>
                <th>Deficiência</th>
                <th>Turma</th>
                <th>Acertos</th>
                <th>% Acertos</th>
                <th>Média</th>
                <th>Desempenho</th>
            </tr>
        </thead>
        <tbody>
            @forelse($estatisticas as $estatistica)
                <tr>
                    <td>{{ $estatistica['escola_nome'] }}</td>
                    <td>{{ $estatistica['aluno_nome'] }}</td>
                    <td>{{ $filtros['deficiencias'][$estatistica['deficiencia']] ?? $estatistica['deficiencia'] }}</td>
                    <td>{{ $estatistica['turma'] }}</td>
                    <td>{{ $estatistica['acertos'] }}/{{ $estatistica['total_questoes'] }}</td>
                    <td>{{ number_format($estatistica['porcentagem'], 2) }}%</td>
                    <td>{{ number_format($estatistica['media'], 2) }}</td>
                    <td>
                        @php
                            $badgeClass = [
                                'Ótimo' => 'success',
                                'Regular' => 'warning',
                                'Ruim' => 'danger'
                            ][$estatistica['desempenho']];
                        @endphp
                        <span class="badge badge-{{ $badgeClass }}">
                            {{ $estatistica['desempenho'] }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Nenhum resultado encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>EduHabil+ - Sistema de Gestão Educacional</p>
        <p>Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
    </div>
</body>
</html>