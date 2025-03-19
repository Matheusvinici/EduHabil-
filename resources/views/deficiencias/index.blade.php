@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Deficiências</h1>
    <a href="{{ route('deficiencias.create') }}" class="btn btn-primary mb-3">Nova Deficiência</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($deficiencias as $deficiencia)
                <tr>
                    <td>{{ $deficiencia->id }}</td>
                    <td>{{ $deficiencia->nome }}</td>
                    <td>{{ $deficiencia->descricao }}</td>
                    <td>
                        <a href="{{ route('deficiencias.show', $deficiencia->id) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('deficiencias.edit', $deficiencia->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('deficiencias.destroy', $deficiencia->id) }}" method="POST" style="display:inline;">
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