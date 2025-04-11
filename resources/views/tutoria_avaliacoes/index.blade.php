@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Avaliações de Tutoria</h4>
        <a href="{{ route('tutoria_avaliacoes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nova Avaliação
        </a>
    </div>

    <form method="GET" action="{{ route('tutoria_avaliacoes.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar por tutor, escola ou data">
            <button class="btn btn-outline-secondary" type="submit">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
    </form>

    @if(session('message'))
        <div class="alert alert-{{ session('type', 'success') }} alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tutor</th>
                        <th>Escola</th>
                        <th>Data da Visita</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tutoria_avaliacoes as $avaliacao)
                        <tr>
                            <td>{{ $avaliacao->tutor->name ?? '-' }}</td>
                            <td>{{ $avaliacao->escola->nome ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($avaliacao->data_visita)->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('tutoria_avaliacoes.edit', $avaliacao->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('tutoria_avaliacoes.destroy', $avaliacao->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Deseja remover esta avaliação?')">
                                        <i class="fas fa-trash-alt"></i> Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Nenhuma avaliação registrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3">
                {{ $tutoria_avaliacoes->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
