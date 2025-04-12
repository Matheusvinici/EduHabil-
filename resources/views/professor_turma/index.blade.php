@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Vinculações - Professor/Turma</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <a href="{{ route('professor-turma.select-escola') }}" class="btn btn-success mb-3">
    <i class="fas fa-plus"></i> Nova Vinculação
</a>
<form method="GET" action="{{ route('professor-turma.index') }}" class="row g-3 mb-4">
    <div class="col-md-5">
        <label for="escola_id" class="form-label">Filtrar por Escola:</label>
        <select name="escola_id" id="escola_id" class="form-select">
            <option value="">Todas as escolas</option>
            @foreach ($escolas as $escola)
                <option value="{{ $escola->id }}" {{ request('escola_id') == $escola->id ? 'selected' : '' }}>
                    {{ $escola->nome }}
                </option>
            @endforeach
        </select>
    </div>

   

    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-filter"></i> Filtrar
        </button>
    </div>
</form>


    <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
            <tr>
                <th>Escola</th>
                <th>Turma</th>
                <th>Código do Professor</th>
                <th>Professor</th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vinculacoes as $vinculo)
                <tr>
                    <td>{{ $vinculo->escola ?? 'Não informada' }}</td>
                    <td>{{ $vinculo->nome_turma ?? 'Não informada' }}</td>
                    <td>{{ $vinculo->professor_id ?? 'N/A' }}</td>
                    <td>{{ $vinculo->professor ?? 'Não informado' }}</td>
                    <td class="text-center d-flex gap-2 justify-content-center">
                        <a href="{{ route('professor-turma.edit', ['professor_id' => $vinculo->professor_id, 'turma_id' => $vinculo->turma_id]) }}"
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>

                        <form action="{{ route('professor-turma.destroy', ['professor_id' => $vinculo->professor_id, 'turma_id' => $vinculo->turma_id]) }}"
                              method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta vinculação?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Nenhuma vinculação encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
