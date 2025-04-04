@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Criar Característica</h1>
    <form action="{{ route('caracteristicas.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="deficiencia_id">Deficiência</label>
            <select name="deficiencia_id" id="deficiencia_id" class="form-control" required>
                @foreach ($deficiencias as $deficiencia)
                    <option value="{{ $deficiencia->id }}">{{ $deficiencia->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
</div>
@endsection