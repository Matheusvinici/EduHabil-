@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">
                <i class="fas fa-check-circle"></i> Correção do Gabarito - {{ $simulado->nome }}
            </h3>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Informações do Aluno</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>Aluno:</strong> {{ $aluno->name }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Turma:</strong> {{ $dados['turma_id'] }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Raça/Cor:</strong> {{ $dados['raca'] }}
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Gabarito Capturado</h5>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ Storage::url($imagePath) }}" alt="Gabarito capturado" class="img-fluid border rounded">
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Respostas Identificadas</h5>
                            <span class="badge bg-primary">
                                {{ count($respostas) }} / {{ count($simulado->perguntas) }} questões
                            </span>
                        </div>
                        
                        <div class="card-body">
                            <form method="POST" action="{{ route('gabarito.salvar', $simulado) }}">
                                @csrf
                                <input type="hidden" name="aluno_id" value="{{ $dados['aluno_id'] }}">
                                <input type="hidden" name="turma_id" value="{{ $dados['turma_id'] }}">
                                <input type="hidden" name="raca" value="{{ $dados['raca'] }}">
                                <input type="hidden" name="imagePath" value="{{ $imagePath }}">
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Questão</th>
                                                <th>Resposta Identificada</th>
                                                <th>Corrigir</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($simulado->perguntas as $pergunta)
                                            <tr>
                                                <td>{{ $pergunta->id }}</td>
                                                <td>
                                                    @if(isset($respostas[$pergunta->id]))
                                                        <span class="badge bg-{{ $respostas[$pergunta->id] == $pergunta->resposta_correta ? 'success' : 'danger' }}">
                                                            {{ $respostas[$pergunta->id] }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">Não identificada</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <select name="respostas[{{ $pergunta->id }}]" class="form-select form-select-sm">
                                                        <option value="A" {{ isset($respostas[$pergunta->id]) && $respostas[$pergunta->id] == 'A' ? 'selected' : '' }}>A</option>
                                                        <option value="B" {{ isset($respostas[$pergunta->id]) && $respostas[$pergunta->id] == 'B' ? 'selected' : '' }}>B</option>
                                                        <option value="C" {{ isset($respostas[$pergunta->id]) && $respostas[$pergunta->id] == 'C' ? 'selected' : '' }}>C</option>
                                                        <option value="D" {{ isset($respostas[$pergunta->id]) && $respostas[$pergunta->id] == 'D' ? 'selected' : '' }}>D</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-grid mt-3">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-save me-2"></i> Salvar Correção
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection