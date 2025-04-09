@extends('layouts.app')

@section('title', 'Detalhes da Escola')

@section('header', 'Detalhes da Escola: ' . $escola->nome)

@section('content')
<div class="container-fluid py-4">

    <!-- Média Geral da Escola -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Média Geral da Escola</h5>
        </div>
        <div class="card-body">
            <div class="stat-card bg-light-blue p-3 rounded border text-center">
                <h6 class="stat-title">Média Geral</h6>
                <p class="stat-value">{{ number_format($estatisticas->avg('media'), 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Estatísticas por Aluno -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Estatísticas por Aluno</h5>
            <div>
                <a href="{{ route('respostas_simulados.admin.estatisticas') }}" 
                   class="btn btn-sm btn-light">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered  table-hover">
                    <thead class=" bg-primary text-white">
                        <tr>
                            <th>Aluno</th>
                            <th>Turma</th>
                            <th>Simulado</th>
                            <th>Total Respostas</th>
                            <th>Peso Total</th>
                            <th>Acertos</th>
                            <th>% Acertos</th>
                            <th>Média (0-10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticas as $estatistica)
                            <tr>
                                <td>{{ $estatistica['aluno'] }}</td>
                                <td>{{ $estatistica['nome_turma'] }}</td>
                                <td>{{ $estatistica['simulado'] }}</td>
                                <td>{{ $estatistica['total_questoes'] }}</td>
                                <td>{{ $estatistica['peso_total'] }}</td>
                                <td>{{ $estatistica['peso_acertos'] }}</td>
                                <td>{{ number_format($estatistica['porcentagem'], 2) }}%</td>
                                <td>{{ number_format($estatistica['media'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-blue {
        background-color: #e6f2ff;
    }
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-title {
        font-size: 1rem;
        color: #495057;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0;
        color: #0066cc;
    }
</style>
@endsection
