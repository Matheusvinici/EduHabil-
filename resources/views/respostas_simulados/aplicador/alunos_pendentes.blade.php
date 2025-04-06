@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0">
                        <i class="fas fa-users"></i> Alunos - {{ $simulado->nome }}
                    </h3>
                    <small class="text-white-50">Escola: {{ Auth::user()->escola->nome }}</small>
                </div>
                <a href="{{ route('respostas_simulados.aplicador.create', $simulado) }}" 
                   class="btn btn-light">
                    <i class="fas fa-plus-circle"></i> Novo
                </a>
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs mb-4" id="alunosTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pendentes-tab" data-bs-toggle="tab" 
                            data-bs-target="#pendentes" type="button" role="tab">
                        <i class="fas fa-clock"></i> Pendentes ({{ $alunosPendentes->total() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="respondidos-tab" data-bs-toggle="tab" 
                            data-bs-target="#respondidos" type="button" role="tab">
                        <i class="fas fa-check-circle"></i> Respondidos ({{ $alunosRespondidos->total() }})
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="alunosTabsContent">
                <div class="tab-pane fade show active" id="pendentes" role="tabpanel">
                    @if($alunosPendentes->isEmpty())
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle"></i> Todos os alunos já responderam este simulado!
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Aluno</th>
                                        <th>Turma</th>
                                        <th width="120">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alunosPendentes as $aluno)
                                    <tr>
                                        <td>{{ $aluno['nome'] }}</td>
                                        <td>{{ $aluno['turma'] }}</td>
                                        <td>
                                            <a href="{{ route('respostas_simulados.aplicador.create_aluno', [
                                                'simulado' => $simulado->id,
                                                'aluno_id' => $aluno['id']
                                            ]) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Aplicar
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginação -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $alunosPendentes->links() }}
                        </div>
                    @endif
                </div>

                <div class="tab-pane fade" id="respondidos" role="tabpanel">
                    @if($alunosRespondidos->isEmpty())
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Nenhum aluno respondeu este simulado ainda.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Aluno</th>
                                        <th>Turma</th>
                                        <th>Data</th>
                                        <th width="120">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alunosRespondidos as $aluno)
                                    <tr>
                                        <td>{{ $aluno['nome'] }}</td>
                                        <td>{{ $aluno['turma'] }}</td>
                                        <td>{{ $aluno['data'] }}</td>
                                        <td>
                                            <a href="{{ route('respostas_simulados.aplicador.show', [
                                                'simulado' => $simulado->id,
                                                'aluno_id' => $aluno['id']
                                            ]) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginação -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $alunosRespondidos->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection