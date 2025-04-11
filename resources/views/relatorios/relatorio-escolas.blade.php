@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold text-primary">Relatório de Desempenho por Escola</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Escolas</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-white border-bottom-0 py-3">
            <h5 class="card-title mb-0 text-primary">
                <i class="fas fa-filter me-2"></i>Filtros
            </h5>
        </div>
        <div class="card-body pt-0">
            <form method="GET" action="{{ route('relatorios.estatisticas-escola') }}" class="needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="simulado_id" class="form-label">Simulado</label>
                        <select class="form-select select2" id="simulado_id" name="simulado_id" required>
                            <option value="">Selecione um simulado</option>
                            @foreach($simulados as $simulado)
                                <option value="{{ $simulado->id }}" {{ $simuladoId == $simulado->id ? 'selected' : '' }}>
                                    {{ $simulado->nome }} ({{ $simulado->data?->format('d/m/Y') ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            Por favor selecione um simulado.
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label for="escola_id" class="form-label">Escola</label>
                        <select class="form-select select2" id="escola_id" name="escola_id">
                            <option value="">Todas as escolas</option>
                            @foreach($escolas as $escola)
                                <option value="{{ $escola->id }}" {{ $escolaIdSelecionada == $escola->id ? 'selected' : '' }}>
                                    {{ $escola->nome }}
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
                @if($filtrosAplicados)
                <div class="row mt-3">
                    <div class="col-12">
                        <a href="{{ route('relatorios.estatisticas-escola') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Limpar filtros
                        </a>
                    </div>
                </div>
                @endif
            </form>
        </div>
    </div>

    @if(isset($estatisticasPorEscola) && $estatisticasPorEscola->isNotEmpty())
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Desempenho por Escola
                    </h5>
                    <div>
                        <a href="{{ route('relatorios.exportar-escola-excel', request()->query()) }}" 
                           class="btn btn-sm btn-success me-2" data-bs-toggle="tooltip" title="Exportar Excel">
                            <i class="fas fa-file-excel me-1"></i> Excel
                        </a>
                        <a href="{{ route('relatorios.exportar-escola-pdf', request()->query()) }}" 
                           class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Exportar PDF">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th width="30%">Escola</th>
                                <th class="text-center">Alunos Ativos</th>
                                <th class="text-center">Responderam</th>
                                <th class="text-center">Faltosos</th>
                                <th class="text-center">Média Ponderada</th>
                                <th class="text-center">Projeção TRI</th>
                                <th class="text-center">Meta Atingida</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($estatisticasPorEscola as $escola)
                            <tr>
                                <td>{{ $escola->nome }}</td>
                                <td class="text-center">{{ $escola->alunos_ativos }}</td>
                                <td class="text-center">{{ $escola->alunos_responderam }}</td>
                                <td class="text-center">{{ $escola->alunos_ativos - $escola->alunos_responderam }}</td>
                                <td class="text-center">{{ number_format($escola->media_ponderada, 2, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($escola->projecao_tri, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $escola->atingiu_meta ? 'success' : 'danger' }}">
                                        {{ $escola->atingiu_meta ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Mostrando {{ $estatisticasPorEscola->firstItem() }} a {{ $estatisticasPorEscola->lastItem() }} de {{ $estatisticasPorEscola->total() }} registros
                    </div>
                    <div>
                        {{ $estatisticasPorEscola->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-chart-pie fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted">Nenhum dado encontrado</h3>
                    <p class="text-muted">
                        @if(request()->has('simulado_id'))
                            Não foram encontradas escolas com os filtros aplicados.
                        @else
                            Selecione um simulado para visualizar os dados de desempenho por escola.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px;
        padding-top: 5px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .table th {
        white-space: nowrap;
    }
    .empty-state {
        opacity: 0.7;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Selecione",
            allowClear: true
        });

        // Tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Validação do formulário
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    });
</script>
@endpush