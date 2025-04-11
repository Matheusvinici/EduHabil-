@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
            <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Novo Critério de Avaliação</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('tutoria_criterios.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Categoria</label>
                        <input type="text" name="categoria" class="form-control" placeholder="Ex: Pedagógico, Estrutura" required>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Descrição</label>
                        <input type="text" name="descricao" class="form-control" placeholder="Ex: Qualidade da merenda" required>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <a href="{{ route('tutoria_criterios.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save me-1"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
