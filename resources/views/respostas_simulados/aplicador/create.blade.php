@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Modal de Confirmação -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="confirmModalLabel">Confirmar Aplicação</h5>
                </div>
                <div class="modal-body">
                    <p>Você está prestes a aplicar o simulado <strong>{{ $simulado->nome }}</strong> para:</p>
                    <ul class="list-group mb-3">
                        <li class="list-group-item">
                            <i class="fas fa-user-graduate me-2"></i>
                            <strong>Aluno:</strong> {{ session('aluno_nome') }}
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-school me-2"></i>
                            <strong>Turma:</strong> {{ session('aluno_turma') }}
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-palette me-2"></i>
                            <strong>Raça/Cor:</strong> {{ session('raca') }}
                        </li>
                    </ul>
                    @if($simulado->tempo_limite)
                    <div class="alert alert-warning">
                        <i class="fas fa-clock me-2"></i> Tempo limite: {{ $simulado->tempo_limite }} minutos
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('respostas_simulados.aplicador.create', $simulado) }}?reset=1'">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="startSimulado">
                        <i class="fas fa-play me-2"></i> Iniciar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cabeçalho -->
    <div class="card shadow-lg mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">
                    <i class="fas fa-chalkboard-teacher"></i> Aplicar Simulado: {{ $simulado->nome }}
                </h4>
        </div>
            <div id="timer-container" class="d-none">
                <span class="badge bg-danger fs-5">
                    <i class="fas fa-clock"></i> 
                    <span id="timer">{{ $simulado->tempo_limite ? sprintf('%02d:00:00', $simulado->tempo_limite) : '00:00:00' }}</span>
                </span>
            </div>
        </div>
    </div>

    <div class="card shadow-lg">
        <div class="card-body">
            @if(!session('aluno_selecionado'))
            <!-- Formulário de seleção do aluno -->
            <form method="POST" action="{{ route('respostas_simulados.aplicador.selecionar', $simulado) }}" id="form-selecao">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" name="turma_id" id="turma_id" required>
                                <option value="">Selecione...</option>
                                @foreach($turmas as $turma)
                                    <option value="{{ $turma->id }}" {{ old('turma_id') == $turma->id ? 'selected' : '' }}>
                                        {{ $turma->nome_turma }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="turma_id">Turma</label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-floating" id="aluno-container" style="display: none;">
                            <select class="form-select" name="aluno_id" id="aluno_id" required disabled>
                                <option value="">Selecione...</option>
                            </select>
                            <label for="aluno_id">Aluno</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-floating mb-4">
                    <select class="form-select" name="raca" id="raca" required>
                        <option value="">Selecione...</option>
                        <option value="Branca" {{ old('raca') == 'Branca' ? 'selected' : '' }}>Branca</option>
                        <option value="Preta" {{ old('raca') == 'Preta' ? 'selected' : '' }}>Preta</option>
                        <option value="Parda" {{ old('raca') == 'Parda' ? 'selected' : '' }}>Parda</option>
                        <option value="Amarela" {{ old('raca') == 'Amarela' ? 'selected' : '' }}>Amarela</option>
                        <option value="Indígena" {{ old('raca') == 'Indígena' ? 'selected' : '' }}>Indígena</option>
                        <option value="Prefiro não informar" {{ old('raca') == 'Prefiro não informar' ? 'selected' : '' }}>Prefiro não informar</option>
                    </select>
                    <label for="raca">Raça/Cor do Aluno</label>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" id="btn-selecionar" disabled>
                        <i class="fas fa-user-check me-2"></i> Selecionar Aluno
                    </button>
                </div>
            </form>
            @else
            <!-- Formulário de respostas -->
            <form method="POST" action="{{ route('respostas_simulados.aplicador.store', $simulado) }}" id="form-respostas">
                @csrf
                <input type="hidden" name="aluno_id" value="{{ session('aluno_id') }}">
                <input type="hidden" name="turma_id" value="{{ session('turma_id') }}">
                <input type="hidden" name="raca" value="{{ session('raca') }}">
                <input type="hidden" name="tempo_resposta" id="tempo-resposta" value="0">
                
                <!-- Informações do aluno -->
                <div class="alert alert-info mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-primary me-2">
                                <i class="fas fa-user-graduate"></i> {{ session('aluno_nome') }}
                            </span>
                            <span class="badge bg-secondary me-2">
                                <i class="fas fa-school"></i> {{ session('aluno_turma') }}
                            </span>
                            <span class="badge bg-dark">
                                <i class="fas fa-palette"></i> {{ session('raca') }}
                            </span>
                        </div>
                        <a href="{{ route('respostas_simulados.aplicador.create', $simulado) }}?reset=1" class="btn btn-sm btn-warning">
                            <i class="fas fa-sync-alt me-1"></i> Trocar Aluno
                        </a>
                    </div>
                </div>

                <!-- Perguntas - Uma por página -->
                <div id="questions-container">
                    @foreach($simulado->perguntas as $index => $pergunta)
                    <div class="card mb-4 question-card" id="question-{{ $index + 1 }}" @if($index > 0) style="display: none;" @endif>
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Questão {{ $index + 1 }} de {{ $simulado->perguntas->count() }}</h5>
                            <span class="badge bg-primary">
                                <i class="fas fa-clock me-1"></i> 
                                <span id="question-timer-{{ $index + 1 }}">00:00</span>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="question-text mb-4">
                                {{ $pergunta->enunciado }}
                            </div>
                            
                            @if($pergunta->imagem)
                                <div class="text-center mb-4">
                                    <img src="{{ asset('storage/'.$pergunta->imagem) }}" 
                                         class="img-fluid rounded border" 
                                         style="max-height: 250px;">
                                </div>
                            @endif
                            
                            <div class="list-group">
                                @foreach(['A', 'B', 'C', 'D'] as $letra)
                                <label class="list-group-item d-flex align-items-center hover-highlight">
                                    <input type="radio" name="respostas[{{ $pergunta->id }}]" 
                                           value="{{ $letra }}" class="form-check-input flex-shrink-0 me-3" required
                                           data-question="{{ $index + 1 }}">
                                    <div>
                                        <span class="fw-bold">{{ $letra }}.</span> 
                                        <span class="ms-2">{{ $pergunta->{'alternativa_'.strtolower($letra)} }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                @if($index > 0)
                                <button type="button" class="btn btn-secondary prev-question" 
                                        data-target="question-{{ $index }}">
                                    <i class="fas fa-arrow-left me-2"></i> Anterior
                                </button>
                                @else
                                <div></div>
                                @endif
                                
                                @if($index < $simulado->perguntas->count() - 1)
                                <button type="button" class="btn btn-primary next-question" 
                                        data-target="question-{{ $index + 2 }}"
                                        disabled>
                                    Próxima <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                                @else
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check-circle me-2"></i> Finalizar
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </form>
            @endif
        </div>
    </div>
</div>

<!-- Rodapé -->
<div class="container mt-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center bg-light py-3">
            <p class="mb-1"><strong>EduHabil+</strong> - Sistema de Gestão Educacional</p>
            <p class="mb-0">Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostra o modal de confirmação se aluno foi selecionado
    @if(session('aluno_selecionado'))
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    confirmModal.show();
    @endif

    // Variáveis do timer
    const formRespostas = document.getElementById('form-respostas');
    const tempoInput = document.getElementById('tempo-resposta');
    const timerDisplay = document.getElementById('timer');
    const timerContainer = document.getElementById('timer-container');
    let tempoDecorrido = 0;
    let timer;
    let questionTimers = {};
    const tempoLimite = {{ $simulado->tempo_limite ? $simulado->tempo_limite * 60 : 0 }};
    let tempoRestante = tempoLimite;

    // Configura o botão de iniciar no modal
    document.getElementById('startSimulado')?.addEventListener('click', function() {
        confirmModal.hide();
        startTimer();
        timerContainer.classList.remove('d-none');
    });

    // Função para iniciar o timer regressivo
    function startTimer() {
        // Inicia timers individuais para cada questão
        document.querySelectorAll('.question-card').forEach(card => {
            const questionNum = card.id.split('-')[1];
            questionTimers[questionNum] = 0;
            
            const questionTimerDisplay = document.getElementById(`question-timer-${questionNum}`);
            if (questionTimerDisplay) {
                setInterval(() => {
                    questionTimers[questionNum]++;
                    const minutes = Math.floor(questionTimers[questionNum] / 60).toString().padStart(2, '0');
                    const seconds = (questionTimers[questionNum] % 60).toString().padStart(2, '0');
                    questionTimerDisplay.textContent = `${minutes}:${seconds}`;
                }, 1000);
            }
        });

        // Timer principal (regressivo se houver tempo limite)
        timer = setInterval(() => {
            tempoDecorrido++;
            tempoInput.value = tempoDecorrido;
            
            // Atualiza o display do timer
            if (tempoLimite > 0) {
                // Modo regressivo
                tempoRestante = tempoLimite - tempoDecorrido;
                
                if (tempoRestante <= 0) {
                    clearInterval(timer);
                    // Altera cor para vermelho quando tempo acaba
                    timerDisplay.parentElement.classList.remove('bg-danger');
                    timerDisplay.parentElement.classList.add('bg-dark');
                    alert('O tempo acabou! As respostas serão enviadas automaticamente.');
                    formRespostas.submit();
                    return;
                }
                
                // Altera cor para amarelo quando faltam 5 minutos
                if (tempoRestante <= 300) { // 5 minutos = 300 segundos
                    timerDisplay.parentElement.classList.remove('bg-danger');
                    timerDisplay.parentElement.classList.add('bg-warning', 'text-dark');
                }
                
                const hours = Math.floor(tempoRestante / 3600).toString().padStart(2, '0');
                const minutes = Math.floor((tempoRestante % 3600) / 60).toString().padStart(2, '0');
                const seconds = (tempoRestante % 60).toString().padStart(2, '0');
                timerDisplay.textContent = `${hours}:${minutes}:${seconds}`;
            } else {
                // Modo progressivo (sem tempo limite)
                const hours = Math.floor(tempoDecorrido / 3600).toString().padStart(2, '0');
                const minutes = Math.floor((tempoDecorrido % 3600) / 60).toString().padStart(2, '0');
                const seconds = (tempoDecorrido % 60).toString().padStart(2, '0');
                timerDisplay.textContent = `${hours}:${minutes}:${seconds}`;
            }
        }, 1000);
    }

    // Carregar alunos ao selecionar turma
    $('#turma_id').change(function() {
        const turmaId = $(this).val();
        
        if (!turmaId) {
            $('#aluno-container').hide();
            $('#aluno_id').prop('disabled', true).val('');
            $('#btn-selecionar').prop('disabled', true);
            return;
        }

        $('#aluno-container').show();
        $('#aluno_id').html('<option value="">Carregando...</option>').prop('disabled', true);

        $.ajax({
            url: "{{ route('respostas_simulados.aplicador.alunos', '') }}/" + turmaId,
            type: 'GET',
            success: function(response) {
                let options = '<option value="">Selecione o aluno</option>';
                
                if (response.length > 0 && response[0].id !== 0) {
                    response.forEach(aluno => {
                        options += `<option value="${aluno.id}">${aluno.name}</option>`;
                    });
                    $('#aluno_id').html(options).prop('disabled', false);
                } else {
                    $('#aluno_id').html('<option value="">Nenhum aluno nesta turma</option>').prop('disabled', true);
                }
                
                verificarCampos();
            },
            error: function() {
                $('#aluno_id').html('<option value="">Erro ao carregar alunos</option>');
            }
        });
    });

    // Verificar campos obrigatórios
    function verificarCampos() {
        const turmaVal = $('#turma_id').val();
        const alunoVal = $('#aluno_id').val();
        const racaVal = $('#raca').val();
        
        $('#btn-selecionar').prop('disabled', !(turmaVal && alunoVal && racaVal && alunoVal !== '0'));
    }

    // Monitorar mudanças nos campos
    $('#aluno_id, #raca').change(verificarCampos);

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
    formRespostas?.addEventListener('submit', function() {
        clearInterval(timer);
    });
});
</script>

<style>
    /* Estilos personalizados */
    .hover-highlight:hover {
        background-color: #f8f9fa;
        cursor: pointer;
        transform: translateX(5px);
        transition: all 0.2s ease;
    }
    
    .question-card {
        transition: all 0.3s ease;
    }
    
    .list-group-item {
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }
    
    .list-group-item:hover {
        border-left-color: #0d6efd;
    }
    
    .form-check-input {
        transform: scale(1.2);
        margin-top: 0.1rem;
    }
    
    #timer-container {
        font-family: 'Courier New', monospace;
    }
    
    .card-header {
        font-weight: 600;
    }
    
    .alert-info {
        background-color: #e7f5ff;
        border-color: #a5d8ff;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1.1rem;
    }
    
    .question-text {
        font-size: 1.1rem;
        line-height: 1.6;
    }
    
    /* Responsividade */
    @media (max-width: 768px) {
        .card-header h4 {
            font-size: 1.2rem;
        }
        
        .btn-lg {
            width: 100%;
        }
        
        .badge.fs-5 {
            font-size: 1rem !important;
        }
    }
</style>
@endsection