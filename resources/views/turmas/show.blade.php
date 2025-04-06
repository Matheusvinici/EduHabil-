@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
    <h3 class="mb-0">
        <i class="fas fa-users mr-2"></i>Turma: {{ $turma->nome_turma }}
    </h3>
    <div>
        <span class="badge badge-light">
            <i class="fas fa-key mr-1"></i>Código: {{ $turma->codigo_turma }}
        </span>
        <a href="{{ route('turmas.add-alunos-form', $turma->id) }}" class="btn btn-sm btn-success ml-2">
            <i class="fas fa-user-plus mr-1"></i> Adicionar Alunos
        </a>
        <a href="{{ route('turmas.index') }}" class="btn btn-sm btn-light ml-2">
            <i class="fas fa-arrow-left mr-1"></i> Voltar
        </a>
                <a href="{{ route('turmas.gerar-pdf', $turma->id) }}" class="btn btn-sm btn-danger ml-2">
            <i class="fas fa-file-pdf mr-1"></i> Gerar PDF
        </a>
    </div>
</div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="25%">Nome</th>
                            <th width="25%">Email</th>
                            <th width="15%">Código</th>
                            <th width="20%">Deficiência</th>
                            <th width="15%" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($turma->alunos as $aluno)
                        <tr>
                            <td>{{ $aluno->name }}</td>
                            <td>{{ $aluno->email }}</td>
                            <td><span class="badge badge-secondary">{{ $aluno->codigo_acesso }}</span></td>
                            <td>
                                <span class="badge badge-info">
                                    {{ strtoupper($aluno->deficiencia) ?? 'Não informada' }}
                                </span>
                            </td>
                            <td>
                            <a href="{{ route('turmas.alunos.edit', ['turma' => $turma->id, 'aluno' => $aluno->id]) }}" 
                        class="btn btn-sm btn-primary" title="Editar aluno">
                        Editar
                        </a>

                        </td>

                            <td class="text-center">

                                <button class="btn btn-sm btn-danger delete-aluno ml-1"
                                        data-id="{{ $aluno->id }}"
                                        data-name="{{ $aluno->name }}"
                                        title="Remover aluno">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteAlunoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Confirmar Exclusão
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteAlunoForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Tem certeza que deseja remover o aluno <strong id="alunoNomeDelete"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita!</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt mr-1"></i> Remover
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
