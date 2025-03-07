@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Habilidades</h1>
    <a href="{{ route('habilidades.create') }}" class="btn btn-primary">Cadastrar Habilidade</a>

    <table class="table mt-4">
        <thead>
            <tr>
                <th>Ano</th>
                <th>Disciplina</th>
                <th>Unidade</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($habilidades as $habilidade)
                <tr>
                    <td>{{ $habilidade->ano->nome }}</td>
                    <td>{{ $habilidade->disciplina->nome }}</td>
                    <td>{{ $habilidade->unidade->nome }}</td>
                    <td>{{ $habilidade->descricao }}</td>
                    <td>
                        <a href="{{ route('habilidades.edit', $habilidade) }}" class="btn btn-warning">Editar</a>
                        <form action="{{ route('habilidades.destroy', $habilidade) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
