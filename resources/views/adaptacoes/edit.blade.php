@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Adaptação</h1>
        <a href="{{ route('adaptacoes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <form action="{{ route('adaptacoes.update', $adaptacao->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Seleção de Deficiências -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <label class="mb-0">Deficiências</label>
            </div>
            <div class="card-body">
                @foreach ($deficiencias as $deficiencia)
                    <div class="form-check mb-3">
                        <input type="checkbox" name="deficiencias[]" id="deficiencia_{{ $deficiencia->id }}" value="{{ $deficiencia->id }}" class="deficiencia-checkbox form-check-input"
                            {{ in_array($deficiencia->id, $adaptacao->deficiencias->pluck('id')->toArray()) ? 'checked' : '' }}>
                        <label for="deficiencia_{{ $deficiencia->id }}" class="form-check-label">{{ $deficiencia->nome }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Carregamento Dinâmico de Características -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <label class="mb-0">Características</label>
            </div>
            <div class="card-body" id="caracteristicas-container">
                @foreach ($adaptacao->caracteristicas as $caracteristica)
                    <div class="form-check mb-3" data-deficiencia="{{ $caracteristica->deficiencia_id }}">
                        <input type="checkbox" name="caracteristicas[]" id="caracteristica_${caracteristica.id}" value="{{ $caracteristica->id }}" class="form-check-input"
                            {{ in_array($caracteristica->id, $adaptacao->caracteristicas->pluck('id')->toArray()) ? 'checked' : '' }}>
                        <label for="caracteristica_${caracteristica.id}" class="form-check-label">{{ $caracteristica->nome }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Botão para Atualizar Atividade -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-check"></i> Atualizar Atividade
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