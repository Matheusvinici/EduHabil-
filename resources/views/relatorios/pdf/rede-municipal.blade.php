<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório Completo - Rede Municipal</title>
    <style>
        /* Reset e configurações básicas */
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        /* Layout do container */
        .container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 10px;
        }
        
        /* Cabeçalho */
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 16pt;
            margin: 5px 0;
            color: #2c3e50;
        }
        
        .header-info {
            font-size: 9pt;
            color: #555;
        }
        
        .logo {
            height: 60px;
            margin-bottom: 10px;
        }
        
        /* Filtros aplicados */
        .filters {
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        
        /* Seções do relatório */
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: #3490dc;
            color: white;
            padding: 6px 10px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 11pt;
            border-radius: 4px;
        }
        
        /* Grid de estatísticas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .stat-box {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px;
            text-align: center;
            background-color: #f9f9f9;
        }
        
        .stat-title {
            font-size: 8pt;
            margin-bottom: 5px;
            font-weight: bold;
            color: #6c757d;
        }
        
        .stat-value {
            font-size: 12pt;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .stat-percent {
            font-size: 8pt;
            color: #6c757d;
        }
        
        /* Quadrantes */
        .quadrant-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .quadrant-card {
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .quadrant-header {
            padding: 8px;
            font-weight: bold;
            text-align: center;
            color: white;
        }
        
        .q1 { background-color: #28a745; } /* Verde */
        .q2 { background-color: #dc3545; } /* Vermelho */
        .q3 { background-color: #ffc107; color: #333; } /* Amarelo */
        .q4 { background-color: #17a2b8; } /* Azul */
        
        .quadrant-body {
            padding: 8px;
            text-align: center;
        }
        
        .quadrant-count {
            font-size: 18pt;
            font-weight: bold;
            margin: 5px 0;
        }
        
        /* Tabelas */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }
        
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        
        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Segmentos */
        .segment-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .segment-card {
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .segment-header {
            padding: 8px;
            font-weight: bold;
            text-align: center;
            background-color: #e9ecef;
        }
        
        .segment-body {
            padding: 10px;
            text-align: center;
        }
        
        .progress {
            height: 20px;
            background-color: #e9ecef;
            border-radius: 4px;
            margin: 8px 0;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            line-height: 20px;
            color: white;
            font-size: 8pt;
            text-align: center;
        }
        
        .bg-success { background-color: #28a745; }
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }
        
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        
        /* Rodapé */
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 8pt;
            color: #6c757d;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        
        /* Controle de quebra de página */
        .page-break {
            page-break-after: always;
        }
        
        /* Classes auxiliares */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
        
        @media print {
            body {
                font-size: 9pt;
            }
            .header {
                border-bottom: 1px solid #333;
            }
            .table th, .table td {
                padding: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Cabeçalho -->
        <div class="header">
            <div>
                <img src="{{ storage_path('app/public/images/logo_prefeitura.png') }}" class="logo" alt="Logo Prefeitura">
            </div>
            <h1>Relatório Completo - Rede Municipal</h1>
            <div class="header-info">
                <strong>Simulado:</strong> {{ $simulados->firstWhere('id', request()->simulado_id)->nome ?? 'Todos' }} | 
                <strong>Data:</strong> {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- Dados Gerais -->
        <div class="section">
            <div class="section-title">Dados Gerais</div>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-title">Total de Alunos</div>
                    <div class="stat-value">{{ $totalAlunos }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-title">Alunos Ativos</div>
                    <div class="stat-value">{{ $alunosAtivos }}</div>
                    <div class="stat-percent">
                        {{ $totalAlunos > 0 ? number_format(($alunosAtivos/$totalAlunos)*100, 2) : 0 }}%
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-title">Alunos Responderam</div>
                    <div class="stat-value">{{ $alunosResponderam }}</div>
                    <div class="stat-percent">
                        {{ $alunosAtivos > 0 ? number_format(($alunosResponderam/$alunosAtivos)*100, 2) : 0 }}%
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-title">Taxa de Faltosos</div>
                    <div class="stat-value">{{ $alunosAtivos - $alunosResponderam }}</div>
                    <div class="stat-percent">
                        {{ $alunosAtivos > 0 ? number_format((($alunosAtivos - $alunosResponderam)/$alunosAtivos)*100, 2) : 0 }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Quadrantes -->
        <div class="section">
            <div class="section-title">Análise por Quadrantes</div>
            <div class="quadrant-container">
                <div class="quadrant-card">
                    <div class="quadrant-header q1">🟩 Q1 - Alto Desempenho/Grande</div>
                    <div class="quadrant-body">
                        <div class="quadrant-count">{{ $quadrantes['q1']['count'] }}</div>
                        <div>Escolas</div>
                        <div class="stat-percent">200+ alunos e nota TRI acima da média</div>
                        <div class="text-bold">Média TRI: {{ number_format($quadrantes['q1']['media_tri'] ?? 0, 2) }}</div>
                    </div>
                </div>
                <div class="quadrant-card">
                    <div class="quadrant-header q2">🟥 Q2 - Baixo Desempenho/Grande</div>
                    <div class="quadrant-body">
                        <div class="quadrant-count">{{ $quadrantes['q2']['count'] }}</div>
                        <div>Escolas</div>
                        <div class="stat-percent">200+ alunos e nota TRI abaixo da média</div>
                        <div class="text-bold">Média TRI: {{ number_format($quadrantes['q2']['media_tri'] ?? 0, 2) }}</div>
                    </div>
                </div>
                <div class="quadrant-card">
                    <div class="quadrant-header q3">🟨 Q3 - Baixo Desempenho/Pequena</div>
                    <div class="quadrant-body">
                        <div class="quadrant-count">{{ $quadrantes['q3']['count'] }}</div>
                        <div>Escolas</div>
                        <div class="stat-percent">Menos de 200 alunos e nota TRI abaixo da média</div>
                        <div class="text-bold">Média TRI: {{ number_format($quadrantes['q3']['media_tri'] ?? 0, 2) }}</div>
                    </div>
                </div>
                <div class="quadrant-card">
                    <div class="quadrant-header q4">🟦 Q4 - Alto Desempenho/Pequena</div>
                    <div class="quadrant-body">
                        <div class="quadrant-count">{{ $quadrantes['q4']['count'] }}</div>
                        <div>Escolas</div>
                        <div class="stat-percent">Menos de 200 alunos e nota TRI acima da média</div>
                        <div class="text-bold">Média TRI: {{ number_format($quadrantes['q4']['media_tri'] ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
            <div style="text-align: center; margin-top: 10px;">
                <strong>Média Geral TRI:</strong> {{ number_format($mediaGeralTRI, 2) }}
            </div>
        </div>

        <!-- Análise de Desempenho -->
        <div class="section">
            <div class="section-title">Análise de Desempenho</div>
            <table class="table">
                <thead>
                    <tr>
                        <th rowspan="2">Métrica</th>
                        <th colspan="3">Métricas por Peso</th>
                        <th rowspan="2">Geral</th>
                    </tr>
                    <tr>
                        <th>Peso 1</th>
                        <th>Peso 2</th>
                        <th>Peso 3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Média Tradicional</td>
                        <td class="text-center">{{ number_format($mediasPeso['peso_1'], 2) }}</td>
                        <td class="text-center">{{ number_format($mediasPeso['peso_2'], 2) }}</td>
                        <td class="text-center">{{ number_format($mediasPeso['peso_3'], 2) }}</td>
                        <td class="text-center">{{ number_format($mediasPeso['media_geral'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Média TRI</td>
                        <td class="text-center">{{ number_format($analiseTRI['peso_1']['media'], 2) }}</td>
                        <td class="text-center">{{ number_format($analiseTRI['peso_2']['media'], 2) }}</td>
                        <td class="text-center">{{ number_format($analiseTRI['peso_3']['media'], 2) }}</td>
                        <td class="text-center">{{ number_format($analiseTRI['media_geral'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
            <div style="margin-top: 10px;">
                <strong>Índice de Consistência Interna (Alpha de Cronbach):</strong> 
                {{ number_format($analiseTRI['indice_consistencia'], 2) }}
                @if($analiseTRI['indice_consistencia'] > 0.7)
                    <span class="badge badge-success">Boa consistência</span>
                @elseif($analiseTRI['indice_consistencia'] > 0.5)
                    <span class="badge badge-warning">Consistência moderada</span>
                @else
                    <span class="badge badge-danger">Baixa consistência</span>
                @endif
            </div>
        </div>

        <!-- Médias por Segmento -->
        <div class="section">
            <div class="section-title">Médias por Segmento</div>
            <div class="segment-row">
                <div class="segment-card">
                    <div class="segment-header {{ $projecaoSegmento['1a5']['atingiu_meta'] ? 'bg-success' : 'bg-danger' }}">
                        1º ao 5º Ano
                    </div>
                    <div class="segment-body">
                        <div style="font-size: 14pt; font-weight: bold; margin: 5px 0;">
                            {{ number_format($projecaoSegmento['1a5']['media'], 2) }}
                        </div>
                        <div class="progress">
                            <div class="progress-bar {{ $projecaoSegmento['1a5']['projecao'] >= 6 ? 'bg-success' : 'bg-danger' }}" 
                                 style="width: {{ min(100, $projecaoSegmento['1a5']['projecao'] * 10) }}%">
                                Projeção: {{ number_format($projecaoSegmento['1a5']['projecao'], 1) }}
                            </div>
                        </div>
                        <div style="margin-top: 5px;">
                            <span class="badge {{ $projecaoSegmento['1a5']['atingiu_meta'] ? 'badge-success' : 'badge-danger' }}">
                                Meta: 6.0 | Diferença: {{ $projecaoSegmento['1a5']['diferenca'] >= 0 ? '+' : '' }}{{ $projecaoSegmento['1a5']['diferenca'] }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="segment-card">
                    <div class="segment-header {{ $projecaoSegmento['6a9']['atingiu_meta'] ? 'bg-success' : 'bg-warning' }}">
                        6º ao 9º Ano
                    </div>
                    <div class="segment-body">
                        <div style="font-size: 14pt; font-weight: bold; margin: 5px 0;">
                            {{ number_format($projecaoSegmento['6a9']['media'], 2) }}
                        </div>
                        <div class="progress">
                            <div class="progress-bar {{ $projecaoSegmento['6a9']['projecao'] >= 5 ? 'bg-success' : 'bg-warning' }}" 
                                 style="width: {{ min(100, $projecaoSegmento['6a9']['projecao'] * 10) }}%">
                                Projeção: {{ number_format($projecaoSegmento['6a9']['projecao'], 1) }}
                            </div>
                        </div>
                        <div style="margin-top: 5px;">
                            <span class="badge {{ $projecaoSegmento['6a9']['atingiu_meta'] ? 'badge-success' : 'badge-warning' }}">
                                Meta: 5.0 | Diferença: {{ $projecaoSegmento['6a9']['diferenca'] >= 0 ? '+' : '' }}{{ $projecaoSegmento['6a9']['diferenca'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Escolas por Quadrante -->
        <!-- Escolas por Quadrante -->
@foreach(['q1', 'q2', 'q3', 'q4'] as $quad)
    @if(isset($quadrantes[$quad]) && is_array($quadrantes[$quad]) && 
        isset($quadrantes[$quad]['escolas']) && 
        count($quadrantes[$quad]['escolas']) > 0)
        <div class="section">
            <div class="section-title" style="background-color: {{ 
                $quad == 'q1' ? '#28a745' : 
                ($quad == 'q2' ? '#dc3545' : 
                ($quad == 'q3' ? '#ffc107' : '#17a2b8')) 
            }};">
                @switch($quad)
                    @case('q1') 🟩 Quadrante 1 - Alto Desempenho/Grande @break
                    @case('q2') 🟥 Quadrante 2 - Baixo Desempenho/Grande @break
                    @case('q3') 🟨 Quadrante 3 - Baixo Desempenho/Pequena @break
                    @case('q4') 🟦 Quadrante 4 - Alto Desempenho/Pequena @break
                @endswitch
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Escola</th>
                        <th class="text-center">Alunos</th>
                        <th class="text-center">Média Trad.</th>
                        <th class="text-center">Média TRI</th>
                        <th class="text-center">Diferença</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quadrantes[$quad]['escolas'] as $escola)
                        @if(is_array($escola))
                            <tr>
                                <td>{{ $escola['nome'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $escola['total_alunos'] ?? 0 }}</td>
                                <td class="text-center">{{ number_format($escola['media_simulado'] ?? 0, 2) }}</td>
                                <td class="text-center">{{ number_format($escola['media_tri'] ?? 0, 2) }}</td>
                                <td class="text-center {{ ($escola['media_tri'] ?? 0) >= $mediaGeralTRI ? 'text-success' : 'text-danger' }}">
                                    {{ ($escola['media_tri'] ?? 0) >= $mediaGeralTRI ? '+' : '' }}{{ number_format(($escola['media_tri'] ?? 0) - $mediaGeralTRI, 2) }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endforeach

        <!-- Rodapé -->
        <div class="footer">
            Relatório gerado em {{ now()->format('d/m/Y H:i') }} pelo sistema EduHabil+<br>
            Secretaria Municipal de Educação - Prefeitura de Juazeiro-BA
        </div>
    </div>
</body>
</html>