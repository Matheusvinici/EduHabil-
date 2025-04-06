@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Modal de Confirmação -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="confirmModalLabel">Iniciar Simulado</h5>
                </div>
                <div class="modal-body">
                    <p>Você está prestes a iniciar o simulado <strong>{{ $simulado->nome }}</strong>.</p>
                    <p>Tempo limite: {{ $simulado->tempo_limite ?? 'Sem tempo limite' }} minutos</p>
                    <p>Número de questões: {{ $simulado->perguntas->count() }}</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Após iniciar, o tempo começará a contar e não poderá ser pausado!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('respostas_simulados.aluno.index') }}'">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="startSimulado">Iniciar Simulado</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cabeçalho -->
    <div class="card shadow-lg mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">
                    <i class="fas fa-edit"></i> Responder Simulado: {{ $simulado->nome }}
                </h4>
            </div>
            <div id="timer-container" class="d-none">
                <span class="badge bg-danger fs-5">
                    <i class="fas fa-clock"></i> 
                    <span id="timer">00:00:00</span>
                </span>
            </div>
        </div>
    </div>

    <div class="card shadow-lg">
        <div class="card-body">
            <form id="form-simulado" method="POST" action="{{ route('respostas_simulados.store', $simulado) }}">
                @csrf
                
                <input type="hidden" name="tempo_resposta" id="tempo-resposta" value="0">
                
                <!-- Seção de informações pessoais -->
                <div class="card mb-4" id="personal-info-section">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Informações Pessoais</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-4">
                            <label for="raca" class="fw-bold">Informe sua raça/cor:</label>
                            <select class="form-control" name="raca" id="raca" required>
                                <option value="">Selecione sua raça/cor</option>
                                <option value="Branca">Branca</option>
                                <option value="Preta">Preta</option>
                                <option value="Parda">Parda</option>
                                <option value="Amarela">Amarela</option>
                                <option value="Indígena">Indígena</option>
                                <option value="Prefiro não informar">Prefiro não informar</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary" id="start-test-btn">
                            <i class="fas fa-play"></i> Iniciar Teste
                        </button>
                    </div>
                </div>

                <!-- Seção de questões (inicialmente oculta) -->
                <div id="questions-section" class="d-none">
                    @foreach($simulado->perguntas as $index => $pergunta)
                    <div class="card mb-4 question-card" id="question-{{ $index + 1 }}" 
                         @if($index > 0) style="display: none;" @endif>
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Questão {{ $index + 1 }} de {{ $simulado->perguntas->count() }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{ $pergunta->enunciado }}</p>
                            
                            @if($pergunta->imagem)
                                <img src="{{ asset('storage/'.$pergunta->imagem) }}" class="img-fluid mb-3" style="max-height: 200px;">
                            @endif
                            
                            <div class="list-group">
                                @foreach(['A', 'B', 'C', 'D'] as $letra)
                                <label class="list-group-item d-flex align-items-center">
                                    <input type="radio" name="respostas[{{ $pergunta->id }}]" 
                                           value="{{ $letra }}" class="form-check-input me-3" required
                                           data-question="{{ $index + 1 }}">
                                    <span class="fw-bold">{{ $letra }}.</span> 
                                    <span class="ms-2">{{ $pergunta->{'alternativa_'.strtolower($letra)} }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                @if($index > 0)
                                <button type="button" class="btn btn-secondary prev-question" 
                                        data-target="question-{{ $index }}">
                                    <i class="fas fa-arrow-left"></i> Anterior
                                </button>
                                @else
                                <div></div> <!-- Espaço vazio para alinhamento -->
                                @endif
                                
                                @if($index < $simulado->perguntas->count() - 1)
                                <button type="button" class="btn btn-primary next-question" 
                                        data-target="question-{{ $index + 2 }}"
                                        disabled>
                                    Próxima <i class="fas fa-arrow-right"></i>
                                </button>
                                @else
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check-circle"></i> Finalizar Simulado
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rodapé -->
<div class="container mt-4">
    <div class="card">
        <div class="card-body text-center">
            <p class="mb-1">EduHabil+ - Sistema de Gestão Educacional</p>
            <p class="mb-0">Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostra o modal de confirmação ao carregar a página
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    confirmModal.show();
    
    // Variáveis do timer
    const form = document.getElementById('form-simulado');
    const tempoInput = document.getElementById('tempo-resposta');
    const timerDisplay = document.getElementById('timer');
    const timerContainer = document.getElementById('timer-container');
    let tempoDecorrido = 0;
    let timer;
    
    // Elementos das seções
    const personalInfoSection = document.getElementById('personal-info-section');
    const questionsSection = document.getElementById('questions-section');
    const startTestBtn = document.getElementById('start-test-btn');
    
    // Configura o botão de iniciar no modal
    document.getElementById('startSimulado').addEventListener('click', function() {
        confirmModal.hide();
    });
    
    // Configura o botão de iniciar teste
    startTestBtn.addEventListener('click', function() {
        const racaSelect = document.getElementById('raca');
        
        if (racaSelect.value === '') {
            alert('Por favor, selecione sua raça/cor antes de iniciar o teste.');
            racaSelect.focus();
            return;
        }
        
        personalInfoSection.classList.add('d-none');
        questionsSection.classList.remove('d-none');
        timerContainer.classList.remove('d-none');
        
        // Inicia o cronômetro
        startTimer();
    });
    
    // Função para iniciar o timer
    function startTimer() {
        timer = setInterval(() => {
            tempoDecorrido++;
            tempoInput.value = tempoDecorrido;
            
            // Atualiza o display do timer
            const hours = Math.floor(tempoDecorrido / 3600).toString().padStart(2, '0');
            const minutes = Math.floor((tempoDecorrido % 3600) / 60).toString().padStart(2, '0');
            const seconds = (tempoDecorrido % 60).toString().padStart(2, '0');
            
            timerDisplay.textContent = `${hours}:${minutes}:${seconds}`;
        }, 1000);
        
        // Verifica tempo limite
        @if($simulado->tempo_limite)
        const tempoLimite = {{ $simulado->tempo_limite * 60 }};
        
        setTimeout(() => {
            clearInterval(timer);
            alert('O tempo acabou! Suas respostas serão enviadas automaticamente.');
            form.submit();
        }, tempoLimite * 1000);
        @endif
    }
    
    // Navegação entre questões
    document.querySelectorAll('.next-question').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            document.querySelectorAll('.question-card').forEach(card => {
                card.style.display = 'none';
            });
            document.getElementById(targetId).style.display = 'block';
            
            // Rola para o topo da questão
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
    
    document.querySelectorAll('.prev-question').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            document.querySelectorAll('.question-card').forEach(card => {
                card.style.display = 'none';
            });
            document.getElementById(targetId).style.display = 'block';
            
            // Rola para o topo da questão
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
    
    // Habilita/desabilita botão de próxima questão
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const questionNum = parseInt(this.getAttribute('data-question'));
            const nextButton = document.querySelector(`.next-question[data-target="question-${questionNum + 1}"]`);
            
            if (nextButton) {
                nextButton.disabled = false;
            }
        });
    });
    
    // Submeter o formulário
    form.addEventListener('submit', function() {
        clearInterval(timer);
    });
});
</script>

<style>
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }
    
    .logo-container {
        display: flex;
        align-items: center;
    }
    
    .logo {
        height: 60px;
        margin-right: 15px;
    }
    
    .header-text {
        text-align: right;
    }
    
    .footer {
        margin-top: 30px;
        padding: 15px;
        text-align: center;
        background-color: #f8f9fa;
        border-radius: 5px;
        font-size: 0.9rem;
    }
    
    .question-card {
        transition: all 0.3s ease;
    }
</style>
@endsection