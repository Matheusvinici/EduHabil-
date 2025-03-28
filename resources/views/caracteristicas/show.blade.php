@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="h4 mb-0"><i class="bi bi-tag-fill"></i> Detalhes da Característica</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID:</strong> {{ $caracteristica->id }}</p>
                    <p><strong>Nome:</strong> {{ $caracteristica->nome }}</p>
                    <p><strong>Deficiência:</strong> {{ $caracteristica->deficiencia->nome }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Descrição:</strong></p>
                    <div class="border p-3 rounded bg-light">
                        {{ $caracteristica->descricao ?? 'Nenhuma descrição fornecida' }}
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('caracteristicas.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
                <div class="d-flex gap-2">
                    <a href="{{ route('caracteristicas.edit', $caracteristica->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <form action="{{ route('caracteristicas.destroy', $caracteristica->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Tem certeza que deseja excluir esta característica?')">
                            <i class="bi bi-trash"></i> Excluir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection