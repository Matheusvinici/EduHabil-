@extends('layouts.app')

@section('title', 'Correção do Gabarito - ' . $simulado->nome)

@section('styles')
<style>
    .resposta-correta {
        background-color: #d4edda !important;
    }
    .resposta-incorreta {
        background-color: #f8d7da !important;
    }
    .questao-img {
        max-width: 100px;
        cursor: pointer;
        transition: transform 0.3s;
    }
    .questao-img:hover {
        transform: scale(1.5);
    }
    .table-responsive {
        max-height: 500px;
        overflow-y: auto;
    }
    .sticky-header {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
    }
    .resposta-nao-detectada {
        color: #6c757d;
        font-style: italic;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="card shadow-lg mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fas fa-check-circle me-2"></i>Correção do Gabarito: {{ $simulado->nome }}
            </h4>
            <div class="badge bg-light text-dark fs-6">
                <i class="fas fa-calendar-alt me-1"></i>
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="card-body">
            <!-- Dados do Aluno -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Dados do Aluno</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Nome:</strong> {{ $aluno->name }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Turma:</strong> {{ session('aluno_turma') }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Raça/Cor:</strong> {{ $dados['raca'] }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Dados do Simulado</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Nome:</strong> {{ $simulado->nome }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Descrição:</strong> {{ $simulado->descricao ?? 'N/A' }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Total de Questões:</strong> {{ $totalQuestoes }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visualização do Gabarito -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Gabarito Digitalizado</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/'.$imagePath) }}" class="img-fluid rounded shadow" style="max-height: 400px;" alt="Gabarito digitalizado">
                    <div class="mt-3">
                        <a href="{{ asset('storage/'.$imagePath) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-expand me-1"></i> Visualizar em Tela Cheia
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tabela de Respostas -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list-ol me-2"></i>Correção das Respostas</h5>
                    <div class="badge bg-light text-dark fs-6">
                        {{ count(array_filter($respostas)) }}/{{ $totalQuestoes }} questões detectadas
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-dark sticky-header">
                                <tr>
                                    <th width="10%">Questão</th>
                                    <th width="20%">Resposta Detectada</th>
                                    <th width="15%">Confiança</th>
                                    <th width="45%">Correção Manual</th>
                                    <th width="10%">Visualização</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($i = 1; $i <= $totalQuestoes; $i++)
                                <tr>
                                    <td class="fw-bold">{{ $i }}</td>
                                    <td class="text-center fw-bold fs-5 {{ isset($respostas[$i]['correta']) && $respostas[$i]['correta'] ? 'text-success' : (isset($respostas[$i]) ? 'text-danger' : 'resposta-nao-detectada') }}">
                                        {{ isset($respostas[$i]) ? $respostas[$i]['resposta'] : 'N/D' }}
                                        @if(isset($respostas[$i]['correta']))
                                            @if($respostas[$i]['correta'])
                                                <i class="fas fa-check ms-1"></i>
                                            @else
                                                <i class="fas fa-times ms-1"></i>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
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
                                        <span class="text-muted">Não detectado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-around">
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
                                    <td class="text-center">
                                        @if(isset($respostas[$i]['imagem']) && $respostas[$i]['imagem'])
                                            <img src="{{ $respostas[$i]['imagem'] }}" class="questao-img img-thumbnail" 
                                                 data-bs-toggle="modal" data-bs-target="#imagemModal"
                                                 onclick="document.getElementById('modalImagem').src = this.src">
                                        @else
                                            <span class="text-muted">N/A</span>
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
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-save me-2"></i>Confirmar Correção</h5>
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
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('respostas_simulados.aplicador.camera', $simulado) }}" class="btn btn-lg btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Voltar para Correção
                            </a>
                            
                            <button type="submit" class="btn btn-lg btn-success" id="btnConfirmar">
                                <i class="fas fa-save me-2"></i> Confirmar e Salvar
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Visualização da Questão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImagem" src="" class="img-fluid" alt="Visualização da questão">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
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
        
        // Valida se todas as questões foram respondidas
        const totalQuestoes = parseInt(document.querySelector('input[name="total_questoes"]').value);
        let todasRespondidas = true;
        let mensagemErro = '';
        
        for(let i = 1; i <= totalQuestoes; i++) {
            const radioSelecionado = document.querySelector(`input[name="respostas[${i}]"]:checked`);
            const valorHidden = document.getElementById(`resposta_${i}`).value;
            
            if(!radioSelecionado && valorHidden === '') {
                todasRespondidas = false;
                mensagemErro = `A questão ${i} não foi respondida.`;
                break;
            }
        }
        
        if(!todasRespondidas) {
            Swal.fire({
                title: 'Atenção!',
                text: mensagemErro || 'Você precisa marcar todas as questões antes de salvar.',
                icon: 'warning',
                confirmButtonText: 'Entendi'
            });
            return;
        }
        
        Swal.fire({
            title: 'Confirmar envio?',
            text: 'Deseja realmente salvar estas respostas? Esta ação não pode ser desfeita.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, salvar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostra loading no botão
                const btn = document.getElementById('btnConfirmar');
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Salvando...';
                btn.disabled = true;
                
                // Envia o formulário
                e.target.submit();
            }
        });
    });
    
    // Ativa tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection