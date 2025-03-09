@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detalhes da Escola</h3>
    </div>
    <div class="card-body">
        <p><strong>Nome:</strong> {{ $escola->nome }}</p>
        <p><strong>Endereço:</strong> {{ $escola->endereco }}</p>
        <p><strong>Telefone:</strong> {{ $escola->telefone }}</p>
        <p><strong>Código:</strong> {{ $escola->codigo_escola }}</p>
        <a href="{{ route('escolas.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
</div>
@endsection