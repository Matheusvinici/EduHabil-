@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Simulados Disponíveis</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nome do Simulado</th>
                <th>Quantidade de Perguntas</th>
                <th>Quantidade de Acertos</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($simulados as $simulado)
                <tr>
                    <td>{{ $simulado->nome }}</td>
                    <td>{{ $simulado->perguntas_count }}</td>
                    <td>
                        @if ($simulado->respostas()->where('user_id', auth()->id())->exists())
                            {{ $simulado->respostas()->where('user_id', auth()->id())->where('correta', true)->count() }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($simulado->respostas()->where('user_id', auth()->id())->exists())
                            <a href="{{ route('respostas_simulados.show', $simulado->id) }}" class="btn btn-info">Ver Resultado</a>
                        @else
                            <a href="{{ route('respostas_simulados.create', $simulado->id) }}" class="btn btn-primary">Responder</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection