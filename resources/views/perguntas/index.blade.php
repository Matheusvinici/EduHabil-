@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Perguntas</h3>
            <div class="card-tools">
                <a href="{{ route('perguntas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Criar Nova Pergunta
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Enunciado</th>
                        <th>Ano</th>
                        <th>Disciplina</th>
                        <th>Habilidade</th>
                        <th>Resposta Correta</th>
                        <th>Imagem</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($perguntas as $pergunta)
                        <tr>
                            <td>{{ $pergunta->id }}</td>
                            <td>{{ Str::limit($pergunta->enunciado, 50) }}</td>
                            <td>{{ $pergunta->ano->nome }}</td>
                            <td>{{ $pergunta->disciplina->nome }}</td>
                            <td>{{ $pergunta->habilidade->descricao }}</td>
                            <td>{{ $pergunta->resposta_correta }}</td>
                            <td>
                                @if ($pergunta->imagem)
                                    <a href="{{ asset('storage/' . $pergunta->imagem) }}" target="_blank" title="Abrir imagem em nova guia">
                                        Ver Imagem
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('perguntas.show', $pergunta->id) }}" class="btn btn-info btn-sm" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('perguntas.edit', $pergunta->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('perguntas.destroy', $pergunta->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta pergunta?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Nenhuma pergunta cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection