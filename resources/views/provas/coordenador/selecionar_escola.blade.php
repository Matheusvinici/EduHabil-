@extends('layouts.app')

@section('content')
<div class="container-fluid bg-soft-blue">
    <div class="row vh-100 justify-content-center align-items-center">
        <div class="col-lg-5 col-md-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-primary-blue py-3">
                    <h4 class="mb-0 text-center text-white">
                        <i class="bi bi-building me-2"></i>Selecionar Escola
                    </h4>
                </div>

                <div class="card-body p-4 bg-white-translucent">
                    <form method="POST" action="{{ route('definir.escola') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="escola_id" class="form-label fw-bold text-primary-blue">Escola</label>
                            <select id="escola_id" class="form-select form-select-lg py-2 border-primary-light" name="escola_id" required>
                                <option value="" selected disabled>Selecione uma escola</option>
                                @foreach($escolas as $escola)
                                    <option value="{{ $escola->id }}">{{ $escola->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary-blue btn-lg py-2 fw-bold text-white">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Acessar
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer bg-primary-ultralight py-3 text-center">
                    <small class="text-primary-blue">Sistema Educacional</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-blue: #2c6ecb;
        --primary-light: #a8c4f8;
        --primary-ultralight: #e8f0fe;
        --soft-blue: #f5f8ff;
    }
    
    .bg-soft-blue {
        background-color: var(--soft-blue);
    }
    
    .bg-primary-blue {
        background-color: var(--primary-blue);
    }
    
    .bg-primary-ultralight {
        background-color: var(--primary-ultralight);
    }
    
    .bg-white-translucent {
        background-color: rgba(255, 255, 255, 0.9);
    }
    
    .text-primary-blue {
        color: var(--primary-blue);
    }
    
    .border-primary-light {
        border: 1px solid var(--primary-light);
    }
    
    .btn-primary-blue {
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
        transition: all 0.3s ease;
    }
    
    .btn-primary-blue:hover {
        background-color: #2358a8;
        border-color: #2358a8;
        transform: translateY(-1px);
    }
    
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(44, 110, 203, 0.15) !important;
    }
    
    .form-select:focus, .btn:focus {
        box-shadow: 0 0 0 0.25rem rgba(44, 110, 203, 0.25);
    }
</style>
@endsection