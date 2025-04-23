@extends('layouts.app')

@section('title', 'Estatísticas da Rede')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Estatísticas da Rede</h2>
            
            <!-- Filtro por escola -->
            <form method="GET" class="form-inline mb-4">
                <select name="escola_id" class="form-control mr-2">
                    <option value="">Todas as escolas</option>
                    @foreach($escolas as $escola)
                        <option value="{{ $escola->id }}" {{ $escolaId == $escola->id ? 'selected' : '' }}>
                            {{ $escola->nome }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>
    </div>

    <!-- Cards Resumo -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $totalProvas }}</h3>
                    <p class="card-text">Provas Geradas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Tabelas -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Provas por Escola</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Escola</th>
                                    <th>Provas</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($provasPorEscola as $escola)
                                <tr>
                                    <td>{{ $escola->nome }}</td>
                                    <td>{{ $escola->total }}</td>
                                    <td>
                                    <a href="{{ route('provas.estatisticas-escola', $escola->escola_id) }}"
                                    class="btn btn-sm btn-primary">
                                            Detalhes
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Top 5 Disciplinas</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($topDisciplinas as $disciplina)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $disciplina->nome }}</span>
                            <span class="badge bg-primary rounded-pill">{{ $disciplina->total }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Provas por Ano</h4>
                </div>
                <div class="card-body">
                    <canvas id="chartProvasPorAno" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de provas por ano
    const ctx = document.getElementById('chartProvasPorAno').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($provasPorAno->pluck('nome')),
            datasets: [{
                label: 'Provas por Ano',
                data: @json($provasPorAno->pluck('total')),
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
@endsection