@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Deficiência: {{ $deficiencia->nome }}</h1>
    <p><strong>Descrição:</strong> {{ $deficiencia->descricao }}</p>
    <a href="{{ route('deficiencias.index') }}" class="btn btn-secondary">Voltar</a>
</div>
@endsection