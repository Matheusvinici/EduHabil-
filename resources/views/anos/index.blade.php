@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Anos Escolares</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('anos.create') }}" class="btn btn-primary mb-3">Criar Novo Ano Escolar</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($anos as $ano)
            <tr>
                <td>{{ $ano->id }}</td>
                <td>{{ $ano->nome }}</td>
                <td>
                    <a href="{{ route('anos.edit', $ano->id) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form action="{{ route('anos.destroy', $ano->id) }}" method="POST" style="display:inline-block;">
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
