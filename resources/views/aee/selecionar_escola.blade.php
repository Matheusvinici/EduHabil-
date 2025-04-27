@extends('layouts.app')

@section('content')
<div class="container-fluid bg-soft-green">
    <div class="row vh-100 justify-content-center align-items-center">
        <div class="col-lg-5 col-md-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-primary-green py-3">
                    <h4 class="mb-0 text-center text-white">
                        <i class="bi bi-building me-2"></i>Selecionar Escola - AEE
                    </h4>
                </div>

                <div class="card-body p-4 bg-white-translucent">
                    <form method="POST" action="{{ route('aee.definir.escola') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="escola_id" class="form-label fw-bold text-primary-green">Escola</label>
                            <select id="escola_id" class="form-select form-select-lg py-2 border-primary-light" name="escola_id" required>
                                <option value="" selected disabled>Selecione uma escola</option>
                                @foreach($escolas as $escola)
                                    <option value="{{ $escola->id }}">{{ $escola->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary-green btn-lg py-2 fw-bold text-white">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Acessar
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer bg-primary-ultralight py-3 text-center">
                    <small class="text-primary-green">Sistema Educacional - AEE</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-green: #28a745;
        --primary-light: #a8d8b9;
        --primary-ultralight: #e8f5eb;
        --soft-green: #f5fff8;
    }
    
    .bg-soft-green {
        background-color: var(--soft-green);
    }
    
    .bg-primary-green {
        background-color: var(--primary-green);
    }
    
    .bg-primary-ultralight {
        background-color: var(--primary-ultralight);
    }
    
    .bg-white-translucent {
        background-color: rgba(255, 255, 255, 0.9);
    }
    
    .text-primary-green {
        color: var(--primary-green);
    }
    
    .border-primary-light {
        border: 1px solid var(--primary-light);
    }
    
    .btn-primary-green {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
        transition: all 0.3s ease;
    }
    
    .btn-primary-green:hover {
        background-color: #218838;
        border-color: #218838;
        transform: translateY(-1px);
    }
    
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(40, 167, 69, 0.15) !important;
    }
    
    .form-select:focus, .btn:focus {
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }
</style>
@endsection