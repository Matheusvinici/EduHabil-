@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Responder Prova: {{ $prova->nome }}</h2>

    <form action="{{ route('respostas.store', $prova) }}" method="POST">
    @csrf
    <input type="hidden" name="prova_id" value="{{ $prova->id }}">

    @foreach ($questoes as $questao)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Questão {{ $loop->iteration }}</h5>
                <p class="card-text">{{ $questao->enunciado }}</p>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="respostas[{{ $questao->id }}]" value="A" required>
                    <label class="form-check-label">A) {{ $questao->alternativa_a }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="respostas[{{ $questao->id }}]" value="B" required>
                    <label class="form-check-label">B) {{ $questao->alternativa_b }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="respostas[{{ $questao->id }}]" value="C" required>
                    <label class="form-check-label">C) {{ $questao->alternativa_c }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="respostas[{{ $questao->id }}]" value="D" required>
                    <label class="form-check-label">D) {{ $questao->alternativa_d }}</label>
                </div>
            </div>
        </div>
    @endforeach

    <button type="submit" class="btn btn-success">Finalizar Prova</button>
</form>
</div>
@endsection