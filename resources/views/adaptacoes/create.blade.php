@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0"><i class="fas fa-universal-access"></i> Criar Adaptação</h2>
                <a href="{{ route('adaptacoes.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('adaptacoes.store') }}" method="POST" id="adaptacaoForm">
                @csrf

                <div class="row mb-4">
                    <!-- Coluna de Deficiências -->
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="card h-100 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h3 class="h5 mb-0"><i class="fas fa-wheelchair"></i> Deficiências</h3>
                            </div>
                            <div class="card-body">
                                @forelse ($deficiencias as $deficiencia)
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="deficiencias[]" 
                                               id="deficiencia_{{ $deficiencia->id }}" 
                                               value="{{ $deficiencia->id }}" 
                                               class="deficiencia-checkbox form-check-input">
                                        <label for="deficiencia_{{ $deficiencia->id }}" 
                                               class="form-check-label">{{ $deficiencia->nome }}</label>
                                    </div>
                                @empty
                                    <div class="alert alert-warning">
                                        Nenhuma deficiência cadastrada
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Coluna de Características -->
                    <div class="col-md-6">
                        <div class="card h-100 border-success">
                            <div class="card-header bg-success text-white">
                                <h3 class="h5 mb-0"><i class="fas fa-list-alt"></i> Características</h3>
                            </div>
                            <div class="card-body" id="caracteristicas-container">
                                <div id="empty-message" class="text-muted text-center py-4">
                                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                                    <p>Selecione uma ou mais deficiências para visualizar as características disponíveis</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-check"></i> Gerar Atividade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript para carregar características dinamicamente -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Quando uma deficiência é selecionada
    $('.deficiencia-checkbox').change(function() {
        const deficienciaId = $(this).val();
        const isChecked = $(this).is(':checked');

        if (isChecked) {
            // Carrega as características da deficiência selecionada
            $.get(`/deficiencias/${deficienciaId}/caracteristicas`, function(data) {
                if (data.length > 0) {
                    $('#empty-message').hide();
                    
                    let html = '';
                    data.forEach(caracteristica => {
                        html += `
                            <div class="form-check mb-3" data-deficiencia="${deficienciaId}">
                                <input type="checkbox" name="caracteristicas[]" 
                                       id="caracteristica_${caracteristica.id}" 
                                       value="${caracteristica.id}" 
                                       class="form-check-input caracteristica-checkbox">
                                <label for="caracteristica_${caracteristica.id}" 
                                       class="form-check-label">${caracteristica.nome}</label>
                            </div>
                        `;
                    });
                    $('#caracteristicas-container').append(html);
                } else {
                    $('#empty-message').html(`
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>Nenhuma característica disponível para esta deficiência</p>
                    `).show();
                }
            });
        } else {
            // Remove as características da deficiência desmarcada
            $(`[data-deficiencia="${deficienciaId}"]`).remove();
            
            // Se não houver mais características, mostra a mensagem inicial
            if ($('.caracteristica-checkbox').length === 0) {
                $('#empty-message').html(`
                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                    <p>Selecione uma ou mais deficiências para visualizar as características disponíveis</p>
                `).show();
            }
        }
    });

    // Validação do formulário
    $('#adaptacaoForm').submit(function(e) {
        const deficienciasChecked = $('.deficiencia-checkbox:checked').length > 0;
        const caracteristicasChecked = $('.caracteristica-checkbox:checked').length > 0;
        
        if (!deficienciasChecked || !caracteristicasChecked) {
            e.preventDefault();
            alert('Por favor, selecione pelo menos uma deficiência e uma característica.');
        }
    });
});
</script>

<style>
    .card {
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    
    .form-check-label {
        margin-left: 0.5rem;
        cursor: pointer;
    }
    
    .btn-lg {
        padding: 0.5rem 1.5rem;
        font-size: 1.1rem;
    }
    
    #empty-message {
        padding: 2rem 1rem;
    }
    
    #empty-message i {
        color: #6c757d;
    }
</style>
@endsection