@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cadastrar Turma</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('turmas.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nome_turma">Nome da Turma</label>
                <input type="text" name="nome_turma" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="quantidade_alunos">Quantidade de Alunos</label>
                <input type="number" name="quantidade_alunos" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
</div>
@endsection