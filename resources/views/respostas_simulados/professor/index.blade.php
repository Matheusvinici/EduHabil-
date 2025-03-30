@extends('layouts.app')

@section('title', 'Estatísticas dos Alunos')

@section('header', 'Estatísticas dos Alunos')

@section('content')
<div class="container-fluid">
    <!-- Filtros -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form id="filtroForm" action="{{ route('respostas_simulados.professor.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="simulado_id" class="form-label">Simulado:</label>
                    <select name="simulado_id" id="simulado_id" class="form-select">
                        <option value="">Todos os simulados</option>
                        @foreach ($simulados as $simulado)
                            <option value="{{ $simulado->id }}" {{ request('simulado_id') == $simulado->id ? 'selected' : '' }}>
                                {{ $simulado->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="ano_id" class="form-label">Ano:</label>
                    <select name="ano_id" id="ano_id" class="form-select">
                        <option value="">Todos os anos</option>
                        @foreach ($anos as $ano)
                            <option value="{{ $ano->id }}" {{ request('ano_id') == $ano->id ? 'selected' : '' }}>
                                {{ $ano->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="habilidade_id" class="form-label">Habilidade:</label>
                    <select name="habilidade_id" id="habilidade_id" class="form-select">
                        <option value="">Todas as habilidades</option>
                        @foreach ($habilidades as $habilidade)
                            <option value="{{ $habilidade->id }}" {{ request('habilidade_id') == $habilidade->id ? 'selected' : '' }}>
                                {{ $habilidade->descricao }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter mr-1"></i> Aplicar Filtros
                    </button>
                    <a href="{{ route('respostas_simulados.professor.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-undo mr-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Seção de Resultados (inicialmente oculta) -->
    @if(request()->anyFilled(['simulado_id', 'ano_id', 'habilidade_id']))
    <div id="resultados">
        <!-- Resumo dos Filtros -->
        <div class="alert alert-info mb-4">
            <strong>Filtros aplicados:</strong>
            @if(request('simulado_id'))
                <span class="badge bg-primary me-2">
                    Simulado: {{ $simulados->firstWhere('id', request('simulado_id'))->nome }}
                </span>
            @endif
            @if(request('ano_id'))
                <span class="badge bg-primary me-2">
                    Ano: {{ $anos->firstWhere('id', request('ano_id'))->nome }}
                </span>
            @endif
            @if(request('habilidade_id'))
                <span class="badge bg-primary me-2">
                    Habilidade: {{ $habilidades->firstWhere('id', request('habilidade_id'))->descricao }}
                </span>
            @endif
            
            <div class="btn-group float-end">
                <a href="{{ route('respostas_simulados.professor.export.pdf', request()->query()) }}" 
                   class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                </a>
                <a href="{{ route('respostas_simulados.professor.export.excel', request()->query()) }}" 
                   class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                </a>
            </div>
        </div>

        <!-- Dados Gerais -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Visão Geral</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card bg-white p-3 rounded border">
                            <h6 class="stat-title text-muted">Total de Alunos</h6>
                            <p class="stat-value display-6 text-primary">{{ $totalAlunos }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-white p-3 rounded border">
                            <h6 class="stat-title text-muted">Respostas Registradas</h6>
                            <p class="stat-value display-6 text-info">{{ $totalRespostas }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-white p-3 rounded border">
                            <h6 class="stat-title text-muted">Média Geral</h6>
                            <p class="stat-value display-6 text-success">
                                @php
                                    $medias = array_column($estatisticasPorAluno, 'media_final');
                                    $mediaGeral = count($medias) > 0 ? array_sum($medias) / count($medias) : 0;
                                @endphp
                                {{ number_format($mediaGeral, 2) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-white p-3 rounded border">
                            <h6 class="stat-title text-muted">Taxa de Acerto</h6>
                            <p class="stat-value display-6 text-warning">
                                @php
                                    $porcentagens = array_column($estatisticasPorAluno, 'porcentagem_acertos');
                                    $porcentagemMedia = count($porcentagens) > 0 ? array_sum($porcentagens) / count($porcentagens) : 0;
                                @endphp
                                {{ number_format($porcentagemMedia, 2) }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Média por Simulado -->
        @if(count($mediaTurmaPorSimulado) > 0)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Desempenho por Simulado</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Simulado</th>
                                <th>Média (0-10)</th>
                                <th>Progresso</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mediaTurmaPorSimulado as $media)
                            <tr>
                                <td>{{ $media['simulado'] }}</td>
                                <td>{{ number_format($media['media_turma'], 2) }}</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $media['media_turma'] >= 7 ? 'success' : ($media['media_turma'] >= 5 ? 'warning' : 'danger') }}" 
                                             role="progressbar" 
                                             style="width: {{ $media['media_turma'] * 10 }}%" 
                                             aria-valuenow="{{ $media['media_turma'] * 10 }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ number_format($media['media_turma'], 2) }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Desempenho dos Alunos -->
        @if(count($estatisticasPorAluno) > 0)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Desempenho Individual</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Aluno</th>
                                <th>Respostas</th>
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
                                <td>
                                    <span class="badge bg-{{ $estatistica['porcentagem_acertos'] >= 70 ? 'success' : ($estatistica['porcentagem_acertos'] >= 50 ? 'warning' : 'danger') }}">
                                        {{ number_format($estatistica['porcentagem_acertos'], 2) }}%
                                    </span>
                                </td>
                                <td>{{ number_format($estatistica['media_final'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Habilidades -->
        @if(count($estatisticasPorHabilidade) > 0)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Desempenho por Habilidade</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Habilidade</th>
                                <th>Respostas</th>
                                <th>Acertos</th>
                                <th>% Acertos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($estatisticasPorHabilidade as $estatistica)
                            <tr>
                                <td>{{ $estatistica['habilidade'] }}</td>
                                <td>{{ $estatistica['total_respostas'] }}</td>
                                <td>{{ $estatistica['acertos'] }}</td>
                                <td>
                                    <span class="badge bg-{{ $estatistica['porcentagem_acertos'] >= 70 ? 'success' : ($estatistica['porcentagem_acertos'] >= 50 ? 'warning' : 'danger') }}">
                                        {{ number_format($estatistica['porcentagem_acertos'], 2) }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i> Aplique os filtros acima para visualizar as estatísticas.
    </div>
    @endif
</div>

<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .stat-title {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    .stat-value {
        font-weight: 600;
        margin-bottom: 0;
    }
    .table th {
        white-space: nowrap;
        font-weight: 600;
    }
    .progress {
        border-radius: 10px;
        background-color: #f0f0f0;
    }
    .progress-bar {
        border-radius: 10px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #resultados {
        display: none;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostra os resultados se houver filtros aplicados
    @if(request()->anyFilled(['simulado_id', 'ano_id', 'habilidade_id']))
        document.getElementById('resultados').style.display = 'block';
    @endif

    // Adiciona máscara de carregamento ao submeter o formulário
    document.getElementById('filtroForm').addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Aplicando...';
        submitBtn.disabled = true;
    });
});
</script>
@endsection