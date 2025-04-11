@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Filtros -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter"></i> Filtros
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('relatorios.estatisticas-questoes') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="simulado_id">Simulado</label>
                            <select class="form-control" name="simulado_id" id="simulado_id">
                                <option value="">Todos os Simulados</option>
                                @foreach($simulados as $simulado)
                                    <option value="{{ $simulado->id }}" {{ request('simulado_id') == $simulado->id ? 'selected' : '' }}>
                                        {{ $simulado->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="disciplina_id">Disciplina</label>
                            <select class="form-control" name="disciplina_id" id="disciplina_id">
                                <option value="">Todas as Disciplinas</option>
                                @foreach($disciplinas as $disciplina)
                                    <option value="{{ $disciplina->id }}" {{ request('disciplina_id') == $disciplina->id ? 'selected' : '' }}>
                                        {{ $disciplina->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ano_id">Ano/Série</label>
                            <select class="form-control" name="ano_id" id="ano_id">
                                <option value="">Todos os Anos</option>
                                @foreach($anos as $ano)
                                    <option value="{{ $ano->id }}" {{ request('ano_id') == $ano->id ? 'selected' : '' }}>
                                        {{ $ano->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <a href="{{ route('relatorios.estatisticas-questoes') }}" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Limpar
                        </a>
                        @if(request()->has('simulado_id'))
                        <a href="{{ route('relatorios.exportar-questoes-pdf', request()->query()) }}" class="btn btn-danger ml-2">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        <a href="{{ route('relatorios.exportar-questoes-excel', request()->query()) }}" class="btn btn-success ml-2">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->hasAny(['simulado_id', 'disciplina_id', 'ano_id']))
    <!-- Resumo por Disciplina -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-chart-pie"></i> Resumo por Disciplina
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Disciplina</th>
                            <th class="text-center">Questões</th>
                            <th class="text-center">Média Simples</th>
                            <th class="text-center">Média Ponderada</th>
                            <th class="text-center">% Acerto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estatisticasPorDisciplina as $disciplina)
                        <tr>
                            <td>{{ $disciplina->disciplina }}</td>
                            <td class="text-center">{{ $disciplina->total_questoes }}</td>
                            <td class="text-center">{{ number_format($disciplina->media_simples, 2) }}</td>
                            <td class="text-center">{{ number_format($disciplina->media_ponderada, 2) }}</td>
                            <td class="text-center">{{ number_format($disciplina->percentual_acerto, 2) }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Nenhum dado encontrado com os filtros selecionados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detalhamento por Questão -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-question-circle"></i> Detalhamento por Questão
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Disciplina</th>
                            <th>Questão (resumo)</th>
                            <th class="text-center">Peso</th>
                            <th class="text-center">Habilidade</th>
                            <th class="text-center">Respostas</th>
                            <th class="text-center">Acertos</th>
                            <th class="text-center">% Acerto</th>
                            <th class="text-center">Média Ponderada</th>
                            <th class="text-center">TRI Médio</th>
                            <th class="text-center">Parâmetros TRI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estatisticasPorQuestao as $questao)
                        <tr>
                            <td>{{ $questao->disciplina }}</td>
                            <td>
                                <a href="#" data-toggle="modal" data-target="#questaoModal{{ $questao->id }}">
                                    {{ Str::limit(strip_tags($questao->enunciado), 50) }}
                                </a>
                                
                                <!-- Modal -->
                                <div class="modal fade" id="questaoModal{{ $questao->id }}" tabindex="-1" role="dialog" aria-labelledby="questaoModalLabel{{ $questao->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="questaoModalLabel{{ $questao->id }}">Detalhes da Questão</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <h6>Enunciado:</h6>
                                                <p>{!! nl2br(e($questao->enunciado)) !!}</p>
                                                
                                                <div class="row mt-4">
                                                    <div class="col-md-6">
                                                        <h6>Estatísticas:</h6>
                                                        <ul class="list-group">
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                Total de Respostas
                                                                <span class="badge badge-primary badge-pill">{{ $questao->total_respostas }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                Acertos
                                                                <span class="badge badge-success badge-pill">{{ $questao->acertos }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                Erros
                                                                <span class="badge badge-danger badge-pill">{{ $questao->total_respostas - $questao->acertos }}</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Parâmetros TRI:</h6>
                                                        <ul class="list-group">
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                Discriminação (a)
                                                                <span>{{ $questao->tri_a }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                Dificuldade (b)
                                                                <span>{{ $questao->tri_b }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                Acerto Casual (c)
                                                                <span>{{ $questao->tri_c }}</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                {{ $questao->peso }}
                                <span class="badge badge-{{ ['primary', 'warning', 'danger'][$questao->peso-1] }}">
                                    {{ ['Baixo', 'Médio', 'Alto'][$questao->peso-1] }}
                                </span>
                            </td>
                            <td>{{ $questao->habilidade }}</td>
                            <td class="text-center">{{ $questao->total_respostas }}</td>
                            <td class="text-center">{{ $questao->acertos }}</td>
                            <td class="text-center">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $questao->percentual_acerto >= 70 ? 'success' : ($questao->percentual_acerto >= 40 ? 'warning' : 'danger') }}" 
                                         role="progressbar" style="width: {{ $questao->percentual_acerto }}%" 
                                         aria-valuenow="{{ $questao->percentual_acerto }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($questao->percentual_acerto, 1) }}%
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">{{ number_format($questao->media_ponderada, 2) }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $questao->tri_medio >= 7 ? 'success' : ($questao->tri_medio >= 5 ? 'warning' : 'danger') }}">
                                    {{ $questao->tri_medio }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info" data-toggle="tooltip" title="Discriminação: {{ $questao->tri_a }}">
                                    a:{{ $questao->tri_a }}
                                </span>
                                <span class="badge badge-warning" data-toggle="tooltip" title="Dificuldade: {{ $questao->tri_b }}">
                                    b:{{ $questao->tri_b }}
                                </span>
                                <span class="badge badge-secondary" data-toggle="tooltip" title="Acerto casual: {{ $questao->tri_c }}">
                                    c:{{ $questao->tri_c }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">Nenhuma questão encontrada com os filtros selecionados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            @if($estatisticasPorQuestao->hasPages())
            <div class="row mt-3">
                <div class="col-md-12">
                    {{ $estatisticasPorQuestao->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <h4><i class="fas fa-filter fa-3x mb-3 text-muted"></i></h4>
            <h5 class="text-muted">Selecione os filtros para visualizar as estatísticas das questões</h5>
            <p class="text-muted">Utilize os filtros acima para gerar relatórios específicos</p>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection