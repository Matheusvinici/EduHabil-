@extends('layouts.app')

@section('content')
    <h1>Detalhes da Nota</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>Escola:</strong> {{ $nota->avaliacao->escola->nome }}</p>
            <p><strong>Tutor:</strong> {{ $nota->avaliacao->tutor->name }}</p>
            <p><strong>Critério:</strong> {{ $nota->criterio->categoria }} - {{ $nota->criterio->descricao }}</p>
            <p><strong>Nota:</strong> {{ $nota->nota }}/5</p>
            <p><strong>Data da Visita:</strong> {{ $nota->avaliacao->data_visita->format('d/m/Y') }}</p>
        </div>
    </div>
    <a href="{{ route('avaliacoes.show', $nota->avaliacao_id) }}" class="btn btn-secondary mt-3">Voltar</a>
@endsection