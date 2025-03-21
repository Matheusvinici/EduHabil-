@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Provas da Escola</h1>

    <!-- Formulário de pesquisa -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('provas.coordenador.index') }}" method="GET" class="form-inline">
                <div class="form-group mr-3">
                    <label for="professor_nome" class="mr-2">Pesquisar por Professor:</label>
                    <input type="text" name="professor_nome" id="professor_nome" class="form-control" value="{{ request('professor_nome') }}" placeholder="Nome do professor">
                </div>
                <button type="submit" class="btn btn-primary">Pesquisar</button>
            </form>
        </div>
    </div>

    <!-- Tabela de provas -->
    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Nome</th>
                        <th>Ano</th>
                        <th>Disciplina</th>
                        <th>Professor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($provas as $prova)
                        <tr>
                            <td>{{ $prova->nome }}</td>
                            <td>{{ $prova->ano->nome }}</td>
                            <td>{{ $prova->disciplina->nome }}</td>
                            <td>{{ $prova->professor->name }}</td>
                            <td>
                                <a href="{{ route('provas.gerarPDF', $prova) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-download"></i> Baixar PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Nenhuma prova encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginação -->
    <div class="d-flex justify-content-center mt-4">
        {{ $provas->appends(['professor_nome' => request('professor_nome')])->links() }}
    </div>
</div>
@endsection