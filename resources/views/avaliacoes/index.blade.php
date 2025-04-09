@extends('layouts.app')

@section('content')
    <h1>Avaliações de Tutoria</h1>
    <a href="{{ route('avaliacoes.create') }}" class="btn btn-primary">Nova Avaliação</a>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Tutor</th>
                <th>Escola</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($avaliacoes as $avaliacao)
                <tr>
                    <td>{{ $avaliacao->tutor->name }}</td>
                    <td>{{ $avaliacao->escola->nome }}</td>
                    <td>
                        <a href="{{ route('avaliacoes.edit', $avaliacao->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('avaliacoes.destroy', $avaliacao->id) }}" method="POST" class="d-inline">
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