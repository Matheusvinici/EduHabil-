@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>Detalhes do Simulado: {{ $simulado->nome }}
                </h5>
                <a href="{{ route('respostas_simulados.aplicador.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-light-blue border-0">
                        <div class="card-body text-center">
                            <h6 class="text-primary">Aluno</h6>
                            <h4>{{ $aluno->name }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light-blue border-0">
                        <div class="card-body text-center">
                            <h6 class="text-primary">Escola</h6>
                            <h4>{{ $escola->nome }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light-blue border-0">
                        <div class="card-body text-center">
                            <h6 class="text-primary">Data</h6>
                            <h4>{{ $data->format('d/m/Y H:i') }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">% Acertos</h6>
                            <h2 class="{{ $porcentagem >= 70 ? 'text-success' : ($porcentagem >= 50 ? 'text-warning' : 'text-danger') }}">
                                {{ $porcentagem }}%
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Média (Peso)</h6>
                            <h2 class="text-primary">{{ $media }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Nota TRI</h6>
                            <h2 class="text-primary">{{ $triScore }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Questão</th>
                            <th>Resposta</th>
                            <th>Correta</th>
                            <th>Alternativa Marcada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($respostas as $resposta)
                        <tr>
                            <td>{{ $resposta->pergunta->enunciado }}</td>
                            <td>{{ $resposta->pergunta->resposta_correta }}</td>
                            <td>
                                @if($resposta->correta)
                                    <span class="badge bg-success">Correta</span>
                                @else
                                    <span class="badge bg-danger">Incorreta</span>
                                @endif
                            </td>
                            <td>{{ $resposta->resposta }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-blue {
        background-color: #e7f5ff;
    }
    .card {
        border-radius: 10px;
    }
</style>
@endsection