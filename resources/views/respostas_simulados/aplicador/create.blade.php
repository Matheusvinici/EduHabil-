@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Mensagens de feedback -->
    @if(session('success'))
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 fs-3"></i>
                    <div>
                        <h5 class="mb-1">{{ session('success') }}</h5>
                        @if(session('nota_aluno'))
                        <p class="mb-0">Nota do aluno <strong>{{ session('aluno_nome') }}</strong>: 
                            <span class="badge bg-primary fs-6 px-2 py-1">{{ session('nota_aluno') }}/10</span>
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
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-3 fs-3"></i>
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
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="confirmModalLabel">Confirmar Aplicação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                @if($simulado->descricao)
                <p class="mb-0 small">{{ $simulado->descricao }}</p>
                @endif
            </div>
            <div id="timer-container" class="d-none">
                <span class="badge bg-danger fs-5">
                    <i class="fas fa-clock"></i> 
                    <span id="timer">{{ $simulado->tempo_limite ? sprintf('%02d:00:00', $simulado->tempo_limite) : '00:00:00' }}</span>
                </span>
            </div>
        </div>
    </div>

    @if(!session('aluno_selecionado'))
    <!-- Formulário de seleção do aluno -->
    <div class="card shadow-lg mb-4">
        <div class="card-body">
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
        </div>
    </div>
    @else
    <!-- Formulário de respostas em formato de gabarito -->
    <div class="card shadow-lg">
        <div class="card-body">
            <div class="alert alert-primary mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1"><i class="fas fa-user-graduate me-2"></i> {{ session('aluno_nome') }}</h5>
                        <div class="mt-2">
                            <span class="badge bg-secondary me-2"><i class="fas fa-school me-1"></i> {{ session('aluno_turma') }}</span>
                            <span class="badge bg-dark me-2"><i class="fas fa-palette me-1"></i> {{ session('raca') }}</span>
                            @if($simulado->tempo_limite)
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-clock me-1"></i> {{ $simulado->tempo_limite }} minutos
                            </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('respostas_simulados.aplicador.create', $simulado) }}?reset=1" class="btn btn-sm btn-outline-secondary">
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
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th width="10%" class="text-center align-middle">Questão</th>
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
                                <td class="text-center align-middle hover-highlight" onclick="selectOption(this)">
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
                    <button type="submit" class="btn btn-primary btn-lg py-3">
                        <i class="fas fa-check-circle me-2"></i> Finalizar Respostas
                    </button>
                    <a href="{{ route('respostas_simulados.aplicador.create', $simulado) }}?reset=1" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-2"></i> Cancelar e Selecionar Outro Aluno
                    </a>
                </div>
            </form>
        </div>
    </div>
    @endif
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

<style>
    .table-primary {
        background-color: #e7f5ff;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .form-check-input {
        width: 1.2em;
        height: 1.2em;
        margin-top: 0;
    }
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .hover-highlight:hover {
        background-color: #f1f8ff;
        cursor: pointer;
    }
    .nota-badge {
        font-size: 1.1rem;
        padding: 0.35rem 0.75rem;
    }
    .alert-success {
        border-left: 4px solid #2e7d32;
    }
    .alert-danger {
        border-left: 4px solid #c62828;
    }
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.85rem;
        }
        .form-check-input {
            width: 1em;
            height: 1em;
        }
        .btn-lg {
            padding: 0.5rem 1rem;
            font-size: 1rem;
        }
    }
</style>

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
        timer = setInterval(() => {
            tempoDecorrido++;
            tempoInput.value = tempoDecorrido;
            
            // Atualiza o display do timer
            if (tempoLimite > 0) {
                // Modo regressivo
                tempoRestante = tempoLimite - tempoDecorrido;
                
                if (tempoRestante <= 0) {
                    clearInterval(timer);
                    timerDisplay.parentElement.classList.remove('bg-danger');
                    timerDisplay.parentElement.classList.add('bg-dark');
                    alert('O tempo acabou! As respostas serão enviadas automaticamente.');
                    formRespostas.submit();
                    return;
                }
                
                // Altera cor para amarelo quando faltam 5 minutos
                if (tempoRestante <= 300) {
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

    // Selecionar alternativa ao clicar na célula
    window.selectOption = function(cell) {
        const radio = cell.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
            // Desmarca outras opções da mesma questão (segurança)
            const row = cell.parentNode;
            row.querySelectorAll('input[type="radio"]').forEach(r => {
                if (r !== radio) r.checked = false;
            });
        }
    };

    // Verificar campos obrigatórios
    function verificarCampos() {
        const turmaVal = $('#turma_id').val();
        const alunoVal = $('#aluno_id').val();
        const racaVal = $('#raca').val();
        
        // Desabilita se: turma não selecionada, ou aluno não selecionado/igual a 0, ou raça não selecionada
        $('#btn-selecionar').prop('disabled', !(turmaVal && alunoVal && alunoVal !== '0' && racaVal));
    }

    // Monitorar mudanças nos campos
    $('#aluno_id, #raca').change(verificarCampos);

    // Verifica se há mensagem de sucesso e reseta o formulário
    @if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        // Rola para o topo para mostrar a mensagem
        window.scrollTo(0, 0);
        
        // Limpa a sessão do aluno selecionado após 5 segundos
        setTimeout(() => {
            fetch('{{ route("respostas_simulados.aplicador.clear_session", $simulado) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
        }, 5000);
    });
    @endif

    // Validação antes de enviar
    formRespostas?.addEventListener('submit', function(e) {
        const totalQuestions = {{ $simulado->perguntas->count() }};
        const answered = document.querySelectorAll('input[type="radio"]:checked').length;
        
        if (answered < totalQuestions) {
            e.preventDefault();
            alert(`Por favor, responda todas as questões. Faltam ${totalQuestions - answered}.`);
        } else {
            // Confirmação antes de enviar
            if (!confirm('Tem certeza que deseja finalizar e enviar as respostas?')) {
                e.preventDefault();
            }
        }
    });
});

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
        data: {
            turma_id: turmaId,
            simulado_id: simuladoId
        },
        success: function(response) {
            let options = '<option value="">Selecione o aluno</option>';
            
            if (response.length > 0 && response[0].id !== 0) {
                response.forEach(aluno => {
                    options += `<option value="${aluno.id}">${aluno.name}</option>`;
                });
                $('#aluno_id').html(options).prop('disabled', false);
            } else {
                $('#aluno_id').html('<option value="0">Todos os alunos já responderam</option>').prop('disabled', true);
            }
            
            verificarCampos();
        },
        error: function(xhr) {
            let errorMsg = 'Erro ao carregar alunos';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg += ': ' + xhr.responseJSON.message;
            }
            $('#aluno_id').html(`<option value="0">${errorMsg}</option>`);
        }
    });
});
</script>
@endsection