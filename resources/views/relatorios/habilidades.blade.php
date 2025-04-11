@extends('layouts.app')

@section('title', 'Estatísticas de Habilidades por Disciplina')

@section('header', 'Estatísticas de Habilidades por Disciplina')

@section('content')
<div class="container-fluid py-4">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h5 class="card-title mb-0">Filtros</h5>
            @if(request()->hasAny(['simulado_id', 'disciplina_id', 'ano_id', 'habilidade_id']))
                <div>
                    <a href="{{ route('relatorios.habilidades.pdf', request()->query()) }}"
                       class="btn btn-sm btn-light text-danger">
                        <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                    </a>
                    <a href="{{ route('relatorios.habilidades.excel', request()->query()) }}"
                       class="btn btn-sm btn-light text-success ml-2">
                        <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                    </a>
                </div>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('relatorios.habilidades') }}" method="GET" class="row g-3">
                <div class="col-md-3 col-sm-6 mb-3">
                    <label for="simulado_id" class="form-label">Simulado:</label>
                    <select name="simulado_id" id="simulado_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($simulados as $simulado)
                            <option value="{{ $simulado->id }}" {{ isset($filtros['simulado_id']) && $filtros['simulado_id'] == $simulado->id ? 'selected' : '' }}>{{ $simulado->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <label for="disciplina_id" class="form-label">Disciplina:</label>
                    <select name="disciplina_id" id="disciplina_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($disciplinas as $disciplina)
                            <option value="{{ $disciplina->id }}" {{ isset($filtros['disciplina_id']) && $filtros['disciplina_id'] == $disciplina->id ? 'selected' : '' }}>{{ $disciplina->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-6 mb-3">
                    <label for="ano_id" class="form-label">Ano:</label>
                    <select name="ano_id" id="ano_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($anos as $ano)
                            <option value="{{ $ano->id }}" {{ isset($filtros['ano_id']) && $filtros['ano_id'] == $ano->id ? 'selected' : '' }}>{{ $ano->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <label for="habilidade_id" class="form-label">Habilidade:</label>
                    <select name="habilidade_id" id="habilidade_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($habilidades as $habilidade)
                            <option value="{{ $habilidade->id }}" {{ isset($filtros['habilidade_id']) && $filtros['habilidade_id'] == $habilidade->id ? 'selected' : '' }}>{{ $habilidade->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>
                    <a href="{{ route('relatorios.habilidades') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-sync-alt mr-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if(request()->hasAny(['simulado_id', 'disciplina_id', 'ano_id', 'habilidade_id']))
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">Estatísticas de Habilidades por Disciplina</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Disciplina</th>
                                <th>Habilidade</th>
                                <th>Descrição</th>
                                <th class="text-center">Total Questões</th>
                                <th class="text-center">Total Respostas</th>
                                <th class="text-center">Acertos</th>
                                <th class="text-center">Média Simples</th>
                                <th class="text-center">Média Ponderada</th>
                                <th class="text-center">% Acerto</th>
                                <th class="text-center">TRI Médio (0-10)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($estatisticasPorHabilidade as $habilidade)
                                <tr>
                                    <td>{{ $habilidade->disciplina_nome }}</td>
                                    <td>{{ $habilidade->descricao }}</td>
                                    <td>{{ $habilidade->descricao }}</td>
                                    <td class="text-center">{{ $habilidade->total_questoes }}</td>
                                    <td class="text-center">{{ $habilidade->total_respostas }}</td>
                                    <td class="text-center">{{ $habilidade->acertos }}</td>
                                    <td class="text-center">{{ $habilidade->media_simples }}</td>
                                    <td class="text-center">{{ $habilidade->media_ponderada }}</td>
                                    <td class="text-center">{{ $habilidade->percentual_acerto }}%</td>
                                    <td class="text-center">{{ $habilidade->tri_medio }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center">Nenhuma estatística de habilidade encontrada com os filtros selecionados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $estatisticasPorHabilidade->appends($filtros)->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info" role="alert">
            Selecione os filtros acima para visualizar as estatísticas de habilidades por disciplina.
        </div>
    @endif
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#disciplina_id').change(function() {
                var disciplinaId = $(this).val();
                var simuladoId = $('#simulado_id').val();
                var anoId = $('#ano_id').val();
                var habilidadeId = $('#habilidade_id').val();

                var url = "{{ route('relatorios.habilidades') }}";
                var params = {};
                if (simuladoId) params.simulado_id = simuladoId;
                if (disciplinaId) params.disciplina_id = disciplinaId;
                if (anoId) params.ano_id = anoId;
                if (habilidadeId) params.habilidade_id = habilidadeId;

                window.location.href = url + '?' + $.param(params);
            });

            $('#simulado_id').change(function() {
                var simuladoId = $(this).val();
                var disciplinaId = $('#disciplina_id').val();
                var anoId = $('#ano_id').val();
                var habilidadeId = $('#habilidade_id').val();

                var url = "{{ route('relatorios.habilidades') }}";
                var params = {};
                if (simuladoId) params.simulado_id = simuladoId;
                if (disciplinaId) params.disciplina_id = disciplinaId;
                if (anoId) params.ano_id = anoId;
                if (habilidadeId) params.habilidade_id = habilidadeId;

                window.location.href = url + '?' + $.param(params);
            });

            $('#ano_id').change(function() {
                var anoId = $(this).val();
                var simuladoId = $('#simulado_id').val();
                var disciplinaId = $('#disciplina_id').val();
                var habilidadeId = $('#habilidade_id').val();

                var url = "{{ route('relatorios.habilidades') }}";
                var params = {};
                if (simuladoId) params.simulado_id = simuladoId;
                if (disciplinaId) params.disciplina_id = disciplinaId;
                if (anoId) params.ano_id = anoId;
                if (habilidadeId) params.habilidade_id = habilidadeId;

                window.location.href = url + '?' + $.param(params);
            });

            $('#habilidade_id').change(function() {
                var habilidadeId = $(this).val();
                var simuladoId = $('#simulado_id').val();
                var disciplinaId = $('#disciplina_id').val();
                var anoId = $('#ano_id').val();

                var url = "{{ route('relatorios.habilidades') }}";
                var params = {};
                if (simuladoId) params.simulado_id = simuladoId;
                if (disciplinaId) params.disciplina_id = disciplinaId;
                if (anoId) params.ano_id = anoId;
                if (habilidadeId) params.habilidade_id = habilidadeId;

                window.location.href = url + '?' + $.param(params);
            });
        });
    </script>
@endpush