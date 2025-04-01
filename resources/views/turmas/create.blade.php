@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cadastrar Turma</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('turmas.store') }}" method="POST" id="turmaForm">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="nome_turma">Nome da Turma</label>
                        <input type="text" name="nome_turma" class="form-control" placeholder="Digite o nome da turma" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Alunos</label>
                <div id="alunos-container">
                    <div class="input-group mb-2 aluno-input">
                        <input type="text" name="alunos[]" class="form-control" placeholder="Digite o nome do aluno" required>
                        <div class="input-group-append">
                            <button class="btn btn-outline-danger remover-aluno" type="button">Remover</button>
                        </div>
                    </div>
                </div>
                <button type="button" id="adicionar-aluno" class="btn btn-secondary mt-2">Adicionar Aluno</button>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adiciona novo campo de aluno
    document.getElementById('adicionar-aluno').addEventListener('click', function() {
        const container = document.getElementById('alunos-container');
        const novoInput = document.createElement('div');
        novoInput.className = 'input-group mb-2 aluno-input';
        novoInput.innerHTML = `
            <input type="text" name="alunos[]" class="form-control" placeholder="Digite o nome do aluno" required>
            <div class="input-group-append">
                <button class="btn btn-outline-danger remover-aluno" type="button">Remover</button>
            </div>
        `;
        container.appendChild(novoInput);
    });

    // Remove campo de aluno
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remover-aluno')) {
            const inputGroup = e.target.closest('.aluno-input');
            // Não permitir remover o último campo
            if (document.querySelectorAll('.aluno-input').length > 1) {
                inputGroup.remove();
            } else {
                alert('É necessário ter pelo menos um aluno cadastrado.');
            }
        }
    });
});
</script>
@endsection