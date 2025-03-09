@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cadastrar Nova Escola</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('escolas.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nome">Nome da Escola</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="endereco">Endereço</label>
                <input type="text" name="endereco" class="form-control">
            </div>
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" name="telefone" class="form-control">
            </div>
            <div class="form-group">
                <label for="codigo_escola">Código da Escola</label>
                <input type="text" name="codigo_escola" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
</div>
@endsection