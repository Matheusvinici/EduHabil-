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
                <div class="col-md-6">
                    <!-- Formulário para gerar códigos adicionais -->
                    <form action="{{ route('turmas.gerar-codigos-adicionais', $turma->id) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="form-group">
                            <label for="quantidade_adicionais"><strong>Quantidade de Códigos Adicionais:</strong></label>
                            <input type="number" name="quantidade_adicionais" class="form-control" min="1" required>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus-circle"></i> Gerar Códigos
                        </button>
                    </form>
                </div>
            </div>

            <!-- Lista de alunos da turma -->
            <h4 class="mb-3">Alunos da Turma</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
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
                                <td>{{ $aluno->codigo_acesso }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection