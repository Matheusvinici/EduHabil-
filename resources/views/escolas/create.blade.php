@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cadastrar Nova Escola</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('escolas.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nome">Nome da Escola</label>
                        <input type="text" name="nome" class="form-control" placeholder="Digite o nome da escola" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="codigo_escola">Código INEP</label>
                        <input type="text" name="codigo_escola" class="form-control" placeholder="Digite o código INEP" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="endereco">Endereço</label>
                        <input type="text" name="endereco" class="form-control" placeholder="Digite o endereço">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" name="telefone" class="form-control" placeholder="Digite o telefone">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection