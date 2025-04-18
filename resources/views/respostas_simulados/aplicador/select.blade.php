@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Aplicação de Simulados
                </h3>
                @if($escolaSelecionada)
                <div class="badge bg-light-blue text-primary">
                    <i class="fas fa-school me-1"></i> 
                    {{ $escolaSelecionada->nome }}
                </div>
                @endif
            </div>
        </div>
        
        <div class="card-body">
            <!-- Seletor de Escola - Estilo Aprimorado -->
            <div class="card mb-4 border-0 bg-light-blue">
                <div class="card-body p-3">
                    <form method="POST" action="{{ route('respostas_simulados.aplicador.select_escola') }}" id="escolaForm">
                        @csrf
                        <div class="row g-2 align-items-center">
                            <div class="col-md-9">
                                <div class="form-floating">
                                    <select class="form-select border-primary" name="escola_id" id="escola_id" required>
                                        <option value="">Selecione uma escola...</option>
                                        @foreach($escolas as $escola)
                                            <option value="{{ $escola->id }}" 
                                                {{ $escolaSelecionada && $escolaSelecionada->id == $escola->id ? 'selected' : '' }}>
                                                {{ $escola->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="escola_id" class="text-primary">Escola</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100 py-3">
                                    <i class="fas fa-check me-1"></i> Confirmar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(!$escolaSelecionada)
                <div class="alert alert-info bg-light-blue border-primary">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-3 fs-4 text-primary"></i>
                        <div>
                            <h5 class="mb-1 text-primary">Selecione uma escola</h5>
                            <p class="mb-0">Escolha a escola onde o simulado será aplicado para visualizar os disponíveis.</p>
                        </div>
                    </div>
                </div>
            @else
                @if($simulados->isEmpty())
                    <div class="alert alert-info bg-light-blue border-primary">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-3 fs-4 text-primary"></i>
                            <div>
                                <h5 class="mb-1 text-primary">Nenhum simulado disponível</h5>
                                <p class="mb-0">Não há simulados cadastrados para aplicação nesta escola.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($simulados as $simulado)
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                <div class="card-header bg-primary text-white py-3">
                                    <h5 class="mb-0">{{ $simulado->nome }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <span class="badge bg-blue-soft text-primary">
                                            <i class="fas fa-question-circle me-1"></i> {{ $simulado->perguntas_count }} questões
                                        </span>
                                        @if($simulado->tempo_limite)
                                        <span class="badge bg-yellow-soft text-dark">
                                            <i class="fas fa-clock me-1"></i> {{ $simulado->tempo_limite }} min
                                        </span>
                                        @endif
                                    </div>
                                    <p class="card-text text-muted mb-4">{{ Str::limit($simulado->descricao, 120) }}</p>
                                    
                                    <div class="d-grid gap-2">
                                        <div class="btn-group w-100">
                                            <a href="{{ route('respostas_simulados.aplicador.create', $simulado) }}" 
                                               class="btn btn-primary flex-grow-1">
                                                <i class="fas fa-pencil-alt me-1"></i> Inserir Manual
                                            </a>
                                            <a href="{{ route('respostas_simulados.aplicador.camera', $simulado) }}" 
                                               class="btn btn-success flex-grow-1">
                                                <i class="fas fa-camera me-1"></i> Ler Gabarito
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Paginação Estilizada -->
                    @if($simulados->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                {{ $simulados->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </ul>
                        </nav>
                    </div>
                    @endif
                @endif
            @endif
        </div>
    </div>
</div>

<style>
    .bg-light-blue {
        background-color: #e7f5ff;
    }
    .bg-blue-soft {
        background-color: #d0ebff;
    }
    .bg-yellow-soft {
        background-color: #fff3bf;
    }
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 123, 255, 0.15);
        transform: translateY(-2px);
    }
    .transition-all {
        transition: all 0.3s ease;
    }
    .card-header {
        border-radius: 0.375rem 0.375rem 0 0 !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seleção automática quando muda a escola
    const escolaSelect = document.getElementById('escola_id');
    if (escolaSelect) {
        escolaSelect.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('escolaForm').submit();
            }
        });
    }
});
</script>
@endsection