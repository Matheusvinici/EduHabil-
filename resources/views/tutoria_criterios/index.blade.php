@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Critérios de Avaliação</h4>
        <a href="{{ route('tutoria_criterios.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Novo Critério
        </a>
    </div>

    @if(session('message'))
        <div class="alert alert-{{ session('type', 'success') }} alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Categoria</th>
                        <th>Descrição</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tutoria_criterios as $tutoria_criterio)
                        <tr>
                            <td>{{ $tutoria_criterio->categoria }}</td>
                            <td>{{ $tutoria_criterio->descricao }}</td>
                            <td class="text-end">
                                <a href="{{ route('tutoria_criterios.edit', $tutoria_criterio->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('tutoria_criterios.destroy', $tutoria_criterio->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este critério?')">
                                        <i class="fas fa-trash-alt"></i> Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">Nenhum critério cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3">
                {{ $tutoria_criterios->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
