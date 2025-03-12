@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detalhes da Escola</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Nome:</strong> {{ $escola->nome }}</p>
                <p><strong>Endereço:</strong> {{ $escola->endereco }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Telefone:</strong> {{ $escola->telefone }}</p>
                <p><strong>Código INEP:</strong> {{ $escola->codigo_escola }}</p> <!-- Alterado para "Código INEP" -->
            </div>
        </div>
        <div class="mt-3"> <!-- Adicionado margem superior para separar o botão -->
            <a href="{{ route('escolas.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
</div>
@endsection