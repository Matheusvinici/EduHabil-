@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Editar Critério</h4>
        <a href="{{ route('tutoria_criterios.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Há alguns problemas com os dados informados:<br><br>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow rounded-4">
        <div class="card-body">
            <form action="{{ route('tutoria_criterios.update', $tutoria_criterio->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoria</label>
                    <input type="text" name="categoria" id="categoria" class="form-control" value="{{ old('categoria', $tutoria_criterio->categoria) }}" required>
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <input type="text" name="descricao" id="descricao" class="form-control" value="{{ old('descricao', $tutoria_criterio->descricao) }}" required>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Atualizar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
