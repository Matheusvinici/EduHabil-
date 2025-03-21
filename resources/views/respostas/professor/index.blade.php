@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Respostas dos Alunos</h2>

    <!-- Formulário de filtro por nome da prova -->
    <form method="GET" action="{{ route('respostas.professor.index') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="prova_nome" class="form-control" placeholder="Pesquisar por nome da prova" value="{{ $provaNome }}">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    @if ($provas->isEmpty())
        <div class="alert alert-info">
            Nenhuma prova encontrada.
        </div>
    @else
        <!-- Tabela de porcentagem de acertos por prova -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Porcentagem de Acertos por Prova</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Prova</th>
                            <th>Porcentagem de Acertos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($porcentagemAcertosPorProva as $provaId => $dados)
                            <tr>
                                <td>{{ $dados['nome'] }}</td>
                                <td>{{ number_format($dados['porcentagem'], 2) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Lista de provas e alunos -->
        @foreach ($provas as $prova)
            <div class="card mb-4">
                <div class="card-header">
                    <h3>{{ $prova->nome }}</h3>
                    <p class="mb-0">Disciplina: {{ $prova->disciplina->nome }}</p>
                </div>
                <div class="card-body">
                    <h4>Alunos que responderam:</h4>

                    @if ($prova->respostas->isEmpty())
                        <div class="alert alert-warning">
                            Nenhum aluno respondeu esta prova.
                        </div>
                    @else
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Acertos</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prova->respostas->groupBy('user_id') as $respostasAluno)
                                    @php
                                        $aluno = $respostasAluno->first()->user;
                                        $totalRespostas = $respostasAluno->count();
                                        $totalAcertos = $respostasAluno->where('correta', true)->count();
                                    @endphp
                                    <tr>
                                        <td>{{ $aluno->name }}</td>
                                        <td>{{ $totalAcertos }} de {{ $totalRespostas }}</td>
                                        <td>
                                            <a href="{{ route('respostas.professor.show', ['prova' => $prova->id, 'aluno' => $aluno->id]) }}" class="btn btn-info">
                                                Ver Respostas
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection