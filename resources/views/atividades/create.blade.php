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
                        
                        <!-- Primeira Linha - Ano/Série -->
                        <div class="row g-4 mb-4">
                            <!-- Ano/Série -->
                            <div class="col-md-12">
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

                        <!-- Disciplinas - Adição Dinâmica -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-semibold text-primary mb-0">
                                    <i class="fas fa-book me-2"></i> Disciplinas
                                </label>
                                <button type="button" id="addDisciplina" class="btn btn-sm btn-primary rounded-4">
                                    <i class="fas fa-plus me-1"></i> Adicionar Disciplina
                                </button>
                            </div>
                            
                            <div id="disciplinasContainer">
                                <!-- Primeiro campo de disciplina -->
                                <div class="row g-3 mb-3 disciplina-row">
                                    <div class="col-md-10">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white rounded-start-4">
                                                <i class="fas fa-book"></i>
                                            </span>
                                            <select name="disciplinas[]" class="form-select disciplina-select rounded-end-4 py-3" required>
                                                <option value="" disabled selected>Selecione uma disciplina</option>
                                                @foreach($disciplinas as $disciplina)
                                                <option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-disciplina rounded-4" disabled>
                                            <i class="fas fa-times"></i> Remover
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Habilidades - Adição Dinâmica -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-semibold text-primary mb-0">
                                    <i class="fas fa-bullseye me-2"></i> Habilidades
                                </label>
                                <button type="button" id="addHabilidade" class="btn btn-sm btn-primary rounded-4">
                                    <i class="fas fa-plus me-1"></i> Adicionar Habilidade
                                </button>
                            </div>
                            
                            <div id="habilidadesContainer">
                                <!-- Primeiro campo de habilidade -->
                                <div class="row g-3 mb-3 habilidade-row">
                                    <div class="col-md-10">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white rounded-start-4">
                                                <i class="fas fa-bullseye"></i>
                                            </span>
                                            <select name="habilidades[]" class="form-select habilidade-select rounded-end-4 py-3" required>
                                                <option value="" disabled selected>Selecione uma habilidade</option>
                                                @foreach($habilidades as $habilidade)
                                                <option value="{{ $habilidade->id }}" data-descricao="{{ $habilidade->descricao }}">
                                                    {{ $habilidade->codigo }} - {{ Str::limit($habilidade->descricao, 40) }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-habilidade rounded-4" disabled>
                                            <i class="fas fa-times"></i> Remover
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Título da Atividade -->
                        <div class="mb-4">
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
                        <!-- Links de Sugestões -->
<div class="mb-4">
    <label for="links_sugestoes" class="form-label fw-semibold text-primary mb-2">
        <i class="fas fa-link me-2"></i> Sugestões de Links (Livros, Vídeos, Sites)
    </label>
    <div class="input-group">
        <span class="input-group-text bg-primary text-white rounded-top-4 align-items-start pt-3">
            <i class="fas fa-external-link-alt"></i>
        </span>
        <textarea name="links_sugestoes" id="links_sugestoes" class="form-control rounded-bottom-4 rounded-end-4" 
                  rows="3" placeholder="Coloque um link por linha. Exemplo:&#10;https://www.exemplo.com/livro&#10;https://www.youtube.com/video"></textarea>
    </div>
    <small class="text-muted">Insira um link por linha. Links serão transformados em clicáveis automaticamente.</small>
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
    
    .btn-outline-danger {
        transition: all 0.3s ease;
    }
    
    .btn-outline-danger:hover {
        background-color: #f8d7da;
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
    }
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contadores para IDs únicos
    let disciplinaCounter = 1;
    let habilidadeCounter = 1;
    
    // Função para atualizar os selects disponíveis
    function updateSelectOptions(containerClass) {
        const selects = document.querySelectorAll(`.${containerClass}`);
        const selectedValues = [];
        
        // Coletar todos os valores já selecionados
        selects.forEach(select => {
            if (select.value) {
                selectedValues.push(select.value);
            }
        });
        
        // Atualizar opções em cada select
        selects.forEach(select => {
            const currentValue = select.value;
            Array.from(select.options).forEach(option => {
                if (option.value && option.value !== '') {
                    option.disabled = selectedValues.includes(option.value) && option.value !== currentValue;
                }
            });
        });
    }
    
    // Adicionar nova disciplina
    document.getElementById('addDisciplina').addEventListener('click', function() {
        const container = document.getElementById('disciplinasContainer');
        const newRow = document.createElement('div');
        newRow.className = 'row g-3 mb-3 disciplina-row';
        newRow.innerHTML = `
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-primary text-white rounded-start-4">
                        <i class="fas fa-book"></i>
                    </span>
                    <select name="disciplinas[]" class="form-select disciplina-select rounded-end-4 py-3" required>
                        <option value="" disabled selected>Selecione uma disciplina</option>
                        @foreach($disciplinas as $disciplina)
                        <option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <button type="button" class="btn btn-outline-danger btn-sm remove-disciplina rounded-4">
                    <i class="fas fa-times"></i> Remover
                </button>
            </div>
        `;
        container.appendChild(newRow);
        
        // Atualizar botões de remoção
        updateRemoveButtons('.remove-disciplina', '.disciplina-row');
        // Atualizar opções disponíveis
        updateSelectOptions('disciplina-select');
    });
    
    // Adicionar nova habilidade
    document.getElementById('addHabilidade').addEventListener('click', function() {
        const container = document.getElementById('habilidadesContainer');
        const newRow = document.createElement('div');
        newRow.className = 'row g-3 mb-3 habilidade-row';
        newRow.innerHTML = `
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-primary text-white rounded-start-4">
                        <i class="fas fa-bullseye"></i>
                    </span>
                    <select name="habilidades[]" class="form-select habilidade-select rounded-end-4 py-3" required>
                        <option value="" disabled selected>Selecione uma habilidade</option>
                        @foreach($habilidades as $habilidade)
                        <option value="{{ $habilidade->id }}" data-descricao="{{ $habilidade->descricao }}">
                            {{ $habilidade->codigo }} - {{ Str::limit($habilidade->descricao, 40) }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <button type="button" class="btn btn-outline-danger btn-sm remove-habilidade rounded-4">
                    <i class="fas fa-times"></i> Remover
                </button>
            </div>
        `;
        container.appendChild(newRow);
        
        // Atualizar botões de remoção
        updateRemoveButtons('.remove-habilidade', '.habilidade-row');
        // Atualizar opções disponíveis
        updateSelectOptions('habilidade-select');
    });
    
    // Função para atualizar botões de remoção
    function updateRemoveButtons(buttonClass, rowClass) {
        const buttons = document.querySelectorAll(buttonClass);
        const rows = document.querySelectorAll(rowClass);
        
        buttons.forEach((button, index) => {
            // Habilitar todos os botões
            button.disabled = false;
            
            // Desabilitar o botão se for o primeiro item
            if (rows.length === 1) {
                button.disabled = true;
            }
            
            // Adicionar evento de clique
            button.addEventListener('click', function() {
                if (!button.disabled) {
                    const row = button.closest(rowClass);
                    row.remove();
                    
                    // Atualizar opções disponíveis
                    if (rowClass === '.disciplina-row') {
                        updateSelectOptions('disciplina-select');
                        updateRemoveButtons('.remove-disciplina', '.disciplina-row');
                    } else {
                        updateSelectOptions('habilidade-select');
                        updateRemoveButtons('.remove-habilidade', '.habilidade-row');
                    }
                }
            });
        });
    }
    
    // Atualizar selects quando mudam
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('disciplina-select')) {
            updateSelectOptions('disciplina-select');
        } else if (e.target.classList.contains('habilidade-select')) {
            updateSelectOptions('habilidade-select');
        }
    });
    
    // Validação do formulário
    const form = document.getElementById('activityForm');
    form.addEventListener('submit', function(e) {
        // Valida se pelo menos uma disciplina foi selecionada
        const disciplinas = document.querySelectorAll('.disciplina-select');
        let disciplinaSelected = false;
        disciplinas.forEach(select => {
            if (select.value) disciplinaSelected = true;
        });
        
        if (!disciplinaSelected) {
            e.preventDefault();
            alert('Selecione pelo menos uma disciplina');
            return false;
        }
        
        // Valida se pelo menos uma habilidade foi selecionada
        const habilidades = document.querySelectorAll('.habilidade-select');
        let habilidadeSelected = false;
        habilidades.forEach(select => {
            if (select.value) habilidadeSelected = true;
        });
        
        if (!habilidadeSelected) {
            e.preventDefault();
            alert('Selecione pelo menos uma habilidade');
            return false;
        }
    });
    
    // Inicializar botões de remoção
    updateRemoveButtons('.remove-disciplina', '.disciplina-row');
    updateRemoveButtons('.remove-habilidade', '.habilidade-row');
});
</script>
@endsection
@endsection