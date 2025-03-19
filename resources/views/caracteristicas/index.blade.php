@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Características</h1>
    <a href="{{ route('caracteristicas.create') }}" class="btn btn-primary mb-3">Nova Característica</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Deficiência</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($caracteristicas as $caracteristica)
                <tr>
                    <td>{{ $caracteristica->id }}</td>
                    <td>{{ $caracteristica->nome }}</td>
                    <td>{{ $caracteristica->descricao }}</td>
                    <td>{{ $caracteristica->deficiencia->nome }}</td>
                    <td>
                        <a href="{{ route('caracteristicas.show', $caracteristica->id) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('caracteristicas.edit', $caracteristica->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('caracteristicas.destroy', $caracteristica->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection