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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><i class="bi bi-building"></i> Listagem de Escolas</h1>
        <a href="{{ route('escolas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Nova Escola
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>Nome</th>
                            <th class="d-none d-md-table-cell">Endereço</th>
                            <th class="d-none d-sm-table-cell">Telefone</th>
                            <th class="d-none d-lg-table-cell">Código INEP</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($escolas as $escola)
                        <tr>
                            <td>{{ Str::limit($escola->nome, 30) }}</td>
                            <td class="d-none d-md-table-cell">{{ Str::limit($escola->endereco, 30) }}</td>
                            <td class="d-none d-sm-table-cell">{{ $escola->telefone }}</td>
                            <td class="d-none d-lg-table-cell">{{ $escola->codigo_escola }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('escolas.show', $escola->id) }}" 
                                       class="btn btn-sm btn-info text-nowrap">
                                        Ver
                                    </a>
                                    <a href="{{ route('escolas.edit', $escola->id) }}" 
                                       class="btn btn-sm btn-primary text-nowrap">
                                        Editar
                                    </a>
                                    <form action="{{ route('escolas.destroy', $escola->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger text-nowrap" 
                                            onclick="return confirm('Tem certeza que deseja excluir esta escola? Todos os dados relacionados serão perdidos.')">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="text-center py-5">
                                    <i class="bi bi-building" style="font-size: 3rem; color: #6c757d;"></i>
                                    <h5 class="mt-3">Nenhuma escola cadastrada</h5>
                                    <p class="text-muted">Você ainda não cadastrou nenhuma escola.</p>
                                    <a href="{{ route('escolas.create') }}" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-circle me-1"></i> Cadastrar Primeira Escola
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($escolas->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $escolas->links('pagination::bootstrap-5') }}
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
    
    @media (max-width: 768px) {
        .btn-sm {
            padding: 0.25rem;
        }
        
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }
        
        .d-flex.justify-content-between h1 {
            font-size: 1.5rem;
        }
    }
</style>
@endsection