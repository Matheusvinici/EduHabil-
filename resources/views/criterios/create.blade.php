@extends('layouts.app')

@section('content')
    <h1>Adicionar Critério</h1>
    <form action="{{ route('criterios.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Categoria</label>
            <input type="text" name="categoria" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Descrição</label>
            <input type="text" name="descricao" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
@endsection