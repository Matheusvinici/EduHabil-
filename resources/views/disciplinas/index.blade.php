@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Disciplinas</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('disciplinas.create') }}" class="btn btn-primary mb-3">Criar Nova Disciplina</a>

    <table class="table table-bordered">
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
                    <a href="{{ route('disciplinas.edit', $disciplina->id) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form action="{{ route('disciplinas.destroy', $disciplina->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
