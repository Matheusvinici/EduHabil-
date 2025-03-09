@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listagem de Escolas</h3>
        <div class="card-tools">
            <a href="{{ route('escolas.create') }}" class="btn btn-primary">Nova Escola</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Endereço</th>
                    <th>Telefone</th>
                    <th>Código</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($escolas as $escola)
                    <tr>
                        <td>{{ $escola->id }}</td>
                        <td>{{ $escola->nome }}</td>
                        <td>{{ $escola->endereco }}</td>
                        <td>{{ $escola->telefone }}</td>
                        <td>{{ $escola->codigo_escola }}</td>
                        <td>
                            <a href="{{ route('escolas.show', $escola->id) }}" class="btn btn-sm btn-info">Ver</a>
                            <a href="{{ route('escolas.edit', $escola->id) }}" class="btn btn-sm btn-primary">Editar</a>
                            <form action="{{ route('escolas.destroy', $escola->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection