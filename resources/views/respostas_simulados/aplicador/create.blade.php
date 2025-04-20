@extends('layouts.app')

@section('title', 'Aplicar Simulado - ' . $simulado->nome)

@section('styles')
<style>
    /* Palette de cores azuis */
    :root {
        --azul-50: #eff6ff;
        --azul-100: #dbeafe;
        --azul-200: #bfdbfe;
        --azul-300: #93c5fd;
        --azul-400: #60a5fa;
        --azul-500: #3b82f6;
        --azul-600: #2563eb;
        --azul-700: #1d4ed8;
        --azul-800: #1e40af;
        --azul-900: #1e3a8a;
    }

    /* Estrutura principal */
    body {
        background-color: #f8fafc;
    }

    /* Cabeçalhos */
    .bg-primary-gradient {
        background: linear-gradient(135deg, var(--azul-600), var(--azul-800)) !important;
    }

    .card-header-azul {
        background-color: var(--azul-700);
        color: white;
    }

    /* Botões */
    .btn-azul {
        background-color: var(--azul-600);
        border-color: var(--azul-600);
        color: white;
    }

    .btn-azul:hover {
        background-color: var(--azul-700);
        border-color: var(--azul-700);
    }

    .btn-outline-azul {
        color: var(--azul-600);
        border-color: var(--azul-600);
    }

    .btn-outline-azul:hover {
        background-color: var(--azul-600);
        color: white;
    }

    /* Alertas */
    .alert-azul {
        background-color: var(--azul-50);
        border-left: 4px solid var(--azul-500);
        color: var(--azul-900);
    }

    /* Tabelas */
    .table-azul thead th {
        background-color: var(--azul-600);
        color: white;
    }

    .table-hover-azul tbody tr:hover {
        background-color: var(--azul-50);
    }

    /* Formulários */
    .form-control:focus, .form-select:focus {
        border-color: var(--azul-400);
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
    }

    /* Responsividade para mobile */
    @media (max-width: 768px) {
        .container {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .card {
            border-radius: 0;
            margin-left: -0.5rem;
            margin-right: -0.5rem;
            box-shadow: none;
            border: 1px solid #e2e8f0;
        }
        
        .card-header {
            padding: 0.75rem;
        }
        
        .card-header h4 {
            font-size: 1.1rem;
        }
        
        .table-responsive {
            font-size: 0.8rem;
        }
        
        .btn-lg {
            padding: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-floating label {
            font-size: 0.8rem;
        }
        
        .form-control, .form-select {
            padding: 0.375rem 0.5rem;
            height: calc(2.25rem + 2px);
        }
        
        .table td, .table th {
            padding: 0.5rem 0.3rem;
        }
        
        .form-check-input {
            width: 1.1em;
            height: 1.1em;
        }
        
        /* Mensagens */
        .alert {
            padding: 0.75rem;
            margin-left: 0.25rem;
            margin-right: 0.25rem;
        }
        
        .alert h5 {
            font-size: 0.95rem;
        }
    }

    @media (max-width: 576px) {
        .row > [class^="col-"] {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }
        
        .modal-dialog {
            margin: 0.25rem auto;
            max-width: 95%;
        }
        
        /* Timer em mobile */
        #timer-container .badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        
        /* Botões em formulários */
        .d-grid .btn {
            font-size: 0.85rem;
        }
    }

    /* Efeitos de interação */
    .hover-azul:hover {
        background-color: var(--azul-50) !important;
        cursor: pointer;
    }

    /* Modal */
    .modal-azul .modal-header {
        background-color: var(--azul-700);
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container px-0 px-sm-3 py-3">
    <!-- Mensagens de feedback -->
    @if(session('success'))
    <div class="row mb-3 g-0">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show m-2 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>
                        <h5 class="mb-1">{{ session('success') }}</h5>
                        @if(session('nota_aluno'))
                        <p class="mb-0 d-flex align-items-center">
                            <span class="me-2">Nota:</span>
                            <span class="badge bg-primary">{{ session('nota_aluno') }}/10</span>
                        </p>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="row mb-3 g-0">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show m-2 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div>
                        <h5 class="mb-0">{{ session('error') }}</h5>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal de Confirmação -->
    <div class="modal fade modal-azul" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">
                        <i class="fas fa-check-circle me-2"></i>Confirmar Aplicação
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Você está prestes a aplicar o simulado:</p>
                    <div class="alert alert-azul mb-3">
                        <h6 class="fw-bold">{{ $simulado->nome }}</h6>
                        @if($simulado->descricao)
                        <p class="mb-0 small">{{ $simulado->descricao }}</p>
                        @endif
                    </div>
                    
                    <div class="card mb-3 border-azul">
                        <div class="card-body">
                            <h6 class="fw-bold text-azul mb-3">
                                <i class="fas fa-user-graduate me-2"></i>Dados do Aluno
                            </h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center py-2 px-0 border-azul">
                                    <i class="fas fa-user text-azul me-2"></i>
                                    <span class="fw-semibold">{{ session('aluno_nome') }}</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center py-2 px-0 border-azul">
                                    <i class="fas fa-school text-azul me-2"></i>
                                    <span class="fw-semibold">{{ session('aluno_turma') }}</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center py-2 px-0 border-azul">
                                    <i class="fas fa-palette text-azul me-2"></i>
                                    <span class="fw-semibold">{{ session('raca') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    @if($simulado->tempo_limite)
                    <div class="alert alert-warning d-flex align-items-center">
                        <i class="fas fa-clock me-2"></i>
                        <div>
                            <strong>Tempo limite:</strong> {{ $simulado->tempo_limite }} minutos
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='{{ route('respostas_simulados.aplicador.create', $simulado) }}?reset=1'">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-azul" id="startSimulado">
                        <i class="fas fa-play me-2"></i> Iniciar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cabeçalho -->
    <div class="card shadow-sm mb-4 border-azul">
        <div class="card-header card-header-azul d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-2 mb-md-0">
                <h4 class="mb-1">
                    <i class="fas fa-chalkboard-teacher me-2"></i> 
                    {{ $simulado->nome }}
                </h4>
                @if($simulado->descricao)
                <p class="mb-0 small opacity-75">{{ $simulado->descricao }}</p>
                @endif
            </div>
            <div id="timer-container" class="d-none">
                <span class="badge bg-danger">
                    <i class="fas fa-clock me-1"></i>
                    <span id="timer">{{ $simulado->tempo_limite ? sprintf('%02d:00', $simulado->tempo_limite) : '00:00' }}</span>
                </span>
            </div>
        </div>
    </div>

    @if(!session('aluno_selecionado'))
    <!-- Formulário de seleção do aluno -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('respostas_simulados.aplicador.selecionar', $simulado) }}" id="form-selecao">
                @csrf
                
                <div class="row g-3 mb-4">
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
                            <label for="turma_id">
                                <i class="fas fa-users-class me-1"></i> Turma
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-floating" id="aluno-container" style="display: none;">
                            <select class="form-select" name="aluno_id" id="aluno_id" required disabled>
                                <option value="">Selecione...</option>
                            </select>
                            <label for="aluno_id">
                                <i class="fas fa-user-graduate me-1"></i> Aluno
                            </label>
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
                    <label for="raca">
                        <i class="fas fa-palette me-1"></i> Raça/Cor do Aluno
                    </label>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-azul btn-lg py-3" id="btn-selecionar" disabled>
                        <i class="fas fa-user-check me-2"></i> Selecionar Aluno
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <!-- Formulário de respostas -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="alert alert-azul mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="mb-2 mb-md-0">
                        <h5 class="mb-1 d-flex align-items-center">
                            <i class="fas fa-user-graduate me-2"></i> 
                            {{ session('aluno_nome') }}
                        </h5>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <span class="badge bg-azul">
                                <i class="fas fa-school me-1"></i> {{ session('aluno_turma') }}
                            </span>
                            <span class="badge bg-secondary">
                                <i class="fas fa-palette me-1"></i> {{ session('raca') }}
                            </span>
                            @if($simulado->tempo_limite)
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-clock me-1"></i> {{ $simulado->tempo_limite }} min
                            </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('respostas_simulados.aplicador.create', $simulado) }}?reset=1" 
                       class="btn btn-sm btn-outline-azul">
                        <i class="fas fa-sync-alt me-1"></i> Trocar Aluno
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('respostas_simulados.aplicador.store', $simulado) }}" id="form-respostas">
                @csrf
                <input type="hidden" name="aluno_id" value="{{ session('aluno_id') }}">
                <input type="hidden" name="turma_id" value="{{ session('turma_id') }}">
                <input type="hidden" name="raca" value="{{ session('raca') }}">
                <input type="hidden" name="tempo_resposta" id="tempo-resposta" value="0">
                
                <div class="table-responsive mb-4">
                    <table class="table table-hover table-azul mb-0">
                        <thead>
                            <tr>
                                <th width="15%" class="text-center align-middle">Questão</th>
                                @foreach($alternativas as $letra)
                                <th class="text-center align-middle">{{ $letra }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($simulado->perguntas as $pergunta)
                            <tr>
                                <td class="text-center fw-bold align-middle">{{ $loop->iteration }}</td>
                                @foreach($alternativas as $letra)
                                <td class="text-center align-middle hover-azul" onclick="selectOption(this)">
                                    <div class="form-check d-flex justify-content-center m-0">
                                        <input class="form-check-input" type="radio" 
                                               name="respostas[{{ $pergunta->id }}]" 
                                               value="{{ $letra }}" 
                                               id="q{{ $pergunta->id }}{{ $letra }}"
                                               required>
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-azul btn-lg py-3">
                        <i class="fas fa-check-circle me-2"></i> Finalizar Respostas
                    </button>
                    <a href="{{ route('respostas_simulados.aplicador.create', $simulado) }}?reset=1" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-2"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

<!-- Rodapé -->
<footer class="mt-4">
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center bg-light py-2">
                <p class="mb-0 small">
                    <strong>EduHabil+</strong> - Sistema de Gestão Educacional
                </p>
            </div>
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostra o modal de confirmação se aluno foi selecionado
    @if(session('aluno_selecionado'))
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    confirmModal.show();
    @endif

    // Timer
    const formRespostas = document.getElementById('form-respostas');
    const tempoInput = document.getElementById('tempo-resposta');
    const timerDisplay = document.getElementById('timer');
    const timerContainer = document.getElementById('timer-container');
    let tempoDecorrido = 0;
    let timer;
    const tempoLimite = {{ $simulado->tempo_limite ? $simulado->tempo_limite * 60 : 0 }};
    let tempoRestante = tempoLimite;

    // Iniciar simulado
    document.getElementById('startSimulado')?.addEventListener('click', function() {
        confirmModal.hide();
        startTimer();
        timerContainer.classList.remove('d-none');
    });

    // Função do timer
    function startTimer() {
        timer = setInterval(() => {
            tempoDecorrido++;
            tempoInput.value = tempoDecorrido;
            
            if (tempoLimite > 0) {
                tempoRestante = tempoLimite - tempoDecorrido;
                
                if (tempoRestante <= 0) {
                    clearInterval(timer);
                    alert('Tempo esgotado! Enviando respostas...');
                    formRespostas.submit();
                    return;
                }
                
                const minutes = Math.floor(tempoRestante / 60).toString().padStart(2, '0');
                const seconds = (tempoRestante % 60).toString().padStart(2, '0');
                timerDisplay.textContent = `${minutes}:${seconds}`;
            } else {
                const minutes = Math.floor(tempoDecorrido / 60).toString().padStart(2, '0');
                const seconds = (tempoDecorrido % 60).toString().padStart(2, '0');
                timerDisplay.textContent = `${minutes}:${seconds}`;
            }
        }, 1000);
    }

    // Selecionar alternativa
    window.selectOption = function(cell) {
        const radio = cell.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
            const row = cell.parentNode;
            row.querySelectorAll('input[type="radio"]').forEach(r => {
                if (r !== radio) r.checked = false;
            });
        }
    };

    // Verificar campos do formulário
    function verificarCampos() {
        const turmaVal = $('#turma_id').val();
        const alunoVal = $('#aluno_id').val();
        const racaVal = $('#raca').val();
        $('#btn-selecionar').prop('disabled', !(turmaVal && alunoVal && alunoVal !== '0' && racaVal));
    }

    // Carregar alunos ao selecionar turma
    $('#turma_id').change(function() {
        const turmaId = $(this).val();
        const simuladoId = {{ $simulado->id }};
        
        if (!turmaId) {
            $('#aluno-container').hide();
            $('#aluno_id').prop('disabled', true).val('');
            $('#btn-selecionar').prop('disabled', true);
            return;
        }

        $('#aluno-container').show();
        $('#aluno_id').html('<option value="">Carregando...</option>').prop('disabled', true);

        $.ajax({
            url: "{{ route('respostas_simulados.aplicador.alunos') }}",
            type: 'GET',
            data: { turma_id: turmaId, simulado_id: simuladoId },
            success: function(response) {
                let options = '<option value="">Selecione o aluno</option>';
                
                if (response.length > 0) {
                    response.forEach(aluno => {
                        options += `<option value="${aluno.id}">${aluno.name}</option>`;
                    });
                    $('#aluno_id').html(options).prop('disabled', false);
                } else {
                    $('#aluno_id').html('<option value="0">Nenhum aluno disponível</option>');
                }
                
                verificarCampos();
            },
            error: function(xhr) {
                $('#aluno_id').html('<option value="0">Erro ao carregar</option>');
            }
        });
    });

    // Monitorar mudanças nos campos
    $('#aluno_id, #raca').change(verificarCampos);

    // Validação antes de enviar respostas
    formRespostas?.addEventListener('submit', function(e) {
        const totalQuestions = {{ $simulado->perguntas->count() }};
        const answered = document.querySelectorAll('input[type="radio"]:checked').length;
        
        if (answered < totalQuestions) {
            e.preventDefault();
            alert(`Responda todas as questões! Faltam ${totalQuestions - answered}.`);
        } else if (!confirm('Confirmar envio das respostas?')) {
            e.preventDefault();
        }
    });

    // Rolar para o topo se houver mensagem
    @if(session('success'))
    window.scrollTo(0, 0);
    @endif
});
</script>
@endsection