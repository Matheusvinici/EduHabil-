@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Editar Respostas: {{ $simulado->nome }}
                </h5>
                <a href="{{ route('respostas_simulados.aplicador.show', [$simulado->id, $aluno->id]) }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Editando respostas do aluno <strong>{{ $aluno->name }}</strong>
            </div>

            <form method="POST" action="{{ route('respostas_simulados.aplicador.update', [$simulado->id, $aluno->id]) }}">
                @csrf
                @method('PUT')
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Questão</th>
                                <th>Alternativas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($respostas as $resposta)
                            <tr>
                                <td width="60%">
                                    <p class="fw-bold mb-1">Questão {{ $loop->iteration }}</p>
                                    <p class="mb-0">{{ $resposta->pergunta->enunciado }}</p>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @foreach($alternativas as $letra)
                                        <input type="radio" 
                                               class="btn-check" 
                                               name="respostas[{{ $resposta->pergunta_id }}]" 
                                               id="q{{ $resposta->pergunta_id }}{{ $letra }}" 
                                               value="{{ $letra }}"
                                               {{ $resposta->resposta == $letra ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary" for="q{{ $resposta->pergunta_id }}{{ $letra }}">
                                            {{ $letra }}
                                        </label>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg py-3">
                        <i class="fas fa-save me-2"></i> Salvar Alterações
                    </button>
                    <a href="{{ route('respostas_simulados.aplicador.show', [$simulado->id, $aluno->id]) }}" 
                       class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-group .btn {
        min-width: 40px;
    }
    .btn-check:checked + .btn {
        background-color: #0d6efd;
        color: white;
    }
</style>
@endsection