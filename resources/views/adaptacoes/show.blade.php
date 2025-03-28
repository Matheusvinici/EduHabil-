@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg">
        <!-- Cabeçalho -->
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0">
                    <i class="fas fa-universal-access me-2"></i>Detalhes da Adaptação #{{ $adaptacao->id }}
                </h2>
                <a href="{{ route('adaptacoes.index') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>

        <!-- Corpo do Card -->
        <div class="card-body">
            <!-- Seção Superior - Grid de 2 colunas -->
            <div class="row g-4 mb-4">
                <!-- Coluna do Recurso -->
                <div class="col-lg-6">
                    <div class="card h-100 border-info">
                        <div class="card-header bg-info text-white d-flex align-items-center">
                            <i class="fas fa-tools me-2"></i>
                            <h3 class="h5 mb-0">Recurso Educacional</h3>
                        </div>
                        <div class="card-body">
                            <h4 class="text-primary">{{ $adaptacao->recurso->nome }}</h4>
                            <div class="mb-3">
                                <h5 class="h6 text-muted">Descrição:</h5>
                                <p class="ps-3">{{ $adaptacao->recurso->descricao }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <h5 class="h6 text-muted">Como trabalhar:</h5>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($adaptacao->recurso->como_trabalhar)) !!}
                                </div>
                            </div>
                            
                            <div>
                                <h5 class="h6 text-muted">Direcionamentos:</h5>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($adaptacao->recurso->direcionamentos)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Coluna de Deficiências e Características -->
                <div class="col-lg-6">
                    <div class="h-100 d-flex flex-column gap-3">
                        <!-- Card de Deficiências -->
                        <div class="card flex-grow-1 border-primary">
                            <div class="card-header bg-primary text-white d-flex align-items-center">
                                <i class="fas fa-wheelchair me-2"></i>
                                <h3 class="h5 mb-0">Deficiências Atendidas</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">
                                    @forelse($adaptacao->deficiencias as $deficiencia)
                                        <span class="badge bg-primary py-2 px-3">
                                            <i class="fas fa-fw fa-user-shield me-1"></i>
                                            {{ $deficiencia->nome }}
                                        </span>
                                    @empty
                                        <div class="text-muted">Nenhuma deficiência associada</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Card de Características -->
                        <div class="card flex-grow-1 border-success">
                            <div class="card-header bg-success text-white d-flex align-items-center">
                                <i class="fas fa-list-alt me-2"></i>
                                <h3 class="h5 mb-0">Características</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">
                                    @forelse($adaptacao->caracteristicas as $caracteristica)
                                        <span class="badge bg-success py-2 px-3">
                                            <i class="fas fa-fw fa-tag me-1"></i>
                                            {{ $caracteristica->nome }}
                                        </span>
                                    @empty
                                        <div class="text-muted">Nenhuma característica associada</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Área de Ações -->
            <div class="d-flex justify-content-between align-items-center border-top pt-4">
                <a href="{{ route('adaptacoes.gerarPDF', $adaptacao->id) }}" 
                   class="btn btn-info px-4">
                    <i class="fas fa-file-pdf me-2"></i>Gerar PDF
                </a>

                <form action="{{ route('adaptacoes.destroy', $adaptacao->id) }}" method="POST" 
                      class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir permanentemente esta adaptação?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fas fa-trash-alt me-2"></i>Excluir Adaptação
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
        padding: 1rem 1.25rem;
    }
    
    .badge {
        font-size: 0.85rem;
        font-weight: 500;
        letter-spacing: 0.5px;
        border-radius: 0.375rem;
    }
    
    .bg-light {
        background-color: #f8fafc !important;
    }
    
    .rounded {
        border-radius: 0.5rem !important;
    }
    
    .text-muted {
        color: #6c757d !important;
    }
</style>
@endsection