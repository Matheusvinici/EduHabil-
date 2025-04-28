@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-joystick"></i> Atividades Interventivas</h1>
        <a href="{{ route('atividades.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Nova Atividade
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Título</th>
                            <th class="d-none d-md-table-cell">Disciplinas</th>
                            <th class="d-none d-sm-table-cell">Ano</th>
                            <th class="d-none d-lg-table-cell">Habilidades</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($atividades as $atividade)
                        <tr>
                            <td>{{ Str::limit($atividade->titulo, 25) }}</td>
                            <td class="d-none d-md-table-cell">
                                @foreach($atividade->disciplinas as $disciplina)
                                    <span class="badge bg-primary me-1 mb-1">{{ $disciplina->nome }}</span>
                                @endforeach
                            </td>
                            <td class="d-none d-sm-table-cell">
                                {{ $atividade->ano->nome }}
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @foreach($atividade->habilidades as $habilidade)
                                    <span class="badge bg-info text-dark me-1 mb-1" 
                                          data-bs-toggle="tooltip" 
                                          title="{{ $habilidade->descricao }}">
                                        {{ Str::limit($habilidade->descricao, 15) }}
                                    </span>
                                @endforeach
                            </td>
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
                            <td colspan="5" class="text-center py-4">Nenhuma atividade encontrada</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $atividades->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    // Ativa tooltips
    document.addEventListener('DOMContentLoaded', function() {
        const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltips.map(function(tooltip) {
            return new bootstrap.Tooltip(tooltip);
        });
    });
</script>

<style>
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        font-size: 0.85em;
    }
    
    .table th {
        font-weight: 600;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endsection