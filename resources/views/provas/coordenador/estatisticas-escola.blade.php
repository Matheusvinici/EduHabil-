@extends('layouts.app')

@section('title', 'Estatísticas da Escola')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Estatísticas da Escola: {{ $escola->nome }}</h2>
            
            @if(in_array(auth()->user()->role, ['admin', 'tutor', 'aplicador']))
            <a href="{{ route('provas.estatisticas-rede') }}" class="btn btn-secondary">
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
                    <h3 class="card-title">{{ $totalProvas }}</h3>
                    <p class="card-text">Provas Geradas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $professores->count() }}</h3>
                    <p class="card-text">Professores Ativos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $topDisciplinas->first()->total ?? 0 }}</h3>
                    <p class="card-text">Disciplina Mais Utilizada</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Tabelas -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Professores</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Professor</th>
                                    <th>Provas</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($professores as $professor)
                                <tr>
                                    <td>{{ $professor->name }}</td>
                                    <td>{{ $professor->provas_count }}</td>
                                    <td>
                                    <a href="{{ route('provas.professor.index', ['professor_id' => $professor->id]) }}" 
   class="btn btn-primary-blue">
    Ver Provas
</a>                                </tr>
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

    <!-- Provas Recentes -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">Provas Recentes</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Ano</th>
                                    <th>Disciplina</th>
                                    <th>Professor</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($provasRecentes as $prova)
                                <tr>
                                    <td>{{ $prova->nome }}</td>
                                    <td>{{ $prova->ano->nome }}</td>
                                    <td>{{ $prova->disciplina->nome }}</td>
                                    <td>{{ $prova->professor->name }}</td>
                                    <td>{{ $prova->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('provas.show', $prova->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a href="{{ route('provas.gerarPDF', $prova->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i> PDF
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
@endsection