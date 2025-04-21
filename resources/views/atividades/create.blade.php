@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <!-- Cabeçalho com gradiente azul -->
                <div class="card-header bg-primary-gradient py-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-plus-circle fs-3 text-white me-3"></i>
                        <h2 class="h4 mb-0 text-white fw-bold">Criar Nova Atividade</h2>
                    </div>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-3 fs-4"></i>
                            <div>
                                <h5 class="mb-1 fw-bold">Atividade cadastrada com sucesso!</h5>
                                <p class="mb-0">Você pode visualizá-la na lista de atividades.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form action="{{ route('atividades.store') }}" method="POST" id="activityForm">
                        @csrf
                        
          <!-- Primeira Linha - Disciplina e Ano/Série -->
<div class="row g-4 mb-4">
    <!-- Disciplina -->
    <div class="col-md-6">
        <label for="disciplina_id" class="form-label fw-semibold text-primary mb-2">
            <i class="fas fa-book me-2"></i> Disciplina
        </label>
        <div class="input-group">
            <span class="input-group-text bg-primary text-white rounded-start-4">
                <i class="fas fa-book"></i>
            </span>
            <select name="disciplina_id" id="disciplina_id" class="form-select rounded-end-4 py-3" required>
                <option value="" disabled selected>Selecione uma disciplina</option>
                @foreach($disciplinas as $disciplina)
                <option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <!-- Ano/Série -->
    <div class="col-md-6">
        <label for="ano_id" class="form-label fw-semibold text-primary mb-2">
            <i class="fas fa-graduation-cap me-2"></i> Ano/Série
        </label>
        <div class="input-group">
            <span class="input-group-text bg-primary text-white rounded-start-4">
                <i class="fas fa-graduation-cap"></i>
            </span>
            <select name="ano_id" id="ano_id" class="form-select rounded-end-4 py-3" required>
                <option value="" disabled selected>Selecione o ano/série</option>
                @foreach($anos as $ano)
                <option value="{{ $ano->id }}">{{ $ano->nome }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<!-- Segunda Linha - Título e Habilidade -->
<div class="row g-4 mb-4">
    <!-- Título -->
    <div class="col-md-6">
        <label for="titulo" class="form-label fw-semibold text-primary mb-2">
            <i class="fas fa-heading me-2"></i> Título da Atividade
        </label>
        <div class="input-group">
            <span class="input-group-text bg-primary text-white rounded-start-4">
                <i class="fas fa-heading"></i>
            </span>
            <input type="text" name="titulo" id="titulo" class="form-control rounded-end-4 py-3" 
                   placeholder="Ex: Introdução à Fotossíntese - 7º ano" required>
        </div>
    </div>
    
    <!-- Habilidade -->
    <div class="col-md-6">
        <label for="habilidade_id" class="form-label fw-semibold text-primary mb-2">
            <i class="fas fa-bullseye me-2"></i> Habilidade
        </label>
        <div class="input-group">
            
            <select name="habilidade_id" id="habilidade_id" class="form-select rounded-end-4 py-3" required>
                <option value="" disabled selected>Selecione uma habilidade</option>
                @foreach($habilidades as $habilidade)
                <option value="{{ $habilidade->id }}" title="{{ $habilidade->descricao }}">
                    {{ $habilidade->codigo }} - {{ Str::limit($habilidade->descricao, 40) }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
                        
                        <!-- Objetivo -->
                        <div class="mb-4">
                            <label for="objetivo" class="form-label fw-semibold text-primary mb-2">
                                <i class="fas fa-bullseye me-2"></i> Objetivo de Aprendizagem
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white rounded-top-4 align-items-start pt-3">
                                    <i class="fas fa-bullseye"></i>
                                </span>
                                <textarea name="objetivo" id="objetivo" class="form-control rounded-bottom-4 rounded-end-4" 
                                          rows="4" placeholder="O que os alunos devem aprender com esta atividade?" required></textarea>
                            </div>
                        </div>
                        
                        <!-- Metodologia e Materiais -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="metodologia" class="form-label fw-semibold text-primary mb-2">
                                    <i class="fas fa-list-ol me-2"></i> Etapas da Aula
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white rounded-top-4 align-items-start pt-3">
                                        <i class="fas fa-list-ol"></i>
                                    </span>
                                    <textarea name="metodologia" id="metodologia" class="form-control rounded-bottom-4 rounded-end-4" 
                                              rows="6" placeholder="Descreva as etapas da aula..." required></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="materiais" class="form-label fw-semibold text-primary mb-2">
                                    <i class="fas fa-tools me-2"></i> Materiais Necessários
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white rounded-top-4 align-items-start pt-3">
                                        <i class="fas fa-tools"></i>
                                    </span>
                                    <textarea name="materiais" id="materiais" class="form-control rounded-bottom-4 rounded-end-4" 
                                              rows="6" placeholder="Liste os materiais necessários..." required></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Atividade Proposta -->
                        <div class="mb-4">
                            <label for="resultados_esperados" class="form-label fw-semibold text-primary mb-2">
                                <i class="fas fa-tasks me-2"></i> Atividade Proposta
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white rounded-top-4 align-items-start pt-3">
                                    <i class="fas fa-tasks"></i>
                                </span>
                                <textarea name="resultados_esperados" id="resultados_esperados" class="form-control rounded-bottom-4 rounded-end-4" 
                                          rows="5" placeholder="Descreva a atividade que os alunos realizarão..." required></textarea>
                            </div>
                        </div>
                        
                        <!-- Botões com espaçamento adequado -->
                        <div class="d-flex flex-column flex-md-row justify-content-end gap-3 pt-4">
                            <a href="{{ url()->previous() }}" class="btn btn-lg btn-outline-primary px-4 rounded-4 fw-semibold">
                                <i class="fas fa-times me-2"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-lg btn-primary px-4 rounded-4 shadow-sm fw-semibold">
                                <i class="fas fa-save me-2"></i> Cadastrar Atividade
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Gradiente azul profissional */
    .bg-primary-gradient {
        background: linear-gradient(135deg, #2B5598, #3a7bd5);
        border: none;
    }
    
    /* Estilos para cards */
    .rounded-4 {
        border-radius: 1rem !important;
    }
    
    .card {
        border: none;
        box-shadow: 0 10px 30px rgba(43, 85, 152, 0.1);
    }
    
    /* Estilos para os selects e inputs */
    .form-select, .form-control {
        border-radius: 0.75rem !important;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
        padding: 0.75rem 1rem;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: #2B5598;
        box-shadow: 0 0 0 0.25rem rgba(43, 85, 152, 0.15);
    }
    
    /* Input groups estilizados */
    .input-group-text {
        transition: all 0.3s ease;
    }
    
    .input-group:focus-within .input-group-text {
        background-color: #244785;
    }
    
    /* Tooltip customizado */
    .tooltip-inner {
        max-width: 300px;
        padding: 0.75rem 1rem;
        background-color: #2B5598;
        font-size: 0.9rem;
        text-align: left;
    }
    
    .bs-tooltip-auto[data-popper-placement^=top] .tooltip-arrow::before,
    .bs-tooltip-top .tooltip-arrow::before {
        border-top-color: #2B5598;
    }
    
    /* Botões estilizados */
    .btn-primary {
        background-color: #2B5598;
        border-color: #2B5598;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background-color: #244785;
        border-color: #244785;
        transform: translateY(-2px);
    }
    
    .btn-outline-primary {
        color: #2B5598;
        border-color: #2B5598;
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
        background-color: #e9f2ff;
    }
    
    /* Alertas */
    .alert-success {
        border-left: 4px solid #2e7d32;
    }
    
    /* Responsividade */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }
        
        .btn-lg {
            padding: 0.75rem 1.5rem;
        }
        
        .form-select, .form-control {
            padding: 0.5rem 0.75rem;
        }
        
        .tooltip-inner {
            max-width: 200px;
            font-size: 0.8rem;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializa tooltips para as habilidades
    const habilidadeSelect = document.getElementById('habilidade_id');
    const tooltip = new bootstrap.Tooltip(habilidadeSelect, {
        trigger: 'hover',
        placement: 'top',
        html: true,
        selector: 'option[data-descricao]'
    });
    
    // Atualiza o conteúdo do tooltip dinamicamente
    habilidadeSelect.addEventListener('mouseover', function(e) {
        if (e.target.tagName === 'OPTION' && e.target.value) {
            const codigo = e.target.textContent;
            const descricao = e.target.getAttribute('data-descricao');
            e.target.setAttribute('title', `${codigo} - ${descricao}`);
        }
    });
    
    // Mostra mensagem de sucesso se existir
    @if(session('success'))
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.classList.add('animate__animated', 'animate__fadeIn');
        }
    }, 100);
    @endif

    // Validação do formulário
    const form = document.getElementById('activityForm');
    form.addEventListener('submit', function(e) {
        // Adicione aqui qualquer validação adicional necessária
    });
});
</script>
@endsection