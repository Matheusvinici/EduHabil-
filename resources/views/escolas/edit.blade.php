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
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nome">Nome da Escola</label>
                        <input type="text" name="nome" class="form-control" value="{{ $escola->nome }}" placeholder="Digite o nome da escola" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="codigo_escola">Código INEP</label>
                        <input type="text" name="codigo_escola" class="form-control" value="{{ $escola->codigo_escola }}" placeholder="Digite o código INEP" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <!--<div class="col-md-8">
                    <div class="form-group">
                        <label for="endereco">Endereço</label>
                        <input type="text" name="endereco" class="form-control" value="{{ $escola->endereco }}" placeholder="Digite o endereço">
                    </div>
                </div>-->
                <!--<div class="col-md-4">
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" name="telefone" class="form-control" value="{{ $escola->telefone }}" placeholder="Digite o telefone">
                    </div>
                </div>-->
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Atualizar</button>
            </div>
        </form>
    </div>
</div>
@endsection
