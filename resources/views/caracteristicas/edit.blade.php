@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Característica</h1>
    <form action="{{ route('caracteristicas.update', $caracteristica->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="deficiencia_id">Deficiência</label>
            <select name="deficiencia_id" id="deficiencia_id" class="form-control" required>
                @foreach ($deficiencias as $deficiencia)
                    <option value="{{ $deficiencia->id }}" {{ $deficiencia->id == $caracteristica->deficiencia_id ? 'selected' : '' }}>
                        {{ $deficiencia->nome }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control" value="{{ $caracteristica->nome }}" required>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" class="form-control">{{ $caracteristica->descricao }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Atualizar</button>
    </form>
</div>
@endsection