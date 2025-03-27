@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Perguntas</h3>
                <a href="{{ route('perguntas.create') }}" class="btn btn-primary float-right">Cadastrar Nova Pergunta</a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Ano</th>
                                <th scope="col">Disciplina</th>
                                <th scope="col">Enunciado</th>
                                <th scope="col">Resposta Correta</th>
                                <th scope="col">Imagem</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($perguntas as $pergunta)
                                <tr>
                                    <td>{{ $pergunta->id }}</td>
                                    <td>{{ $pergunta->ano->nome }}</td>
                                    <td>{{ $pergunta->disciplina->nome }}</td>
                                    <td>{{ Str::limit($pergunta->enunciado, 50) }}</td>
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
                                        <a href="{{ route('perguntas.show', $pergunta->id) }}" class="btn btn-info btn-sm">Detalhes</a>
                                        <a href="{{ route('perguntas.edit', $pergunta->id) }}" class="btn btn-primary btn-sm">Editar</a>

                                        <form action="{{ route('perguntas.destroy', $pergunta->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta pergunta?')">Excluir</button>
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
