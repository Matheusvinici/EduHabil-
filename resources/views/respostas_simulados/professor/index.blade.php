@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-lg">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #4a90e2;">
            <h5 class="mb-0">
                <i class="fas fa-chart-line"></i> Desempenho dos Alunos - SAEB/IDEB
            </h5>
            <div>
                <button onclick="window.print()" class="btn btn-light btn-sm me-2">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <a href="{{ route('respostas_simulados.professor.exportar.pdf', request()->all()) }}" 
                   class="btn btn-light btn-sm me-2">
                    <i class="fas fa-file-pdf text-danger"></i> PDF
                </a>
            </div>
        </div>
        
        <div class="card-body">
            @if(isset($mensagemSemTurma) && $mensagemSemTurma)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> {{ $mensagemSemTurma }}
                </div>
            @else
            <!-- Filtros -->
            <form method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="turma_id" class="form-label">Turma</label>
                        <select name="turma_id" id="turma_id" class="form-select" onchange="this.form.submit()">
                            @foreach($turmas as $turma)
                            <option value="{{ $turma->id }}" {{ ($filtros['turma_id'] ?? '') == $turma->id ? 'selected' : '' }}>
                                {{ $turma->nome_turma }} ({{ $turma->alunos->count() }} alunos)
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="simulado_id" class="form-label">Simulado</label>
                        <select name="simulado_id" id="simulado_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos os Simulados</option>
                            @foreach($simulados as $simulado)
                            <option value="{{ $simulado->id }}" {{ ($filtros['simulado_id'] ?? '') == $simulado->id ? 'selected' : '' }}>
                                {{ $simulado->nome }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="habilidade_id" class="form-label">Habilidade</label>
                        <select name="habilidade_id" id="habilidade_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Todas as Habilidades</option>
                            @foreach($habilidades as $habilidade)
                            <option value="{{ $habilidade->id }}" {{ ($filtros['habilidade_id'] ?? '') == $habilidade->id ? 'selected' : '' }}>
                                {{ $habilidade->descricao }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Resumo Estatístico -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Alunos na Turma</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAlunosTurma }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Alunos Responderam</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estatisticas->total() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Alunos Não Responderam</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $alunosSemResposta->total() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Alunos com Deficiência</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $alunosComDeficiencia }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-wheelchair fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabela de Médias -->
            @if($mediasTurma->isNotEmpty())
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color: #f8f9fa;">
                    <h6 class="m-0 font-weight-bold text-primary">Médias por Simulado</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center align-middle">
                            <thead style="background-color: #dfeaf5;">
                                <tr>
                                    <th>Simulado</th>
                                    <th>Alunos</th>
                                    <th>Média %</th>
                                    <th>Média Nota</th>
                                    <th>Desempenho</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mediasTurma as $media)
                                @php
                                    $desempenhoClass = $media['media_porcentagem'] >= 70 ? 'success' : 
                                                      ($media['media_porcentagem'] >= 50 ? 'warning' : 'danger');
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $media['simulado'] }}</td>
                                    <td>{{ $media['quantidade_alunos'] }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $media['media_porcentagem'] >= 70 ? '#28a745' : ($media['media_porcentagem'] >= 50 ? '#ffc107' : '#dc3545') }}; color: white; padding: 6px 10px; border-radius: 6px;">
                                            {{ number_format($media['media_porcentagem'], 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge fw-bold" style="background-color: #007bff; color: white; padding: 6px 10px; border-radius: 6px;">
                                            {{ number_format($media['media_nota'], 1) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $desempenhoClass }}">
                                            @if($media['media_porcentagem'] >= 70)
                                                Ótimo
                                            @elseif($media['media_porcentagem'] >= 50)
                                                Regular
                                            @else
                                                Ruim
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $mediasTurma->appends(request()->except('medias_page'))->links() }}
                    </div>
                </div>
            </div>
            @endif
            
    
            <!-- Tabela de Resultados Detalhados -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color: #f8f9fa;">
                    <h6 class="m-0 font-weight-bold text-primary">Resultados Detalhados</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Exportar:</div>
                            <a class="dropdown-item" href="{{ route('respostas_simulados.professor.exportar.pdf', request()->query()) }}">PDF</a>
                            <a class="dropdown-item" href="{{ route('respostas_simulados.professor.exportar.excel', request()->query()) }}">Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($estatisticas->isEmpty())
                        <div class="alert alert-info text-center">Nenhum resultado encontrado para os filtros selecionados</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center align-middle" width="100%" cellspacing="0">
                                <thead style="background-color: #dfeaf5;">
                                    <tr>
                                        <th>Aluno</th>
                                        <th>Turma</th>
                                        <th>Simulado</th>
                                        <th>Questões</th>
                                        <th>Acertos</th>
                                        <th>%</th>
                                        <th class="text-primary">Média</th>
                                        <th>Deficiência</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($estatisticas as $est)
                                    <tr>
                                        <td class="fw-semibold">{{ $est['aluno'] }}</td>
                                        <td>{{ $est['turma'] }}</td>
                                        <td>{{ $est['simulado'] }}</td>
                                        <td>{{ $est['total_questoes'] }}</td>
                                        <td>{{ $est['acertos'] }}</td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $est['porcentagem'] >= 70 ? '#28a745' : ($est['porcentagem'] >= 50 ? '#ffc107' : '#dc3545') }}; color: white; padding: 6px 10px; border-radius: 6px;">
                                                {{ number_format($est['porcentagem'], 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge fs-5 fw-bold" style="background-color: #007bff; color: white; padding: 8px 12px; border-radius: 8px;">
                                                {{ number_format($est['media'], 1) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($est['deficiencia'])
                                                <span class="badge bg-danger">Sim</span>
                                            @else
                                                <span class="badge bg-secondary">Não</span>
                                            @endif
                                        </td>
                                        <td>{{ $est['data']->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $estatisticas->appends(request()->except('resultados_page'))->links() }}
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Estatísticas por Habilidade -->
            @if(empty($filtros['habilidade_id']) && $estatisticasHabilidades->isNotEmpty())
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color: #f8f9fa;">
                    <h6 class="m-0 font-weight-bold text-primary">Desempenho por Habilidade</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center align-middle">
                            <thead style="background-color: #dfeaf5;">
                                <tr>
                                    <th>Habilidade</th>
                                    <th>Respostas</th>
                                    <th>Acertos</th>
                                    <th>%</th>
                                    <th>Média</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($estatisticasHabilidades as $estHab)
                                <tr>
                                    <td class="fw-semibold">{{ $estHab['habilidade'] }}</td>
                                    <td>{{ $estHab['total_respostas'] }}</td>
                                    <td>{{ $estHab['acertos'] }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $estHab['porcentagem'] >= 70 ? '#28a745' : ($estHab['porcentagem'] >= 50 ? '#ffc107' : '#dc3545') }}; color: white; padding: 6px 10px; border-radius: 6px;">
                                            {{ number_format($estHab['porcentagem'], 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge fw-bold" style="background-color: #007bff; color: white; padding: 6px 10px; border-radius: 6px;">
                                            {{ number_format($estHab['media'], 1) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $estatisticasHabilidades->appends(request()->except('habilidades_page'))->links() }}
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Alunos que não responderam -->
            @if($alunosSemResposta->isNotEmpty())
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color: #f8f9fa;">
                    <h6 class="m-0 font-weight-bold text-warning">Alunos que ainda não responderam</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center align-middle">
                            <thead style="background-color: #fff3cd;">
                                <tr>
                                    <th>Aluno</th>
                                    <th>Turma</th>
                                    <th>Deficiência</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alunosSemResposta as $aluno)
                                <tr>
                                    <td class="fw-semibold">{{ $aluno->name }}</td>
                                    <td>{{ $turmaSelecionada->nome_turma }}</td>
                                    <td>
                                        @if($aluno->deficiencia)
                                            <span class="badge bg-danger">Sim</span>
                                        @else
                                            <span class="badge bg-secondary">Não</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $alunosSemResposta->appends(request()->except('alunos_sem_resposta_page'))->links() }}
                    </div>
                </div>
            </div>
            @endif
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurações comuns
    Chart.defaults.font.family = 'Nunito, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
    Chart.defaults.color = '#858796';
    
    // Dados para os gráficos (simplificados)
    const desempenhoData = {
        otimo: {{ $estatisticas->where('porcentagem', '>=', 70)->count() }},
        regular: {{ $estatisticas->whereBetween('porcentagem', [50, 69])->count() }},
        ruim: {{ $estatisticas->where('porcentagem', '<', 50)->count() }}
    };

    const habilidadesData = {
        labels: @json($estatisticasHabilidades->pluck('habilidade')),
        valores: @json($estatisticasHabilidades->pluck('porcentagem'))
    };

    // Gráfico de Pizza (Desempenho)
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Ótimo (≥70%)', 'Regular (50-69%)', 'Ruim (<50%)'],
            datasets: [{
                data: [desempenhoData.otimo, desempenhoData.regular, desempenhoData.ruim],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                hoverBackgroundColor: ['#218838', '#e0a800', '#c82333'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const value = context.raw;
                            const percentage = Math.round((value / total) * 100);
                            return `${context.label}: ${value} (${percentage}%)`;
                        }
                    }
                },
                legend: {
                    display: false
                }
            }
        }
    });

    // Gráfico de Barras (Habilidades)
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: habilidadesData.labels,
            datasets: [{
                label: 'Desempenho (%)',
                data: habilidadesData.valores,
                backgroundColor: '#4e73df',
                hoverBackgroundColor: '#2e59d9',
                borderColor: '#4e73df',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                },
                y: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.x.toFixed(1) + '%';
                        }
                    }
                },
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endpush