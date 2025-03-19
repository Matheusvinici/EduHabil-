@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Característica: {{ $caracteristica->nome }}</h1>
    <p><strong>Descrição:</strong> {{ $caracteristica->descricao }}</p>
    <p><strong>Deficiência:</strong> {{ $caracteristica->deficiencia->nome }}</p>
    <a href="{{ route('caracteristicas.index') }}" class="btn btn-secondary">Voltar</a>
</div>
@endsection