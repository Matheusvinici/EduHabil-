@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Lista de Provas</h3>
                <div class="card-tools">
                    <a href="{{ route('provas.create') }}" class="btn btn-light btn-sm">
                        Criar Nova Prova
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
                            <th width="15%">Nome</th>
                            <th width="10%">Ano</th>
                            <th width="15%">Disciplina</th>
                            <th width="15%">Escola</th>
                            <th width="15%">Professor</th> <!-- Nova coluna -->
                            <th width="15%">Downloads</th>
                            <th width="15%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($provas as $prova)
                            <tr>
                                <td>{{ $prova->nome }}</td>
                                <td>{{ $prova->ano->nome }}</td>
                                <td>{{ $prova->disciplina->nome }}</td>
                                <td>{{ $prova->escola->nome ?? 'N/A' }}</td>
                                <td>{{ $prova->professor->name ?? 'N/A' }}</td> <!-- Exibindo nome do professor -->
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('provas.gerarPDF', $prova) }}" 
                                           class="btn btn-outline-info">
                                            Sem Gabarito
                                        </a>
                                        <a href="{{ route('provas.gerarPDFGabarito', $prova) }}" 
                                           class="btn btn-outline-secondary">
                                            Com Gabarito
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('provas.show', $prova->id) }}" 
                                           class="btn btn-sm btn-outline-primary mr-1">
                                            Visualizar
                                        </a>
                                      
                                        <form action="{{ route('provas.destroy', $prova) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Tem certeza que deseja excluir permanentemente a prova \'{{ addslashes($prova->nome) }}\'?')">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Nenhuma prova cadastrada ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($provas->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $provas->links() }}
                </div>
            @endif
        </div>
        <div class="card-footer text-muted">
            Total de {{ $provas->total() }} provas cadastradas
        </div>
    </div>
</div>
@endsection