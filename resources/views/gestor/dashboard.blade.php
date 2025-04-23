@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0">Painel do Gestor</h5>
                    <div>
                        @if ($escola)
                            <span class="badge bg-light text-primary mr-2">
                                <i class="fas fa-school"></i> {{ $escola->nome }}
                            </span>
                        @else
                            <span class="badge bg-warning mr-2">
                                Nenhuma escola selecionada
                            </span>
                        @endif
                        <a href="{{ route('gestor.selecionar.escola') }}"
                           class="btn btn-sm btn-light">
                            <i class="fas fa-exchange-alt"></i> Trocar Escola
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="lead">Bem-vindo, <strong>{{ Auth::user()->name }}</strong>!</p>
                    
                    @if ($escola)
                        <div class="alert alert-primary">
                            <i class="fas fa-info-circle"></i> Você está gerenciando a escola <strong>{{ $escola->nome }}</strong>
                        </div>

                        <!-- Resumo Estatístico -->
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <h3 class="text-primary">{{ $escola->turmas_count }}</h3>
                                        <p class="mb-0 text-muted">Turmas Ativas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                        <h3 class="text-success">{{ $escola->alunos_count }}</h3>
                                        <p class="mb-0 text-muted">Alunos</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-info h-100">
                                    <div class="card-body text-center">
                                        <h3 class="text-info">{{ $escola->professores_count }}</h3>
                                        <p class="mb-0 text-muted">Professores</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-warning h-100">
                                    <div class="card-body text-center">
                                        <h3 class="text-warning">{{ $atividades_count ?? 0 }}</h3>
                                        <p class="mb-0 text-muted">Atividades</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Professores -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-chalkboard-teacher"></i> Corpo Docente</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $professores = \App\Models\User::where('role', 'professor')
                                                        ->whereHas('escolas', function ($query) use ($escola) {
                                                            $query->where('escolas.id', $escola->id);
                                                        })
                                                        ->withCount(['turmasLecionadas' => function($q) use ($escola) {
                                                            $q->where('escola_id', $escola->id);
                                                        }])
                                                        ->get();
                                @endphp

                                @if($professores->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Professor</th>
                                                    <th class="text-center">Turmas</th>
                                                    <th>Contato</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($professores as $professor)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $professor->name }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary rounded-pill">
                                                            {{ $professor->turmas_lecionadas_count }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small>{{ $professor->email }}</small>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        Nenhum professor cadastrado nesta escola.
                                    </div>
                                @endif
                            </div>
                        </div>

                    

                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-exclamation-circle"></i> Por favor, selecione uma escola para visualizar o painel de gestão.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 0.5rem;
    }
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    .table th {
        border-top: none;
    }
</style>
@endsection