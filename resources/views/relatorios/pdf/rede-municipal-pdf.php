<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório Completo - Rede Municipal</title>
    <style>
        /* Estilos otimizados */
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.2;
            margin: 0;
            padding: 5px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 5px;
            padding-bottom: 5px;
            border-bottom: 1px solid #000;
        }
        .header h1 {
            font-size: 12pt;
            margin: 2px 0;
        }
        .header-info {
            font-size: 8pt;
        }
        .section {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #f0f0f0;
            padding: 3px 5px;
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 8pt;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 3px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5px;
            margin-bottom: 10px;
        }
        .stat-box {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
        }
        .quadrant-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px;
            margin-bottom: 10px;
        }
        .quadrant-card {
            border: 1px solid #ddd;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            font-size: 7pt;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
        }
        .progress {
            height: 15px;
            background-color: #f0f0f0;
            margin: 3px 0;
        }
        .progress-bar {
            height: 100%;
            font-size: 7pt;
            line-height: 15px;
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório Completo - Rede Municipal</h1>
        <div class="header-info">
            <strong>Simulado:</strong> {{ $simulados->firstWhere('id', request()->simulado_id)->nome ?? 'Todos' }} | 
            <strong>Data:</strong> {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Seção de Dados Gerais -->
    <div class="section">
        <div class="section-title">Dados Gerais</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div>Total de Alunos</div>
                <div><strong>{{ $totalAlunos }}</strong></div>
            </div>
            <div class="stat-box">
                <div>Alunos Ativos</div>
                <div><strong>{{ $alunosAtivos }}</strong></div>
                <div>{{ $totalAlunos > 0 ? number_format(($alunosAtivos/$totalAlunos)*100, 2) : 0 }}%</div>
            </div>
            <div class="stat-box">
                <div>Alunos Responderam</div>
                <div><strong>{{ $alunosResponderam }}</strong></div>
                <div>{{ $alunosAtivos > 0 ? number_format(($alunosResponderam/$alunosAtivos)*100, 2) : 0 }}%</div>
            </div>
            <div class="stat-box">
                <div>Taxa de Faltosos</div>
                <div><strong>{{ $alunosAtivos - $alunosResponderam }}</strong></div>
                <div>{{ $alunosAtivos > 0 ? number_format((($alunosAtivos - $alunosResponderam)/$alunosAtivos)*100, 2) : 0 }}%</div>
            </div>
        </div>
    </div>

    <!-- Quadrantes -->
    <div class="section">
        <div class="section-title">Análise por Quadrantes</div>
        <div class="quadrant-container">
            @foreach(['q1', 'q2', 'q3', 'q4'] as $quad)
            <div class="quadrant-card">
                <div style="background-color: {{ 
                    $quad == 'q1' ? '#28a745' : 
                    ($quad == 'q2' ? '#dc3545' : 
                    ($quad == 'q3' ? '#ffc107' : '#17a2b8')) 
                }}; color: {{ $quad == 'q3' ? '#000' : '#fff' }}; padding: 5px; font-weight: bold;">
                    @switch($quad)
                        @case('q1') Q1 - Alto Desempenho/Grande @break
                        @case('q2') Q2 - Baixo Desempenho/Grande @break
                        @case('q3') Q3 - Baixo Desempenho/Pequena @break
                        @case('q4') Q4 - Alto Desempenho/Pequena @break
                    @endswitch
                </div>
                <div style="padding: 5px; text-align: center;">
                    <div style="font-size: 14pt; font-weight: bold;">{{ $quadrantes[$quad]['count'] }}</div>
                    <div>Escolas</div>
                    <div style="font-size: 7pt;">Média TRI: {{ number_format($quadrantes[$quad]['media_tri'] ?? 0, 2) }}</div>
                </div>
            </div>
            @endforeach
        </div>
        <div style="text-align: center; margin-top: 5px; font-weight: bold;">
            Média Geral TRI: {{ number_format($mediaGeralTRI, 2) }}
        </div>
    </div>

    <!-- Análise de Desempenho -->
    <div class="section">
        <div class="section-title">Análise de Desempenho</div>
        <table class="table">
            <tr>
                <th>Métrica</th>
                <th>Peso 1</th>
                <th>Peso 2</th>
                <th>Peso 3</th>
                <th>Geral</th>
            </tr>
            <tr>
                <td>Média Tradicional</td>
                <td>{{ number_format($mediasPeso['peso_1'], 2) }}</td>
                <td>{{ number_format($mediasPeso['peso_2'], 2) }}</td>
                <td>{{ number_format($mediasPeso['peso_3'], 2) }}</td>
                <td>{{ number_format($mediasPeso['media_geral'], 2) }}</td>
            </tr>
            <tr>
                <td>Média TRI</td>
                <td>{{ number_format($analiseTRI['peso_1']['media'], 2) }}</td>
                <td>{{ number_format($analiseTRI['peso_2']['media'], 2) }}</td>
                <td>{{ number_format($analiseTRI['peso_3']['media'], 2) }}</td>
                <td>{{ number_format($analiseTRI['media_geral'], 2) }}</td>
            </tr>
        </table>
        <div style="margin-top: 5px;">
            <strong>Índice de Consistência Interna (Alpha de Cronbach):</strong> 
            {{ number_format($analiseTRI['indice_consistencia'], 2) }}
            @if($analiseTRI['indice_consistencia'] > 0.7)
                <span style="background-color: #d4edda; color: #155724; padding: 2px 4px; border-radius: 3px; font-size: 7pt;">Boa consistência</span>
            @elseif($analiseTRI['indice_consistencia'] > 0.5)
                <span style="background-color: #fff3cd; color: #856404; padding: 2px 4px; border-radius: 3px; font-size: 7pt;">Consistência moderada</span>
            @else
                <span style="background-color: #f8d7da; color: #721c24; padding: 2px 4px; border-radius: 3px; font-size: 7pt;">Baixa consistência</span>
            @endif
        </div>
    </div>

    <!-- Médias por Segmento -->
    <div class="section">
        <div class="section-title">Médias por Segmento</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 5px;">
            @foreach(['1a5', '6a9'] as $segmento)
            <div style="border: 1px solid #ddd; border-radius: 3px;">
                <div style="background-color: {{ 
                    $projecaoSegmento[$segmento]['atingiu_meta'] ? 
                    ($segmento == '1a5' ? '#28a745' : '#17a2b8') : 
                    ($segmento == '1a5' ? '#dc3545' : '#ffc107') 
                }}; color: {{ $segmento == '6a9' && !$projecaoSegmento[$segmento]['atingiu_meta'] ? '#000' : '#fff' }}; 
                padding: 5px; font-weight: bold; text-align: center;">
                    {{ $segmento == '1a5' ? '1º ao 5º Ano' : '6º ao 9º Ano' }}
                </div>
                <div style="padding: 5px; text-align: center;">
                    <div style="font-size: 12pt; font-weight: bold; margin: 3px 0;">
                        {{ number_format($projecaoSegmento[$segmento]['media'], 2) }}
                    </div>
                    <div class="progress">
                        <div style="width: {{ min(100, $projecaoSegmento[$segmento]['projecao'] * 10) }}%; 
                            background-color: {{ 
                                ($segmento == '1a5' && $projecaoSegmento[$segmento]['projecao'] >= 6) || 
                                ($segmento == '6a9' && $projecaoSegmento[$segmento]['projecao'] >= 5) ? 
                                '#28a745' : ($segmento == '1a5' ? '#dc3545' : '#ffc107') 
                            }};" class="progress-bar">
                            {{ number_format($projecaoSegmento[$segmento]['projecao'], 1) }}
                        </div>
                    </div>
                    <div style="margin-top: 3px; font-size: 8pt;">
                        Meta: {{ $segmento == '1a5' ? '6.0' : '5.0' }} | 
                        Diferença: {{ $projecaoSegmento[$segmento]['diferenca'] >= 0 ? '+' : '' }}{{ $projecaoSegmento[$segmento]['diferenca'] }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Escolas por Quadrante -->
    @foreach(['q1', 'q2', 'q3', 'q4'] as $quad)
        @if($quadrantes[$quad]['count'] > 0)
        <div class="section">
            <div class="section-title" style="background-color: {{ 
                $quad == 'q1' ? '#28a745' : 
                ($quad == 'q2' ? '#dc3545' : 
                ($quad == 'q3' ? '#ffc107' : '#17a2b8')) 
            }}; color: {{ $quad == 'q3' ? '#000' : '#fff' }};">
                {{ $quad == 'q1' ? 'Q1 - Alto Desempenho/Grande' : 
                  ($quad == 'q2' ? 'Q2 - Baixo Desempenho/Grande' : 
                  ($quad == 'q3' ? 'Q3 - Baixo Desempenho/Pequena' : 'Q4 - Alto Desempenho/Pequena')) }}
            </div>
            <table class="table">
                <tr>
                    <th>Escola</th>
                    <th>Alunos</th>
                    <th>Média Trad.</th>
                    <th>Média TRI</th>
                    <th>Diferença</th>
                </tr>
                @foreach($quadrantes[$quad]['escolas'] as $escola)
                <tr>
                    <td>{{ $escola['nome'] }}</td>
                    <td>{{ $escola['total_alunos'] }}</td>
                    <td>{{ number_format($escola['media_simulado'], 2) }}</td>
                    <td>{{ number_format($escola['media_tri'], 2) }}</td>
                    <td style="color: {{ $escola['media_tri'] >= $mediaGeralTRI ? '#28a745' : '#dc3545' }};">
                        {{ $escola['media_tri'] >= $mediaGeralTRI ? '+' : '' }}{{ number_format($escola['media_tri'] - $mediaGeralTRI, 2) }}
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif
    @endforeach

    <div class="footer">
        Relatório gerado em {{ now()->format('d/m/Y H:i') }}<br>
        Sistema EduHabil+ - Secretaria Municipal de Educação
    </div>
</body>
</html>