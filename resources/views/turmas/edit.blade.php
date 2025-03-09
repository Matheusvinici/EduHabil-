@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Turma</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('turmas.update', $turma->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="nome">Nome da Turma</label>
                <input type="text" name="nome" class="form-control" value="{{ $turma->nome }}" required>
            </div>
            <div class="form-group">
                <label for="quantidade_alunos">Quantidade de Alunos</label>
                <input type="number" name="quantidade_alunos" class="form-control" value="{{ $turma->quantidade_alunos }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>
</div>
@endsection