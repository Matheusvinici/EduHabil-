@extends('layouts.app')

@section('title', 'Estatísticas de Simulados')

@section('header', 'Estatísticas de Simulados')

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
        <h5 class="card-title mb-0">Dados Gerais</h5>
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

    <!-- NOVA SEÇÃO: Estatísticas por Deficiência -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Distribuição por Deficiência</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-primary bg-primary text-white">
                                <tr>
                                    <th>Tipo de Deficiência</th>
                                    <th>Quantidade de Alunos</th>
                                    <th>% do Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($alunosPorDeficiencia as $item)
                                    <tr>
                                        <td>
                                            @switch($item['deficiencia'])
                                                @case('DV') Deficiência Visual @break
                                                @case('DA') Deficiência Auditiva @break
                                                @case('DF') Deficiência Física @break
                                                @case('DI') Deficiência Intelectual @break
                                                @case('TEA') Autismo @break
                                                @default {{ $item['deficiencia'] }}
                                            @endswitch
                                        </td>
                                        <td>{{ $item['total'] }}</td>
                                        <td>{{ $totalAlunos > 0 ? number_format(($item['total'] / $totalAlunos) * 100, 2) : 0 }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Desempenho por Deficiência</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-primary bg-primary text-white">
                                <tr>
                                    <th>Deficiência</th>
                                    <th>Total Respostas</th>
                                    <th>% Acertos</th>
                                    <th>Média (0-10)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($estatisticasPorDeficiencia as $estatistica)
                                    <tr>
                                        <td>
                                            @switch($estatistica['deficiencia'])
                                                @case('DV') Deficiência Visual @break
                                                @case('DA') Deficiência Auditiva @break
                                                @case('DF') Deficiência Física @break
                                                @case('DI') Deficiência Intelectual @break
                                                @case('TEA') Autismo @break
                                                @case('ND') Sem deficiência @break
                                                @default {{ $estatistica['deficiencia'] }}
                                            @endswitch
                                        </td>
                                        <td>{{ $estatistica['total_respostas'] }}</td>
                                        <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                                        <td>{{ number_format($estatistica['media_final'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mantenha todas as seções existentes abaixo -->
    <!-- Estatísticas por Raça/Cor -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Estatísticas por Raça/Cor</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-primary bg-primary text-white">
                        <tr>
                            <th>Raça/Cor</th>
                            <th>Total de Respostas</th>
                            <th>Acertos</th>
                            <th>% de Acertos</th>
                            <th>Média Final (0-10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticasPorRaca as $estatistica)
                            <tr>
                                <td>{{ $estatistica['raca'] }}</td>
                                <td>{{ $estatistica['total_respostas'] }}</td>
                                <td>{{ $estatistica['acertos'] }}</td>
                                <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                                <td>{{ number_format($estatistica['media_final'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

                        
             <!-- Tabela de Estatísticas por Questão -->
                @if($estatisticasPorQuestao->count())
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">Estatísticas por Questão</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Disciplina</th>
                                        <th>Questão</th>
                                        <th>Habilidade</th>
                                        <th>Média</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($estatisticasPorQuestao as $questao)
                                        <tr>
                                            <td>{{ $questao->disciplina }}</td>
                                            <td>{{ Str::limit($questao->enunciado, 50) }}</td>
                                            <td>{{ $questao->habilidade_resumida }}...</td>
                                            <td>{{ number_format($questao->media, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer clearfix">
                            {{ $estatisticasPorQuestao->links() }}
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Nenhum dado encontrado. Aplique os filtros para visualizar as estatísticas.
                    </div>
                @endif

    <!-- Estatísticas por Escola -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Estatísticas por Escola</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-primary bg-primary text-white">
                        <tr>
                            <th>Escola</th>
                            <th>Total de Respostas</th>
                            <th>Acertos</th>
                            <th>% de Acertos</th>
                            <th>Média Final (0-10)</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticasPorEscola as $estatistica)
                            <tr>
                                <td>{{ $estatistica['escola'] }}</td>
                                <td>{{ $estatistica['total_respostas'] }}</td>
                                <td>{{ $estatistica['acertos'] }}</td>
                                <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                                <td>{{ number_format($estatistica['media_final'], 2) }}</td>
                                <td>
                                    <a href="{{ route('respostas_simulados.admin.detalhes-escola', [
                                        'escola_id' => $escolas->firstWhere('nome', $estatistica['escola'])->id,
                                        'simulado_id' => $request->simulado_id,
                                        'ano_id' => $request->ano_id,
                                        'habilidade_id' => $request->habilidade_id
                                    ]) }}" 
                                    class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Ver
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
            <h5 class="card-title mb-0">Estatísticas por Ano</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-primary bg-primary text-white">
                        <tr>
                            <th>Ano</th>
                            <th>Total de Respostas</th>
                            <th>Acertos</th>
                            <th>% de Acertos</th>
                            <th>Média Final (0-10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticasPorAno as $estatistica)
                            <tr>
                                <td>{{ $estatistica['ano'] }}</td>
                                <td>{{ $estatistica['total_respostas'] }}</td>
                                <td>{{ $estatistica['acertos'] }}</td>
                                <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                                <td>{{ number_format($estatistica['media_final'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Médias Gerais -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title">Médias Gerais</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="stat-card bg-light p-3 rounded">
                        <h6 class="stat-title">Média Geral (1º ao 5º Ano)</h6>
                        <p class="stat-value">{{ number_format($mediaGeral1a5, 2) }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card bg-light p-3 rounded">
                        <h6 class="stat-title">Média Geral (6º ao 9º Ano)</h6>
                        <p class="stat-value">{{ number_format($mediaGeral6a9, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas por Habilidade -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Estatísticas por Habilidade</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-primary bg-primary text-white">
                        <tr>
                            <th>Habilidade</th>
                            <th>Total de Respostas</th>
                            <th>Acertos</th>
                            <th>% de Acertos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticasPorHabilidade as $estatistica)
                            <tr>
                                <td>{{ $estatistica['habilidade'] }}</td>
                                <td>{{ $estatistica['total_respostas'] }}</td>
                                <td>{{ $estatistica['acertos'] }}</td>
                                <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

   
    <!-- Scripts para Gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico de Desempenho por Habilidade
        const ctxHabilidades = document.getElementById('graficoHabilidades').getContext('2d');
        new Chart(ctxHabilidades, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($estatisticasPorHabilidade, 'habilidade')) !!},
                datasets: [{
                    label: '% de Acertos',
                    data: {!! json_encode(array_column($estatisticasPorHabilidade, 'porcentagem_acertos')) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Porcentagem de Acertos (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Habilidades'
                        }
                    }
                }
            }
        });

        // Gráfico de Média por Escola
        const ctxEscolas = document.getElementById('graficoEscolas').getContext('2d');
        new Chart(ctxEscolas, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($estatisticasPorEscola, 'escola')) !!},
                datasets: [{
                    label: 'Média Final (0-10)',
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

        // Novo Gráfico: Distribuição por Deficiência
        const ctxDeficiencias = document.getElementById('graficoDeficiencias').getContext('2d');
        new Chart(ctxDeficiencias, {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_map(function($item) {
                    switch($item['deficiencia']) {
                        case 'DV': return 'Deficiência Visual';
                        case 'DA': return 'Deficiência Auditiva';
                        case 'DF': return 'Deficiência Física';
                        case 'DI': return 'Deficiência Intelectual';
                        case 'TEA': return 'Autismo';
                        default: return $item['deficiencia'];
                    }
                }, $alunosPorDeficiencia)) !!},
                datasets: [{
                    data: {!! json_encode(array_column($alunosPorDeficiencia, 'total')) !!},
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.raw;
                                const percentage = Math.round((value / total) * 100);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    </script>
    @endif

    <style>
        .stat-card {
            transition: transform 0.3s ease;
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
    </style>
</div>
@endsection