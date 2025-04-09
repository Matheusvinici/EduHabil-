@extends('layouts.app')

@section('content')
    <h1>Editar Critério</h1>
    <form action="{{ route('criterios.update', $criterio->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Categoria</label>
            <input type="text" name="categoria" class="form-control" value="{{ $criterio->categoria }}" required>
        </div>
        <div class="form-group">
            <label>Descrição</label>
            <input type="text" name="descricao" class="form-control" value="{{ $criterio->descricao }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Atualizar</button>
    </form>
@endsection