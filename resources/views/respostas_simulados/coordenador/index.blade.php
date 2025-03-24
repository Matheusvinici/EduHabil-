@extends('layouts.app')

@section('title', 'Estatísticas do Coordenador')

@section('header', 'Estatísticas do Coordenador')

@section('content')
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Filtros</h5>
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
                <div class="col-md-12 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Dados Gerais -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Dados Gerais</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Total de Alunos:</strong> {{ $totalAlunos }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Total de Professores:</strong> {{ $totalProfessores }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Total de Respostas:</strong> {{ $totalRespostas }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Médias por Faixa de Ano -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Médias por Faixa de Ano</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Média 1º ao 5º Ano:</strong> {{ number_format($media1a5, 2) }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Média 6º ao 9º Ano:</strong> {{ number_format($media6a9, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Média Geral da Escola -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Média Geral da Escola</h5>
        </div>
        <div class="card-body">
            <p><strong>Média Geral (0-10):</strong> {{ number_format($mediaGeralEscola, 2) }}</p>
        </div>
    </div>

    <!-- Estatísticas por Turma -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Estatísticas por Turma</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Turma</th>
                            <th>Total de Respostas</th>
                            <th>Acertos</th>
                            <th>% de Acertos</th>
                            <th>Média Final (0-10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticasPorTurma as $estatistica)
                            <tr>
                                <td>{{ $estatistica['turma'] }}</td>
                                <td>{{ $estatistica['total_respostas'] }}</td>
                                <td>{{ $estatistica['acertos'] }}</td>
                                <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                                <td>{{ number_format($estatistica['media_final'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Estatísticas por Habilidade -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Estatísticas por Habilidade</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Habilidade</th>
                            <th>Total de Respostas</th>
                            <th>Acertos</th>
                            <th>% de Acertos</th>
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

    <!-- Gráfico de Acerto por Questão -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Gráfico de Acerto por Questão</h5>
        </div>
        <div class="card-body">
            <canvas id="acertoPorQuestaoChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Gráfico de Média por Simulado -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Média por Simulado</h5>
        </div>
        <div class="card-body">
            <canvas id="mediaPorSimuladoChart" width="400" height="200"></canvas>
        </div>
    </div>

    @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Gráfico de Acerto por Questão
            const acertoPorQuestaoCtx = document.getElementById('acertoPorQuestaoChart').getContext('2d');
            const acertoPorQuestaoChart = new Chart(acertoPorQuestaoCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($questoes->pluck('id')) !!},
                    datasets: [{
                        label: 'Percentual de Acerto',
                        data: {!! json_encode($questoes->pluck('percentual_acerto')) !!},
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
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
            const mediaPorSimuladoChart = new Chart(mediaPorSimuladoCtx, {
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
@endsection