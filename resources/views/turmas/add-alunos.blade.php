@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-user-plus mr-2"></i>Adicionar Alunos à Turma: {{ $turma->nome_turma }}
                </h3>
                <a href="{{ route('turmas.show', $turma->id) }}" class="btn btn-sm btn-light">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar
                </a>
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

            <form action="{{ route('turmas.add-alunos', $turma->id) }}" method="POST" id="addAlunosForm">
                @csrf
                
                <div class="form-group">
                    <label>Cadastrar Novos Alunos</label>
                    <div id="alunos-container">
                        <div class="input-group mb-2 aluno-input">
                            <input type="text" name="alunos[]" class="form-control" placeholder="Digite o nome completo do aluno" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-danger remover-aluno" type="button">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="adicionar-aluno" class="btn btn-secondary mt-2">
                        <i class="fas fa-plus mr-1"></i> Adicionar Outro Aluno
                    </button>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    Para cada aluno será gerado automaticamente:
                    <ul class="mb-0 mt-2">
                        <li>Um e-mail único</li>
                        <li>Um código de acesso (que será a senha inicial)</li>
                    </ul>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Cadastrar Alunos
                    </button>
                </div>
            </form>
        </div>
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
            <input type="text" name="alunos[]" class="form-control" placeholder="Digite o nome completo do aluno" required>
            <div class="input-group-append">
                <button class="btn btn-outline-danger remover-aluno" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(novoInput);
    });

    // Remove campo de aluno
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remover-aluno') || 
            e.target.closest('.remover-aluno')) {
            const btn = e.target.classList.contains('remover-aluno') 
                ? e.target 
                : e.target.closest('.remover-aluno');
            const inputGroup = btn.closest('.aluno-input');
            
            if (document.querySelectorAll('.aluno-input').length > 1) {
                inputGroup.remove();
            } else {
                alert('É necessário cadastrar pelo menos um aluno.');
            }
        }
    });

    // Validação do formulário
    document.getElementById('addAlunosForm').addEventListener('submit', function(e) {
        const inputs = document.querySelectorAll('input[name="alunos[]"]');
        let vazios = 0;
        
        inputs.forEach(input => {
            if (input.value.trim() === '') vazios++;
        });
        
        if (vazios > 0) {
            e.preventDefault();
            alert('Por favor, preencha todos os nomes dos alunos ou remova os campos vazios.');
        }
    });
});
</script>
@endsection