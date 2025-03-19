@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Criar Adaptação</h1>
        <a href="{{ route('adaptacoes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <form action="{{ route('adaptacoes.store') }}" method="POST">
        @csrf

        <!-- Linha com Deficiências e Características lado a lado -->
        <div class="row mb-4">
            <!-- Coluna de Deficiências -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <label class="mb-0">Deficiências</label>
                    </div>
                    <div class="card-body">
                        @foreach ($deficiencias as $deficiencia)
                            <div class="form-check mb-3">
                                <input type="checkbox" name="deficiencias[]" id="deficiencia_{{ $deficiencia->id }}" value="{{ $deficiencia->id }}" class="deficiencia-checkbox form-check-input">
                                <label for="deficiencia_{{ $deficiencia->id }}" class="form-check-label">{{ $deficiencia->nome }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Coluna de Características -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <label class="mb-0">Características</label>
                    </div>
                    <div class="card-body" id="caracteristicas-container">
                        <!-- As características serão carregadas aqui via JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Botão para Gerar Atividade -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-check"></i> Gerar Atividade
            </button>
        </div>
    </form>
</div>

<!-- JavaScript para carregar características dinamicamente -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Quando uma deficiência é selecionada
        $('.deficiencia-checkbox').change(function() {
            const deficienciaId = $(this).val();
            const isChecked = $(this).is(':checked');

            if (isChecked) {
                // Carrega as características da deficiência selecionada
                $.get(`/deficiencias/${deficienciaId}/caracteristicas`, function(data) {
                    let html = '';
                    data.forEach(caracteristica => {
                        html += `
                            <div class="form-check mb-3" data-deficiencia="${deficienciaId}">
                                <input type="checkbox" name="caracteristicas[]" id="caracteristica_${caracteristica.id}" value="${caracteristica.id}" class="form-check-input">
                                <label for="caracteristica_${caracteristica.id}" class="form-check-label">${caracteristica.nome}</label>
                            </div>
                        `;
                    });
                    $('#caracteristicas-container').append(html);
                });
            } else {
                // Remove as características da deficiência desmarcada
                $(`[data-deficiencia="${deficienciaId}"]`).remove();
            }
        });
    });
</script>
@endsection