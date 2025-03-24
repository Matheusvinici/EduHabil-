@extends('layouts.app')

@section('title', 'Estatísticas de Simulados')

@section('header', 'Estatísticas de Simulados')

@section('content')
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Filtros</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('respostas_simulados.admin.estatisticas') }}" method="GET" class="row g-3">
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
                    <label for="escola_id" class="form-label">Escola:</label>
                    <select name="escola_id" id="escola_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($escolas as $escola)
                            <option value="{{ $escola->id }}" {{ $request->escola_id == $escola->id ? 'selected' : '' }}>{{ $escola->nome }}</option>
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
                <div class="col-md-3">
                    <p><strong>Total de Simulados:</strong> {{ $totalSimulados }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Total de Professores:</strong> {{ $totalProfessores }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Total de Alunos:</strong> {{ $totalAlunos }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Total de Respostas:</strong> {{ $totalRespostas }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Professores que Responderam:</strong> {{ $professoresResponderam }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Alunos que Responderam:</strong> {{ $alunosResponderam }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas por Escola -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Estatísticas por Escola</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Escola</th>
                            <th>Total de Respostas</th>
                            <th>Acertos</th>
                            <th>% de Acertos</th>
                            <th>Média Final (0-10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticasPorEscola as $estatistica)
                            <tr>
                                <td>{{ $estatistica['escola'] }}</td>
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

    <!-- Estatísticas por Ano -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Estatísticas por Ano</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Ano</th>
                            <th>Total de Respostas</th>
                            <th>Acertos</th>
                            <th>% de Acertos</th>
                            <th>Média Final (0-10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticasPorAno as $estatistica)
                            <tr>
                                <td>{{ $estatistica['ano'] }}</td>
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

    <!-- Médias Gerais -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Médias Gerais</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Média Geral (1º ao 5º Ano):</strong> {{ number_format($mediaGeral1a5, 2) }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Média Geral (6º ao 9º Ano):</strong> {{ number_format($mediaGeral6a9, 2) }}</p>
                </div>
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

    <!-- Gráficos -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Desempenho por Habilidade</h5>
                </div>
                <div class="card-body">
                    <canvas id="graficoHabilidades"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Média por Escola</h5>
                </div>
                <div class="card-body">
                    <canvas id="graficoEscolas"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para Gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico de Desempenho por Habilidade
        const ctxHabilidades = document.getElementById('graficoHabilidades').getContext('2d');
        new Chart(ctxHabilidades, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($estatisticasPorHabilidade, 'habilidade')) !!},
                datasets: [{
                    label: '% de Acertos',
                    data: {!! json_encode(array_column($estatisticasPorHabilidade, 'porcentagem_acertos')) !!},
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
                            text: 'Porcentagem de Acertos (%)'
                        }
                    }
                }
            }
        });

        // Gráfico de Média por Escola
        const ctxEscolas = document.getElementById('graficoEscolas').getContext('2d');
        new Chart(ctxEscolas, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($estatisticasPorEscola, 'escola')) !!},
                datasets: [{
                    label: 'Média Final (0-10)',
                    data: {!! json_encode(array_column($estatisticasPorEscola, 'media_final')) !!},
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
                            text: 'Média Final (0-10)'
                        }
                    }
                }
            }
        });
    </script>
@endsection