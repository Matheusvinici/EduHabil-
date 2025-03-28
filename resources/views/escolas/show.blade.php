@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Detalhes da Escola</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-item">
                        <h5>Nome</h5>
                        <p>{{ $escola->nome }}</p>
                    </div>
                    <div class="detail-item mt-3">
                        <h5>Endereço</h5>
                        <p>{{ $escola->endereco }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <h5>Telefone</h5>
                        <p>{{ $escola->telefone }}</p>
                    </div>
                    <div class="detail-item mt-3">
                        <h5>Código INEP</h5>
                        <p>{{ $escola->codigo_escola }}</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 border-top pt-3">
                <a href="{{ route('escolas.index') }}" class="btn btn-secondary">Voltar para a lista</a>
                <a href="{{ route('escolas.edit', $escola->id) }}" class="btn btn-primary ml-2">Editar Escola</a>
            </div>
        </div>
    </div>
</div>

<style>
    .detail-item {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .detail-item h5 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 5px;
    }
    .detail-item p {
        color: #212529;
        font-size: 16px;
        margin-bottom: 0;
    }
    .card {
        border: none;
        border-radius: 10px;
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
</style>
@endsection