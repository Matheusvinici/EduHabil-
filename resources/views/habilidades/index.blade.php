@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h3 class="card-title mb-0">Lista de Habilidades</h3>
            <div class="card-tools">
                <a href="{{ route('habilidades.create') }}" class="btn btn-light">
                    <i class="fas fa-plus"></i> Criar Habilidade
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive"> 
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="thead-dark">
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
                                <td class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('habilidades.show', $habilidade) }}" class="btn btn-info btn-sm" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('habilidades.edit', $habilidade) }}" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('habilidades.destroy', $habilidade) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta habilidade?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $habilidades->links() }}
            </div>
        </div>
    </div>
</div>
@endsection