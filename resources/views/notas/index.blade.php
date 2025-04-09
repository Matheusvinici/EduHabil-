@extends('layouts.app')

@section('content')
    <h1>Notas de Avaliação</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Escola</th>
                <th>Tutor</th>
                <th>Critério</th>
                <th>Nota</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notas as $nota)
                <tr>
                    <td>{{ $nota->avaliacao->escola->nome }}</td>
                    <td>{{ $nota->avaliacao->tutor->name }}</td>
                    <td>{{ $nota->criterio->descricao }}</td>
                    <td>{{ $nota->nota }}/5</td>
                    <td>
                        <a href="{{ route('notas.edit', $nota->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('notas.destroy', $nota->id) }}" method="POST" class="d-inline">
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