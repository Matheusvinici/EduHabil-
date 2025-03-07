@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Responder Prova: {{ $prova->nome }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('respostas.store', $prova->id) }}" method="POST">
        @csrf

        @foreach ($prova->questoes as $questao)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Questão {{ $loop->iteration }}</h5>
                    <p class="card-text">{{ $questao->enunciado }}</p>

                    <div class="form-group">
                        <label>Sua Resposta:</label>
                        <div>
                            @foreach (['A', 'B', 'C', 'D'] as $alternativa)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="respostas[{{ $questao->id }}]" id="questao_{{ $questao->id }}_{{ $alternativa }}" value="{{ $alternativa }}" required>
                                    <label class="form-check-label" for="questao_{{ $questao->id }}_{{ $alternativa }}">{{ $alternativa }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-primary">Enviar Respostas</button>
    </form>
</div>
@endsection