@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Cabeçalho com título e botão -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Lista de Adaptações</h1>
        <a href="{{ route('adaptacoes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova Adaptação
        </a>
    </div>

    <!-- Tabela de Adaptações -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-blue">
                <tr>
                    <th>ID</th>
                    <th>Recurso</th>
                    <th>Professor</th>
                    <th>Escola</th>
                    <th>Deficiências</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($adaptacoes as $adaptacao)
                    <tr>
                        <td>{{ $adaptacao->id }}</td>
                        <td>{{ $adaptacao->recurso->nome }}</td>
                        <td>
                            {{ $adaptacao->user->name ?? 'N/A' }}
                        </td>
                        <!-- Dentro da tabela, na coluna da escola -->
                    <td>
                        @if($adaptacao->user->escolas->isNotEmpty())
                            @foreach($adaptacao->user->escolas as $escola)
                                <span class="badge bg-secondary">{{ $escola->nome }}</span>
                            @endforeach
                        @else
                            N/A
                        @endif
                    </td>
                        <td>
                            @foreach ($adaptacao->deficiencias as $deficiencia)
                                <span class="badge bg-primary">{{ $deficiencia->nome }}</span>
                            @endforeach
                        </td>
                       
                        <td>
                            <a href="{{ route('adaptacoes.gerarPDF', $adaptacao) }}" class="btn btn-info btn-sm">Baixar PDF</a>
                            <a href="{{ route('adaptacoes.show', $adaptacao->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <form action="{{ route('adaptacoes.destroy', $adaptacao->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Deseja realmente excluir?')">
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
        {{ $adaptacoes->links() }}
    </div>
</div>

<!-- Adicionando Font Awesome para ícones -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<!-- Estilo personalizado -->
<style>
    .thead-blue {
        background-color: #0d6efd;
        color: white;
    }
    .badge.bg-primary {
        background-color: #0d6efd !important;
    }
    .badge.bg-success {
        background-color: #198754 !important;
    }
    .btn-sm {
        margin-right: 5px;
    }
    /* Novo estilo para melhorar a exibição */
    td {
        vertical-align: middle;
    }
    .badge {
        margin-bottom: 2px;
        display: inline-block;
    }
</style>
@endsection