@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold text-primary">Relatório por Ano de Ensino</h2>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('relatorios.estatisticas-ano') }}">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="simulado_id" class="form-label">Simulado</label>
                        <select class="form-select" id="simulado_id" name="simulado_id" required>
                            <option value="">Selecione um simulado</option>
                            @foreach($simulados as $simulado)
                                <option value="{{ $simulado->id }}" {{ $simuladoId == $simulado->id ? 'selected' : '' }}>
                                    {{ $simulado->nome }} ({{ $simulado->ano->nome ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="ano_id" class="form-label">Ano de Ensino</label>
                        <select class="form-select" id="ano_id" name="ano_id">
                            <option value="">Todos os anos</option>
                            @foreach($anosEnsino as $ano)
                                <option value="{{ $ano->id }}" {{ $anoEnsinoId == $ano->id ? 'selected' : '' }}>
                                    {{ $ano->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resultados Consolidados -->
    @if($simuladoId && $consolidado->isNotEmpty())
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-white border-bottom-0 py-3">
            <h5 class="card-title mb-0 text-primary">
                <i class="fas fa-chart-pie me-2"></i>Visão Consolidada
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Segmento</th>
                            <th class="text-center">Alunos</th>
                            <th class="text-center">Média Ponderada</th>
                            <th class="text-center">Média TRI</th>
                            <th class="text-center">Diferença</th>
                            <th class="text-center">Projeção IDEB</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consolidado as $item)
                        <tr>
                            <td>{{ $item->segmento }}</td>
                            <td class="text-center">{{ $item->total_alunos }}</td>
                            <td class="text-center">{{ number_format($item->media_ponderada, 2, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($item->media_tri, 2, ',', '.') }}</td>
                            <td class="text-center {{ $item->diferenca_media_tri > 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($item->diferenca_media_tri, 2, ',', '.') }}
                            </td>
                            <td class="text-center">{{ number_format($item->projecao_ideb, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Resultados Detalhados -->
    @if($estatisticas->isNotEmpty())
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom-0 py-3">
            <h5 class="card-title mb-0 text-primary">
                <i class="fas fa-chart-bar me-2"></i>Desempenho por Ano de Ensino
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Ano de Ensino</th>
                            <th class="text-center">Alunos</th>
                            <th class="text-center">Média Ponderada</th>
                            <th class="text-center">Média TRI</th>
                            <th class="text-center">Diferença</th>
                            <th class="text-center">Projeção IDEB</th>
                            <th class="text-center">Meta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticas as $item)
                            <tr>
                                <td>{{ $item->ano_ensino }}</td>
                                <td class="text-center">{{ $item->total_alunos }}</td>
                                <td class="text-center">{{ number_format($item->media_ponderada, 2, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($item->media_tri, 2, ',', '.') }}</td>
                                <td class="text-center {{ $item->diferenca_media_tri > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($item->diferenca_media_tri, 2, ',', '.') }}
                                </td>
                                <td class="text-center">{{ number_format($item->projecao_ideb, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $item->atingiu_meta ? 'success' : 'danger' }}">
                                        {{ $item->atingiu_meta ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando {{ $estatisticas->firstItem() }} a {{ $estatisticas->lastItem() }} de {{ $estatisticas->total() }} registros
                </div>
                <div>
                    {{ $estatisticas->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
    @elseif($filtrosAplicados)
    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5">
            <div class="empty-state">
                <i class="fas fa-chart-pie fa-4x text-muted mb-4"></i>
                <h3 class="text-muted">Nenhum dado encontrado</h3>
                <p class="text-muted">
                    Não foram encontrados resultados com os filtros aplicados.
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection