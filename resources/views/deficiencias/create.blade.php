@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0"><i class="bi bi-wheelchair"></i> Registrar Nova Deficiência</h2>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('deficiencias.store') }}" method="POST">
                        @csrf
                        
                        <!-- Nome -->
                        <div class="form-group mb-4">
                            <label for="nome" class="form-label">Nome da Deficiência</label>
                            <input type="text" name="nome" id="nome" class="form-control" 
                                   placeholder="Ex: Deficiência Visual, Deficiência Auditiva" required>
                            @error('nome')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Descrição -->
                        <div class="form-group mb-4">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea name="descricao" id="descricao" class="form-control" rows="5"
                                      placeholder="Descreva as características principais desta deficiência...
Ex: A deficiência visual pode variar desde a cegueira total até a perda parcial da visão..."
                            ></textarea>
                            @error('descricao')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Botões de ação -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('deficiencias.index') }}" class="btn btn-secondary px-4 py-2">
                                <i class="bi bi-x-circle me-2"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-save me-2"></i> Salvar Deficiência
                            </button>
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
        margin-bottom: 0.5rem;
    }
    
    .form-control {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        padding: 0.5rem 0.75rem;
    }
    
    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    textarea.form-control {
        min-height: 120px;
    }
    
    .card {
        border: none;
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
        padding: 1rem 1.5rem;
    }
    
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .btn {
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    .btn-secondary:hover {
        background-color: #5c636a;
        border-color: #565e64;
    }
</style>
@endsection