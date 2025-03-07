@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Criar Nova Disciplina</h1>

    <form action="{{ route('disciplinas.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nome">Nome da Disciplina</label>
            <input type="text" name="nome" id="nome" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success mt-3">Salvar</button>
    </form>
</div>
@endsection
