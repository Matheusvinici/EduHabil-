@extends('layouts.app')

@section('content')
    <h1>Critérios de Avaliação</h1>
    <a href="{{ route('criterios.create') }}" class="btn btn-primary">Novo Critério</a>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Categoria</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($criterios as $criterio)
                <tr>
                    <td>{{ $criterio->categoria }}</td>
                    <td>{{ $criterio->descricao }}</td>
                    <td>
                        <a href="{{ route('criterios.edit', $criterio->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('criterios.destroy', $criterio->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection