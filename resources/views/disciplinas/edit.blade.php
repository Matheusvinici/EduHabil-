@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Editar Disciplina</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('disciplinas.update', $disciplina->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="nome">Nome da Disciplina</label>
                    <input type="text" name="nome" id="nome" class="form-control" value="{{ $disciplina->nome }}" placeholder="Digite o nome da disciplina" required>
                </div>
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection