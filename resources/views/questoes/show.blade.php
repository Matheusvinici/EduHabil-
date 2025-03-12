@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-lg border-0 rounded">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title m-0">Detalhes da Questão</h3>
            <a href="{{ route('questoes.index') }}" class="btn btn-danger">Voltar</a>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>ID:</strong> {{ $questao->id }}</li>
                <li class="list-group-item"><strong>Ano:</strong> {{ $questao->ano->nome }}</li>
                <li class="list-group-item"><strong>Disciplina:</strong> {{ $questao->disciplina->nome }}</li>
                <li class="list-group-item"><strong>Enunciado:</strong> {{ $questao->enunciado }}</li>
                <li class="list-group-item"><strong>Alternativa A:</strong> {{ $questao->alternativa_a }}</li>
                <li class="list-group-item"><strong>Alternativa B:</strong> {{ $questao->alternativa_b }}</li>
                <li class="list-group-item"><strong>Alternativa C:</strong> {{ $questao->alternativa_c }}</li>
                <li class="list-group-item"><strong>Alternativa D:</strong> {{ $questao->alternativa_d }}</li>
                <li class="list-group-item"><strong>Resposta Correta:</strong> <span class="badge badge-success">{{ $questao->resposta_correta }}</span></li>
            </ul>
        </div>
    </div>
</div>
@endsection