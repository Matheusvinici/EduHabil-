@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Detalhes do Recurso</h1>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ $recurso->nome }}</h5>
        </div>
        <div class="card-body">
            <p><strong>Descrição:</strong> {{ $recurso->descricao }}</p>
            <p><strong>Como Trabalhar:</strong> {{ $recurso->como_trabalhar }}</p>
            <p><strong>Direcionamentos:</strong> {{ $recurso->direcionamentos }}</p>

            <h5>Deficiências:</h5>
            <ul>
                @foreach ($recurso->deficiencias as $deficiencia)
                    <li>{{ $deficiencia->nome }}</li>
                @endforeach
            </ul>

            <h5>Características:</h5>
            <ul>
                @foreach ($recurso->caracteristicas as $caracteristica)
                    <li>{{ $caracteristica->nome }}</li>
                @endforeach
            </ul>
        </div>
        <div class="card-footer">
            <a href="{{ route('recursos.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
</div>
@endsection