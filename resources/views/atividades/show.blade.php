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
                            <a href="{{ route('atividades.index') }}" class="btn btn-sm btn-light">
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
                                <label class="form-label">Disciplinas</label>
                                <div class="p-2 bg-light rounded">
                                    @foreach($atividade->disciplinas as $disciplina)
                                        <span class="badge bg-primary me-1 mb-1">{{ $disciplina->nome }}</span>
                                    @endforeach
                                </div>
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
                                <label class="form-label">Habilidades</label>
                                <div class="p-2 bg-light rounded">
                                    @foreach($atividade->habilidades as $habilidade)
                                        <span class="badge bg-info text-dark me-1 mb-1">{{ Str::limit($habilidade->descricao, 20) }}</span>
                                    @endforeach
                                </div>
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
                    <!-- Links de Sugestões -->
<div class="mb-4">
    <label class="form-label fw-semibold text-primary mb-2">
        <i class="fas fa-link me-2"></i> Sugestões de Links
    </label>
    <div class="form-control bg-light" style="min-height: 100px;">
        @if($atividade->links_sugestoes)
            @foreach(explode("\n", $atividade->links_sugestoes) as $link)
                @if(trim($link))
                    <div class="mb-1">
                        <a href="{{ $link }}" target="_blank" class="text-primary">
                            {{ $link }}
                        </a>
                    </div>
                @endif
            @endforeach
        @else
            Nenhum link sugerido
        @endif
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
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Tem certeza?')">
                                <i class="bi bi-trash"></i> Excluir
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
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    .badge {
        font-weight: 500;
    }
</style>
@endsection