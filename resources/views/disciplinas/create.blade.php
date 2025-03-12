@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Criar Nova Disciplina</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('disciplinas.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nome">Nome da Disciplina</label>
                    <input type="text" name="nome" id="nome" class="form-control" placeholder="Digite o nome da disciplina" required>
                </div>
                <div class="form-group text-right"> <!-- Alinhar o botão à direita -->
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection