@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="h4 mb-0"><i class="bi bi-list-check"></i> Lista de Habilidades</h3>
                <a href="{{ route('habilidades.create') }}" class="btn btn-light btn-responsive">
                    <i class="bi bi-plus-circle"></i>
                    <span class="d-none d-sm-inline ms-1">Criar Habilidade</span>
                </a>
            </div>
        </div>
        
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Ano</th>
                            <th class="d-none d-md-table-cell">Disciplina</th>
                            <th>Descrição</th>
                            <th style="width: 160px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($habilidades->sortByDesc('id') as $habilidade)
                        <tr>
                            <td>{{ $habilidade->id }}</td>
                            <td>{{ $habilidade->ano->nome }}</td>
                            <td class="d-none d-md-table-cell">{{ $habilidade->disciplina->nome }}</td>
                            <td>{{ Str::limit($habilidade->descricao, 50) }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1 gap-sm-2 justify-content-center">
                                    <a href="{{ route('habilidades.show', $habilidade->id) }}" 
                                       class="btn btn-action btn-info">
                                        <i class="bi bi-eye"></i>
                                        <span class="d-none d-sm-inline ms-1">Ver</span>
                                    </a>
                                    <a href="{{ route('habilidades.edit', $habilidade->id) }}" 
                                       class="btn btn-action btn-warning">
                                        <i class="bi bi-pencil"></i>
                                        <span class="d-none d-sm-inline ms-1">Editar</span>
                                    </a>
                                    <form action="{{ route('habilidades.destroy', $habilidade->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-action btn-danger" 
                                            onclick="return confirm('Tem certeza que deseja excluir esta habilidade?')">
                                            <i class="bi bi-trash"></i>
                                            <span class="d-none d-sm-inline ms-1">Excluir</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="bi bi-exclamation-circle" style="font-size: 2rem; color: #6c757d;"></i>
                                <p class="mt-2">Nenhuma habilidade cadastrada</p>
                                <a href="{{ route('habilidades.create') }}" class="btn btn-primary mt-2 btn-responsive">
                                    <i class="bi bi-plus-circle"></i>
                                    <span class="d-none d-sm-inline ms-1">Criar Habilidade</span>
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($habilidades->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $habilidades->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .table {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .table thead th {
        border-bottom: none;
        font-weight: 600;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    /* Estilo para botões responsivos */
    .btn-responsive {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        white-space: nowrap;
    }
    
    .btn-action {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }
    
    /* Ajustes para telas pequenas */
    @media (max-width: 576px) {
        .btn-responsive, .btn-action {
            padding: 0.25rem 0.5rem;
        }
        
        .btn-responsive span, .btn-action span {
            display: none;
        }
        
        .btn-responsive i, .btn-action i {
            margin-right: 0;
        }
    }
    
    /* Ajustes para telas médias */
    @media (min-width: 576px) and (max-width: 768px) {
        .btn-responsive, .btn-action {
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
        }
    }
    
    .btn-light {
        background-color: #f8f9fa;
        border-color: #f8f9fa;
        color: #212529;
    }
    
    .btn-light:hover {
        background-color: #e2e6ea;
        border-color: #dae0e5;
    }
</style>
@endsection