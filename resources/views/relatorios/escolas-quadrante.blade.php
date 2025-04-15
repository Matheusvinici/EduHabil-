@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                @switch($quadrante)
                    @case('q1') 🟩 Quadrante 1 - Alto Desempenho/Grande @break
                    @case('q2') 🟥 Quadrante 2 - Baixo Desempenho/Grande @break
                    @case('q3') 🟨 Quadrante 3 - Baixo Desempenho/Pequena @break
                    @case('q4') 🟦 Quadrante 4 - Alto Desempenho/Pequena @break
                @endswitch
            </h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Critério:</strong> {{ $tituloQuadrante }}<br>
                <strong>Média Geral TRI:</strong> {{ number_format($mediaGeralTRI, 2) }}<br>
                <strong>Total de escolas:</strong> {{ count($escolas) }}
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Escola</th>
                            <th>Total de Alunos</th>
                            <th>Média Tradicional</th>
                            <th>Média TRI</th>
                            <th>Diferença para Média Geral</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($escolas as $escola)
                            <tr>
                                <td>{{ $escola['nome'] }}</td>
                                <td>{{ $escola['total_alunos'] }}</td>
                                <td>{{ number_format($escola['media_simulado'], 2) }}</td>
                                <td>{{ number_format($escola['media_tri'], 2) }}</td>
                                <td class="{{ $escola['media_tri'] >= $mediaGeralTRI ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($escola['media_tri'] - $mediaGeralTRI, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Nenhuma escola encontrada neste quadrante</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                <a href="{{ route('relatorios.rede-municipal', $filtros) }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Voltar ao Relatório
                </a>
            </div>
        </div>
    </div>
</div>
@endsection