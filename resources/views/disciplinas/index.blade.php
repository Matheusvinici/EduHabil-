@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="h4 mb-0"><i class="bi bi-book"></i> Lista de Disciplinas</h3>
                <a href="{{ route('disciplinas.create') }}" class="btn btn-light">
                    <i class="bi bi-plus-circle me-1"></i> Nova Disciplina
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
                            <th>Nome</th>
                            <th style="width: 180px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($disciplinas->sortByDesc('id') as $disciplina)
                        <tr>
                            <td>{{ $disciplina->id }}</td>
                            <td>{{ $disciplina->nome }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('disciplinas.show', $disciplina->id) }}" 
                                       class="btn btn-sm btn-info text-nowrap">
                                        <i class="bi bi-eye"></i> <span class="d-none d-md-inline">Ver</span>
                                    </a>
                                    <a href="{{ route('disciplinas.edit', $disciplina->id) }}" 
                                       class="btn btn-sm btn-warning text-nowrap">
                                        <i class="bi bi-pencil"></i> <span class="d-none d-md-inline">Editar</span>
                                    </a>
                                    <form action="{{ route('disciplinas.destroy', $disciplina->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger text-nowrap" 
                                            onclick="return confirm('Tem certeza que deseja excluir esta disciplina?')">
                                            <i class="bi bi-trash"></i> <span class="d-none d-md-inline">Excluir</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">
                                <i class="bi bi-bookmark-x" style="font-size: 2rem; color: #6c757d;"></i>
                                <p class="mt-2">Nenhuma disciplina cadastrada</p>
                                <a href="{{ route('disciplinas.create') }}" class="btn btn-primary mt-2">
                                    <i class="bi bi-plus-circle me-1"></i> Criar Primeira Disciplina
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($disciplinas->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $disciplinas->links('pagination::bootstrap-5') }}
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
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
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
    
    @media (max-width: 768px) {
        .btn-sm {
            padding: 0.25rem;
        }
    }
</style>
@endsection