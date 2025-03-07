@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Questões</h1>
        <a href="{{ route('questoes.create') }}" class="btn btn-primary mb-3">Cadastrar Nova Questão</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Ano</th>
                    <th scope="col">Disciplina</th>
                    <th scope="col">Habilidade</th>
                    <th scope="col">Enunciado</th>
                    <th scope="col">Resposta Correta</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($questoes as $questao)
                    <tr>
                        <td>{{ $questao->ano->nome }}</td>
                        <td>{{ $questao->disciplina->nome }}</td>
                        <td>{{ $questao->habilidade->descricao }}</td>
                        <td>{{ $questao->enunciado }}</td>
                        <td>{{ $questao->resposta_correta }}</td>
                        <td>
                            <a href="{{ route('questoes.edit', $questao->id) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('questoes.destroy', $questao->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
