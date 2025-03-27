@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><i class="bi bi-joystick"></i> Atividades Lúdicas</h1>
        <a href="{{ route('atividades.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Criar Nova Atividade
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>Título</th>
                            <th class="d-none d-md-table-cell">Disciplina</th>
                            <th class="d-none d-sm-table-cell">Ano</th>
                            <th class="d-none d-lg-table-cell">Habilidade</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($atividades as $atividade)
                        <tr>
                            <td>{{ Str::limit($atividade->titulo, 30) }}</td>
                            <td class="d-none d-md-table-cell">{{ $atividade->disciplina->nome }}</td>
                            <td class="d-none d-sm-table-cell">{{ $atividade->ano->nome }}</td>
                            <td class="d-none d-lg-table-cell">{{ Str::limit($atividade->habilidade->descricao, 30) }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('atividades.show', $atividade->id) }}" 
                                       class="btn btn-sm btn-info text-nowrap">
                                        <i class="bi bi-eye"></i> <span class="d-none d-md-inline">Ver</span>
                                    </a>
                                    <a href="{{ route('atividades.edit', $atividade->id) }}" 
                                       class="btn btn-sm btn-warning text-nowrap">
                                        <i class="bi bi-pencil"></i> <span class="d-none d-md-inline">Editar</span>
                                    </a>
                                    <form action="{{ route('atividades.destroy', $atividade->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger text-nowrap" 
                                            onclick="return confirm('Tem certeza que deseja excluir esta atividade?')">
                                            <i class="bi bi-trash"></i> <span class="d-none d-md-inline">Excluir</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Nenhuma atividade encontrada.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($atividades->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        {{-- Previous Page Link --}}
                        @if($atividades->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">&laquo;</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $atividades->previousPageUrl() }}" rel="prev">&laquo;</a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach($atividades->getUrlRange(1, $atividades->lastPage()) as $page => $url)
                            @if($page == $atividades->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if($atividades->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $atividades->nextPageUrl() }}" rel="next">&raquo;</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">&raquo;</span>
                            </li>
                        @endif
                    </ul>
                </nav>
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
    
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .pagination .page-link {
        color: #0d6efd;
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