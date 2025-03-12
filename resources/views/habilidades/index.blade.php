@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Habilidades</h3>
            <div class="card-tools">
                <a href="{{ route('habilidades.create') }}" class="btn btn-primary">Cadastrar Habilidade</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive"> <!-- Melhora a responsividade da tabela -->
                <table class="table table-bordered table-striped table-hover"> <!-- Adiciona estilos à tabela -->
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ano</th>
                            <th>Disciplina</th>
                            <th>Descrição</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($habilidades as $habilidade)
                            <tr>
                                <td>{{ $habilidade->id }}</td>
                                <td>{{ $habilidade->ano->nome }}</td>
                                <td>{{ $habilidade->disciplina->nome }}</td>
                                <td>{{ $habilidade->descricao }}</td>
                                <td>
                                    <a href="{{ route('habilidades.show', $habilidade) }}" class="btn btn-info btn-sm">Ver</a>
                                    <a href="{{ route('habilidades.edit', $habilidade) }}" class="btn btn-warning btn-sm">Editar</a>
                                    <form action="{{ route('habilidades.destroy', $habilidade) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta habilidade?')">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection