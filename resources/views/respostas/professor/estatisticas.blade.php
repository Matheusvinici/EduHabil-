@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Estatísticas das Provas</h2>

    <!-- Filtros -->
    <form action="{{ route('respostas.professor.estatisticas') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <label for="prova_id">Prova:</label>
                <select name="prova_id" id="prova_id" class="form-control">
                    <option value="">Todas</option>
                    @foreach ($provas as $prova)
                        <option value="{{ $prova->id }}">{{ $prova->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="habilidade_id">Habilidade:</label>
                <select name="habilidade_id" id="habilidade_id" class="form-control">
                    <option value="">Todas</option>
                    @foreach ($habilidades as $habilidade)
                        <option value="{{ $habilidade->id }}">{{ $habilidade->descricao }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="ano_id">Ano:</label>
                <select name="ano_id" id="ano_id" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($anos as $ano)
                        <option value="{{ $ano->id }}">{{ $ano->nome }}</option>
                    @endforeach
                </select>
            </div>
            
        </div>
        <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
    </form>

    <!-- Tabela de estatísticas -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Prova</th>
                <th>Aluno</th>
                <th>Acertos</th>
                <th>Total de Questões</th>
                <th>% de Acertos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($estatisticas as $estatistica)
                <tr>
                    <td>{{ $estatistica['prova'] }}</td>
                    <td>{{ $estatistica['aluno'] }}</td>
                    <td>{{ $estatistica['acertos'] }}</td>
                    <td>{{ $estatistica['total_questoes'] }}</td>
                    <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Botão para gerar PDF -->
    <a href="{{ route('respostas.professor.estatisticas.pdf') }}" class="btn btn-success">Gerar PDF</a>
</div>
@endsection