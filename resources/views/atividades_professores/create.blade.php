@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Card redesenhado -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <!-- Header moderno -->
                <div class="card-header bg-primary text-white py-3" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">
                            <i class="bi bi-magic me-2"></i> Criar Nova Atividade
                        </h2>
                        <span class="badge bg-white text-primary rounded-pill fs-6">Novo</span>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Alertas modernos -->
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                            <div>
                                <strong>Erro!</strong> {{ session('error') }}
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                    @endif
                    
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                            <div>
                                <strong>Sucesso!</strong> {{ session('success') }}
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                    @endif
                    
                    <form action="{{ route('atividades_professores.store') }}" method="POST">
                        @csrf
                        
                        <!-- Linha de seleção - Disciplina, Ano e Habilidade -->
                        <div class="row g-3 mb-4">
                            <!-- Disciplina -->
                            <div class="col-md-4">
                                <label for="disciplina_id" class="form-label fw-medium text-primary mb-2">
                                    <i class="bi bi-book me-2"></i> Disciplina
                                </label>
                                <select name="disciplina_id" id="disciplina_id" class="form-select py-3" style="border-radius: 8px;" required>
                                    <option value="" disabled selected>Selecione uma disciplina</option>
                                    @foreach($disciplinas as $disciplina)
                                    <option value="{{ $disciplina->id }}" @if(old('disciplina_id')==$disciplina->id) selected @endif>
                                        {{ $disciplina->nome }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Ano -->
                            <div class="col-md-4">
                                <label for="ano_id" class="form-label fw-medium text-primary mb-2">
                                    <i class="bi bi-mortarboard me-2"></i> Ano/Série
                                </label>
                                <select name="ano_id" id="ano_id" class="form-select py-3" style="border-radius: 8px;" required>
                                    <option value="" disabled selected>Selecione o ano/série</option>
                                    @foreach($anos as $ano)
                                    <option value="{{ $ano->id }}" @if(old('ano_id')==$ano->id) selected @endif>
                                        {{ $ano->nome }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Habilidade - Versão corrigida -->
                            <div class="col-md-4">
                                <label for="habilidade_id" class="form-label fw-medium text-primary mb-2">
                                    <i class="bi bi-bullseye me-2"></i> Habilidade
                                </label>
                                <select name="habilidade_id" id="habilidade_id" class="form-select py-3" style="border-radius: 8px;" required>
                                    <option value="" disabled selected>Selecione uma habilidade</option>
                                    @foreach($habilidades as $habilidade)
                                    <option value="{{ $habilidade->id }}" 
                                        data-descricao="{{ $habilidade->descricao }}"
                                        @if(old('habilidade_id')==$habilidade->id) selected @endif>
                                        {{ $habilidade->codigo }} - {{ Str::limit($habilidade->descricao, 35) }}
                                    </option>
                                    @endforeach
                                </select>
                                <div id="detalhes-habilidade" class="mt-2 p-3 bg-light rounded" style="display: none;">
                                    <small class="text-muted d-block fw-bold">Descrição completa:</small>
                                    <span id="texto-detalhes-habilidade" class="small"></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botão de ação -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4 py-3 fw-medium" style="border-radius: 8px;">
                                <i class="bi bi-lightning-charge-fill me-2"></i> Gerar Atividade
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Mostrar detalhes da habilidade quando selecionada
    document.getElementById('habilidade_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const detalhesDiv = document.getElementById('detalhes-habilidade');
        const textoDetalhes = document.getElementById('texto-detalhes-habilidade');
        
        if(selectedOption.value && selectedOption.dataset.descricao) {
            textoDetalhes.textContent = selectedOption.dataset.descricao;
            detalhesDiv.style.display = 'block';
        } else {
            detalhesDiv.style.display = 'none';
        }
    });
</script>
@endsection
@endsection