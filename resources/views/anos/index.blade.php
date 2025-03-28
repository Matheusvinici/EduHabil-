@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Mensagens de Status -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><i class="bi bi-calendar3"></i> Anos Escolares</h1>
        <a href="{{ route('anos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Novo Ano Escolar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th style="width: 150px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anos as $ano)
                        <tr>
                            <td>{{ $ano->id }}</td>
                            <td>{{ $ano->nome }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('anos.edit', $ano->id) }}" class="btn btn-sm btn-warning text-nowrap">
                                        <i class="bi bi-pencil"></i> <span class="d-none d-md-inline">Editar</span>
                                    </a>
                                    <form action="{{ route('anos.destroy', $ano->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger text-nowrap" 
                                            onclick="return confirm('Tem certeza que deseja excluir este ano escolar?')">
                                            <i class="bi bi-trash"></i> <span class="d-none d-md-inline">Excluir</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">
                                <i class="bi bi-calendar-x" style="font-size: 2rem; color: #6c757d;"></i>
                                <p class="mt-2">Nenhum ano escolar cadastrado</p>
                                <a href="{{ route('anos.create') }}" class="btn btn-primary mt-2">
                                    <i class="bi bi-plus-circle me-1"></i> Criar Primeiro Ano
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
    
    @media (max-width: 768px) {
        .btn-sm {
            padding: 0.25rem;
        }
    }
</style>
@endsection