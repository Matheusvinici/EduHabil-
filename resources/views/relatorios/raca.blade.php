@extends('layouts.app')

@section('title', 'Estatísticas por Raça/Cor')

@section('header', 'Estatísticas por Raça/Cor')

@section('content')
<div class="container-fluid py-4">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h5 class="card-title mb-0">Filtros</h5>
            @if(request()->hasAny(['simulado_id', 'ano_id', 'disciplina_id', 'raca']))
                <div>
                    <a href="{{ route('relatorios.raca.pdf', request()->query()) }}"
                       class="btn btn-sm btn-light text-danger">
                        <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                    </a>
                    <a href="{{ route('relatorios.raca.excel', request()->query()) }}"
                       class="btn btn-sm btn-light text-success ml-2">
                        <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                    </a>
                </div>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('relatorios.raca') }}" method="GET" class="row g-3">
                <div class="col-md-3 col-sm-6 mb-3">
                    <label for="simulado_id" class="form-label">Simulado:</label>
                    <select name="simulado_id" id="simulado_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($simulados as $simulado)
                            <option value="{{ $simulado->id }}" {{ request('simulado_id') == $simulado->id ? 'selected' : '' }}>
                                {{ $simulado->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
           
               
                <div class="col-md-3 col-sm-6 mb-3">
                    <label for="raca" class="form-label">Raça/Cor:</label>
                    <select name="raca" id="raca" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($racasDisponiveis as $racaOpcao)
                            <option value="{{ $racaOpcao }}" {{ request('raca') == $racaOpcao ? 'selected' : '' }}>
                                {{ $racaOpcao ?: 'Não informado' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>
                    <a href="{{ route('relatorios.raca') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-sync-alt mr-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if(request()->hasAny(['simulado_id', 'ano_id', 'disciplina_id', 'raca']))
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">Estatísticas por Raça/Cor</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Raça/Cor</th>
                                <th class="text-center">Total Respostas</th>
                                <th class="text-center">% do Total</th>
                                <th class="text-center">Acertos</th>
                                <th class="text-center">% Acerto</th>
                                <th class="text-center">Média Ponderada (0-10)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($estatisticasPorRaca as $raca)
                                <tr>
                                    <td>{{ $raca->raca ?: 'Não informado' }}</td>
                                    <td class="text-center">{{ $raca->total_respostas }}</td>
                                    <td class="text-center">{{ $raca->percentual_total ?? 0 }}%</td>
                                    <td class="text-center">{{ $raca->acertos }}</td>
                                    <td class="text-center">{{ $raca->percentual_acerto }}%</td>
                                    <td class="text-center">{{ $raca->media_ponderada }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nenhuma estatística encontrada com os filtros selecionados.</td>
                                </tr>
                            @endforelse
                            @if($estatisticasPorRaca->isNotEmpty())
                                <tr class="table-primary">
                                    <td><strong>Total Geral</strong></td>
                                    <td class="text-center"><strong>{{ $totalRespostas }}</strong></td>
                                    <td class="text-center"><strong>100%</strong></td>
                                    <td class="text-center"><strong>{{ $totalAcertos }}</strong></td>
                                    <td class="text-center">
                                        <strong>{{ $totalRespostas > 0 ? round(($totalAcertos / $totalRespostas) * 100, 2) : 0 }}%</strong>
                                    </td>
                                    <td class="text-center">
                                        @if($estatisticasPorRaca->isNotEmpty())
                                            <strong>{{ round($estatisticasPorRaca->avg('media_ponderada'), 2) }}</strong>
                                        @else
                                            <strong>0</strong>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info" role="alert">
            Selecione os filtros acima para visualizar as estatísticas por raça/cor.
        </div>
    @endif
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Atualizar automaticamente ao mudar os filtros
            $('#simulado_id, #ano_id, #disciplina_id, #raca').change(function() {
                $('form').submit();
            });
        });
    </script>
@endpush