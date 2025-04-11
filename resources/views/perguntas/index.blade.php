@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Lista de Perguntas</h3>
            <a href="{{ route('perguntas.create') }}" class="btn btn-light float-right">Nova Pergunta</a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead bg-dark">
                        <tr>
                            <th>ID</th>
                            <th>Ano</th>
                            <th>Disciplina</th>
                            <th>Enunciado</th>
                            <th>Peso</th>
                            <th>TRI (a/b/c)</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($perguntas as $pergunta)
                            <tr>
                                <td>{{ $pergunta->id }}</td>
                                <td>{{ $pergunta->ano->nome }}</td>
                                <td>{{ $pergunta->disciplina->nome }}</td>
                                <td>{{ Str::limit($pergunta->enunciado, 50) }}</td>
                                <td>{{ $pergunta->peso }}</td>
                                <td>
                                    {{ $pergunta->tri_a }} / 
                                    {{ $pergunta->tri_b }} / 
                                    {{ $pergunta->tri_c }}
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('perguntas.show', $pergunta->id) }}" class="btn btn-sm btn-info" title="Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('perguntas.edit', $pergunta->id) }}" class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('perguntas.destroy', $pergunta->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Tem certeza?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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