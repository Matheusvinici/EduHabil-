@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Cabeçalho com título e botão -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Recursos</h1>
        <a href="{{ route('recursos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Recurso
        </a>
    </div>

    <!-- Tabela de Recursos -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-blue"> <!-- Alterado para thead-blue -->
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Deficiências</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recursos as $recurso)
                    <tr>
                        <td>{{ $recurso->id }}</td>
                        <td>{{ $recurso->nome }}</td>
                        <td>
                            @foreach ($recurso->deficiencias as $deficiencia)
                                <span class="badge bg-primary">{{ $deficiencia->nome }}</span> <!-- Alterado para bg-primary -->
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('recursos.show', $recurso->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <a href="{{ route('recursos.edit', $recurso->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <form action="{{ route('recursos.destroy', $recurso->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')">
                                    <i class="fas fa-trash"></i> Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="d-flex justify-content-center mt-4">
        {{ $recursos->links() }}
    </div>
</div>

<!-- Adicionando Font Awesome para ícones -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<!-- Estilo personalizado para thead-blue -->
<style>
    .thead-blue {
        background-color: #0d6efd; /* Azul do Bootstrap */
        color: white;
    }
    .btn-primary {
        background-color: #0d6efd; /* Azul do Bootstrap */
        border-color: #0d6efd;
    }
    .btn-primary:hover {
        background-color: #0b5ed7; /* Azul mais escuro ao passar o mouse */
        border-color: #0a58ca;
    }
    .badge.bg-primary {
        background-color: #0d6efd !important; /* Azul do Bootstrap */
    }
</style>
@endsection