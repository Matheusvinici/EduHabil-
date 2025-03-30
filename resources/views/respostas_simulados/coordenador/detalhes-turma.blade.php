@extends('layouts.app')

@section('title', 'Detalhes da Turma')

@section('header', 'Detalhes da Turma: ' . $turma->nome_turma)

@section('content')
<div class="container-fluid py-4">
    <!-- Filtros Aplicados -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Filtros Aplicados</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @if($request->simulado_id)
                <div class="col-md-3">
                    <p><strong>Simulado:</strong> {{ App\Models\Simulado::find($request->simulado_id)?->nome ?? 'N/A' }}</p>
                </div>
                @endif
                @if($request->ano_id)
                <div class="col-md-3">
                    <p><strong>Ano:</strong> {{ App\Models\Ano::find($request->ano_id)?->nome ?? 'N/A' }}</p>
                </div>
                @endif
                @if($request->habilidade_id)
                <div class="col-md-3">
                    <p><strong>Habilidade:</strong> {{ App\Models\Habilidade::find($request->habilidade_id)?->descricao ?? 'N/A' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Média Geral da Turma -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Média Geral da Turma</h5>
        </div>
        <div class="card-body">
            <div class="stat-card bg-light-blue p-3 rounded border text-center">
                <h6 class="stat-title">Média Geral (0-10)</h6>
                <p class="stat-value">{{ number_format($mediaGeralTurma, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Estatísticas por Aluno -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Estatísticas por Aluno</h5>
            <div>
                <a href="{{ route('respostas_simulados.coordenador.index', request()->query()) }}" 
                   class="btn btn-sm btn-light">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-primary bg-primary text-white">
                        <tr>
                            <th>Aluno</th>
                            <th>Total Respostas</th>
                            <th>Acertos</th>
                            <th>% Acertos</th>
                            <th>Média (0-10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticasPorAluno as $estatistica)
                            <tr>
                                <td>{{ $estatistica['aluno'] }}</td>
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