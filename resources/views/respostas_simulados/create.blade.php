@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Responder Simulado: {{ $simulado->nome }}</h2>

    <form action="{{ route('respostas_simulados.store', $simulado) }}" method="POST">
        @csrf
        <input type="hidden" name="simulado_id" value="{{ $simulado->id }}">

        @foreach ($perguntas as $pergunta)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pergunta {{ $loop->iteration }}</h5>
                    <p class="card-text">{{ $pergunta->enunciado }}</p>

                    @if($pergunta->imagem)
                        <img src="{{ asset('storage/' . $pergunta->imagem) }}" alt="Imagem da pergunta" style="max-width: 100%;" class="mb-3">
                    @endif

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="respostas[{{ $pergunta->id }}]" value="A" required>
                        <label class="form-check-label">A) {{ $pergunta->alternativa_a }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="respostas[{{ $pergunta->id }}]" value="B">
                        <label class="form-check-label">B) {{ $pergunta->alternativa_b }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="respostas[{{ $pergunta->id }}]" value="C">
                        <label class="form-check-label">C) {{ $pergunta->alternativa_c }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="respostas[{{ $pergunta->id }}]" value="D">
                        <label class="form-check-label">D) {{ $pergunta->alternativa_d }}</label>
                    </div>
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-success">Finalizar Simulado</button>
    </form>
</div>
@endsection