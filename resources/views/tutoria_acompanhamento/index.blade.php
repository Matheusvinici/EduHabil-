@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Acompanhamento de Avaliações</h4>
        </div>
        
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Filtros</h5>
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Escola</label>
                            <select name="escola_id" class="form-select">
                                <option value="">Todas</option>
                                @foreach($escolas as $escola)
                                    <option value="{{ $escola->id }}" {{ request('escola_id') == $escola->id ? 'selected' : '' }}>{{ $escola->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prioridade</label>
                            <select name="prioridade" class="form-select">
                                <option value="">Todas</option>
                                <option value="alta" {{ request('prioridade') == 'alta' ? 'selected' : '' }}>Alta</option>
                                <option value="media" {{ request('prioridade') == 'media' ? 'selected' : '' }}>Média</option>
                                <option value="baixa" {{ request('prioridade') == 'baixa' ? 'selected' : '' }}>Baixa</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
                            <a href="{{ route('tutoria.acompanhamento') }}" class="btn btn-sm btn-outline-secondary">Limpar</a>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Resumo</h5>
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-danger">Alta: {{ $resumo['alta'] }}</span>
                                <span class="badge bg-warning">Média: {{ $resumo['media'] }}</span>
                                <span class="badge bg-success">Baixa: {{ $resumo['baixa'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Escola</th>
                            <th>Critério</th>
                            <th>Nota</th>
                            <th>Prioridade</th>
                            <th>Ação de Melhoria</th>
                            <th>Responsável</th>
                            <th>Prazo</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($acompanhamentos as $item)
                            <tr class="@if($item->prioridade == 'alta') table-danger @elseif($item->prioridade == 'media') table-warning @else table-success @endif">
                                <td>{{ $item->avaliacao->escola->nome }}</td>
                                <td>{{ $item->criterio->categoria }}</td>
                                <td>{{ $item->avaliacao->criterios->find($item->criterio_id)->pivot->nota }}</td>
                                <td>
                                    <span class="badge @if($item->prioridade == 'alta') bg-danger @elseif($item->prioridade == 'media') bg-warning @else bg-success @endif">
                                        {{ ucfirst($item->prioridade) }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($item->acao_melhoria, 50) }}</td>
                                <td>{{ $item->responsavel->name }}</td>
                                <td>{{ $item->prazo->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge @if($item->status == 'concluido') bg-success @elseif($item->status == 'em_andamento') bg-info @else bg-secondary @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('tutoria.acompanhamento.edit', $item->id) }}" class="btn btn-sm btn-primary">Editar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection