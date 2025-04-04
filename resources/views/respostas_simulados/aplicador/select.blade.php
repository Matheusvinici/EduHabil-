@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Selecionar Simulado para Aplicação</h3>
                <div class="badge bg-white text-primary">
                    <i class="fas fa-school mr-1"></i> 
                    {{ $escolaUsuario->nome ?? 'Nenhuma escola vinculada' }}
                </div>
            </div>
        </div>
        
        <div class="card-body">
            @if(!$escolaUsuario)
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Você não está vinculado a nenhuma escola. Contate o administrador.
                </div>
            @endif
            
            <div class="row">
                @foreach($simulados as $simulado)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-left-primary">
                        <div class="card-body">
                            <h5 class="card-title text-primary">{{ $simulado->nome }}</h5>
                            <div class="mb-2">
                                <span class="badge bg-info text-white">
                                    {{ $simulado->perguntas_count }} questões
                                </span>
                                @if($simulado->tempo_limite)
                                <span class="badge bg-warning text-dark ml-1">
                                    <i class="fas fa-clock mr-1"></i> {{ $simulado->tempo_limite }} min
                                </span>
                                @endif
                            </div>
                            <p class="card-text">{{ Str::limit($simulado->descricao, 100) }}</p>
                        </div>
                        <div class="card-footer bg-transparent">
                            @if($escolaUsuario)
                            <a href="{{ route('respostas_simulados.aplicador.create', $simulado) }}" 
                               class="btn btn-primary btn-block">
                                <i class="fas fa-play mr-1"></i> Aplicar
                            </a>
                            @else
                            <button class="btn btn-secondary btn-block" disabled>
                                <i class="fas fa-ban mr-1"></i> Indisponível
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection