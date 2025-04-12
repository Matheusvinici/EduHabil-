@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-user-tie mr-2"></i>
                    Detalhes do Usuário: <span class="text-primary">{{ $user->name }}</span>
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}"><i class="fas fa-users"></i> Usuários</a></li>
                    <li class="breadcrumb-item active">Detalhes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-1"></i>
                            Informações do Usuário
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Informações Pessoais -->
                            <div class="col-md-6">
                                <div class="info-box bg-light p-4 rounded">
                                    <h4 class="mb-3 text-primary">
                                        <i class="fas fa-user-circle mr-2"></i>
                                        Dados Pessoais
                                    </h4>
                                    <div class="user-details">
                                        <p class="detail-item">
                                            <i class="fas fa-user mr-2 text-muted"></i>
                                            <strong>Nome:</strong> {{ $user->name }}
                                        </p>
                                        <p class="detail-item">
                                            <i class="fas fa-envelope mr-2 text-muted"></i>
                                            <strong>Email:</strong> {{ $user->email }}
                                        </p>
                                        <p class="detail-item">
                                            <i class="fas fa-id-card mr-2 text-muted"></i>
                                            <strong>CPF:</strong> {{ $user->cpf ?? 'Não informado' }}
                                        </p>
                                        <p class="detail-item">
                                            <i class="fas fa-key mr-2 text-muted"></i>
                                            <strong>Código de Acesso:</strong> 
                                            <span class="badge bg-secondary">{{ $user->codigo_acesso ?? 'Não informado' }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Informações do Sistema -->
                            <div class="col-md-6">
                                <div class="info-box bg-light p-4 rounded">
                                    <h4 class="mb-3 text-primary">
                                        <i class="fas fa-cogs mr-2"></i>
                                        Dados do Sistema
                                    </h4>
                                    <div class="system-details">
                                        <p class="detail-item">
                                            <i class="fas fa-user-tag mr-2 text-muted"></i>
                                            <strong>Papel:</strong> 
                                            <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'coordenador' ? 'warning' : 'primary') }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </p>
                                        
                                        <p class="detail-item">
                                            <i class="fas fa-school mr-2 text-muted"></i>
                                            <strong>Escola(s):</strong>
                                            @if($user->escolas->isNotEmpty())
                                                <div class="mt-2">
                                                    @foreach($user->escolas as $escola)
                                                        <span class="d-block mb-1">
                                                            <i class="fas fa-check-circle text-success mr-1"></i>
                                                            {{ $escola->nome }}
                                                            <small class="text-muted ml-2">(Vinculado em: {{ $escola->pivot->created_at->format('d/m/Y') }})</small>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">Não vinculado</span>
                                            @endif
                                        </p>
                                        
                                        <p class="detail-item">
                                            <i class="fas fa-calendar-plus mr-2 text-muted"></i>
                                            <strong>Criado em:</strong> {{ $user->created_at->format('d/m/Y H:i') }}
                                        </p>
                                        <p class="detail-item">
                                            <i class="fas fa-calendar-check mr-2 text-muted"></i>
                                            <strong>Atualizado em:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ações -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                                            <i class="fas fa-edit mr-1"></i> Editar
                                        </a>
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary ml-2">
                                            <i class="fas fa-arrow-left mr-1"></i> Voltar
                                        </a>
                                    </div>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                            <i class="fas fa-trash-alt mr-1"></i> Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .info-box {
        border-left: 4px solid #007bff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        height: 100%;
    }
    
    .detail-item {
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 0;
    }
    
    .detail-item:last-child {
        border-bottom: none;
    }
    
    .card-primary.card-outline {
        border-top: 3px solid #007bff;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
</style>
@endsection