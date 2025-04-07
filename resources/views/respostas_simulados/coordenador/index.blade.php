@extends('layouts.app')

@section('title', 'Dashboard do Coordenador')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line"></i> Dashboard de Desempenho
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Simulado*</label>
                    <select name="simulado_id" class="form-select" required>
                        <option value="">Selecione um simulado</option>
                        @foreach($filtros['simulados'] as $simulado)
                            <option value="{{ $simulado->id }}" {{ $request->simulado_id == $simulado->id ? 'selected' : '' }}>
                                {{ $simulado->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ano</label>
                    <select name="ano_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($filtros['anos'] as $ano)
                            <option value="{{ $ano->id }}" {{ $request->ano_id == $ano->id ? 'selected' : '' }}>
                                {{ $ano->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Turma</label>
                    <select name="turma_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($filtros['turmas'] as $turma)
                            <option value="{{ $turma->id }}" {{ $request->turma_id == $turma->id ? 'selected' : '' }}>
                                {{ $turma->nome_turma }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Habilidade</label>
                    <select name="habilidade_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($filtros['habilidades'] as $habilidade)
                            <option value="{{ $habilidade->id }}" {{ $request->habilidade_id == $habilidade->id ? 'selected' : '' }}>
                                {{ $habilidade->descricao }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Aplicar Filtros
                    </button>
                    <a href="{{ route('respostas_simulados.coordenador.index') }}" class="btn btn-secondary">
                        <i class="fas fa-sync-alt"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($semFiltro)
        <!-- Mensagem quando não há filtro aplicado -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-filter fa-3x text-muted mb-3"></i>
                        <h3 class="text-muted">Selecione um simulado para visualizar os dados</h3>
                        <p class="text-muted">Utilize os filtros acima para escolher um simulado específico</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Resumo Estatístico -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Alunos na Escola</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAlunosEscola }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Alunos (Filtro)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAlunosFiltrados }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Responderam</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $alunosResponderam }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Faltantes ({{ number_format($percentualFaltantes, 1) }}%)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $alunosFaltantes }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-times fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Médias Gerais -->
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Média Geral da Escola</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($mediaGeralEscola, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-star fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Média 1º ao 5º Ano</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($mediaSegmentos['1a5'], 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-child fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Média 6º ao 9º Ano</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($mediaSegmentos['6a9'], 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Turmas -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Desempenho por Turma</h6>
                <div>
                    <a href="{{ route('respostas_simulados.coordenador.export', ['type' => 'pdf'] + $request->query()) }}" 
                       class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('respostas_simulados.coordenador.export', ['type' => 'excel'] + $request->query()) }}" 
                       class="btn btn-sm btn-success ml-1">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Turma</th>
                                <th>Total Alunos</th>
                                <th>Responderam</th>
                                <th>Faltantes</th>
                                <th>% Faltantes</th>
                                <th>Média</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($estatisticasPorTurma as $turma)
                            @php
                                $faltantesTurma = $turma['total_alunos'] - $turma['alunos_responderam'];
                                $percentualFaltantesTurma = $turma['total_alunos'] > 0 ? ($faltantesTurma / $turma['total_alunos']) * 100 : 0;
                            @endphp
                            <tr>
                                <td>{{ $turma['nome_turma'] }}</td>
                                <td>{{ $turma['total_alunos'] ?? 0 }}</td>
                                <td>{{ $turma['alunos_responderam'] ?? 0 }}</td>
                                <td>{{ $faltantesTurma }}</td>
                                <td>{{ number_format($percentualFaltantesTurma, 1) }}%</td>
                                <td>{{ isset($turma['media']) ? number_format($turma['media'], 2) : '0.00' }}</td>
                                <td>
                                    <a href="{{ route('respostas_simulados.coordenador.turma', $turma['id']) }}" 
                                    class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Detalhes
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Nenhum dado disponível com os filtros atuais</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Médias por Questão -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Médias por Questão</h6>
                <small>Ordenado por desempenho (menor para maior)</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Questão</th>
                                <th>Disciplina</th>
                                <th>Habilidade</th>
                                <th>% Acerto</th>
                                <th>Média (0-10)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mediasPorQuestao as $questao)
                            <tr>
                                <td>{{ $questao['enunciado'] }}</td>
                                <td>{{ $questao['disciplina'] }}</td>
                                <td>{{ $questao['habilidade'] }}</td>
                                <td>{{ number_format($questao['percentual_acerto'], 2) }}%</td>
                                <td>{{ number_format($questao['media'], 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Nenhuma questão respondida com os filtros atuais</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Questões com Melhor/Pior Desempenho -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-success text-white">
                        <h6 class="m-0 font-weight-bold">Top 5 Melhores Questões</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Questão</th>
                                        <th>Disciplina</th>
                                        <th>% Acerto</th>
                                        <th>Média</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($graficosData['questoes']['melhores'] as $questao)
                                    <tr>
                                        <td>{{ $questao['enunciado'] }}</td>
                                        <td>{{ $questao['disciplina'] }}</td>
                                        <td>{{ number_format($questao['percentual_acerto'], 2) }}%</td>
                                        <td>{{ number_format($questao['media'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-danger text-white">
                        <h6 class="m-0 font-weight-bold">Top 5 Piores Questões</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Questão</th>
                                        <th>Disciplina</th>
                                        <th>% Acerto</th>
                                        <th>Média</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($graficosData['questoes']['piores'] as $questao)
                                    <tr>
                                        <td>{{ $questao['enunciado'] }}</td>
                                        <td>{{ $questao['disciplina'] }}</td>
                                        <td>{{ number_format($questao['percentual_acerto'], 2) }}%</td>
                                        <td>{{ number_format($questao['media'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Médias por Simulado -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-info text-white">
                <h6 class="m-0 font-weight-bold">Desempenho no Simulado</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Simulado</th>
                                <th>Média (0-10)</th>
                                <th>Respostas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mediasPorSimulado as $simulado)
                            <tr>
                                <td>{{ $simulado['nome'] }}</td>
                                <td>{{ number_format($simulado['media'], 2) }}</td>
                                <td>{{ $simulado['total_respostas'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">Nenhum dado disponível</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

@if(!$semFiltro)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Segmentos
    const segmentoCtx = document.getElementById('segmentoChart');
    if (segmentoCtx) {
        new Chart(segmentoCtx, {
            type: 'bar',
            data: {
                labels: ['1º ao 5º Ano', '6º ao 9º Ano'],
                datasets: [{
                    label: 'Média (0-10)',
                    data: [{{ $mediaSegmentos['1a5'] }}, {{ $mediaSegmentos['6a9'] }}],
                    backgroundColor: ['#4e73df', '#1cc88a'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673'],
                    borderColor: "rgba(234, 236, 244, 1)",
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    }

    // Gráfico de Habilidades
    const habilidadesCtx = document.getElementById('habilidadesChart');
    if (habilidadesCtx) {
        new Chart(habilidadesCtx, {
            type: 'doughnut',
            data: {
                labels: @json($graficosData['habilidades']->pluck('descricao')),
                datasets: [{
                    data: @json($graficosData['habilidades']->pluck('porcentagem_acertos')),
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#858796', '#5a5c69', '#3a3b45', '#2c3e50', '#18bc9c'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw.toFixed(2) + '%';
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                    }
                },
                cutout: '70%'
            }
        });
    }
});
</script>
@endpush
@endif
@endsection