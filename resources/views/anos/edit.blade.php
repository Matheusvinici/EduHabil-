@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0"><i class="bi bi-pencil-square"></i> Editar Ano Escolar</h2>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('anos.update', $ano->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-4">
                            <label for="nome" class="form-label">Nome do Ano Escolar</label>
                            <input type="text" name="nome" id="nome" class="form-control" 
                                   value="{{ old('nome', $ano->nome) }}" required>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-save me-2"></i> Atualizar
                            </button>
                            <a href="{{ route('anos.index') }}" class="btn btn-secondary px-4 py-2">
                                <i class="bi bi-x-circle me-2"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-label {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .form-control {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .card {
        border: none;
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
</style>
@endsection