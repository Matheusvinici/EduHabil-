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
        <div class="table-responsive"> <!-- Adicionado para melhorar a responsividade da tabela -->
            <table class="table table-bordered table-striped table-hover"> <!-- Adicionado table-striped e table-hover para melhorar a estilização -->
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Endereço</th>
                        <th>Telefone</th>
                        <th>Código INEP</th> <!-- Alterado para "Código INEP" -->
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
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta escola?')">Excluir</button> <!-- Adicionado confirmação antes de excluir -->
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection