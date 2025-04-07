@extends('layouts.app')

@section('title', 'Gráficos de Estatísticas')

@section('content')
<div class="container">
    <h1 class="mb-4">Gráficos de Estatísticas</h1>

    <div class="mb-4">
        <canvas id="graficoQuestoes" width="400" height="200"></canvas>
    </div>

    <div class="mb-4">
        <canvas id="graficoAnos" width="400" height="200"></canvas>
    </div>

    <a href="{{ route('estatisticas.admin') }}" class="btn btn-secondary">Voltar</a>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxQuestoes = document.getElementById('graficoQuestoes').getContext('2d');
    const graficoQuestoes = new Chart(ctxQuestoes, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($estatisticasPorQuestao)) !!},
            datasets: [{
                label: '% Acertos por Questão',
                data: {!! json_encode(array_values($estatisticasPorQuestao)) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.6)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    const ctxAnos = document.getElementById('graficoAnos').getContext('2d');
    const graficoAnos = new Chart(ctxAnos, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_keys($estatisticasPorAno)) !!},
            datasets: [{
                label: '% Acertos por Ano',
                data: {!! json_encode(array_values($estatisticasPorAno)) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(153, 102, 255, 0.6)'
                ]
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
@endsection
