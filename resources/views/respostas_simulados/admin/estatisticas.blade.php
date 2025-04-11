@extends('layouts.app')

@section('title', 'Estatísticas de Simulados - TRI')

@section('header', 'Estatísticas de Simulados - Modelo TRI')

@section('content')
<div class="container-fluid py-4">
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h5 class="card-title mb-0">Filtros</h5>
            @if(request()->hasAny(['simulado_id', 'ano_id', 'escola_id', 'habilidade_id', 'deficiencia']))
                <div>
                    <a href="{{ route('respostas_simulados.admin.export.pdf', request()->query()) }}" 
                       class="btn btn-sm btn-light text-primary">
                        <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                    </a>
                    <a href="{{ route('respostas_simulados.admin.export.excel', request()->query()) }}" 
                       class="btn btn-sm btn-light text-success ml-2">
                        <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                    </a>
                </div>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('respostas_simulados.admin.estatisticas') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="simulado_id" class="form-label">Simulado:</label>
                    <select name="simulado_id" id="simulado_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($simulados as $simulado)
                            <option value="{{ $simulado->id }}" {{ $request->simulado_id == $simulado->id ? 'selected' : '' }}>{{ $simulado->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="ano_id" class="form-label">Ano:</label>
                    <select name="ano_id" id="ano_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($anos as $ano)
                            <option value="{{ $ano->id }}" {{ $request->ano_id == $ano->id ? 'selected' : '' }}>{{ $ano->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="escola_id" class="form-label">Escola:</label>
                    <select name="escola_id" id="escola_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($escolas as $escola)
                            <option value="{{ $escola->id }}" {{ $request->escola_id == $escola->id ? 'selected' : '' }}>{{ $escola->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="habilidade_id" class="form-label">Habilidade:</label>
                    <select name="habilidade_id" id="habilidade_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($habilidades as $habilidade)
                            <option value="{{ $habilidade->id }}" {{ $request->habilidade_id == $habilidade->id ? 'selected' : '' }}>{{ $habilidade->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="deficiencia" class="form-label">Deficiência:</label>
                    <select name="deficiencia" id="deficiencia" class="form-select">
                        <option value="">Todas</option>
                        <option value="DV" {{ $request->deficiencia == 'DV' ? 'selected' : '' }}>Deficiência Visual</option>
                        <option value="DA" {{ $request->deficiencia == 'DA' ? 'selected' : '' }}>Deficiência Auditiva</option>
                        <option value="DF" {{ $request->deficiencia == 'DF' ? 'selected' : '' }}>Deficiência Física</option>
                        <option value="DI" {{ $request->deficiencia == 'DI' ? 'selected' : '' }}>Deficiência Intelectual</option>
                        <option value="TEA" {{ $request->deficiencia == 'TEA' ? 'selected' : '' }}>Autismo (TEA)</option>
                        <option value="ND" {{ $request->deficiencia == 'ND' ? 'selected' : '' }}>Sem deficiência</option>
                    </select>
                </div>
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>
                    <a href="{{ route('respostas_simulados.admin.estatisticas') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-sync-alt mr-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if(request()->hasAny(['simulado_id', 'ano_id', 'escola_id', 'habilidade_id', 'deficiencia']))
    <!-- Barra de Progresso -->
    <div class="progress mb-4" style="height: 8px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" 
             style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <!-- Dados Gerais -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h5 class="card-title mb-0">Dados Gerais - Modelo TRI</h5>
            <div class="badge bg-light text-primary">
                Filtros Aplicados: 
                @if($request->simulado_id) Simulado: {{ $simulados->firstWhere('id', $request->simulado_id)->nome }} @endif
                @if($request->ano_id) | Ano: {{ $anos->firstWhere('id', $request->ano_id)->nome }} @endif
                @if($request->escola_id) | Escola: {{ $escolas->firstWhere('id', $request->escola_id)->nome }} @endif
                @if($request->habilidade_id) | Habilidade: {{ $habilidades->firstWhere('id', $request->habilidade_id)->descricao }} @endif
                @if($request->deficiencia) | Deficiência: 
                    @switch($request->deficiencia)
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
                <div class="col-md-2">
                    <div class="stat-card bg-light p-3 rounded">
                        <h6 class="stat-title">Total de Simulados</h6>
                        <p class="stat-value">{{ $totalSimulados }}</p>
                        <small class="text-muted">Filtrado: {{ $filtros['simulado_id'] ? '1' : $totalSimulados }}</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card bg-light p-3 rounded">
                        <h6 class="stat-title">Professores Cadastrados</h6>
                        <p class="stat-value">{{ $totalProfessores }}</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card bg-light p-3 rounded">
                        <h6 class="stat-title">Alunos Cadastrados</h6>
                        <p class="stat-value">{{ $totalAlunos }}</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card bg-light p-3 rounded">
                        <h6 class="stat-title">Alunos c/ Deficiência</h6>
                        <p class="stat-value">{{ $totalAlunosComDeficiencia }}</p>
                        <small class="text-muted">{{ $totalAlunos > 0 ? number_format(($totalAlunosComDeficiencia/$totalAlunos)*100, 2) : 0 }}%</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card bg-light p-3 rounded">
                        <h6 class="stat-title">Respostas</h6>
                        <p class="stat-value">{{ $totalRespostas }}</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-card bg-light p-3 rounded">
                        <h6 class="stat-title">Alunos Responderam</h6>
                        <p class="stat-value">{{ $alunosResponderam }}</p>
                        <small class="text-muted">{{ $totalAlunos > 0 ? number_format(($alunosResponderam/$totalAlunos)*100, 2) : 0 }}%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Explicação do Modelo TRI -->
    <div class="card mb-4 bg-light">
        <div class="card-header bg-info text-white">
            <h5 class="card-title mb-0"><i class="fas fa-info-circle mr-2"></i>Sobre o Modelo TRI</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <p>O sistema utiliza a <strong>Teoria de Resposta ao Item (TRI)</strong> para cálculo das notas, considerando:</p>
                    <ul>
                        <li><strong>Peso das questões</strong> (1 a 3) - Questões mais difíceis valem mais</li>
                        <li><strong>Padronização da escala</strong> de 0 a 10 pontos</li>
                        <li><strong>Consistência no desempenho</strong> - Acertos em questões difíceis compensam erros em fáceis</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-secondary">
                        <h6><i class="fas fa-calculator mr-2"></i>Fórmula da Nota:</h6>
                        <p class="mb-1">Nota = (Σ(Pontos Obtidos) / Σ(Pesos Totais)) × 10</p>
                        <small>Onde: Pontos Obtidos = Acertos × Peso da Questão</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- ========== NOVA SEÇÃO: RESUMO TRI ========== -->
        <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="card-title mb-0"><i class="fas fa-brain mr-2"></i> Análise TRI</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-card bg-light p-3 rounded border">
                        <h6 class="stat-title">Média Theta (Habilidade)</h6>
                        <p class="stat-value">{{ number_format($mediaTRI, 3) }} θ</p>
                        <small class="text-muted">Escala: -3.0 (baixa) a +3.0 (alta)</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-light p-3 rounded border">
                        <h6 class="stat-title">Nota TRI Convertida</h6>
                        <p class="stat-value">{{ number_format($mediaTRI * 3.33, 1) }}</p>
                        <small class="text-muted">Equivalente na escala 0-10</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-light p-3 rounded border">
                        <h6 class="stat-title">Distribuição de Habilidade</h6>
                        <div class="d-flex justify-content-between">
                            <span class="badge bg-danger">Muito Baixa: {{ $distribuicaoTheta['muito_baixa'] }}</span>
                            <span class="badge bg-warning">Baixa: {{ $distribuicaoTheta['baixa'] }}</span>
                            <span class="badge bg-info">Adequada: {{ $distribuicaoTheta['adequada'] }}</span>
                            <span class="badge bg-success">Avançada: {{ $distribuicaoTheta['avancada'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== NOVA SEÇÃO: PROJEÇÃO IDEB ========== -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0"><i class="fas fa-chart-line mr-2"></i> Projeção IDEB</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Atenção:</strong> Projeção baseada em modelo preliminar (fator: {{ $fatorAjusteIDEB }}). 
                Será calibrada após primeiro IDEB oficial.
            </div>

            <div class="row text-center">
                <!-- Anos Iniciais -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 {{ $alertaMeta['atingiu_meta'] ? 'border-success' : 'border-danger' }}">
                        <div class="card-header bg-{{ $alertaMeta['atingiu_meta'] ? 'success' : 'danger' }} text-white">
                            <h6>Anos Iniciais (1º-5º)</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-around mb-3">
                                <div>
                                    <small>TRI</small>
                                    <div class="h5 text-primary">{{ number_format($mediaTRI * 3.33, 1) }}</div>
                                </div>
                                <div>
                                    <small>Peso</small>
                                    <div class="h5 text-warning">{{ number_format($mediaGeral1a5, 1) }}</div>
                                </div>
                                <div>
                                    <small>Híbrida</small>
                                    <div class="h5 text-dark">{{ number_format($notaHibridaGeral1a5, 1) }}</div>
                                </div>
                            </div>
                            
                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar bg-{{ $projecaoIDEB1a5 >= 6 ? 'success' : 'danger' }}" 
                                     style="width: {{ $projecaoIDEB1a5 * 10 }}%">
                                    <strong>{{ number_format($projecaoIDEB1a5, 1) }}</strong>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small>Meta: 6.0</small>
                                <small class="{{ $alertaMeta['diferenca_meta'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $alertaMeta['diferenca_meta'] >= 0 ? '+' : '' }}{{ $alertaMeta['diferenca_meta'] }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Anos Finais -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-secondary text-white">
                            <h6>Anos Finais (6º-9º)</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-around mb-3">
                                <div>
                                    <small>TRI</small>
                                    <div class="h5 text-primary">{{ number_format($mediaTRI * 3.33, 1) }}</div>
                                </div>
                                <div>
                                    <small>Peso</small>
                                    <div class="h5 text-warning">{{ number_format($mediaGeral6a9, 1) }}</div>
                                </div>
                                <div>
                                    <small>Híbrida</small>
                                    <div class="h5 text-dark">{{ number_format($notaHibridaGeral6a9, 1) }}</div>
                                </div>
                            </div>
                            
                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar bg-info" 
                                     style="width: {{ $projecaoIDEB6a9 * 10 }}%">
                                    <strong>{{ number_format($projecaoIDEB6a9, 1) }}</strong>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small>Fator: {{ $fatorAjusteIDEB }}</small>
                                <small>Híbrida: 70% TRI + 30% Peso</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela Comparativa por Escola -->
            <div class="mt-4">
                <h5><i class="fas fa-school mr-2"></i> Desempenho por Escola</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>Escola</th>
                                <th class="text-center">TRI (θ)</th>
                                <th class="text-center">Nota TRI</th>
                                <th class="text-center">Nota Peso</th>
                                <th class="text-center bg-light">Híbrida</th>
                                <th class="text-center {{ $metaIDEB >= 6 ? 'bg-success text-white' : 'bg-warning' }}">Projeção IDEB</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($estatisticasPorEscola as $escola)
                            <tr>
                                <td>{{ $escola['escola'] }}</td>
                                <td class="text-center">{{ number_format($escola['media_tri'], 3) }}</td>
                                <td class="text-center">{{ $escola['nota_tri_convertida'] }}</td>
                                <td class="text-center">{{ $escola['media_ponderada'] }}</td>
                                <td class="text-center bg-light">{{ $escola['media_hibrida'] }}</td>
                                <td class="text-center {{ $escola['atingiu_meta'] ? 'text-success' : 'text-danger' }}">
                                    <strong>{{ $escola['projecao_ideb'] }}</strong>
                                    @if($escola['atingiu_meta'])
                                        <i class="fas fa-check ml-1"></i>
                                    @else
                                        <i class="fas fa-exclamation-triangle ml-1"></i>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas por Escola -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Desempenho por Escola - Modelo TRI</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-primary bg-primary text-white">
                        <tr>
                            <th>Escola</th>
                            <th>Respostas</th>
                            <th>Pontos Ponderados</th>
                            <th>Total de Pesos</th>
                            <th>% Acertos</th>
                            <th>Média TRI (0-10)</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticasPorEscola as $estatistica)
                            <tr>
                                <td>{{ $estatistica['escola'] }}</td>
                                <td>{{ $estatistica['total_respostas'] }}</td>
                                <td>{{ $estatistica['pontos_ponderados'] }}</td>
                                <td>{{ $estatistica['total_peso'] }}</td>
                                <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                                <td>
                                    <span class="badge bg-{{ $estatistica['media_final'] >= 7 ? 'success' : ($estatistica['media_final'] >= 5 ? 'warning' : 'danger') }}">
                                        {{ number_format($estatistica['media_final'], 2) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('respostas_simulados.admin.detalhes-escola', [
                                        'escola_id' => $escolas->firstWhere('nome', $estatistica['escola'])->id,
                                        'simulado_id' => $request->simulado_id,
                                        'ano_id' => $request->ano_id,
                                        'habilidade_id' => $request->habilidade_id
                                    ]) }}" 
                                    class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Detalhes
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Estatísticas por Ano -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Desempenho por Ano - Modelo TRI</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-bordered">
    <thead>
        <tr>
            <th>Ano</th>
            <th>Respostas</th>
            <th>Pontos Ponderados</th>
            <th>Total de Pesos</th>
            <th>% Acertos</th>
            <th>Média Final (0-10)</th>
            <th>Média TRI (0-10)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($estatisticasPorAno as $estatistica)
        <tr>
            <td>{{ $estatistica['ano'] }}</td>
            <td>{{ $estatistica['total_respostas'] }}</td>
            <td>{{ $estatistica['pontos_ponderados'] }}</td>
            <td>{{ $estatistica['total_peso'] }}</td>
            <td>{{ $estatistica['porcentagem_acertos'] }}%</td>
            <td>{{ $estatistica['media_final'] }}</td>
            <td>{{ $estatistica['media_tri'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
            </div>
        </div>
    </div>

    <!-- Estatísticas por Habilidade -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Desempenho por Habilidade - Modelo TRI</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-primary bg-primary text-white">
                        <tr>
                            <th>Habilidade</th>
                            <th>Respostas</th>
                            <th>Pontos Ponderados</th>
                            <th>Total de Pesos</th>
                            <th>% Acertos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticasPorHabilidade as $estatistica)
                            <tr>
                                <td>{{ $estatistica['habilidade'] }}</td>
                                <td>{{ $estatistica['total_respostas'] }}</td>
                                <td>{{ $estatistica['pontos_ponderados'] }}</td>
                                <td>{{ $estatistica['total_peso'] }}</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $estatistica['porcentagem_acertos'] >= 70 ? 'success' : ($estatistica['porcentagem_acertos'] >= 50 ? 'warning' : 'danger') }}" 
                                             role="progressbar" style="width: {{ $estatistica['porcentagem_acertos'] }}%" 
                                             aria-valuenow="{{ $estatistica['porcentagem_acertos'] }}" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($estatistica['porcentagem_acertos'], 1) }}%
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

    <!-- Estatísticas por Questão -->
    <div class="card mb-4">
    <div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">Desempenho por Questão - Modelo TRI</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-primary bg-primary text-white">
                    <tr>
                        <th>Disciplina</th>
                        <th>Questão</th>
                        <th>Peso</th>
                        <th>Habilidade</th>
                        <th>Respostas</th>
                        <th>Acertos</th>
                        <th>Média Simples</th>
                        <th>Média Ponderada</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($estatisticasPorQuestao as $questao)
                        <tr>
                            <td>{{ $questao->disciplina }}</td>
                            <td>{{ Str::limit($questao->enunciado, 50) }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $questao->peso == 3 ? 'danger' : ($questao->peso == 2 ? 'warning' : 'primary') }}">
                                    {{ $questao->peso }}
                                </span>
                            </td>
                            <td>{{ Str::limit($questao->habilidade, 30) }}</td>
                            <td>{{ $questao->total_respostas }}</td>
                            <td>{{ $questao->acertos }}</td>
                            <td>{{ number_format($questao->media_simples * 100, 2) }}%</td>
                            <td>{{ number_format($questao->media_ponderada * 100, 2) }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Nenhuma questão encontrada com os filtros aplicados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $estatisticasPorQuestao->links() }}
        </div>
    </div>
</div>
    </div>

    <!-- Estatísticas por Raça/Cor -->
    <div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">Desempenho por Raça/Cor - Modelo TRI</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Raça/Cor</th>
                        <th>Respostas</th>
                        <th>Pontos Ponderados</th>
                        <th>Total de Pesos</th>
                        <th>% Acertos</th>
                        <th>Média TRI (0-10)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($estatisticasPorRaca as $estatistica)
                    <tr>
                        <td>{{ $estatistica['raca'] }}</td>
                        <td>{{ $estatistica['total_respostas'] }}</td>
                        <td>{{ $estatistica['pontos_ponderados'] }}</td>
                        <td>{{ $estatistica['total_peso'] }}</td>
                        <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                        <td>
                            <span class="badge bg-{{ $estatistica['media_tri'] >= 7 ? 'success' : ($estatistica['media_tri'] >= 5 ? 'warning' : 'danger') }}">
                                {{ number_format($estatistica['media_tri'], 2) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
    <!-- Estatísticas por Deficiência -->
    <div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title">Distribuição por Deficiência</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Quantidade</th>
                            <th>% do Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alunosPorDeficiencia as $item)
                        <tr>
                            <td>
                                @switch($item['deficiencia'])
                                    @case('DV') Def. Visual @break
                                    @case('DA') Def. Auditiva @break
                                    @case('DF') Def. Física @break
                                    @case('DI') Def. Intelectual @break
                                    @case('TEA') Autismo @break
                                    @case('ND') Sem deficiência @break
                                    @default Outros
                                @endswitch
                            </td>
                            <td>{{ $item['total'] }}</td>
                            <td>{{ $item['percentual'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title">Desempenho por Deficiência</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Respostas</th>
                            <th>Pontos</th>
                            <th>Pesos</th>
                            <th>Média</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estatisticasPorDeficiencia as $estat)
                        <tr>
                            <td>
                                @switch($estat['deficiencia'])
                                    @case('DV') Def. Visual @break
                                    @case('DA') Def. Auditiva @break
                                    @case('DF') Def. Física @break
                                    @case('DI') Def. Intelectual @break
                                    @case('TEA') Autismo @break
                                    @case('ND') Sem deficiência @break
                                    @default Outros
                                @endswitch
                            </td>
                            <td>{{ $estat['total_respostas'] }}</td>
                            <td>{{ $estat['pontos_ponderados'] }}</td>
                            <td>{{ $estat['total_peso'] }}</td>
                            <td class="{{ $estat['media_final'] >= 7 ? 'text-success' : ($estat['media_final'] >= 5 ? 'text-warning' : 'text-danger') }}">
                                <strong>{{ $estat['media_final'] }}</strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <!-- Médias Gerais -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title">Médias Gerais - Modelo TRI</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="stat-card bg-light p-3 rounded">
                        <h6 class="stat-title">Média TRI (1º ao 5º Ano)</h6>
                        <p class="stat-value">{{ number_format($mediaGeral1a5, 2) }}</p>
                        <div class="progress mt-2" style="height: 10px;">
                            <div class="progress-bar bg-{{ $mediaGeral1a5 >= 7 ? 'success' : ($mediaGeral1a5 >= 5 ? 'warning' : 'danger') }}" 
                                 role="progressbar" style="width: {{ $mediaGeral1a5 * 10 }}%" 
                                 aria-valuenow="{{ $mediaGeral1a5 * 10 }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card bg-light p-3 rounded">
                        <h6 class="stat-title">Média TRI (6º ao 9º Ano)</h6>
                        <p class="stat-value">{{ number_format($mediaGeral6a9, 2) }}</p>
                        <div class="progress mt-2" style="height: 10px;">
                            <div class="progress-bar bg-{{ $mediaGeral6a9 >= 7 ? 'success' : ($mediaGeral6a9 >= 5 ? 'warning' : 'danger') }}" 
                                 role="progressbar" style="width: {{ $mediaGeral6a9 * 10 }}%" 
                                 aria-valuenow="{{ $mediaGeral6a9 * 10 }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Desempenho por Habilidade</h5>
                </div>
                <div class="card-body">
                    <canvas id="graficoHabilidades" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Média por Escola</h5>
                </div>
                <div class="card-body">
                    <canvas id="graficoEscolas" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        .stat-card {
            transition: transform 0.3s ease;
            border-left: 4px solid #0066cc;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
        }
        .stat-title {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0;
            color: #0066cc;
        }
        .table th {
            white-space: nowrap;
            background-color: #f8f9fa;
        }
        .bg-primary {
            background-color: #0066cc !important;
        }
        .btn-primary {
            background-color: #0066cc;
            border-color: #0066cc;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .badge.bg-primary {
            background-color: #0066cc !important;
        }
        .badge.bg-success {
            background-color: #28a745 !important;
        }
        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #212529;
        }
        .badge.bg-danger {
            background-color: #dc3545 !important;
        }
    </style>
</div>

<!-- Scripts para Gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    
    // Gráfico de Média por Escola
    const ctxEscolas = document.getElementById('graficoEscolas').getContext('2d');
    new Chart(ctxEscolas, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($estatisticasPorEscola, 'escola')) !!},
            datasets: [{
                label: 'Média TRI (0-10)',
                data: {!! json_encode(array_column($estatisticasPorEscola, 'media_final')) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 10,
                    title: {
                        display: true,
                        text: 'Média Final (0-10)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Escolas'
                    }
                }
            }
        }
    });
</script>
@endsection