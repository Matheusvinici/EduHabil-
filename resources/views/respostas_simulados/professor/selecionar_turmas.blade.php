@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Selecione suas turmas</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('professor.selecionar_turmas') }}">
                @csrf
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Selecione uma ou mais turmas que você deseja acompanhar.
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Turmas disponíveis:</label>
                    
                    <div class="row">
                        @foreach($turmas as $turma)
                        <div class="col-md-4 mb-3">
                            <div class="form-check card p-3">
                                <input class="form-check-input" type="checkbox" 
                                       name="turmas[]" value="{{ $turma->id }}"
                                       id="turma_{{ $turma->id }}"
                                       @if(in_array($turma->id, $turmasSelecionadas)) checked @endif>
                                <label class="form-check-label" for="turma_{{ $turma->id }}">
                                    <strong>{{ $turma->nome_turma }}</strong><br>
                                    <small class="text-muted">
                                        Código: {{ $turma->codigo_turma }} | 
                                        Alunos: {{ $turma->quantidade_alunos }}
                                    </small>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Seleção
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection