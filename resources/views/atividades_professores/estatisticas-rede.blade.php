@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-0">Estatísticas da Rede</h2>
        </div>
    </div>

    <!-- Cards Resumo -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $totalAtividades }}</h3>
                    <p class="card-text">Atividades Geradas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $atividadesPorEscola->count() }}</h3>
                    <p class="card-text">Escolas Ativas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $topHabilidades->first()->total ?? 0 }}</h3>
                    <p class="card-text">Habilidade Mais Utilizada</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Tabelas -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Atividades por Escola</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Escola</th>
                                    <th>Atividades</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($atividadesPorEscola as $escola)
                            <tr>
                                <td>{{ $escola->nome }}</td>
                                <td>{{ $escola->total }}</td>
                                <td>
                                <a href="{{ route('atividades_professores.estatisticas-escola', $escola->escola_id) }}" class="btn btn-sm btn-primary">
    <i class="fas fa-chart-bar"></i> Ver Estatísticas
</a>                                </td>
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
                    <h4 class="mb-0">Top 5 Habilidades</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($topHabilidades as $habilidade)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <strong>{{ $habilidade->descricao }}</strong> 
                            </span>
                            <span class="badge bg-primary rounded-pill">{{ $habilidade->total }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Atividades por Ano de Ensino</h4>
                </div>
                <div class="card-body">
                    <canvas id="chartAnos" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de atividades por ano
    const ctx = document.getElementById('chartAnos').getContext('2d');
    const chartAnos = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($atividadesPorAno->pluck('nome')),
            datasets: [{
                label: 'Atividades por Ano',
                data: @json($atividadesPorAno->pluck('total')),
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