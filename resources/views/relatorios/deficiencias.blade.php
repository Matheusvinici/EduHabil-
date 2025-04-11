@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('relatorios.deficiencias') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="simulado_id">Simulado</label>
                            <select class="form-control" name="simulado_id" id="simulado_id" required>
                                <option value="">Selecione um simulado</option>
                                @foreach($filtros['simulados'] as $simulado)
                                    <option value="{{ $simulado->id }}" {{ request('simulado_id') == $simulado->id ? 'selected' : '' }}>
                                        {{ $simulado->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="deficiencia">Deficiência</label>
                            <select class="form-control" name="deficiencia" id="deficiencia">
                                <option value="">Todas</option>
                                @foreach($filtros['deficiencias'] as $key => $value)
                                    <option value="{{ $key }}" {{ request('deficiencia') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="escola_id">Escola</label>
                            <select class="form-control" name="escola_id" id="escola_id">
                                <option value="">Todas</option>
                                @foreach($filtros['escolas'] as $escola)
                                    <option value="{{ $escola->id }}" {{ request('escola_id') == $escola->id ? 'selected' : '' }}>
                                        {{ $escola->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <a href="{{ route('relatorios.deficiencias') }}" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!$semFiltro)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Resumo Geral</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card bg-light p-3 rounded">
                            <h6 class="stat-title">Total de Alunos</h6>
                            <p class="stat-value">{{ $totalAlunos }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-light p-3 rounded">
                            <h6 class="stat-title">Responderam</h6>
                            <p class="stat-value">{{ $totalResponderam }}</p>
                            <small class="text-muted">{{ $totalAlunos > 0 ? number_format(($totalResponderam/$totalAlunos)*100, 2) : 0 }}%</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-light p-3 rounded">
                            <h6 class="stat-title">Média Geral</h6>
                            <p class="stat-value">{{ number_format($mediaGeral, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Desempenho por Aluno</h5>
                <div>
                    <form method="GET" action="{{ route('relatorios.deficiencias') }}" class="d-inline">
                        <input type="hidden" name="simulado_id" value="{{ request('simulado_id') }}">
                        <input type="hidden" name="deficiencia" value="{{ request('deficiencia') }}">
                        <input type="hidden" name="escola_id" value="{{ request('escola_id') }}">
                        <input type="hidden" name="export_pdf" value="1">
                        <button type="submit" class="btn btn-sm btn-danger mr-2">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                    </form>
                    <form method="GET" action="{{ route('relatorios.deficiencias') }}" class="d-inline">
                        <input type="hidden" name="simulado_id" value="{{ request('simulado_id') }}">
                        <input type="hidden" name="deficiencia" value="{{ request('deficiencia') }}">
                        <input type="hidden" name="escola_id" value="{{ request('escola_id') }}">
                        <input type="hidden" name="export_excel" value="1">
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>Escola</th>
                                <th>Aluno</th>
                                <th>Deficiência</th>
                                <th>Turma</th>
                                <th class="text-center">Acertos</th>
                                <th class="text-center">% Acertos</th>
                                <th class="text-center">Média</th>
                                <th class="text-center">Desempenho</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($estatisticas as $estatistica)
                                <tr>
                                    <td>{{ $estatistica['escola_nome'] }}</td>
                                    <td>{{ $estatistica['aluno_nome'] }}</td>
                                    <td>{{ $filtros['deficiencias'][$estatistica['deficiencia']] ?? $estatistica['deficiencia'] }}</td>
                                    <td>{{ $estatistica['turma'] }}</td>
                                    <td class="text-center">{{ $estatistica['acertos'] }}/{{ $estatistica['total_questoes'] }}</td>
                                    <td class="text-center">{{ number_format($estatistica['porcentagem'], 2) }}%</td>
                                    <td class="text-center">{{ number_format($estatistica['media'], 2) }}</td>
                                    <td class="text-center">
                                        @php
                                            $badgeClass = [
                                                'Ótimo' => 'success',
                                                'Regular' => 'warning',
                                                'Ruim' => 'danger'
                                            ][$estatistica['desempenho']];
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}">
                                            {{ $estatistica['desempenho'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nenhum resultado encontrado com os filtros selecionados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card mb-4">
            <div class="card-body text-center py-5">
                <h4><i class="fas fa-filter fa-2x mb-3 text-muted"></i></h4>
                <p class="text-muted">Selecione um simulado para visualizar os dados</p>
            </div>
        </div>
    @endif
</div>
@endsection