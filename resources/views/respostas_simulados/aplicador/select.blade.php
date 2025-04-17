@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-chalkboard-teacher"></i> Aplicação de Simulados
                </h3>
                <div class="badge bg-light text-primary">
                    <i class="fas fa-school"></i> 
                    {{ $escolaSelecionada->nome ?? 'Selecione uma escola' }}
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Seletor de Escola -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('respostas_simulados.aplicador.select_escola') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-floating">
                                    <select class="form-select" name="escola_id" id="escola_id" required>
                                        <option value="">Selecione uma escola</option>
                                        @foreach($escolas as $escola)
                                            <option value="{{ $escola->id }}" 
                                                {{ $escolaSelecionada && $escolaSelecionada->id == $escola->id ? 'selected' : '' }}>
                                                {{ $escola->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="escola_id">Escola</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary h-100 w-100">
                                    <i class="fas fa-check"></i> Selecionar Escola
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(!$escolaSelecionada)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Selecione uma escola para visualizar os simulados disponíveis.
                </div>
            @else
                @if($simulados->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Nenhum simulado disponível para aplicação.
                    </div>
                @else
                    <div class="row">
                        @foreach($simulados as $simulado)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-start border-primary border-4">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">{{ $simulado->nome }}</h5>
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <span class="badge bg-info">
                                            <i class="fas fa-question-circle"></i> {{ $simulado->perguntas_count }} questões
                                        </span>
                                        @if($simulado->tempo_limite)
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock"></i> {{ $simulado->tempo_limite }} min
                                        </span>
                                        @endif
                                    </div>
                                    <p class="card-text text-muted">{{ Str::limit($simulado->descricao, 100) }}</p>
                                </div>
                                <div class="card-footer bg-transparent d-flex justify-content-between">
                                    <a href="{{ route('respostas_simulados.aplicador.alunos_pendentes', $simulado) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-list-check"></i> Alunos
                                    </a>
                                    <div class="btn-group">
                                        <a href="{{ route('respostas_simulados.aplicador.create', $simulado) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-pencil-alt"></i> Manual
                                        </a>
                                       <!-- Substitua o link da câmera por: -->
                                       <a href="{{ route('respostas_simulados.aplicador.camera', $simulado) }}" 
                                        class="btn btn-success btn-sm">
                                            <i class="fas fa-camera"></i> Ler Gabarito
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Paginação -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $simulados->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection