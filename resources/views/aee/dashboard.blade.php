@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Painel do AEE</h5>
                    <div>
                        @if ($escola)
                            <span class="badge bg-primary mr-2">
                                Escola atual: {{ $escola->nome }}
                            </span>
                        @else
                            <span class="badge bg-warning mr-2">
                                Nenhuma escola selecionada
                            </span>
                        @endif
                        <a href="{{ route('aee.selecionar.escola') }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-exchange-alt"></i> Mudar Escola
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p>Bem-vindo, {{ Auth::user()->name }}!</p>
                    @if ($escola)
                        <p>Você está atualmente vinculado à escola <strong>{{ $escola->nome }}</strong>.</p>
                        
                        <div class="mt-4">
                            <h5>Informações da Escola</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>Endereço:</strong> {{ $escola->endereco }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Telefone:</strong> {{ $escola->telefone }}
                                </li>
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Por favor, selecione uma escola para continuar.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection