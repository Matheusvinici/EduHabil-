@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Lista de Simulados</h3>
                <div class="card-tools">
                    <a href="{{ route('simulados.create') }}" class="btn btn-light btn-sm">
                        Criar Novo Simulado
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th width="10%">ID</th>
                            <th width="20%">Nome</th>
                            <th width="20%">Ações</th>
                            <th width="25%">Simulados com Gabarito</th>
                            <th width="25%">Simulados sem Gabarito</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($simulados as $simulado)
                            <tr>
                                <td>{{ $simulado->id }}</td>
                                <td>{{ $simulado->nome }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('simulados.show', $simulado->id) }}" 
                                           class="btn btn-sm btn-outline-primary mr-1">
                                            Ver
                                        </a>
                                        <a href="{{ route('simulados.edit', $simulado->id) }}" 
                                           class="btn btn-sm btn-outline-warning mr-1">
                                            Editar
                                        </a>
                                        <form action="{{ route('simulados.destroy', $simulado->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Tem certeza que deseja excluir este simulado?')">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('simulados.gerarPdf', $simulado->id) }}" 
                                           class="btn btn-outline-success">
                                            Baixar Simulado
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('simulados.gerarPdfEscolas', $simulado->id) }}" 
                                           class="btn btn-outline-success mr-1">
                                            Baixar Simulado
                                        </a>
                                        <a href="{{ route('simulados.baixa-visao-escola', $simulado->id) }}" 
                                           class="btn btn-outline-primary mr-1">
                                            Prova Ampliada
                                        </a>
                                        <a href="{{ route('simulados.gerar-pdf-braille', $simulado->id) }}" 
                                           class="btn btn-outline-secondary">
                                            Prova em Braille
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Nenhum simulado cadastrado ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($simulados->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $simulados->links() }}
                </div>
            @endif
        </div>
        <div class="card-footer text-muted">
            Total de {{ $simulados->total() }} simulados cadastrados
        </div>
    </div>
</div>
@endsection