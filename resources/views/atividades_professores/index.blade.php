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
        <h1 class="mb-0"><i class="bi bi-joystick"></i> Minhas Atividades</h1>
        <a href="{{ route('atividades_professores.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Criar Nova Atividade
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
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
                        @forelse($atividadesProfessores as $atividadeProfessor)
                        <tr>
                            <td>{{ Str::limit($atividadeProfessor->atividade->titulo, 30) }}</td>
                            <td class="d-none d-md-table-cell">{{ $atividadeProfessor->atividade->disciplina->nome }}</td>
                            <td class="d-none d-sm-table-cell">{{ $atividadeProfessor->atividade->ano->nome }}</td>
                            <td class="d-none d-lg-table-cell">{{ Str::limit($atividadeProfessor->atividade->habilidade->descricao, 30) }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('atividades_professores.show', $atividadeProfessor->id) }}" 
                                       class="btn btn-sm btn-info text-nowrap">
                                        <i class="bi bi-eye"></i> <span class="d-none d-md-inline">Ver</span>
                                    </a>
                                    <a href="{{ route('atividades_professores.download', $atividadeProfessor->id) }}" 
                                       class="btn btn-sm btn-success text-nowrap">
                                        <i class="bi bi-download"></i> <span class="d-none d-md-inline">PDF</span>
                                    </a>
                                    <form action="{{ route('atividades_professores.destroy', $atividadeProfessor->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger text-nowrap" 
                                            onclick="return confirm('Tem certeza que deseja remover esta atividade?')">
                                            <i class="bi bi-trash"></i> <span class="d-none d-md-inline">Remover</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="text-center py-5">
                                    <i class="bi bi-folder-x" style="font-size: 3rem; color: #6c757d;"></i>
                                    <h5 class="mt-3">Nenhuma atividade encontrada</h5>
                                    <p class="text-muted">Você ainda não criou nenhuma atividade.</p>
                                    <a href="{{ route('atividades_professores.create') }}" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-circle me-1"></i> Criar Primeira Atividade
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($atividadesProfessores->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $atividadesProfessores->links('pagination::bootstrap-5') }}
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
    }
</style>
@endsection