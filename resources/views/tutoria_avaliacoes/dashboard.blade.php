@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4">Dashboard de Escolas por Quadrante</h2>
        </div>
    </div>

    <div class="row">
        <!-- Quadrante Vermelho (1-4) -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-danger h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">Prioridade Máxima</h5>
                    <small>Notas: 1-4</small>
                </div>
                <div class="card-body">
                    <h3 class="text-center">{{ $escolasVermelho->count() }}</h3>
                    <p class="text-center">Escolas que precisam de atenção imediata</p>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('tutoria.quadrante', ['quadrante' => 'vermelho']) }}" class="btn btn-outline-danger w-100">
                        Ver Detalhes
                    </a>
                </div>
            </div>
        </div>

        <!-- Quadrante Amarelo (5-6) -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-warning h-100">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">Prioridade Média</h5>
                    <small>Notas: 5-6</small>
                </div>
                <div class="card-body">
                    <h3 class="text-center">{{ $escolasAmarelo->count() }}</h3>
                    <p class="text-center">Escolas que precisam de melhorias</p>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('tutoria.quadrante', ['quadrante' => 'amarelo']) }}" class="btn btn-outline-warning w-100">
                        Ver Detalhes
                    </a>
                </div>
            </div>
        </div>

        <!-- Quadrante Verde (7-8) -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-success h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Prioridade Baixa</h5>
                    <small>Notas: 7-8</small>
                </div>
                <div class="card-body">
                    <h3 class="text-center">{{ $escolasVerde->count() }}</h3>
                    <p class="text-center">Escolas com bom desempenho</p>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('tutoria.quadrante', ['quadrante' => 'verde']) }}" class="btn btn-outline-success w-100">
                        Ver Detalhes
                    </a>
                </div>
            </div>
        </div>

        <!-- Quadrante Azul (9-10) -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-primary h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Excelência</h5>
                    <small>Notas: 9-10</small>
                </div>
                <div class="card-body">
                    <h3 class="text-center">{{ $escolasAzul->count() }}</h3>
                    <p class="text-center">Escolas exemplares</p>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('tutoria.quadrante', ['quadrante' => 'azul']) }}" class="btn btn-outline-primary w-100">
                        Ver Detalhes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de distribuição -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Distribuição das Escolas por Quadrante</h5>
                </div>
                <div class="card-body">
                    <canvas id="quadrantesChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('quadrantesChart').getContext('2d');
        const quadrantesChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Vermelho (1-4)', 'Amarelo (5-6)', 'Verde (7-8)', 'Azul (9-10)'],
                datasets: [{
                    data: [
                        {{ $escolasVermelho->count() }},
                        {{ $escolasAmarelo->count() }},
                        {{ $escolasVerde->count() }},
                        {{ $escolasAzul->count() }}
                    ],
                    backgroundColor: [
                        '#dc3545',
                        '#ffc107',
                        '#28a745',
                        '#007bff'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    });
</script>
@endsection