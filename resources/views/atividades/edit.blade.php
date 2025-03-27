@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0"><i class="bi bi-pencil-square"></i> Editar Atividade</h2>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('atividades.update', $atividade->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Primeira linha com 3 colunas -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="disciplina_id" class="form-label">Disciplina</label>
                                    <select name="disciplina_id" id="disciplina_id" class="form-select" required>
                                        <option value="" disabled>Selecione uma disciplina</option>
                                        @foreach($disciplinas as $disciplina)
                                        <option value="{{ $disciplina->id }}" {{ $atividade->disciplina_id == $disciplina->id ? 'selected' : '' }}>
                                            {{ $disciplina->nome }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ano_id" class="form-label">Ano/Série</label>
                                    <select name="ano_id" id="ano_id" class="form-select" required>
                                        <option value="" disabled>Selecione o ano/série</option>
                                        @foreach($anos as $ano)
                                        <option value="{{ $ano->id }}" {{ $atividade->ano_id == $ano->id ? 'selected' : '' }}>
                                            {{ $ano->nome }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="habilidade_id" class="form-label">Habilidade</label>
                                    <select name="habilidade_id" id="habilidade_id" class="form-select" required>
                                        <option value="" disabled>Selecione a habilidade</option>
                                        @foreach($habilidades as $habilidade)
                                        <option value="{{ $habilidade->id }}" {{ $atividade->habilidade_id == $habilidade->id ? 'selected' : '' }}>
                                            {{ $habilidade->descricao }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Título -->
                        <div class="form-group mb-4">
                            <label for="titulo" class="form-label">Título da Atividade</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" 
                                   value="{{ old('titulo', $atividade->titulo) }}" required>
                        </div>
                        
                        <!-- Objetivo -->
                        <div class="form-group mb-4">
                            <label for="objetivo" class="form-label">Objetivo de Aprendizagem</label>
                            <textarea name="objetivo" id="objetivo" class="form-control" rows="3" required>{{ old('objetivo', $atividade->objetivo) }}</textarea>
                        </div>
                        
                        <!-- Metodologia e Materiais (2 colunas) -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="metodologia" class="form-label">Etapas da Aula</label>
                                    <textarea name="metodologia" id="metodologia" class="form-control" rows="5" required>{{ old('metodologia', $atividade->metodologia) }}</textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="materiais" class="form-label">Materiais Necessários</label>
                                    <textarea name="materiais" id="materiais" class="form-control" rows="5" required>{{ old('materiais', $atividade->materiais) }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Atividade Proposta -->
                        <div class="form-group mb-4">
                            <label for="resultados_esperados" class="form-label">Atividade Proposta</label>
                            <textarea name="resultados_esperados" id="resultados_esperados" class="form-control" rows="5" required>{{ old('resultados_esperados', $atividade->resultados_esperados) }}</textarea>
                        </div>
                        
                        <!-- Botão de submit -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-save me-2"></i> Atualizar Atividade
                            </button>
                            <a href="{{ route('atividades.show', $atividade->id) }}" class="btn btn-secondary px-4 py-2">
                                <i class="bi bi-x-circle me-2"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-label {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .form-control, .form-select {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    textarea.form-control {
        min-height: 120px;
    }
    
    .card {
        border: none;
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
</style>
@endsection