<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório da Rede Municipal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px; /* Aumentei ainda mais o tamanho base */
            line-height: 1.5;
            margin: 5px;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }
        .header img {
            height: 80px;
            margin-bottom: 5px;
        }
        .title {
            font-size: 22px; /* Título principal maior */
            font-weight: bold;
            margin: 10px 0;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .card-header {
            background-color: #4e73df;
            color: white;
            padding: 12px 15px; /* Padding maior */
            border-radius: 5px 5px 0 0;
            margin: -15px -15px 15px -15px;
            font-weight: bold;
            font-size: 18px; /* Cabeçalho maior */
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 15px; /* Fonte da tabela maior */
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px 10px; /* Células mais espaçosas */
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 16px; /* Cabeçalho da tabela maior */
        }
        /* Cores para status */
        .text-success { color: #28a745; font-weight: bold; }
        .text-danger { color: #dc3545; font-weight: bold; }
        .text-warning { color: #ffc107; font-weight: bold; }
        .text-info { color: #17a2b8; font-weight: bold; }
        
        /* Alertas */
        .alert {
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 16px;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 5px solid #17a2b8;
        }
        
        /* Cartões de estatísticas */
        .stat-card {
            border-left: 5px solid #4e73df;
            padding: 12px 15px;
            margin-bottom: 15px;
            background: white;
            border-radius: 5px;
        }
        .stat-title {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .stat-value {
            font-size: 24px; /* Valor bem maior */
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        /* Seções de quadrantes */
        .quadrant-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .quadrant-title {
            font-weight: bold;
            margin-bottom: 12px;
            padding: 10px 12px;
            border-radius: 5px;
            font-size: 18px; /* Título do quadrante maior */
        }
        /* Cores dos quadrantes */
        .q1-title { background-color: #d4edda; color: #155724; border-left: 5px solid #28a745; }
        .q2-title { background-color: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; }
        .q3-title { background-color: #fff3cd; color: #856404; border-left: 5px solid #ffc107; }
        .q4-title { background-color: #d1ecf1; color: #0c5460; border-left: 5px solid #17a2b8; }
        
        .quadrant-description {
            font-size: 16px;
            margin-bottom: 12px;
        }
        .quadrant-stats {
            font-size: 16px;
            margin-bottom: 12px;
            font-weight: bold;
        }
        .no-schools {
            font-style: italic;
            color: #6c757d;
            padding: 12px 0;
            font-size: 16px;
        }
        
        /* Layout responsivo */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -10px;
            margin-left: -10px;
        }
        .col {
            flex: 1;
            padding: 0 10px;
            min-width: 0;
        }
        
        /* Barras de progresso */
        .progress {
            height: 30px; /* Barra mais alta */
            background-color: #e9ecef;
            border-radius: 20px;
            margin-bottom: 15px;
        }
        .progress-bar {
            height: 100%;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px; /* Texto na barra maior */
        }
        /* Cores das barras */
        .bg-success { background-color: #28a745; }
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; }
        .bg-info { background-color: #17a2b8; }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 10px;
            color: white;
            font-weight: 500;
            font-size: 14px;
        }
        
        /* Melhorias para impressão */
        @media print {
            body {
                font-size: 18px !important;
            }
            .table {
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ $logoPath }}" alt="Logo Prefeitura">
        <div class="title">Relatório da Rede Municipal</div>
        <div style="font-size: 16px;">
            Simulado: {{ $simulados->firstWhere('id', request('simulado_id'))->nome ?? 'Todos' }} | 
            Data: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Dados Gerais - Com fontes maiores -->
    <div class="card">
        <div class="card-header">Dados Gerais</div>
        <div class="row">
            <div class="col">
                <div class="stat-card">
                    <h6 class="stat-title">Total de Alunos</h6>
                    <p class="stat-value">{{ $totalAlunos }}</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card">
                    <h6 class="stat-title">Alunos Ativos</h6>
                    <p class="stat-value">{{ $alunosAtivos }}</p>
                    <div style="font-size: 16px;">{{ $totalAlunos > 0 ? number_format(($alunosAtivos/$totalAlunos)*100, 2) : 0 }}%</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-card">
                    <h6 class="stat-title">Alunos Responderam</h6>
                    <p class="stat-value">{{ $alunosResponderam }}</p>
                    <div style="font-size: 16px;">{{ $alunosAtivos > 0 ? number_format(($alunosResponderam/$alunosAtivos)*100, 2) : 0 }}%</div>
                </div>
            </div>
            <div class="col">
                <div class="stat-card">
                    <h6 class="stat-title">Taxa de Faltosos</h6>
                    <p class="stat-value">{{ $alunosAtivos - $alunosResponderam }}</p>
                    <div style="font-size: 16px;">{{ $alunosAtivos > 0 ? number_format((($alunosAtivos - $alunosResponderam)/$alunosAtivos)*100, 2) : 0 }}%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Médias Ponderadas e TRI - Com fontes maiores -->
    <div class="card">
        <div class="card-header">Análise de Desempenho</div>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th rowspan="2" style="vertical-align: middle; font-size: 16px;">Métrica</th>
                        <th colspan="3" style="text-align: center; font-size: 16px;">Métricas por Peso</th>
                        <th rowspan="2" style="text-align: center; vertical-align: middle; font-size: 16px;">Geral</th>
                    </tr>
                    <tr>
                        <th style="text-align: center; font-size: 16px;">Peso 1</th>
                        <th style="text-align: center; font-size: 16px;">Peso 2</th>
                        <th style="text-align: center; font-size: 16px;">Peso 3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-size: 15px;">Média Tradicional</td>
                        <td style="text-align: center; font-size: 15px;">{{ number_format($mediasPeso['peso_1'], 2) }}</td>
                        <td style="text-align: center; font-size: 15px;">{{ number_format($mediasPeso['peso_2'], 2) }}</td>
                        <td style="text-align: center; font-size: 15px;">{{ number_format($mediasPeso['peso_3'], 2) }}</td>
                        <td style="text-align: center; font-size: 15px;">{{ number_format($mediasPeso['media_geral'], 2) }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 15px;">Média TRI</td>
                        <td style="text-align: center; font-size: 15px;">{{ number_format($analiseTRI['peso_1']['media'], 2) }}</td>
                        <td style="text-align: center; font-size: 15px;">{{ number_format($analiseTRI['peso_2']['media'], 2) }}</td>
                        <td style="text-align: center; font-size: 15px;">{{ number_format($analiseTRI['peso_3']['media'], 2) }}</td>
                        <td style="text-align: center; font-size: 15px;">{{ number_format($analiseTRI['media_geral'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="alert alert-info">
            <strong style="font-size: 16px;">Índice de Consistência Interna (Alpha de Cronbach):</strong> 
            <span style="font-size: 16px;">{{ number_format($analiseTRI['indice_consistencia'], 2) }}</span>
            @if($analiseTRI['indice_consistencia'] > 0.7)
                <span class="badge" style="background-color: #28a745; font-size: 14px;">Boa consistência</span>
            @elseif($analiseTRI['indice_consistencia'] > 0.5)
                <span class="badge" style="background-color: #ffc107; color: black; font-size: 14px;">Consistência moderada</span>
            @else
                <span class="badge" style="background-color: #dc3545; font-size: 14px;">Baixa consistência</span>
            @endif
        </div>
    </div>

    <!-- Quadrantes de Desempenho - Com fontes maiores -->
    <div class="card">
        <div class="card-header">Análise por Quadrantes</div>
        <div class="alert alert-info">
            <strong style="font-size: 16px;">Média Geral TRI da Rede:</strong> 
            <span style="font-size: 16px;">{{ number_format($mediaGeralTRI, 2) }}</span>
        </div>
        
        <div class="row">
            <!-- Quadrante 1 -->
            <div class="col">
                <div class="quadrant-section">
                    <div class="quadrant-title q1-title">
                         Q1 - Alto Desempenho/Grande ({{ $quadrantes['q1']['count'] }} escolas)
                    </div>
                    <p class="quadrant-description">200+ alunos e nota TRI acima da média</p>
                    <p class="quadrant-stats">Média TRI: {{ number_format($quadrantes['q1']['media_tri'], 2) }}</p>
                    
                    @if($quadrantes['q1']['count'] > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 35%; font-size: 15px;">Escola</th>
                                    <th style="width: 15%; font-size: 15px;">Alunos</th>
                                    <th style="width: 15%; font-size: 15px;">Média Trad.</th>
                                    <th style="width: 15%; font-size: 15px;">Média TRI</th>
                                    <th style="width: 20%; font-size: 15px;">Diferença</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quadrantes['q1']['escolas'] as $escola)
                                <tr>
                                    <td style="font-size: 14px;">{{ $escola['nome'] }}</td>
                                    <td style="font-size: 14px;">{{ $escola['total_alunos'] }}</td>
                                    <td style="font-size: 14px;">{{ number_format($escola['media_simulado'], 2) }}</td>
                                    <td style="font-size: 14px;">{{ number_format($escola['media_tri'], 2) }}</td>
                                    <td style="font-size: 14px;" class="{{ $escola['media_tri'] >= $mediaGeralTRI ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($escola['media_tri'] - $mediaGeralTRI, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="no-schools">Nenhuma escola encontrada neste quadrante</p>
                    @endif
                </div>
            </div>
            
            <!-- Quadrante 2 -->
            <div class="col">
                <div class="quadrant-section">
                    <div class="quadrant-title q2-title">
                         Q2 - Baixo Desempenho/Grande ({{ $quadrantes['q2']['count'] }} escolas)
                    </div>
                    <p class="quadrant-description">200+ alunos e nota TRI abaixo da média</p>
                    <p class="quadrant-stats">Média TRI: {{ number_format($quadrantes['q2']['media_tri'], 2) }}</p>
                    
                    @if($quadrantes['q2']['count'] > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 35%; font-size: 15px;">Escola</th>
                                    <th style="width: 15%; font-size: 15px;">Alunos</th>
                                    <th style="width: 15%; font-size: 15px;">Média Trad.</th>
                                    <th style="width: 15%; font-size: 15px;">Média TRI</th>
                                    <th style="width: 20%; font-size: 15px;">Diferença</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quadrantes['q2']['escolas'] as $escola)
                                <tr>
                                    <td style="font-size: 14px;">{{ $escola['nome'] }}</td>
                                    <td style="font-size: 14px;">{{ $escola['total_alunos'] }}</td>
                                    <td style="font-size: 14px;">{{ number_format($escola['media_simulado'], 2) }}</td>
                                    <td style="font-size: 14px;">{{ number_format($escola['media_tri'], 2) }}</td>
                                    <td style="font-size: 14px;" class="{{ $escola['media_tri'] >= $mediaGeralTRI ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($escola['media_tri'] - $mediaGeralTRI, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="no-schools">Nenhuma escola encontrada neste quadrante</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Quadrante 3 -->
            <div class="col">
                <div class="quadrant-section">
                    <div class="quadrant-title q3-title">
                         Q3 - Baixo Desempenho/Pequena ({{ $quadrantes['q3']['count'] }} escolas)
                    </div>
                    <p class="quadrant-description">Menos de 200 alunos e nota TRI abaixo da média</p>
                    <p class="quadrant-stats">Média TRI: {{ number_format($quadrantes['q3']['media_tri'], 2) }}</p>
                    
                    @if($quadrantes['q3']['count'] > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 35%; font-size: 15px;">Escola</th>
                                    <th style="width: 15%; font-size: 15px;">Alunos</th>
                                    <th style="width: 15%; font-size: 15px;">Média Trad.</th>
                                    <th style="width: 15%; font-size: 15px;">Média TRI</th>
                                    <th style="width: 20%; font-size: 15px;">Diferença</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quadrantes['q3']['escolas'] as $escola)
                                <tr>
                                    <td style="font-size: 14px;">{{ $escola['nome'] }}</td>
                                    <td style="font-size: 14px;">{{ $escola['total_alunos'] }}</td>
                                    <td style="font-size: 14px;">{{ number_format($escola['media_simulado'], 2) }}</td>
                                    <td style="font-size: 14px;">{{ number_format($escola['media_tri'], 2) }}</td>
                                    <td style="font-size: 14px;" class="{{ $escola['media_tri'] >= $mediaGeralTRI ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($escola['media_tri'] - $mediaGeralTRI, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="no-schools">Nenhuma escola encontrada neste quadrante</p>
                    @endif
                </div>
            </div>
            
            <!-- Quadrante 4 -->
            <div class="col">
                <div class="quadrant-section">
                    <div class="quadrant-title q4-title">
                         Q4 - Alto Desempenho/Pequena ({{ $quadrantes['q4']['count'] }} escolas)
                    </div>
                    <p class="quadrant-description">Menos de 200 alunos e nota TRI acima da média</p>
                    <p class="quadrant-stats">Média TRI: {{ number_format($quadrantes['q4']['media_tri'], 2) }}</p>
                    
                    @if($quadrantes['q4']['count'] > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 35%; font-size: 15px;">Escola</th>
                                    <th style="width: 15%; font-size: 15px;">Alunos</th>
                                    <th style="width: 15%; font-size: 15px;">Média Trad.</th>
                                    <th style="width: 15%; font-size: 15px;">Média TRI</th>
                                    <th style="width: 20%; font-size: 15px;">Diferença</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quadrantes['q4']['escolas'] as $escola)
                                <tr>
                                    <td style="font-size: 14px;">{{ $escola['nome'] }}</td>
                                    <td style="font-size: 14px;">{{ $escola['total_alunos'] }}</td>
                                    <td style="font-size: 14px;">{{ number_format($escola['media_simulado'], 2) }}</td>
                                    <td style="font-size: 14px;">{{ number_format($escola['media_tri'], 2) }}</td>
                                    <td style="font-size: 14px;" class="{{ $escola['media_tri'] >= $mediaGeralTRI ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($escola['media_tri'] - $mediaGeralTRI, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="no-schools">Nenhuma escola encontrada neste quadrante</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Médias por Segmento - Com fontes maiores -->
    <div class="card">
        <div class="card-header">Médias por Segmento</div>
        <div class="row">
            <div class="col">
                <div style="border: 1px solid {{ $projecaoSegmento['1a5']['atingiu_meta'] ? '#28a745' : '#dc3545' }}; border-radius: 5px; padding: 15px; margin-bottom: 20px;">
                    <div style="background-color: {{ $projecaoSegmento['1a5']['atingiu_meta'] ? '#28a745' : '#dc3545' }}; color: white; padding: 12px; margin: -15px -15px 15px -15px; border-radius: 5px 5px 0 0;">
                        <h6 style="margin: 0; font-size: 17px;">1º ao 5º Ano</h6>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div style="margin-bottom: 12px;">
                                <div style="font-size: 15px; font-weight: bold;">Média Tradicional</div>
                                <div style="font-size: 20px; font-weight: bold;">{{ number_format($projecaoSegmento['1a5']['media'], 2) }}</div>
                            </div>
                        </div>
                        <div class="col">
                            <div style="margin-bottom: 12px;">
                                <div style="font-size: 15px; font-weight: bold;">Média TRI</div>
                                <div style="font-size: 20px; font-weight: bold;">{{ number_format($projecaoSegmento['1a5']['media_tri'], 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $projecaoSegmento['1a5']['projecao'] * 10 }}%; background-color: {{ $projecaoSegmento['1a5']['atingiu_meta'] ? '#28a745' : '#dc3545' }};">
                            <span style="font-size: 15px;">Projeção TRI: {{ number_format($projecaoSegmento['1a5']['projecao'], 1) }}</span>
                        </div>
                    </div>
                    <div style="margin-top: 12px;">
                        <span class="badge" style="background-color: {{ $projecaoSegmento['1a5']['atingiu_meta'] ? '#28a745' : '#dc3545' }}; font-size: 14px;">
                            Meta: 6.0 | Diferença: {{ $projecaoSegmento['1a5']['diferenca'] >= 0 ? '+' : '' }}{{ $projecaoSegmento['1a5']['diferenca'] }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col">
                <div style="border: 1px solid {{ $projecaoSegmento['6a9']['atingiu_meta'] ? '#28a745' : '#ffc107' }}; border-radius: 5px; padding: 15px; margin-bottom: 20px;">
                    <div style="background-color: {{ $projecaoSegmento['6a9']['atingiu_meta'] ? '#28a745' : '#ffc107' }}; color: white; padding: 12px; margin: -15px -15px 15px -15px; border-radius: 5px 5px 0 0;">
                        <h6 style="margin: 0; font-size: 17px;">6º ao 9º Ano</h6>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div style="margin-bottom: 12px;">
                                <div style="font-size: 15px; font-weight: bold;">Média Tradicional</div>
                                <div style="font-size: 20px; font-weight: bold;">{{ number_format($projecaoSegmento['6a9']['media'], 2) }}</div>
                            </div>
                        </div>
                        <div class="col">
                            <div style="margin-bottom: 12px;">
                                <div style="font-size: 15px; font-weight: bold;">Média TRI</div>
                                <div style="font-size: 20px; font-weight: bold;">{{ number_format($projecaoSegmento['6a9']['media_tri'], 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $projecaoSegmento['6a9']['projecao'] * 10 }}%; background-color: {{ $projecaoSegmento['6a9']['atingiu_meta'] ? '#28a745' : '#ffc107' }};">
                            <span style="font-size: 15px;">Projeção TRI: {{ number_format($projecaoSegmento['6a9']['projecao'], 1) }}</span>
                        </div>
                    </div>
                    <div style="margin-top: 12px;">
                        <span class="badge" style="background-color: {{ $projecaoSegmento['6a9']['atingiu_meta'] ? '#28a745' : '#ffc107' }}; font-size: 14px;">
                            Meta: 5.0 | Diferença: {{ $projecaoSegmento['6a9']['diferenca'] >= 0 ? '+' : '' }}{{ $projecaoSegmento['6a9']['diferenca'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>