@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Respostas da Prova: {{ $prova->nome }}</h2>

    @foreach ($respostas as $resposta)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Aluno: {{ $resposta->user->name }}</h5>
                <p class="card-text">{{ $resposta->questao->enunciado }}</p>

                <p><strong>Resposta do Aluno:</strong> {{ $resposta->resposta }}</p>
                <p><strong>Resposta Correta:</strong> {{ $resposta->questao->resposta_correta }}</p>

                @if ($resposta->correta)
                    <span class="badge bg-success">Correta</span>
                @else
                    <span class="badge bg-danger">Incorreta</span>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection