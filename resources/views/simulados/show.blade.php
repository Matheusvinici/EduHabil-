@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title m-0">Detalhes do Simulado</h3>
        </div>
        <div class="card-body">
            <h4 class="mb-4">{{ $simulado->nome }}</h4>

            <div class="mb-3">
                <strong>Tempo Limite:</strong>
                {{ $simulado->tempo_limite ? $simulado->tempo_limite . ' minutos' : 'Sem limite' }}
            </div>

            <div class="mb-3">
                <strong>Ano:</strong>
                {{ $simulado->ano->nome }}
            </div>

            <div class="mb-3">
                <strong>Descrição:</strong>
                <p>{{ $simulado->descricao }}</p>
            </div>

            <div class="mb-3">
                <strong>Perguntas:</strong>
                <ul class="list-group">
                    @foreach ($simulado->perguntas as $pergunta)
                        <li class="list-group-item">
                            <strong>{{ $pergunta->enunciado }}</strong>
                            @if ($pergunta->imagem)
                                <div class="text-center mt-2">
                                    <img src="{{ asset('storage/' . $pergunta->imagem) }}" alt="Imagem da pergunta" class="img-fluid" style="max-width: 500px;">
                                </div>
                            @endif
                            <div class="mt-2">
                                <p>A) {{ $pergunta->alternativa_a }}</p>
                                <p>B) {{ $pergunta->alternativa_b }}</p>
                                <p>C) {{ $pergunta->alternativa_c }}</p>
                                <p>D) {{ $pergunta->alternativa_d }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="text-end">
                <a href="{{ route('simulados.index') }}" class="btn btn-secondary">Voltar</a>
            </div>
        </div>
    </div>
</div>
@endsection