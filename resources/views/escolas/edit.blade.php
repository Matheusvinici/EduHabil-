@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Escola</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('escolas.update', $escola->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="nome">Nome da Escola</label>
                <input type="text" name="nome" class="form-control" value="{{ $escola->nome }}" required>
            </div>
            <div class="form-group">
                <label for="endereco">Endereço</label>
                <input type="text" name="endereco" class="form-control" value="{{ $escola->endereco }}">
            </div>
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" name="telefone" class="form-control" value="{{ $escola->telefone }}">
            </div>
            <div class="form-group">
                <label for="codigo_escola">Código da Escola</label>
                <input type="text" name="codigo_escola" class="form-control" value="{{ $escola->codigo_escola }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>
</div>
@endsection