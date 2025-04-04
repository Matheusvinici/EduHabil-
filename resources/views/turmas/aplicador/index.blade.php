@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listagem de Turmas</h3>
        <div class="card-tools">
            <a href="{{ route('turmas.create') }}" class="btn btn-primary">Nova Turma</a>
            
            
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nome da Turma</th>
                    <th>Quantidade de Alunos</th>
                    <th>Código da Turma</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($turmas as $turma)
                <tr>
                    <td>{{ $turma->nome_turma }}</td>
                    <td>{{ $turma->quantidade_alunos }}</td>
                    <td>{{ $turma->codigo_turma }}</td>
                    <td>
                     

                        <!-- Modal para adicionar novos alunos -->
                        <div class="modal fade" id="modalAdicionarAlunos{{ $turma->id }}" tabindex="-1" role="dialog" aria-labelledby="modalAdicionarAlunosLabel{{ $turma->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalAdicionarAlunosLabel{{ $turma->id }}">Adicionar Alunos à Turma</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                    <form action="{{ route('turmas.gerar-codigos-adicionais', $turma->id) }}" method="POST" id="formAdicionarAlunos{{ $turma->id }}">
                                        @csrf
                                        <div id="alunos-container-{{ $turma->id }}">
                                            <div class="input-group mb-2 aluno-input">
                                                <input type="text" name="alunos[]" class="form-control" placeholder="Digite o nome do aluno" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-danger remover-aluno" type="button">Remover</button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary mt-2">Salvar</button>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Link para visualizar a turma -->
                        <a href="{{ route('turmas.show', $turma->id) }}" class="btn btn-sm btn-info">Ver Detalhes</a>

                        <!-- Link para editar a turma -->
                        <a href="{{ route('turmas.edit', $turma->id) }}" class="btn btn-sm btn-warning">Editar</a>

                        <!-- Formulário para excluir a turma -->
                        <form action="{{ route('turmas.destroy', $turma->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta turma? Todos os alunos vinculados também serão removidos.')">Excluir</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adiciona novo campo de aluno nos modais
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('adicionar-aluno')) {
            const containerId = e.target.getAttribute('data-container');
            const container = document.getElementById(containerId);
            const novoInput = document.createElement('div');
            novoInput.className = 'input-group mb-2 aluno-input';
            novoInput.innerHTML = `
                <input type="text" name="alunos[]" class="form-control" placeholder="Digite o nome do aluno" required>
                <div class="input-group-append">
                    <button class="btn btn-outline-danger remover-aluno" type="button">Remover</button>
                </div>
            `;
            container.appendChild(novoInput);
        }
    });

    // Remove campo de aluno
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remover-aluno')) {
            const inputGroup = e.target.closest('.aluno-input');
            // Não permitir remover o último campo
            if (inputGroup.parentElement.querySelectorAll('.aluno-input').length > 1) {
                inputGroup.remove();
            } else {
                alert('É necessário ter pelo menos um aluno cadastrado.');
            }
        }
    });
});
</script>
@endsection