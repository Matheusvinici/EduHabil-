@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Filtros -->
    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter mr-2"></i>Filtros
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('relatorios.rede-municipal') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="simulado_id">Simulado</label>
                            <select class="form-control" name="simulado_id" id="simulado_id" required>
                                <option value="">Selecione um simulado</option>
                                @foreach($simulados as $simulado)
                                    <option value="{{ $simulado->id }}" {{ request('simulado_id') == $simulado->id ? 'selected' : '' }}>
                                        {{ $simulado->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                   
                  
                </div>

                <div class="row mt-2">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <a href="{{ route('relatorios.rede-municipal') }}" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    @if(request()->has('simulado_id'))
    <!-- Botões de Exportação -->
    <div class="row mb-4">
        <div class="col-md-12 text-right">
        <div class="mt-3 d-flex flex-wrap gap-2">
    <a href="{{ route('relatorios.rede-municipal.pdf', [
        'simulado_id' => request('simulado_id'),
    ]) }}" class="btn btn-danger" target="_blank">
        <i class="fas fa-file-pdf"></i> Gerar PDF
    </a>
    
    <form action="{{ route('relatorios.exportar-excel') }}" method="POST" class="d-inline">
        @csrf
        <!-- Campos ocultos com os parâmetros do filtro -->
        <input type="hidden" name="simulado_id" value="{{ request('simulado_id') }}">
    
        <button type="submit" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </button>
    </form>
</div>
        </div>
    </div>

    <!-- Barra de Progresso -->
    <div class="progress mb-4" style="height: 8px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" 
             style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- Dados Gerais -->
    <div class="card mb-4 shadow">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h5 class="card-title mb-0">Dados Gerais</h5>
            <div class="badge bg-light text-primary">
                Filtros Aplicados: 
                @if(request()->simulado_id) Simulado: {{ $simulados->firstWhere('id', request()->simulado_id)->nome }} @endif
                @if(request()->ano_id) | Ano: {{ $anos->firstWhere('id', request()->ano_id)->nome }} @endif
                @if(request()->escola_id) | Escola: {{ $escolas->firstWhere('id', request()->escola_id)->nome }} @endif
                @if(request()->deficiencia) | Deficiência: 
                    @switch(request()->deficiencia)
                        @case('DV') Visual @break
                        @case('DA') Auditiva @break
                        @case('DF') Física @break
                        @case('DI') Intelectual @break
                        @case('TEA') Autismo @break
                        @case('ND') Sem deficiência @break
                    @endswitch
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card bg-light p-3 rounded shadow-sm">
                        <h6 class="stat-title">Total de Alunos</h6>
                        <p class="stat-value">{{ $totalAlunos }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-light p-3 rounded shadow-sm">
                        <h6 class="stat-title">Alunos Ativos</h6>
                        <p class="stat-value">{{ $alunosAtivos }}</p>
                        <small class="text-muted">{{ $totalAlunos > 0 ? number_format(($alunosAtivos/$totalAlunos)*100, 2) : 0 }}%</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-light p-3 rounded shadow-sm">
                        <h6 class="stat-title">Alunos Responderam</h6>
                        <p class="stat-value">{{ $alunosResponderam }}</p>
                        <small class="text-muted">{{ $alunosAtivos > 0 ? number_format(($alunosResponderam/$alunosAtivos)*100, 2) : 0 }}%</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-light p-3 rounded shadow-sm">
                        <h6 class="stat-title">Taxa de Faltosos</h6>
                        <p class="stat-value">{{ $alunosAtivos - $alunosResponderam }}</p>
                        <small class="text-muted">{{ $alunosAtivos > 0 ? number_format((($alunosAtivos - $alunosResponderam)/$alunosAtivos)*100, 2) : 0 }}%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Médias Ponderadas e TRI -->
    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Análise de Desempenho</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th rowspan="2">Métrica</th>
                            <th colspan="3" class="text-center">Métricas por Peso</th>
                            <th rowspan="2" class="text-center">Geral</th>
                        </tr>
                        <tr>
                            <th class="text-center">Peso 1</th>
                            <th class="text-center">Peso 2</th>
                            <th class="text-center">Peso 3</th>
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
            </div>
            <div class="mt-3">
                <div class="alert alert-info">
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
        </div>
    </div>

    <!-- Gráfico de Dispersão por Escola -->
    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Desempenho das Escolas por Tamanho e Nota Média</h5>
        </div>
        <div class="card-body">
            <div class="chart-container" style="position: relative; height: 400px;">
                <canvas id="graficoDispersao"></canvas>
            </div>
            <div class="mt-3 text-center">
                <p class="text-muted">
                    Total de escolas: {{ count($dadosEscolas) }} | 
                    Média Geral TRI: {{ number_format($mediaGeralTRI, 2) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Quadrantes de Desempenho -->
    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Análise por Quadrantes</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <strong>Média Geral TRI da Rede:</strong> {{ number_format($mediaGeralTRI, 2) }}
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Quadrante 1 -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">🟩 Q1 - Alto Desempenho/Grande</h5>
                        </div>
                        <div class="card-body text-center">
                            <h3 class="text-success">{{ $quadrantes['q1']['count'] }}</h3>
                            <p class="mb-1">Escolas</p>
                            <div class="progress mt-3">
                                <div class="progress-bar bg-success" style="width: {{ $quadrantes['q1']['count'] > 0 ? 100 : 0 }}%">
                                    Média TRI: {{ number_format($quadrantes['q1']['media_tri'], 2) }}
                                </div>
                            </div>
                            <p class="mt-2 small text-muted">200+ alunos e nota TRI acima da média</p>
                            @if($quadrantes['q1']['count'] > 0)
                                <div class="mt-3">
                                    <a href="{{ route('relatorios.escolas-quadrante', [
                                        'simulado_id' => request('simulado_id'),
                                        'quadrante' => 'q1',
                                        'ano_id' => request('ano_id'),
                                        'deficiencia' => request('deficiencia')
                                    ]) }}" class="btn btn-sm btn-outline-success">
                                        Ver Escolas
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Quadrante 2 -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">🟥 Q2 - Baixo Desempenho/Grande</h5>
                        </div>
                        <div class="card-body text-center">
                            <h3 class="text-danger">{{ $quadrantes['q2']['count'] }}</h3>
                            <p class="mb-1">Escolas</p>
                            <div class="progress mt-3">
                                <div class="progress-bar bg-danger" style="width: {{ $quadrantes['q2']['count'] > 0 ? 100 : 0 }}%">
                                    Média TRI: {{ number_format($quadrantes['q2']['media_tri'], 2) }}
                                </div>
                            </div>
                            <p class="mt-2 small text-muted">200+ alunos e nota TRI abaixo da média</p>
                            @if($quadrantes['q2']['count'] > 0)
                                <div class="mt-3">
                                    <a href="{{ route('relatorios.escolas-quadrante', [
                                        'simulado_id' => request('simulado_id'),
                                        'quadrante' => 'q2',
                                        'ano_id' => request('ano_id'),
                                        'deficiencia' => request('deficiencia')
                                    ]) }}" class="btn btn-sm btn-outline-danger">
                                        Ver Escolas
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Quadrante 3 -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-warning">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0">🟨 Q3 - Baixo Desempenho/Pequena</h5>
                        </div>
                        <div class="card-body text-center">
                            <h3 class="text-warning">{{ $quadrantes['q3']['count'] }}</h3>
                            <p class="mb-1">Escolas</p>
                            <div class="progress mt-3">
                                <div class="progress-bar bg-warning" style="width: {{ $quadrantes['q3']['count'] > 0 ? 100 : 0 }}%">
                                    Média TRI: {{ number_format($quadrantes['q3']['media_tri'], 2) }}
                                </div>
                            </div>
                            <p class="mt-2 small text-muted">Menos de 200 alunos e nota TRI abaixo da média</p>
                            @if($quadrantes['q3']['count'] > 0)
                                <div class="mt-3">
                                    <a href="{{ route('relatorios.escolas-quadrante', [
                                        'simulado_id' => request('simulado_id'),
                                        'quadrante' => 'q3',
                                        'ano_id' => request('ano_id'),
                                        'deficiencia' => request('deficiencia')
                                    ]) }}" class="btn btn-sm btn-outline-warning">
                                        Ver Escolas
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Quadrante 4 -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">🟦 Q4 - Alto Desempenho/Pequena</h5>
                        </div>
                        <div class="card-body text-center">
                            <h3 class="text-info">{{ $quadrantes['q4']['count'] }}</h3>
                            <p class="mb-1">Escolas</p>
                            <div class="progress mt-3">
                                <div class="progress-bar bg-info" style="width: {{ $quadrantes['q4']['count'] > 0 ? 100 : 0 }}%">
                                    Média TRI: {{ number_format($quadrantes['q4']['media_tri'], 2) }}
                                </div>
                            </div>
                            <p class="mt-2 small text-muted">Menos de 200 alunos e nota TRI acima da média</p>
                            @if($quadrantes['q4']['count'] > 0)
                                <div class="mt-3">
                                    <a href="{{ route('relatorios.escolas-quadrante', [
                                        'simulado_id' => request('simulado_id'),
                                        'quadrante' => 'q4',
                                        'ano_id' => request('ano_id'),
                                        'deficiencia' => request('deficiencia')
                                    ]) }}" class="btn btn-sm btn-outline-info">
                                        Ver Escolas
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparação Média Tradicional vs TRI -->
    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Comparação Média Tradicional vs TRI</h5>
        </div>
        <div class="card-body">
            <div class="chart-container" style="position: relative; height:400px;">
                <canvas id="graficoComparacao"></canvas>
            </div>
        </div>
    </div>

    <!-- Médias por Segmento -->
    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Médias por Segmento</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm {{ $projecaoSegmento['1a5']['atingiu_meta'] ? 'border-success' : 'border-danger' }}">
                        <div class="card-header {{ $projecaoSegmento['1a5']['atingiu_meta'] ? 'bg-success' : 'bg-danger' }} text-white">
                            <h6>1º ao 5º Ano</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Média Tradicional</h6>
                                    <div class="h4">{{ number_format($projecaoSegmento['1a5']['media'], 2) }}</div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Média TRI</h6>
                                    <div class="h4">{{ number_format($projecaoSegmento['1a5']['media_tri'], 2) }}</div>
                                </div>
                            </div>
                            <div class="progress mt-3" style="height: 25px;">
                                <div class="progress-bar {{ $projecaoSegmento['1a5']['projecao'] >= 6 ? 'bg-success' : 'bg-danger' }}" 
                                     style="width: {{ $projecaoSegmento['1a5']['projecao'] * 10 }}%">
                                    <strong>Projeção TRI: {{ number_format($projecaoSegmento['1a5']['projecao'], 1) }}</strong>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="badge {{ $projecaoSegmento['1a5']['atingiu_meta'] ? 'bg-success' : 'bg-danger' }}">
                                    Meta: 6.0 | Diferença: {{ $projecaoSegmento['1a5']['diferenca'] >= 0 ? '+' : '' }}{{ $projecaoSegmento['1a5']['diferenca'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm {{ $projecaoSegmento['6a9']['atingiu_meta'] ? 'border-success' : 'border-warning' }}">
                        <div class="card-header {{ $projecaoSegmento['6a9']['atingiu_meta'] ? 'bg-success' : 'bg-warning' }} text-white">
                            <h6>6º ao 9º Ano</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Média Tradicional</h6>
                                    <div class="h4">{{ number_format($projecaoSegmento['6a9']['media'], 2) }}</div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Média TRI</h6>
                                    <div class="h4">{{ number_format($projecaoSegmento['6a9']['media_tri'], 2) }}</div>
                                </div>
                            </div>
                            <div class="progress mt-3" style="height: 25px;">
                                <div class="progress-bar {{ $projecaoSegmento['6a9']['projecao'] >= 5 ? 'bg-success' : 'bg-warning' }}" 
                                     style="width: {{ $projecaoSegmento['6a9']['projecao'] * 10 }}%">
                                    <strong>Projeção TRI: {{ number_format($projecaoSegmento['6a9']['projecao'], 1) }}</strong>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="badge {{ $projecaoSegmento['6a9']['atingiu_meta'] ? 'bg-success' : 'bg-warning' }}">
                                    Meta: 5.0 | Diferença: {{ $projecaoSegmento['6a9']['diferenca'] >= 0 ? '+' : '' }}{{ $projecaoSegmento['6a9']['diferenca'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else
    <div class="card mb-4 shadow">
        <div class="card-body text-center py-5">
            <h4><i class="fas fa-filter fa-2x mb-3 text-muted"></i></h4>
            <p class="text-muted">Selecione um simulado para visualizar os dados</p>
        </div>
    </div>
    @endif
</div>

@if(request()->has('simulado_id'))
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.0.2"></script>
<script>
// Gráfico de Dispersão
const ctxDispersao = document.getElementById('graficoDispersao').getContext('2d');
const escolasData = @json($dadosEscolas);
const mediaGeral = {{ $mediaGeralTRI }};

// Preparar dados para o gráfico
const pontos = escolasData.map(escola => ({
    x: escola.total_alunos,
    y: escola.media_tri,
    nome: escola.nome,
    quadrante: escola.total_alunos >= 200
    ? (escola.media_tri >= mediaGeral ? 'q1' : 'q2')
    : (escola.media_tri >= mediaGeral ? 'q4' : 'q3'),
    media_tradicional: escola.media_simulado
}));

new Chart(ctxDispersao, {
    type: 'scatter',
    data: {
        datasets: [
            {
                label: 'Q1 - Grande/Acima',
                data: pontos.filter(p => p.quadrante === 'q1'),
                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1,
                pointRadius: 8,
                pointHoverRadius: 10
            },
            {
                label: 'Q2 - Grande/Abaixo',
                data: pontos.filter(p => p.quadrante === 'q2'),
                backgroundColor: 'rgba(220, 53, 69, 0.7)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 1,
                pointRadius: 8,
                pointHoverRadius: 10
            },
            {
                label: 'Q3 - Pequena/Abaixo',
                data: pontos.filter(p => p.quadrante === 'q3'),
                backgroundColor: 'rgba(255, 193, 7, 0.7)',
                borderColor: 'rgba(255, 193, 7, 1)',
                borderWidth: 1,
                pointRadius: 8,
                pointHoverRadius: 10
            },
            {
                label: 'Q4 - Pequena/Acima',
                data: pontos.filter(p => p.quadrante === 'q4'),
                backgroundColor: 'rgba(23, 162, 184, 0.7)',
                borderColor: 'rgba(23, 162, 184, 1)',
                borderWidth: 1,
                pointRadius: 8,
                pointHoverRadius: 10
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Desempenho das Escolas por Tamanho e Nota Média TRI',
                font: { size: 16 }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return [
                            `${context.raw.nome}`,
                            `Alunos: ${context.raw.x}`,
                            `Média TRI: ${context.raw.y.toFixed(2)}`,
                            `Média Tradicional: ${context.raw.media_tradicional.toFixed(2)}`
                        ];
                    }
                }
            },
            legend: { position: 'bottom' },
            annotation: {
                annotations: {
                    line1: {
                        type: 'line',
                        yMin: mediaGeral,
                        yMax: mediaGeral,
                        borderColor: 'rgb(75, 75, 75)',
                        borderWidth: 2,
                        borderDash: [6, 6],
                        label: {
                            content: 'Média Geral TRI: ' + mediaGeral.toFixed(2),
                            enabled: true,
                            position: 'left'
                        }
                    },
                    line2: {
                        type: 'line',
                        xMin: 200,
                        xMax: 200,
                        borderColor: 'rgb(75, 75, 75)',
                        borderWidth: 2,
                        borderDash: [6, 6],
                        label: {
                            content: 'Limite de tamanho (200 alunos)',
                            enabled: true,
                            position: 'top'
                        }
                    }
                }
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Número de Alunos'
                },
                min: 0,
                max: Math.max(...escolasData.map(e => e.total_alunos)) * 1.1
            },
            y: {
                title: {
                    display: true,
                    text: 'Nota Média TRI'
                },
                min: 0,
                max: 10
            }
        }
    }
});

// Gráfico de Comparação Média vs TRI
const ctxComparacao = document.getElementById('graficoComparacao').getContext('2d');
new Chart(ctxComparacao, {
    type: 'bar',
    data: {
        labels: ['Peso 1', 'Peso 2', 'Peso 3', 'Geral'],
        datasets: [
            {
                label: 'Média Tradicional',
                data: [
                    {{ $mediasPeso['peso_1'] }},
                    {{ $mediasPeso['peso_2'] }},
                    {{ $mediasPeso['peso_3'] }},
                    {{ $mediasPeso['media_geral'] }}
                ],
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                label: 'Média TRI',
                data: [
                    {{ $analiseTRI['peso_1']['media'] }},
                    {{ $analiseTRI['peso_2']['media'] }},
                    {{ $analiseTRI['peso_3']['media'] }},
                    {{ $analiseTRI['media_geral'] }}
                ],
                backgroundColor: 'rgba(255, 159, 64, 0.7)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Comparação entre Média Tradicional e Média TRI',
                font: { size: 16 }
            },
            legend: { position: 'bottom' },
            tooltip: {
                callbacks: {
                    afterBody: function(context) {
                        const index = context[0].dataIndex;
                        const dificuldades = {
                            0: {{ $analiseTRI['peso_1']['dificuldade'] }},
                            1: {{ $analiseTRI['peso_2']['dificuldade'] }},
                            2: {{ $analiseTRI['peso_3']['dificuldade'] }}
                        };
                        const discriminacoes = {
                            0: {{ $analiseTRI['peso_1']['discriminacao'] }},
                            1: {{ $analiseTRI['peso_2']['discriminacao'] }},
                            2: {{ $analiseTRI['peso_3']['discriminacao'] }}
                        };
                        
                        if (index < 3) {
                            return [
                                `Dificuldade: ${dificuldades[index].toFixed(2)}`,
                                `Discriminação: ${discriminacoes[index].toFixed(2)}`
                            ];
                        }
                        return `Consistência: {{ number_format($analiseTRI['indice_consistencia'], 2) }}`;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 10,
                title: {
                    display: true,
                    text: 'Nota Média'
                }
            }
        }
    }
});
</script>
@endif

<style>
.stat-card {
    transition: transform 0.2s;
    border-left: 4px solid #4e73df;
    border-radius: 8px;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.stat-title {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
    font-weight: 600;
}
.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.2rem;
    color: #2c3e50;
}
.chart-container {
    min-height: 300px;
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.card-header {
    font-weight: 600;
}
.progress {
    border-radius: 20px;
    background: #e9ecef;
}
.progress-bar {
    border-radius: 20px;
}
.table {
    border-radius: 8px;
    overflow: hidden;
}
.table thead th {
    background-color: #f8f9fa;
    font-weight: 600;
}
.alert {
    border-radius: 8px;
}
.badge {
    font-weight: 500;
}
</style>
@endsection