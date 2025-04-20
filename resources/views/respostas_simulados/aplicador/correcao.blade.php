@extends('layouts.app')

@section('title', 'Correção do Gabarito - ' . $simulado->nome)

@section('styles')
<style>
    /* Cores azuis */
    .bg-blue-50 { background-color: #eff6ff; }
    .bg-blue-100 { background-color: #dbeafe; }
    .border-blue-200 { border-color: #bfdbfe; }
    .text-blue-700 { color: #1d4ed8; }
    .text-blue-800 { color: #1e40af; }
    
    /* Estilos específicos */
    .resposta-correta {
        background-color: #d1fae5 !important;
        border-left: 4px solid #10b981;
    }
    .resposta-incorreta {
        background-color: #fee2e2 !important;
        border-left: 4px solid #ef4444;
    }
    .resposta-nao-detectada {
        color: #6b7280;
        font-style: italic;
    }
    
    .questao-img {
        max-width: 80px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #bfdbfe;
        border-radius: 4px;
    }
    .questao-img:hover {
        transform: scale(1.8);
        z-index: 100;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .table-responsive {
        max-height: 60vh;
        overflow-y: auto;
    }
    
    .sticky-header {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .progress {
        height: 1.5rem;
        background-color: #e5e7eb;
    }
    .progress-bar {
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Responsividade */
    @media (max-width: 768px) {
        .table td, .table th {
            padding: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-check-label {
            font-size: 0.8rem;
        }
        
        .questao-img {
            max-width: 60px;
        }
    }
    
    @media (max-width: 576px) {
        .d-flex-vertical {
            flex-direction: column !important;
            gap: 1rem !important;
        }
        
        .table-responsive {
            max-height: 50vh;
        }
        
        .card-header h4 {
            font-size: 1.2rem;
        }
    }
</style>
@endsection

@section('content')

<div class="container-fluid px-0 px-md-3 py-3">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary-gradient text-white d-flex flex-column flex-md-row justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <i class="fas fa-check-circle me-3 fs-3"></i>
                <h4 class="mb-0 fw-semibold">Correção do Gabarito: {{ $simulado->nome }}</h4>
            </div>
            <div class="badge bg-white text-blue-800 fs-6 py-2 px-3">
                <i class="fas fa-calendar-alt me-1"></i>
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="card-body">
            <!-- Dados do Aluno e Simulado -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-blue-50 border-blue-200 d-flex align-items-center py-3">
                            <i class="fas fa-user-graduate text-blue-700 me-2 fs-4"></i>
                            <h5 class="mb-0 text-blue-800">Dados do Aluno</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex align-items-center py-2 border-blue-100">
                                    <i class="fas fa-user text-blue-600 me-2 fs-5"></i>
                                    <div>
                                        <small class="text-blue-700">Nome</small>
                                        <div class="fw-semibold">{{ $aluno->name }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex align-items-center py-2 border-blue-100">
                                    <i class="fas fa-school text-blue-600 me-2 fs-5"></i>
                                    <div>
                                        <small class="text-blue-700">Turma</small>
                                        <div class="fw-semibold">{{ session('aluno_turma') }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex align-items-center py-2 border-blue-100">
                                    <i class="fas fa-palette text-blue-600 me-2 fs-5"></i>
                                    <div>
                                        <small class="text-blue-700">Raça/Cor</small>
                                        <div class="fw-semibold">{{ $dados['raca'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-blue-50 border-blue-200 d-flex align-items-center py-3">
                            <i class="fas fa-file-alt text-blue-700 me-2 fs-4"></i>
                            <h5 class="mb-0 text-blue-800">Dados do Simulado</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex align-items-center py-2 border-blue-100">
                                    <i class="fas fa-heading text-blue-600 me-2 fs-5"></i>
                                    <div>
                                        <small class="text-blue-700">Nome</small>
                                        <div class="fw-semibold">{{ $simulado->nome }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex align-items-center py-2 border-blue-100">
                                    <i class="fas fa-align-left text-blue-600 me-2 fs-5"></i>
                                    <div>
                                        <small class="text-blue-700">Descrição</small>
                                        <div class="fw-semibold">{{ $simulado->descricao ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="list-group-item d-flex align-items-center py-2 border-blue-100">
                                    <i class="fas fa-list-ol text-blue-600 me-2 fs-5"></i>
                                    <div>
                                        <small class="text-blue-700">Total de Questões</small>
                                        <div class="fw-semibold">{{ $totalQuestoes }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visualização do Gabarito -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-blue-50 border-blue-200 d-flex align-items-center py-3">
                    <i class="fas fa-image text-blue-700 me-2 fs-4"></i>
                    <h5 class="mb-0 text-blue-800">Gabarito Digitalizado</h5>
                    <span class="badge bg-white text-blue-800 ms-auto">
                        {{ count(array_filter($respostas)) }}/{{ $totalQuestoes }} detectadas
                    </span>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/'.$imagePath) }}" class="img-fluid rounded-3 shadow border border-blue-200" style="max-height: 400px;" alt="Gabarito digitalizado">
                    <div class="mt-3">
                        <a href="{{ asset('storage/'.$imagePath) }}" target="_blank" class="btn btn-outline-blue btn-sm">
                            <i class="fas fa-expand me-1"></i> Visualizar em Tela Cheia
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tabela de Respostas -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-blue-50 border-blue-200 d-flex align-items-center py-3">
                    <i class="fas fa-list-check text-blue-700 me-2 fs-4"></i>
                    <h5 class="mb-0 text-blue-800">Correção das Respostas</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-header">
                                <tr>
                                    <th width="10%" class="bg-blue-50 text-blue-800">Questão</th>
                                    <th width="20%" class="bg-blue-50 text-blue-800">Resposta</th>
                                    <th width="15%" class="bg-blue-50 text-blue-800">Confiança</th>
                                    <th width="45%" class="bg-blue-50 text-blue-800">Correção Manual</th>
                                    <th width="10%" class="bg-blue-50 text-blue-800">Visualizar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($i = 1; $i <= $totalQuestoes; $i++)
                                <tr class="{{ isset($respostas[$i]['correta']) && $respostas[$i]['correta'] ? 'resposta-correta' : (isset($respostas[$i]) ? 'resposta-incorreta' : '') }}">
                                    <td class="fw-bold align-middle">{{ $i }}</td>
                                    <td class="text-center align-middle">
                                        <span class="fw-bold fs-5 d-block {{ isset($respostas[$i]['correta']) && $respostas[$i]['correta'] ? 'text-success' : (isset($respostas[$i]) ? 'text-danger' : 'resposta-nao-detectada') }}">
                                            {{ isset($respostas[$i]) ? $respostas[$i]['resposta'] : 'N/D' }}
                                            @if(isset($respostas[$i]['correta']))
                                                @if($respostas[$i]['correta'])
                                                    <i class="fas fa-check ms-1"></i>
                                                @else
                                                    <i class="fas fa-times ms-1"></i>
                                                @endif
                                            @endif
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        @if(isset($respostas[$i]))
                                        <div class="progress">
                                            <div class="progress-bar {{ $respostas[$i]['confianca'] > 0.7 ? 'bg-success' : ($respostas[$i]['confianca'] > 0.4 ? 'bg-warning' : 'bg-danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $respostas[$i]['confianca'] * 100 }}%" 
                                                 aria-valuenow="{{ $respostas[$i]['confianca'] * 100 }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ number_format($respostas[$i]['confianca'] * 100, 1) }}%
                                            </div>
                                        </div>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex flex-wrap justify-content-evenly gap-1">
                                            @foreach($alternativas as $letra)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" 
                                                       name="respostas[{{ $i }}]" 
                                                       id="questao{{ $i }}{{ $letra }}" 
                                                       value="{{ $letra }}"
                                                       {{ (isset($respostas[$i]) && $respostas[$i]['resposta'] == $letra) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="questao{{ $i }}{{ $letra }}">
                                                    {{ $letra }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if(isset($respostas[$i]['imagem']) && $respostas[$i]['imagem'])
                                            <img src="{{ $respostas[$i]['imagem'] }}" class="questao-img img-thumbnail" 
                                                 data-bs-toggle="modal" data-bs-target="#imagemModal"
                                                 onclick="document.getElementById('modalImagem').src = this.src">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Formulário de Confirmação -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-blue-50 border-blue-200 d-flex align-items-center py-3">
                    <i class="fas fa-save text-blue-700 me-2 fs-4"></i>
                    <h5 class="mb-0 text-blue-800">Confirmar Correção</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('respostas_simulados.aplicador.salvar-gabarito', $simulado) }}" id="formCorrecao">
                        @csrf
                        <input type="hidden" name="aluno_id" value="{{ $aluno->id }}">
                        <input type="hidden" name="turma_id" value="{{ session('turma_id') }}">
                        <input type="hidden" name="raca" value="{{ $dados['raca'] }}">
                        <input type="hidden" name="imagePath" value="{{ $imagePath }}">
                        <input type="hidden" name="total_questoes" value="{{ $totalQuestoes }}">
                        
                        @for($i = 1; $i <= $totalQuestoes; $i++)
                            <input type="hidden" name="respostas[{{ $i }}]" 
                                   value="{{ isset($respostas[$i]) ? $respostas[$i]['resposta'] : '' }}" 
                                   id="resposta_{{ $i }}">
                        @endfor
                        
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                            <a href="{{ route('respostas_simulados.aplicador.camera', $simulado) }}" class="btn btn-outline-blue btn-lg flex-grow-1 py-3">
                                <i class="fas fa-arrow-left me-2"></i> Voltar
                            </a>
                            
                            <button type="submit" class="btn btn-success btn-lg flex-grow-1 py-3" id="btnConfirmar">
                                <i class="fas fa-save me-2"></i> Salvar Correção
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualização de imagens -->
<div class="modal fade" id="imagemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-blue-50 border-blue-200">
                <h5 class="modal-title text-blue-800">Visualização da Questão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="modalImagem" src="" class="img-fluid w-100" alt="Visualização da questão" style="max-height: 70vh;">
            </div>
            <div class="modal-footer bg-blue-50 border-blue-200">
                <button type="button" class="btn btn-blue" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Fechar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Atualiza os campos hidden com os valores dos radio buttons
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const questaoId = this.name.match(/\[(\d+)\]/)[1];
            document.getElementById(`resposta_${questaoId}`).value = this.value;
        });
    });

    // Confirmação antes de enviar
    document.getElementById('formCorrecao').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const totalQuestoes = parseInt(document.querySelector('input[name="total_questoes"]').value);
        let respostasVazias = [];
        
        for(let i = 1; i <= totalQuestoes; i++) {
            const valorHidden = document.getElementById(`resposta_${i}`).value;
            if(valorHidden === '') {
                respostasVazias.push(i);
            }
        }
        
        if(respostasVazias.length > 0) {
            Swal.fire({
                title: 'Atenção!',
                html: `<p>${respostasVazias.length} questões não foram respondidas:</p>
                       <p class="text-muted">${respostasVazias.join(', ')}</p>
                       <p>Deseja realmente salvar assim?</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sim, salvar',
                cancelButtonText: 'Voltar para corrigir'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitForm();
                }
            });
        } else {
            submitForm();
        }
        
        function submitForm() {
            const btn = document.getElementById('btnConfirmar');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Salvando...';
            btn.disabled = true;
            e.target.submit();
        }
    });
    
    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
});
</script>
@endsection