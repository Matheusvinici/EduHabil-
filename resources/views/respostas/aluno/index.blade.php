@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Provas Disponíveis</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nome da Prova</th>
                <th>Quantidade de Questões</th>
                <th>Quantidade de Acertos</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($provas as $prova)
                <tr>
                    <td>{{ $prova->nome }}</td>
                    <td>{{ $prova->questoes_count }}</td>
                    <td>
                        @if ($prova->respostas()->where('user_id', auth()->id())->exists())
                            {{ $prova->respostas()->where('user_id', auth()->id())->where('correta', true)->count() }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($prova->respostas()->where('user_id', auth()->id())->exists())
                            <a href="{{ route('respostas.show', $prova->id) }}" class="btn btn-info">Ver Resultado</a>
                        @else
                            <a href="{{ route('respostas.create', $prova->id) }}" class="btn btn-primary">Responder</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection