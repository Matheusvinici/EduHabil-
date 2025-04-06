@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card border-primary shadow">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-users mr-2"></i>Cadastrar Nova Turma
                </h3>
                <a href="{{ route('turmas.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('turmas.store') }}" method="POST" id="turmaForm">
                @csrf
                
                <div class="row mb-4">
                    <!-- Adicione este campo para seleção de escola -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="escola_id" class="font-weight-bold">Escola</label>
                            <select name="escola_id" class="form-control form-control-lg" required>
                                <option value="">Selecione uma escola</option>
                                @foreach($escolas as $escola)
                                    <option value="{{ $escola->id }}">{{ $escola->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome_turma" class="font-weight-bold">Nome da Turma</label>
                            <input type="text" name="nome_turma" class="form-control form-control-lg" placeholder="Digite o nome da turma" required>
                        </div>
                    </div>
                </div>
                
                <!-- Restante do formulário permanece igual -->
                <div class="form-group">
                    <label class="font-weight-bold">Alunos</label>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Para cada aluno será gerado automaticamente:
                        <ul class="mb-0 mt-2">
                            <li>Um e-mail único</li>
                            <li>Um código de acesso (que será a senha inicial)</li>
                        </ul>
                    </div>
                    
                    <div id="alunos-container" class="mb-3">
                        <div class="input-group mb-2 aluno-input">
                            <input type="text" name="alunos[]" class="form-control" placeholder="Nome completo do aluno" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-danger remover-aluno" type="button">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" id="adicionar-aluno" class="btn btn-outline-primary">
                        <i class="fas fa-plus mr-1"></i> Adicionar Aluno
                    </button>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        <i class="fas fa-save mr-1"></i> Cadastrar Turma
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- O script JavaScript permanece igual -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adiciona novo campo de aluno
    document.getElementById('adicionar-aluno').addEventListener('click', function() {
        const container = document.getElementById('alunos-container');
        const novoInput = document.createElement('div');
        novoInput.className = 'input-group mb-2 aluno-input';
        novoInput.innerHTML = `
            <input type="text" name="alunos[]" class="form-control" placeholder="Nome completo do aluno" required>
            <div class="input-group-append">
                <button class="btn btn-outline-danger remover-aluno" type="button">
                    <i class="fas fa-trash-alt"></i>
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
    document.getElementById('turmaForm').addEventListener('submit', function(e) {
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