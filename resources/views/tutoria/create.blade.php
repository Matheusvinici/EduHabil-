@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Criar Acompanhamento de Tutoria</h4>
                <a href="{{ route('tutoria.acompanhamento.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('tutoria.acompanhamento.store') }}" method="POST">
                @csrf
                <input type="hidden" name="avaliacao_id" value="{{ $avaliacao->id }}">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Escola:</label>
                            <input type="text" class="form-control" value="{{ $avaliacao->escola->nome }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Data da Avaliação:</label>
                            <input type="text" class="form-control" value="{{ $avaliacao->data_visita->format('d/m/Y') }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Tutor Responsável:</label>
                            <input type="text" class="form-control" value="{{ $avaliacao->tutor->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Média Geral:</label>
                            <input type="text" class="form-control" 
                                   value="{{ number_format($avaliacao->criterios->avg('pivot.nota'), 2) }}" readonly>
                        </div>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Critérios para Acompanhamento</h5>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">Selecionar</th>
                                <th style="width: 25%">Categoria</th>
                                <th style="width: 40%">Descrição</th>
                                <th style="width: 10%">Nota</th>
                                <th style="width: 20%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($avaliacao->criterios as $criterio)
                                <tr class="{{ $criterio->pivot->nota <= 2.5 ? 'table-danger' : '' }}">
                                    <td class="text-center">
                                        <input type="checkbox" name="criterios[]" value="{{ $criterio->id }}" 
                                               {{ $criterio->pivot->nota <= 2.5 ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $criterio->categoria }}</td>
                                    <td>{{ $criterio->descricao }}</td>
                                    <td>{{ $criterio->pivot->nota }}</td>
                                    <td>
                                        @if($criterio->pivot->nota <= 2.5)
                                            <span class="badge bg-danger">Crítico</span>
                                        @else
                                            <span class="badge bg-success">OK</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Prioridade:</label>
                            <select name="prioridade" class="form-select" required>
                                <option value="">Selecione...</option>
                                <option value="alta">Alta</option>
                                <option value="media">Média</option>
                                <option value="baixa">Baixa</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Responsável:</label>
                            <select name="responsavel_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                @foreach($tutores as $tutor)
                                    <option value="{{ $tutor->id }}">{{ $tutor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Prazo:</label>
                            <input type="date" name="prazo" class="form-control" required 
                                   min="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Ação de Melhoria:</label>
                    <textarea name="acao_melhoria" class="form-control" rows="3" required></textarea>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Observações:</label>
                    <textarea name="observacoes" class="form-control" rows="2"></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save"></i> Salvar Acompanhamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .table-danger td {
        background-color: #fff5f5;
    }
    .form-control[readonly] {
        background-color: #f8f9fa;
    }
</style>
@endsection