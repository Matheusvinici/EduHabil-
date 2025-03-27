@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="h4 mb-0"><i class="bi bi-file-earmark-text"></i> Detalhes da Atividade</h2>
                        <div>
                            <a href="{{ route('atividades.edit', $atividade->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <a href="{{ route('atividades.index') }}" class="btn btn-sm btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Informações Básicas -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Disciplina</label>
                                <div class="form-control bg-light">{{ $atividade->disciplina->nome }}</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Ano/Série</label>
                                <div class="form-control bg-light">{{ $atividade->ano->nome }}</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Habilidade</label>
                                <div class="form-control bg-light">{{ $atividade->habilidade->descricao }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Título -->
                    <div class="form-group mb-4">
                        <label class="form-label">Título da Atividade</label>
                        <div class="form-control bg-light">{{ $atividade->titulo }}</div>
                    </div>
                    
                    <!-- Objetivo -->
                    <div class="form-group mb-4">
                        <label class="form-label">Objetivo de Aprendizagem</label>
                        <div class="form-control bg-light" style="min-height: 100px;">{!! nl2br(e($atividade->objetivo)) !!}</div>
                    </div>
                    
                    <!-- Conteúdo em 2 colunas -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Etapas da Aula</label>
                                <div class="form-control bg-light" style="min-height: 200px;">{!! nl2br(e($atividade->metodologia)) !!}</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Materiais Necessários</label>
                                <div class="form-control bg-light" style="min-height: 200px;">{!! nl2br(e($atividade->materiais)) !!}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Atividade Proposta -->
                    <div class="form-group mb-4">
                        <label class="form-label">Atividade Proposta</label>
                        <div class="form-control bg-light" style="min-height: 200px;">{!! nl2br(e($atividade->resultados_esperados)) !!}</div>
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="d-flex justify-content-between">
                        <form action="{{ route('atividades.destroy', $atividade->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta atividade?')">
                                <i class="bi bi-trash"></i> Excluir Atividade
                            </button>
                        </form>
                        
                        
                    </div>
                </div>
                
                <div class="card-footer text-muted">
                    <div class="d-flex justify-content-between">
                        <small>
                            <i class="bi bi-calendar-plus"></i> Criado em: {{ $atividade->created_at->format('d/m/Y H:i') }}
                        </small>
                        <small>
                            <i class="bi bi-arrow-repeat"></i> Atualizado em: {{ $atividade->updated_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
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
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    .card {
        border: none;
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    
    .form-control {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
    }
</style>
@endsection