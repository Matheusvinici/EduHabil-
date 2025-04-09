@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-lg">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #4a90e2;">
            <a href="{{ route('respostas_simulados.aplicador.select') }}" class="btn btn-light btn-sm fw-bold text-primary">
                <i class="fas fa-plus-circle me-1"></i> Aplicar Novo Simulado
            </a>
            <h5 class="mb-0">
                <i class="fas fa-chart-bar"></i> Desempenho dos Alunos
            </h5>
        </div>
        <div class="card-body">
            @if($estatisticas->isEmpty())
                <div class="alert alert-info text-center">Nenhum resultado encontrado</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover text-center align-middle">
                        <thead style="background-color: #dfeaf5;">
                            <tr>
                                <th>Aluno</th>
                                <th>Simulado</th>
                                <th>Qtd. Questões</th>
                                <th>Peso Total</th>
                                <th>Peso Acertos</th>
                                <th>%</th>
                                <th class="text-primary">Média</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estatisticas as $est)
                            <tr>
                                <td class="fw-semibold">{{ $est['aluno'] }}</td>
                                <td>{{ $est['simulado'] }}</td>
                                <td>{{ $est['total_questoes'] }}</td>
                                <td>{{ $est['peso_total'] }}</td>
                                <td>{{ $est['peso_acertos'] }}</td>
                                
                                <!-- 🔹 Cores para a porcentagem -->
                                <td>
                                    @php
                                        $porcentagem = $est['porcentagem'];
                                        $cor = $porcentagem >= 75 ? '#28a745' : ($porcentagem >= 50 ? '#ffc107' : '#dc3545');
                                    @endphp
                                    <span class="badge" style="background-color: {{ $cor }}; color: white; padding: 6px 10px; border-radius: 6px;">
                                        {{ number_format($porcentagem, 1) }}%
                                    </span>
                                </td>

                                <!-- 🔹 Destaque na média -->
                                <td>
                                    <span class="badge fs-5 fw-bold" style="background-color: #007bff; color: white; padding: 8px 12px; border-radius: 8px;">
                                        {{ number_format($est['media'], 1) }}
                                    </span>
                                </td>
                                
                                <td>{{ $est['data']->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
