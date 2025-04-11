<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Estatísticas por Raça/Cor</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
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
            font-size: 12px;
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
        
        .total-row {
            font-weight: bold;
            background-color: #e6f2ff !important;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
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
    
    <!-- Título do Relatório -->
    <h1>Relatório de Estatísticas por Raça/Cor</h1>
    
    <!-- Filtros Aplicados -->
    <div class="filters">
        <h3>Filtros Aplicados:</h3>
        @if(request('simulado_id'))
            <p><strong>Simulado:</strong> {{ $simulados->firstWhere('id', request('simulado_id'))?->nome ?? 'N/A' }}</p>
        @endif
        @if(request('ano_id'))
            <p><strong>Ano:</strong> {{ $anos->firstWhere('id', request('ano_id'))?->nome ?? 'N/A' }}</p>
        @endif
        @if(request('disciplina_id'))
            <p><strong>Disciplina:</strong> {{ $disciplinas->firstWhere('id', request('disciplina_id'))?->nome ?? 'N/A' }}</p>
        @endif
    </div>
    
    <!-- Dados -->
    <h2>Estatísticas por Raça/Cor</h2>
    <table>
        <thead>
            <tr>
                <th>Raça/Cor</th>
                <th>Total Respostas</th>
                <th>% do Total</th>
                <th>Acertos</th>
                <th>% Acerto</th>
                <th>Média Ponderada (0-10)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estatisticasPorRaca as $raca)
            <tr>
                <td>{{ $raca->raca ?: 'Não informado' }}</td>
                <td>{{ $raca->total_respostas }}</td>
                <td>{{ $raca->percentual_total ?? 0 }}%</td>
                <td>{{ $raca->acertos }}</td>
                <td>{{ $raca->percentual_acerto }}%</td>
                <td>{{ $raca->media_ponderada }}</td>
            </tr>
            @endforeach
            @if($estatisticasPorRaca->isNotEmpty())
            <tr class="total-row">
                <td><strong>Total Geral</strong></td>
                <td><strong>{{ $totalRespostas }}</strong></td>
                <td><strong>100%</strong></td>
                <td><strong>{{ $totalAcertos }}</strong></td>
                <td><strong>{{ $totalRespostas > 0 ? round(($totalAcertos / $totalRespostas) * 100, 2) : 0 }}%</strong></td>
                <td>
                    <strong>
                        @if($estatisticasPorRaca->isNotEmpty())
                            {{ number_format($estatisticasPorRaca->avg('media_ponderada'), 2) }}
                        @else
                            0
                        @endif
                    </strong>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
    
    <!-- Rodapé -->
    <div class="footer">
        <p>EduHabil+ - Sistema de Gestão Educacional</p>
        <p>Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
    </div>
</body>
</html>