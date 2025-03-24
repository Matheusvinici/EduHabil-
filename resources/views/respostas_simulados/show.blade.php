@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Resultado do Simulado: {{ $simulado->nome }}</h2>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Resumo</h5>
            <p class="card-text">
                Total de perguntas: {{ $total }}<br>
                Total de acertos: {{ $acertos }}<br>
                Porcentagem de acertos: {{ number_format(($acertos / $total) * 100, 2) }}%
            </p>
        </div>
    </div>

    @foreach ($respostas as $resposta)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Pergunta {{ $loop->iteration }}</h5>
                <p class="card-text">{{ $resposta->pergunta->enunciado }}</p>

                @if($resposta->pergunta->imagem)
                    <img src="{{ asset('storage/' . $resposta->pergunta->imagem) }}" alt="Imagem da pergunta" style="max-width: 100%;" class="mb-3">
                @endif

                <p class="card-text">
                    <strong>Sua resposta:</strong> {{ $resposta->resposta }}<br>
                    <strong>Resposta correta:</strong> {{ $resposta->pergunta->resposta_correta }}<br>
                    <strong>Resultado:</strong> 
                    @if($resposta->correta)
                        <span class="text-success">Acertou</span>
                    @else
                        <span class="text-danger">Errou</span>
                    @endif
                </p>
            </div>
        </div>
    @endforeach

    <a href="{{ route('respostas_simulados.aluno.index') }}" class="btn btn-primary">Voltar</a>
</div>
@endsection