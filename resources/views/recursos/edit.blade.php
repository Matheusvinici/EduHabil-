@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Recurso</h1>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <form action="{{ route('recursos.update', $recurso->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Nome e Descrição -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" name="nome" id="nome" class="form-control" value="{{ $recurso->nome }}" placeholder="Digite o nome do recurso" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea name="descricao" id="descricao" class="form-control" rows="5" placeholder="Descreva o recurso em detalhes, incluindo sua finalidade e funcionalidades." required>{{ $recurso->descricao }}</textarea>
                </div>
            </div>
        </div>

        <!-- Como Trabalhar e Direcionamentos -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="como_trabalhar">Como Trabalhar</label>
                    <textarea name="como_trabalhar" id="como_trabalhar" class="form-control" rows="5" placeholder="Explique como o recurso deve ser utilizado ou implementado." required>{{ $recurso->como_trabalhar }}</textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="direcionamentos">Direcionamentos</label>
                    <textarea name="direcionamentos" id="direcionamentos" class="form-control" rows="5" placeholder="Forneça orientações específicas para o uso eficaz do recurso." required>{{ $recurso->direcionamentos }}</textarea>
                </div>
            </div>
        </div>

        <!-- Deficiências -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <label class="mb-0">Deficiências</label>
                    </div>
                    <div class="card-body">
                        @foreach ($deficiencias as $deficiencia)
                            <div class="form-check mb-3">
                                <input type="checkbox" name="deficiencias[]" id="deficiencia_{{ $deficiencia->id }}" value="{{ $deficiencia->id }}" class="form-check-input deficiencia-checkbox"
                                    {{ in_array($deficiencia->id, $recurso->deficiencias->pluck('id')->toArray()) ? 'checked' : '' }}>
                                <label for="deficiencia_{{ $deficiencia->id }}" class="form-check-label">{{ $deficiencia->nome }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Características (serão carregadas dinamicamente) -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <label class="mb-0">Características</label>
                    </div>
                    <div class="card-body" id="caracteristicas-container">
                        @foreach ($recurso->caracteristicas as $caracteristica)
                            <div class="form-check mb-3" data-deficiencia="{{ $caracteristica->deficiencia_id }}">
                                <input type="checkbox" name="caracteristicas[]" id="caracteristica_{{ $caracteristica->id }}" value="{{ $caracteristica->id }}" class="form-check-input"
                                    {{ in_array($caracteristica->id, $recurso->caracteristicas->pluck('id')->toArray()) ? 'checked' : '' }}>
                                <label for="caracteristica_{{ $caracteristica->id }}" class="form-check-label">{{ $caracteristica->nome }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Botão de Envio -->
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary btn-block">Atualizar Recurso</button>
            </div>
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