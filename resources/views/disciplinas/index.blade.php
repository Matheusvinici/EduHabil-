@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Disciplinas</h3>
            <div class="card-tools">
                <a href="{{ route('disciplinas.create') }}" class="btn btn-primary">Criar Nova Disciplina</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive"> <!-- Adicionado para melhorar a responsividade da tabela -->
                <table class="table table-bordered table-striped table-hover"> <!-- Adicionado table-striped e table-hover para melhorar a estilização -->
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($disciplinas as $disciplina)
                            <tr>
                                <td>{{ $disciplina->id }}</td>
                                <td>{{ $disciplina->nome }}</td>
                                <td>
                                    <a href="{{ route('disciplinas.show', $disciplina->id) }}" class="btn btn-info btn-sm">Ver</a>
                                    <a href="{{ route('disciplinas.edit', $disciplina->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                    <form action="{{ route('disciplinas.destroy', $disciplina->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta disciplina?')">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection