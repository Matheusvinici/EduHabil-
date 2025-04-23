@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-0">Estatísticas da Escola: {{ $escola->nome }}</h2>
            @if(in_array(auth()->user()->role, ['admin', 'aplicador', 'tutor']))
            <a href="{{ route('atividades_professores.estatisticas-rede') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar para visão geral
            </a>
            @endif
        </div>
    </div>

    <!-- Cards Resumo -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $totalAtividadesEscola }}</h3>
                    <p class="card-text">Atividades Geradas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $topHabilidades->first()->total ?? 0 }}</h3>
                    <p class="card-text">Habilidade Mais Utilizada</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $atividadesPorAno->first()->total ?? 0 }}</h3>
                    <p class="card-text">Ano com Mais Atividades</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Tabelas -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Atividades Recentes</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Professor</th>
                                    <th>Disciplina</th>
                                    <th>Ano</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($atividades as $atividade)
                                <tr>
                                    <td>{{ $atividade->professor_name }}</td>
                                    <td>{{ $atividade->disciplina_nome }}</td>
                                    <td>{{ $atividade->ano_nome }}</td>
                                    <td>{{ \Carbon\Carbon::parse($atividade->created_at)->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $atividades->links() }}
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
                    @if($topHabilidades->isNotEmpty())
                    <ul class="list-group">
                        @foreach($topHabilidades as $habilidade)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $habilidade->descricao }}</span>
                            <span class="badge bg-primary rounded-pill">{{ $habilidade->total }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="alert alert-info">Nenhuma habilidade registrada ainda.</div>
                    @endif
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Atividades por Ano de Ensino</h4>
                </div>
                <div class="card-body">
                    @if($atividadesPorAno->isNotEmpty())
                    <canvas id="chartAnosEscola" height="200"></canvas>
                    @else
                    <div class="alert alert-info">Nenhuma atividade por ano registrada ainda.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($atividadesPorAno->isNotEmpty())
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartAnosEscola').getContext('2d');
    const chartAnos = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($atividadesPorAno->pluck('nome')),
            datasets: [{
                label: 'Atividades por Ano',
                data: @json($atividadesPorAno->pluck('total')),
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderColor: 'rgba(75, 192, 192, 1)',
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
@endif
@endsection