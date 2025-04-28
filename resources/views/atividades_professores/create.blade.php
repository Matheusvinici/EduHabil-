@extends('layouts.app')

@section('title', 'Gerar Atividade Aleatória')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-primary text-white py-3 rounded-top-3">
                    <h2 class="h5 mb-0">
                        <i class="fas fa-magic me-2"></i> Gerar Atividade Aleatória
                    </h2>
                </div>

                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('atividades_professores.store') }}" method="POST" id="activityForm">
                        @csrf

                        <!-- Selecionar Ano -->
                        <div class="mb-3">
                            <label for="ano_id" class="form-label fw-medium text-primary">
                                <i class="fas fa-graduation-cap me-2"></i> Ano/Série
                            </label>
                            <select name="ano_id" id="ano_id" class="form-select" required>
                                <option value="" selected disabled>Selecione o ano/série</option>
                                @foreach($anos as $ano)
                                    <option value="{{ $ano->id }}">{{ $ano->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Selecionar Disciplina -->
                        <div class="mb-3">
                            <label for="disciplina_id" class="form-label fw-medium text-primary">
                                <i class="fas fa-book me-2"></i> Disciplina
                            </label>
                            <select name="disciplina_id" id="disciplina_id" class="form-select" required>
                                <option value="" selected disabled>Selecione a disciplina</option>
                                @foreach($disciplinas as $disciplina)
                                    <option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Habilidades (dinâmicas) -->
                        <div class="mb-3">
                            <label class="form-label fw-medium text-primary">
                                <i class="fas fa-bullseye me-2"></i> Habilidades
                            </label>
                            <div id="habilidadesContainer" class="border rounded p-3" style="min-height: 100px;">
                                <div class="text-muted text-center">Selecione o ano para carregar as habilidades</div>
                            </div>
                        </div>

                        <!-- Habilidades selecionadas -->
                        <div id="selectedHabilidades" class="mb-3 d-flex flex-wrap gap-2">
                        </div>

                        <!-- Campos ocultos -->
                        <div id="hiddenInputs"></div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-bolt me-2"></i> Gerar Atividade
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const anoSelect = document.getElementById('ano_id');
    const habilidadesContainer = document.getElementById('habilidadesContainer');
    const selectedHabilidades = document.getElementById('selectedHabilidades');
    const hiddenInputs = document.getElementById('hiddenInputs');
    let habilidadesDisponiveis = [];
    let habilidadesSelecionadas = [];

    anoSelect.addEventListener('change', function() {
        const anoId = this.value;
        habilidadesContainer.innerHTML = '<div class="text-center py-2">Carregando habilidades...</div>';

        fetch(`/habilidades-por-ano?ano_id=${anoId}`)
            .then(response => response.json())
            .then(data => {
                habilidadesDisponiveis = data;
                renderizarHabilidades();
            })
            .catch(error => {
                habilidadesContainer.innerHTML = '<div class="alert alert-danger">Erro ao carregar habilidades</div>';
            });
    });

    function renderizarHabilidades() {
        if (habilidadesDisponiveis.length === 0) {
            habilidadesContainer.innerHTML = '<div class="text-center text-muted">Nenhuma habilidade disponível para esse ano.</div>';
            return;
        }

        let html = '<div class="list-group">';
        habilidadesDisponiveis.forEach(habilidade => {
            html += `
                <button type="button" class="list-group-item list-group-item-action"
                    onclick="adicionarHabilidade(${habilidade.id}, '${habilidade.codigo}')">
                    <strong>${habilidade.codigo}</strong> - ${habilidade.descricao.substring(0, 50)}${habilidade.descricao.length > 50 ? '...' : ''}
                </button>
            `;
        });
        html += '</div>';
        habilidadesContainer.innerHTML = html;
    }

    window.adicionarHabilidade = function(id, codigo) {
        if (!habilidadesSelecionadas.includes(id)) {
            habilidadesSelecionadas.push(id);
            atualizarSelecionadas();
        }
    };

    window.removerHabilidade = function(id) {
        habilidadesSelecionadas = habilidadesSelecionadas.filter(h => h !== id);
        atualizarSelecionadas();
    };

    function atualizarSelecionadas() {
        selectedHabilidades.innerHTML = '';
        hiddenInputs.innerHTML = '';

        if (habilidadesSelecionadas.length === 0) {
            selectedHabilidades.innerHTML = '<span class="text-muted">Nenhuma habilidade selecionada.</span>';
            return;
        }

        habilidadesSelecionadas.forEach(id => {
            const habilidade = habilidadesDisponiveis.find(h => h.id == id);
            const badge = document.createElement('span');
            badge.className = 'badge bg-primary d-flex align-items-center';
            badge.style.gap = '5px';
            badge.innerHTML = `
                ${habilidade.codigo}
                <button type="button" class="btn-close btn-close-white btn-sm" onclick="removerHabilidade(${id})"></button>
            `;
            selectedHabilidades.appendChild(badge);

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'habilidades[]';
            hiddenInput.value = id;
            hiddenInputs.appendChild(hiddenInput);
        });
    }
});
</script>
@endsection
