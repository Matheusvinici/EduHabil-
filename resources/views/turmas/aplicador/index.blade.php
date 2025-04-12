@extends('layouts.app')

@section('content')
<div class="container">
                <div class="card border-primary shadow">
                    <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Listagem de Turmas</h3>
                <div>
                    <a href="{{ route('turmas.create') }}" class="btn btn-light mr-2">
                        <i class="fas fa-users mr-1"></i> Cadastro Manual
                    </a>
                    <a href="{{ route('turmas.create-lote') }}" class="btn btn-light">
                        <i class="fas fa-file-excel mr-1"></i> Cadastro em Lote
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form method="GET" action="{{ route('turmas.aplicador.index') }}" class="mb-4">
                <div class="input-group">
                    <input type="text" name="nome_turma" class="form-control" 
                           placeholder="Buscar por nome da turma" value="{{ request('nome_turma') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
                <form method="GET" action="{{ route('turmas.aplicador.index') }}" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="nome_escola" class="form-control" 
                            placeholder="Buscar por nome da escola" value="{{ request('nome_escola') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Buscar Escola
                            </button>
                        </div>
                    </div>
                </form>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Escola</th>
                            <th>Turma</th>
                            <th>Código</th>
                            <th>Alunos</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($turmas as $turma)
                        <tr>
                            <td>
                                @if($turma->escola)
                                    {{ $turma->escola->nome }}
                                @else
                                    <span class="text-danger">Escola não definida</span>
                                @endif
                            </td>
                            <td>{{ $turma->nome_turma }}</td>
                            <td>
                                <span class="badge badge-primary">
                                    {{ $turma->codigo_turma }}
                                </span>
                            </td>
                            <td>
                                {{ $turma->alunos_count ?? $turma->alunos->count() }} alunos
                            </td>
                            <td>
                                <div class="d-flex flex-wrap">
                                    <a href="{{ route('turmas.show', $turma->id) }}" class="btn btn-sm btn-info m-1">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <button class="btn btn-sm btn-warning m-1" data-toggle="modal" 
                                        data-target="#editTurmaModal{{ $turma->id }}">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <a href="{{ route('turmas.gerar-pdf', $turma->id) }}" class="btn btn-sm btn-danger m-1">
                                        <i class="fas fa-file-pdf"></i> Gerar PDF
                                    </a>
                                    <a href="{{ route('turmas.add-alunos-form', $turma->id) }}" class="btn btn-sm btn-success m-1">
                                        <i class="fas fa-user-plus"></i> Alunos
                                    </a>
                                   
                                </div>

                                <!-- Modal para Edição do Nome da Turma -->
                                <div class="modal fade" id="editTurmaModal{{ $turma->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">Editar Turma</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('turmas.update-nome', $turma->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="nome_turma">Nome da Turma</label>
                                                        <input type="text" class="form-control" id="nome_turma" 
                                                            name="nome_turma" value="{{ $turma->nome_turma }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center mt-4">
                {{ $turmas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection