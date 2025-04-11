<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Estatísticas de Habilidades por Disciplina - Admin</title>
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
            height: 80px;
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

        h2 {
            color: #0066cc;
            font-size: 18px;
            margin-top: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
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
            color: white;
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

    <h1>Relatório de Estatísticas de Habilidades por Disciplina</h1>

    <div class="filters">
        <h3>Filtros Aplicados:</h3>
        @if(isset($filtros['simulado_id']) && $filtros['simulado_id'])
            <p><strong>Simulado:</strong> {{ $simulados->firstWhere('id', $filtros['simulado_id'])?->nome ?? 'Todos' }}</p>
        @else
            <p><strong>Simulado:</strong> Todos</p>
        @endif
        @if(isset($filtros['disciplina_id']) && $filtros['disciplina_id'])
            <p><strong>Disciplina:</strong> {{ $disciplinas->firstWhere('id', $filtros['disciplina_id'])?->nome ?? 'Todas' }}</p>
        @else
            <p><strong>Disciplina:</strong> Todas</p>
        @endif
        @if(isset($filtros['ano_id']) && $filtros['ano_id'])
            <p><strong>Ano:</strong> {{ $anos->firstWhere('id', $filtros['ano_id'])?->nome ?? 'Todos' }}</p>
        @else
            <p><strong>Ano:</strong> Todos</p>
        @endif
    </div>

    <h2>Estatísticas de Habilidades por Disciplina</h2>
    @if(isset($estatisticasPorHabilidade) && count($estatisticasPorHabilidade) > 0)
        <table>
            <thead>
                <tr>
                    <th>Disciplina</th>
                    <th>Habilidade (Código)</th>
                    <th>Descrição</th>
                    <th>Total Questões</th>
                    <th>Total Respostas</th>
                    <th>Acertos</th>
                    <th>Média Simples</th>
                    <th>Média Ponderada</th>
                    <th>% Acerto</th>
                    <th>TRI Médio (0-10)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estatisticasPorHabilidade as $habilidade)
                    <tr>
                        <td>{{ $habilidade->disciplina_nome }}</td>
                        <td>{{ $habilidade->codigo }}</td>
                        <td>{{ $habilidade->descricao }}</td>
                        <td style="text-align: center;">{{ $habilidade->total_questoes }}</td>
                        <td style="text-align: center;">{{ $habilidade->total_respostas }}</td>
                        <td style="text-align: center;">{{ $habilidade->acertos }}</td>
                        <td style="text-align: center;">{{ $habilidade->media_simples }}</td>
                        <td style="text-align: center;">{{ $habilidade->media_ponderada }}</td>
                        <td style="text-align: center;">{{ $habilidade->percentual_acerto }}%</td>
                        <td style="text-align: center;">{{ $habilidade->tri_medio }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Nenhuma estatística de habilidade encontrada com os filtros selecionados.</p>
    @endif

    <div class="footer">
        <p>EduHabil+ - Sistema de Gestão Educacional</p>
        <p>Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
    </div>
</body>
</html>