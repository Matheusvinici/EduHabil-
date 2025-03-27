@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Questões</h3>
                <a href="{{ route('questoes.create') }}" class="btn btn-primary float-right">Cadastrar Nova Questão</a>
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
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($questoes as $questao)
                                <tr>
                                    <td>{{ $questao->id }}</td>
                                    <td>{{ $questao->ano->nome }}</td>
                                    <td>{{ $questao->disciplina->nome }}</td>
                                    <td>{{ Str::limit($questao->enunciado, 50) }}</td>
                                    <td>{{ $questao->resposta_correta }}</td>
                                    <td>
                                        <a href="{{ route('questoes.show', $questao->id) }}" class="btn btn-info btn-sm">Detalhes</a>
                                        <a href="{{ route('questoes.edit', $questao->id) }}" class="btn btn-primary btn-sm">Editar</a>

                                        <form action="{{ route('questoes.destroy', $questao->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta questão?')">Excluir</button>
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
