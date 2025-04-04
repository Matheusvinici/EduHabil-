@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-chart-bar"></i> Detalhes do Simulado: {{ $simulado->nome }}
            </h5>
        </div>
        <div class="card-body">
            <!-- Dados do Aluno -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Aluno:</strong> {{ $aluno->name }}</p>
                    <p><strong>Data:</strong> {{ $data_aplicacao->format('d/m/Y H:i') }}</p>
                    <p><strong>Raça/Cor:</strong> {{ $raca }}</p>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <h5 class="mb-1">Desempenho</h5>
                        <p class="mb-1"><strong>Acertos:</strong> {{ $acertos }} / {{ $totalQuestoes }}</p>
                        <p class="mb-1"><strong>Porcentagem:</strong> {{ $porcentagem }}%</p>
                        <div class="progress mt-2" style="height: 20px;">
                            <div class="progress-bar 
                                {{ $porcentagem >= 70 ? 'bg-success' : 
                                   ($porcentagem >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                role="progressbar" 
                                style="width: {{ $porcentagem }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalhes por Pergunta -->
            <h5 class="mb-3"><i class="fas fa-list-ol"></i> Respostas</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Questão</th>
                            <th>Resposta do Aluno</th>
                            <th>Resposta Correta</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detalhesPerguntas as $pergunta)
                        <tr>
                            <td>{{ $pergunta['enunciado'] }}</td>
                            <td>{{ $pergunta['resposta_aluno'] }}</td>
                            <td>{{ $pergunta['alternativa_correta'] }}</td>
                            <td>
                                @if($pergunta['correta'])
                                    <span class="badge badge-success">Acertou</span>
                                @else
                                    <span class="badge badge-danger">Errou</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection