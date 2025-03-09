@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Provas Disponíveis</h2>

    @if ($provas->isEmpty())
        <div class="alert alert-info">
            Nenhuma prova disponível no momento.
        </div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nome da Prova</th>
                    <th>Disciplina</th>
                    <th>Número de Questões</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($provas as $prova)
                    <tr>
                        <td>{{ $prova->nome }}</td>
                        <td>{{ $prova->disciplina->nome }}</td>
                        <td>{{ $prova->questoes_count }}</td>
                        <td>
                            @if (auth()->user()->role === 'aluno')
                                <a href="{{ route('respostas.create', $prova) }}" class="btn btn-primary">Responder</a>
                            @else
                                <a href="{{ route('respostas.show', $prova) }}" class="btn btn-info">Ver Respostas</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection