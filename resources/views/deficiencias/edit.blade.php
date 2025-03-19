@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Deficiência</h1>
    <form action="{{ route('deficiencias.update', $deficiencia->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control" value="{{ $deficiencia->nome }}" required>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" class="form-control">{{ $deficiencia->descricao }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Atualizar</button>
    </form>
</div>
@endsection