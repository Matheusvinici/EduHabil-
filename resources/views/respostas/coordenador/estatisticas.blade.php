@extends('layouts.app')

@section('title', 'Estatísticas da Escola')

@section('header', 'Estatísticas da Escola')

@section('content')
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Filtros</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('respostas.coordenador.estatisticas') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label for="habilidade_id" class="form-label">Habilidade:</label>
                    <select name="habilidade_id" id="habilidade_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($habilidades as $habilidade)
                            <option value="{{ $habilidade->id }}" {{ $request->habilidade_id == $habilidade->id ? 'selected' : '' }}>{{ $habilidade->descricao }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
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
                <div class="col-md-4">
                    <p><strong>Total de Provas:</strong> {{ $totalProvas }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Total de Professores:</strong> {{ $totalProfessores }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Total de Questões Respondidas:</strong> {{ $totalQuestoesRespondidas }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas da Escola -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Estatísticas da Escola</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Escola</th>
                            <th>Total de Questões</th>
                            <th>Acertos</th>
                            <th>% de Acertos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $escola->nome }}</td>
                            <td>{{ $estatisticasEscola['total_questoes'] }}</td>
                            <td>{{ $estatisticasEscola['acertos'] }}</td>
                            <td>{{ number_format($estatisticasEscola['porcentagem_acertos'], 2) }}%</td>
                        </tr>
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
                            <th>Total de Questões</th>
                            <th>Acertos</th>
                            <th>% de Acertos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estatisticasPorHabilidade as $estatistica)
                            <tr>
                                <td>{{ $estatistica['habilidade'] }}</td>
                                <td>{{ $estatistica['total_questoes'] }}</td>
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