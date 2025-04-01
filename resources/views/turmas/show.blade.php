@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Detalhes da Turma: {{ $turma->nome_turma }}</h3>
        </div>
        <div class="card-body">
            <!-- Informações da turma -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Quantidade de Alunos:</strong> {{ $turma->quantidade_alunos }}</p>
                    <p><strong>Código da Turma:</strong> {{ $turma->codigo_turma }}</p>
                </div>
                <div class="col-md-6 text-right">
                    <!-- Botão para abrir modal de adicionar alunos -->
                    <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#modalAdicionarAlunos">
                        <i class="fas fa-user-plus"></i> Adicionar Alunos
                    </button>
                </div>
            </div>

            <!-- Modal para adicionar novos alunos -->
            <div class="modal fade" id="modalAdicionarAlunos" tabindex="-1" role="dialog" aria-labelledby="modalAdicionarAlunosLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="modalAdicionarAlunosLabel">Adicionar Alunos à Turma {{ $turma->nome_turma }}</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('turmas.gerar-codigos-adicionais', $turma->id) }}" method="POST" id="formAdicionarAlunos">
                                @csrf
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Digite os nomes dos alunos abaixo. Os códigos de acesso e e-mails serão gerados automaticamente.
                                </div>
                                
                                <div id="alunos-container">
                                    <div class="row mb-3">
                                        <div class="col-md-10">
                                            <label>Nome do Aluno</label>
                                        </div>
                                    </div>
                                    
                                    <div class="aluno-input-group mb-3">
                                        <div class="input-group">
                                            <input type="text" name="alunos[]" class="form-control" placeholder="Digite o nome completo do aluno" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-danger remover-aluno" type="button">
                                                    <i class="fas fa-trash-alt"></i> Remover
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-secondary" id="adicionar-aluno">
                                        <i class="fas fa-plus"></i> Adicionar Outro Aluno
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Salvar Alunos
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de alunos da turma -->
            <h4 class="mb-3"><i class="fas fa-users"></i> Alunos da Turma</h4>
            @if($turma->alunos->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Nenhum aluno cadastrado nesta turma.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Código de Acesso</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($turma->alunos as $aluno)
                                <tr>
                                    <td>{{ $aluno->name }}</td>
                                    <td>{{ $aluno->email }}</td>
                                    <td><code>{{ $aluno->codigo_acesso }}</code></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adiciona novo campo de aluno
    document.getElementById('adicionar-aluno').addEventListener('click', function() {
        const container = document.getElementById('alunos-container');
        const novoInput = document.createElement('div');
        novoInput.className = 'aluno-input-group mb-3';
        novoInput.innerHTML = `
            <div class="input-group">
                <input type="text" name="alunos[]" class="form-control" placeholder="Digite o nome completo do aluno" required>
                <div class="input-group-append">
                    <button class="btn btn-outline-danger remover-aluno" type="button">
                        <i class="fas fa-trash-alt"></i> Remover
                    </button>
                </div>
            </div>
        `;
        container.appendChild(novoInput);
    });

    // Remove campo de aluno
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remover-aluno')) {
            const inputGroup = e.target.closest('.aluno-input-group');
            if (document.querySelectorAll('.aluno-input-group').length > 1) {
                inputGroup.remove();
            } else {
                alert('É necessário ter pelo menos um aluno cadastrado.');
            }
        }
    });
});
</script>

<style>
.aluno-input-group {
    transition: all 0.3s ease;
}
.remover-aluno {
    transition: all 0.2s ease;
}
.remover-aluno:hover {
    background-color: #dc3545;
    color: white !important;
}
</style>
@endsection

<!-- No final do seu show.blade.php -->
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Adicionar novo aluno
    $('#adicionar-aluno').click(function() {
        const newField = `
        <div class="aluno-input-group mb-3">
            <div class="input-group">
                <input type="text" name="alunos[]" class="form-control" placeholder="Digite o nome completo do aluno" required>
                <div class="input-group-append">
                    <button class="btn btn-outline-danger remover-aluno" type="button">
                        <i class="fas fa-trash-alt"></i> Remover
                    </button>
                </div>
            </div>
        </div>`;
        $('#alunos-container').append(newField);
    });

    // Remover aluno (usando delegation para elementos dinâmicos)
    $('#alunos-container').on('click', '.remover-aluno', function() {
        if ($('.aluno-input-group').length > 1) {
            $(this).closest('.aluno-input-group').remove();
        } else {
            alert('É necessário ter pelo menos um aluno cadastrado.');
        }
    });
});
</script>
@endsection