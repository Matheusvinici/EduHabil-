@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">
                <i class="fas fa-link mr-2"></i>
                Vincular Professor à Turma - {{ $escola->nome }}
            </h2>
        </div>
        
        <div class="card-body">
            {{-- MENSAGENS --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-times-circle mr-2"></i>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
                        
                        {{-- RESUMO DAS VINCULAÇÕES EXISTENTES --}}
            @if(count($vinculacoesExistentes) > 0)
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle mr-2"></i>Vinculações existentes nesta escola:</h5>
                    <ul class="mb-0">
                        @foreach($vinculacoesExistentes as $vinculacao)
                            <li>
                                <strong>{{ $vinculacao['professor'] }}</strong> 
                                na turma <strong>{{ $vinculacao['turma'] }}</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Nenhum professor vinculado a turmas nesta escola ainda.
                            </div>
                        @endif
                        {{-- FORMULÁRIO --}}
            @if(isset($professoresDisponiveis) && isset($turmasDisponiveis) && $professoresDisponiveis->isNotEmpty() && $turmasDisponiveis->isNotEmpty())
                <form action="{{ route('professor-turma.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="escola_id" value="{{ $escola->id }}">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Professores Disponíveis:</label>
                                <select name="professor_id" class="form-control select2" required>
                                    <option value="">Selecione um professor</option>
                                    @foreach($professoresDisponiveis as $professor)
                                        <option value="{{ $professor->id }}">{{ $professor->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    {{ $professoresDisponiveis->count() }} professores disponíveis
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Turmas Disponíveis:</label>
                                <div class="turmas-container" style="max-height: 300px; overflow-y: auto;">
                                    @foreach($turmasDisponiveis as $turma)
                                        <div class="form-check mb-2">
                                            <input type="checkbox" 
                                                name="turmas[]" 
                                                value="{{ $turma->id }}"
                                                id="turma_{{ $turma->id }}"
                                                class="form-check-input">
                                            <label for="turma_{{ $turma->id }}" class="form-check-label">
                                                {{ $turma->nome_turma }}
                                                <small class="text-muted">({{ $turma->serie }})</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">
                                    {{ $turmasDisponiveis->count() }} turmas disponíveis
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Vincular
                        </button>
                        <a href="{{ route('professor-turma.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i> Voltar
                        </a>
                    </div>
                </form>
            @else
                <div class="alert alert-success">
                    <i class="fas fa-check-circle mr-2"></i>
                    @if(!isset($professoresDisponiveis) || $professoresDisponiveis->isEmpty() && (!isset($turmasDisponiveis) || $turmasDisponiveis->isEmpty()))
                        Todas as turmas já possuem professores vinculados e todos os professores já estão alocados!
                    @elseif(!isset($professoresDisponiveis) || $professoresDisponiveis->isEmpty())
                        Todos os professores desta escola já estão vinculados a turmas!
                    @else
                        Todas as turmas desta escola já possuem professores vinculados!
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .turmas-container {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 10px;
    }
    .form-check-label {
        margin-left: 5px;
    }
    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Selecione um professor",
        allowClear: true
    });
});
</script>
@endsection