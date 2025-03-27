@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-lg border-0 rounded">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title m-0">Detalhes da Pergunta</h3>
            <a href="{{ route('perguntas.index') }}" class="btn btn-danger">Voltar</a>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>ID:</strong> {{ $pergunta->id }}</li>
                <li class="list-group-item"><strong>Ano:</strong> {{ $pergunta->ano->nome }}</li>
                <li class="list-group-item"><strong>Disciplina:</strong> {{ $pergunta->disciplina->nome }}</li>
                <li class="list-group-item"><strong>Habilidade:</strong> {{ $pergunta->habilidade->nome }}</li>
                <li class="list-group-item"><strong>Enunciado:</strong> {{ $pergunta->enunciado }}</li>

                @if($pergunta->imagem)
                <li class="list-group-item text-center">
                    <strong>Imagem da Pergunta:</strong>
                    <br>
                    <img src="{{ asset('storage/' . $pergunta->imagem) }}"
                        alt="Imagem da pergunta"
                        class="rounded {{ $pergunta->imagem_tamanho }}">
                </li>
                <li class="list-group-item"><strong>Tamanho da Imagem:</strong>
                    {{ strtoupper(str_replace('w-', '', $pergunta->imagem_tamanho)) }}%
                </li>
                @endif

                <li class="list-group-item"><strong>Alternativa A:</strong> {{ $pergunta->alternativa_a }}</li>
                <li class="list-group-item"><strong>Alternativa B:</strong> {{ $pergunta->alternativa_b }}</li>
                <li class="list-group-item"><strong>Alternativa C:</strong> {{ $pergunta->alternativa_c }}</li>
                <li class="list-group-item"><strong>Alternativa D:</strong> {{ $pergunta->alternativa_d }}</li>
                <li class="list-group-item"><strong>Resposta Correta:</strong>
                    <span class="badge badge-success">{{ $pergunta->resposta_correta }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
