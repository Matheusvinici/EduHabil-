@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Turmas da Escola</h1>

    <!-- Formulário de pesquisa -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('turmas.coordenador.index') }}" method="GET" class="form-inline">
                <div class="form-group mr-3">
                    <label for="nome_turma" class="mr-2">Pesquisar por Turma:</label>
                    <input type="text" name="nome_turma" id="nome_turma" class="form-control" value="{{ request('nome_turma') }}" placeholder="Nome da turma">
                </div>
                <button type="submit" class="btn btn-primary">Pesquisar</button>
            </form>
        </div>
    </div>

    <!-- Tabela de turmas -->
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome da Turma</th>
                        <th>Professor</th>
                        <th>Quantidade de Alunos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($turmas as $turma)
                        <tr>
                            <td>{{ $turma->nome_turma }}</td>
                            <td>{{ $turma->professor->name }}</td>
                            <td>{{ $turma->quantidade_alunos }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Nenhuma turma encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginação -->
    <div class="d-flex justify-content-center mt-4">
        {{ $turmas->appends(['nome_turma' => request('nome_turma')])->links() }}
    </div>
</div>
@endsection