@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('relatorios.rede-municipal') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="simulado_id">Simulado</label>
                            <select class="form-control" name="simulado_id" id="simulado_id" required>
                                <option value="">Selecione um simulado</option>
                                @foreach($simulados as $simulado)
                                    <option value="{{ $simulado->id }}" {{ request('simulado_id') == $simulado->id ? 'selected' : '' }}>
                                        {{ $simulado->nome }}
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
                        <a href="{{ route('relatorios.rede-municipal') }}" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('simulado_id'))
    <!-- Barra de Progresso -->
    <div class="progress mb-4" style="height: 8px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" 
             style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- Dados Gerais -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h5 class="card-title mb-0">Dados Gerais</h5>
            <div class="badge bg-light text-primary">
                Filtros Aplicados: 
                @if(request()->simulado_id) Simulado: {{ $simulados->firstWhere('id', request()->simulado_id)->nome }} @endif
                @if(request()->ano_id) | Ano: {{ $anos->firstWhere('id', request()->ano_id)->nome }} @endif
                @if(request()->escola_id) | Escola: {{ $escolas->firstWhere('id', request()->escola_id)->nome }} @endif
                @if(request()->deficiencia) | Deficiência: 
                    @switch(request()->deficiencia)
                        @case('DV') Visual @break
                        @case('DA') Auditiva @break
                        @case('DF') Física @break
                        @case('DI') Intelectual @break
                        @case('TEA') Autismo @break
                        @case('ND') Sem deficiência @break
                    @endswitch
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card bg-light p-3 rounded">
                        <h6 class="stat-title">Total de Alunos</h6>
                        <p class="stat-value">{{ $totalAlunos }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-light p-3 rounded">
                        <h6 class="stat-title">Alunos Ativos</h6>
                        <p class="stat-value">{{ $alunosAtivos }}</p>
                        <small class="text-muted">{{ $totalAlunos > 0 ? number_format(($alunosAtivos/$totalAlunos)*100, 2) : 0 }}%</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-light p-3 rounded">
                        <h6 class="stat-title">Alunos Responderam</h6>
                        <p class="stat-value">{{ $alunosResponderam }}</p>
                        <small class="text-muted">{{ $alunosAtivos > 0 ? number_format(($alunosResponderam/$alunosAtivos)*100, 2) : 0 }}%</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-light p-3 rounded">
                        <h6 class="stat-title">Taxa de Faltosos</h6>
                        <p class="stat-value">{{ $alunosAtivos - $alunosResponderam }}</p>
                        <small class="text-muted">{{ $alunosAtivos > 0 ? number_format((($alunosAtivos - $alunosResponderam)/$alunosAtivos)*100, 2) : 0 }}%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Médias Ponderadas -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Médias Ponderadas por Peso</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Descrição</th>
                            <th class="text-center">Peso 1</th>
                            <th class="text-center">Peso 2</th>
                            <th class="text-center">Peso 3</th>
                            <th class="text-center">Média Geral</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Média de Acertos</td>
                            <td class="text-center">{{ number_format($mediasPeso['peso_1'], 2) }}</td>
                            <td class="text-center">{{ number_format($mediasPeso['peso_2'], 2) }}</td>
                            <td class="text-center">{{ number_format($mediasPeso['peso_3'], 2) }}</td>
                            <td class="text-center">{{ number_format($mediasPeso['media_geral'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Projeção TRI</td>
                            <td class="text-center">{{ number_format($projecaoTRI['peso_1'], 2) }}</td>
                            <td class="text-center">{{ number_format($projecaoTRI['peso_2'], 2) }}</td>
                            <td class="text-center">{{ number_format($projecaoTRI['peso_3'], 2) }}</td>
                            <td class="text-center">{{ number_format($projecaoTRI['media_geral'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Médias por Segmento -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Médias por Segmento</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-6">
                    <div class="card h-100 {{ $projecaoSegmento['1a5']['atingiu_meta'] ? 'border-success' : 'border-danger' }}">
                        <div class="card-header {{ $projecaoSegmento['1a5']['atingiu_meta'] ? 'bg-success' : 'bg-danger' }} text-white">
                            <h6>1º ao 5º Ano</h6>
                        </div>
                        <div class="card-body">
                            <div class="h2">{{ number_format($projecaoSegmento['1a5']['media'], 2) }}</div>
                            <div class="progress mt-3" style="height: 25px;">
                                <div class="progress-bar {{ $projecaoSegmento['1a5']['projecao'] >= 6 ? 'bg-success' : 'bg-danger' }}" 
                                     style="width: {{ $projecaoSegmento['1a5']['projecao'] * 10 }}%">
                                    <strong>Projeção: {{ number_format($projecaoSegmento['1a5']['projecao'], 1) }}</strong>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="badge {{ $projecaoSegmento['1a5']['atingiu_meta'] ? 'bg-success' : 'bg-danger' }}">
                                    Meta: 6.0 | Diferença: {{ $projecaoSegmento['1a5']['diferenca'] >= 0 ? '+' : '' }}{{ $projecaoSegmento['1a5']['diferenca'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 {{ $projecaoSegmento['6a9']['atingiu_meta'] ? 'border-success' : 'border-warning' }}">
                        <div class="card-header {{ $projecaoSegmento['6a9']['atingiu_meta'] ? 'bg-success' : 'bg-warning' }} text-white">
                            <h6>6º ao 9º Ano</h6>
                        </div>
                        <div class="card-body">
                            <div class="h2">{{ number_format($projecaoSegmento['6a9']['media'], 2) }}</div>
                            <div class="progress mt-3" style="height: 25px;">
                                <div class="progress-bar {{ $projecaoSegmento['6a9']['projecao'] >= 5 ? 'bg-success' : 'bg-warning' }}" 
                                     style="width: {{ $projecaoSegmento['6a9']['projecao'] * 10 }}%">
                                    <strong>Projeção: {{ number_format($projecaoSegmento['6a9']['projecao'], 1) }}</strong>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="badge {{ $projecaoSegmento['6a9']['atingiu_meta'] ? 'bg-success' : 'bg-warning' }}">
                                    Meta: 5.0 | Diferença: {{ $projecaoSegmento['6a9']['diferenca'] >= 0 ? '+' : '' }}{{ $projecaoSegmento['6a9']['diferenca'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Botões de Exportação -->
    <div class="card mb-4">
        <div class="card-body text-center">
            <a href="{{ route('relatorios.rede-municipal.pdf', request()->all()) }}" class="btn btn-danger mr-2">
                <i class="fas fa-file-pdf mr-2"></i> Exportar PDF
            </a>
            <a href="{{ route('relatorios.rede-municipal.excel', request()->all()) }}" class="btn btn-success">
                <i class="fas fa-file-excel mr-2"></i> Exportar Excel
            </a>
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