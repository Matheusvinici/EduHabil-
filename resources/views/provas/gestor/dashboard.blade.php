@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Painel do Gestor</h5>
                    <div>
                        <span class="badge bg-primary mr-2">
                            Escola atual: {{ $escola->nome }}
                        </span>
                        <a href="{{ route('gestor.selecionar-escola') }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-exchange-alt"></i> Mudar Escola
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p>Bem-vindo, {{ Auth::user()->name }}!</p>
                    <p>Você está gerenciando a escola <strong>{{ $escola->nome }}</strong>.</p>

                    <!-- Cards de Resumo -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <h3 class="card-title">{{ $escola->turmas_count }}</h3>
                                    <p class="card-text">Turmas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h3 class="card-title">{{ $escola->alunos_count }}</h3>
                                    <p class="card-text">Alunos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <h3 class="card-title">{{ $escola->professores_count }}</h3>
                                    <p class="card-text">Professores</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Últimas Atividades -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">Últimas Atividades</h5>
                            @if($ultimasAtividades->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                <th>Professor</th>
                                                <th>Disciplina</th>
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ultimasAtividades as $atividade)
                                            <tr>
                                                <td>{{ $atividade->created_at->format('d/m/Y') }}</td>
                                                <td>{{ $atividade->professor->name }}</td>
                                                <td>{{ $atividade->atividade->disciplina->nome }}</td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Nenhuma atividade registrada recentemente.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection