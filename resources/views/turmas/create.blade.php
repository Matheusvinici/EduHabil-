@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cadastrar Turma</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('turmas.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nome_turma">Nome da Turma</label>
                        <input type="text" name="nome_turma" class="form-control" placeholder="Digite o nome da turma" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="quantidade_alunos">Quantidade de Alunos</label>
                        <input type="number" name="quantidade_alunos" class="form-control" placeholder="Digite a quantidade de alunos" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Cadastrar</button>
            </div>
        </form>
    </div>
</div>
@endsection