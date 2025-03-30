@extends('layouts.app')

@section('title', 'Estatísticas de Simulados - Coordenador')

@section('header', 'Estatísticas de Simulados - Coordenador')

@section('content')
<div class="container-fluid py-4">
    <!-- Filtros -->
    <div class="card mb-4 border-primary">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h5 class="card-title mb-0">Filtros</h5>
            @if(request()->hasAny(['simulado_id', 'ano_id', 'turma_id', 'habilidade_id']))
                <div>
                    <a href="{{ route('respostas_simulados.coordenador.export.pdf', request()->query()) }}" 
                       class="btn btn-sm btn-light text-primary">
                        <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                    </a>
                    <a href="{{ route('respostas_simulados.coordenador.export.excel', request()->query()) }}" 
                       class="btn btn-sm btn-light text-success ml-2">
                        <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                    </a>
                </div>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('respostas_simulados.coordenador.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="simulado_id" class="form-label">Simulado:</label>
                    <select name="simulado_id" id="simulado_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($simulados as $simulado)
                            <option value="{{ $simulado->id }}" {{ $request->simulado_id == $simulado->id ? 'selected' : '' }}>{{ $simulado->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="ano_id" class="form-label">Ano:</label>
                    <select name="ano_id" id="ano_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($anos as $ano)
                            <option value="{{ $ano->id }}" {{ $request->ano_id == $ano->id ? 'selected' : '' }}>{{ $ano->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="turma_id" class="form-label">Turma:</label>
                    <select name="turma_id" id="turma_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($turmas as $turma)
                            <option value="{{ $turma->id }}" {{ $request->turma_id == $turma->id ? 'selected' : '' }}>{{ $turma->nome_turma }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="habilidade_id" class="form-label">Habilidade:</label>
                    <select name="habilidade_id" id="habilidade_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($habilidades as $habilidade)
                            <option value="{{ $habilidade->id }}" {{ $request->habilidade_id == $habilidade->id ? 'selected' : '' }}>{{ $habilidade->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>
                    <a href="{{ route('respostas_simulados.coordenador.index') }}" class="btn btn-outline-secondary ml-2">
                        <i class="fas fa-sync-alt mr-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if(request()->hasAny(['simulado_id', 'ano_id', 'turma_id', 'habilidade_id']))
    <!-- Barra de Progresso -->
    <div class="progress mb-4" style="height: 8px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" 
             style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- Dados Gerais -->
    <div class="card mb-4 border-primary">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h5 class="card-title mb-0">Dados Gerais</h5>
            <div class="badge bg-light text-primary">
                Filtros Aplicados: 
                @if($request->simulado_id) Simulado: {{ $simulados->firstWhere('id', $request->simulado_id)->nome }} @endif
                @if($request->ano_id) | Ano: {{ $anos->firstWhere('id', $request->ano_id)->nome }} @endif
                @if($request->turma_id) | Turma: {{ $turmas->firstWhere('id', $request->turma_id)->nome_turma }} @endif
                @if($request->habilidade_id) | Habilidade: {{ $habilidades->firstWhere('id', $request->habilidade_id)->descricao }} @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-card bg-light-blue p-3 rounded border">
                        <h6 class="stat-title">Total de Alunos</h6>
                        <p class="stat-value">{{ $totalAlunos }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-light-blue p-3 rounded border">
                        <h6 class="stat-title">Total de Professores</h6>
                        <p class="stat-value">{{ $totalProfessores }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-light-blue p-3 rounded border">
                        <h6 class="stat-title">Total de Respostas</h6>
                        <p class="stat-value">{{ $totalRespostas }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Médias por Faixa de Ano -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title">Médias por Faixa de Ano</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="stat-card bg-light-blue p-3 rounded border">
                        <h6 class="stat-title">Média 1º ao 5º Ano</h6>
                        <p class="stat-value">{{ number_format($media1a5, 2) }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card bg-light-blue p-3 rounded border">
                        <h6 class="stat-title">Média 6º ao 9º Ano</h6>
                        <p class="stat-value">{{ number_format($media6a9, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Média Geral da Escola -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title">Média Geral da Escola</h5>
        </div>
        <div class="card-body">
            <div class="stat-card bg-light-blue p-3 rounded border text-center">
                <h6 class="stat-title">Média Geral (0-10)</h6>
                <p class="stat-value">{{ number_format($mediaGeralEscola, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Estatísticas por Turma -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title">Estatísticas por Turma</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-primary bg-primary text-white">
                        <tr>
                            <th>Turma</th>
                            <th>Professor</th>
                            <th>Total Respostas</th>
                            <th>Acertos</th>
                            <th>% Acertos</th>
                            <th>Média (0-10)</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticasPorTurma as $estatistica)
                            <tr>
                                <td>{{ $estatistica['turma'] }}</td>
                                <td>{{ $estatistica['professor'] ?? 'N/A' }}</td>
                                <td>{{ $estatistica['total_respostas'] }}</td>
                                <td>{{ $estatistica['acertos'] }}</td>
                                <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                                <td>{{ number_format($estatistica['media_final'], 2) }}</td>
                                <td>
                                    <a href="{{ route('respostas_simulados.coordenador.detalhes-turma', [
                                        'turma_id' => $turmas->firstWhere('nome_turma', $estatistica['turma'])->id,
                                        'simulado_id' => $request->simulado_id,
                                        'ano_id' => $request->ano_id,
                                        'habilidade_id' => $request->habilidade_id
                                    ]) }}" 
                                    class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Estatísticas por Habilidade -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title">Estatísticas por Habilidade</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-primary bg-primary text-white">
                        <tr>
                            <th>Habilidade</th>
                            <th>Total Respostas</th>
                            <th>Acertos</th>
                            <th>% Acertos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticasPorHabilidade as $estatistica)
                            <tr>
                                <td>{{ $estatistica['habilidade'] }}</td>
                                <td>{{ $estatistica['total_respostas'] }}</td>
                                <td>{{ $estatistica['acertos'] }}</td>
                                <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title">Gráfico de Acerto por Questão</h5>
                </div>
                <div class="card-body">
                    <canvas id="acertoPorQuestaoChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title">Média por Simulado</h5>
                </div>
                <div class="card-body">
                    <canvas id="mediaPorSimuladoChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Gráfico de Acerto por Questão
            const acertoPorQuestaoCtx = document.getElementById('acertoPorQuestaoChart').getContext('2d');
            new Chart(acertoPorQuestaoCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($questoes->pluck('id')) !!},
                    datasets: [{
                        label: 'Percentual de Acerto',
                        data: {!! json_encode($questoes->pluck('percentual_acerto')) !!},
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Percentual de Acerto (%)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Questões'
                            }
                        }
                    }
                }
            });

            // Gráfico de Média por Simulado
            const mediaPorSimuladoCtx = document.getElementById('mediaPorSimuladoChart').getContext('2d');
            new Chart(mediaPorSimuladoCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($mediasPorSimulado->pluck('nome')) !!},
                    datasets: [{
                        label: 'Média (0-10)',
                        data: {!! json_encode($mediasPorSimulado->pluck('media')) !!},
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 10,
                            title: {
                                display: true,
                                text: 'Média (0-10)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Simulados'
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endsection
    @endif

    <style>
        .bg-light-blue {
            background-color: #e6f2ff;
        }
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 123, 255, 0.2);
        }
        .stat-title {
            font-size: 0.9rem;
            color: #495057;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .stat-value {
            font-size: 1.75rem;
            font-weight: bold;
            margin-bottom: 0;
            color: #0066cc;
        }
        .table th {
            white-space: nowrap;
            font-weight: 600;
        }
        .table td {
            vertical-align: middle;
        }
        .card-header {
            font-weight: 600;
        }
        .border-primary {
            border-width: 2px;
        }
    </style>
</div>
@endsection