@extends('layouts.app')

@section('title', 'Estatísticas dos Alunos')

@section('header', 'Estatísticas dos Alunos')

@section('content')
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Filtros</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('respostas_simulados.professor.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="simulado_id" class="form-label">Simulado:</label>
                    <select name="simulado_id" id="simulado_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($simulados as $simulado)
                            <option value="{{ $simulado->id }}" {{ $request->simulado_id == $simulado->id ? 'selected' : '' }}>{{ $simulado->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="ano_id" class="form-label">Ano:</label>
                    <select name="ano_id" id="ano_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($anos as $ano)
                            <option value="{{ $ano->id }}" {{ $request->ano_id == $ano->id ? 'selected' : '' }}>{{ $ano->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="habilidade_id" class="form-label">Habilidade:</label>
                    <select name="habilidade_id" id="habilidade_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($habilidades as $habilidade)
                            <option value="{{ $habilidade->id }}" {{ $request->habilidade_id == $habilidade->id ? 'selected' : '' }}>{{ $habilidade->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Dados Gerais -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Dados Gerais</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Total de Alunos:</strong> {{ $totalAlunos }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total de Respostas:</strong> {{ $totalRespostas }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Média da Turma por Simulado -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Média da Turma por Simulado</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Simulado</th>
                            <th>Média da Turma (0-10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mediaTurmaPorSimulado as $media)
                            <tr>
                                <td>{{ $media['simulado'] }}</td>
                                <td>{{ number_format($media['media_turma'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Estatísticas por Aluno -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Estatísticas por Aluno</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Aluno</th>
                            <th>Total de Respostas</th>
                            <th>Acertos</th>
                            <th>% de Acertos</th>
                            <th>Média Final (0-10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticasPorAluno as $estatistica)
                            <tr>
                                <td>{{ $estatistica['aluno'] }}</td>
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

    <!-- Estatísticas por Habilidade -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Estatísticas por Habilidade</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
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
@endsection