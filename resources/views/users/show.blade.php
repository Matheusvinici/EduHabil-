@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Detalhes do Usuário: {{ $user->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
                    <li class="breadcrumb-item active">Detalhes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4><i class="fas fa-user"></i> Informações Pessoais</h4>
                                <hr>
                                <p><strong>Nome:</strong> {{ $user->name }}</p>
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                <p><strong>CPF:</strong> {{ $user->cpf ?? 'Não informado' }}</p>
                                <p><strong>Código de Acesso:</strong> {{ $user->codigo_acesso ?? 'Não informado' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4><i class="fas fa-id-card"></i> Informações do Sistema</h4>
                                <hr>
                                <p>
                                    <strong>Papel:</strong> 
                                    <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'coordenador' ? 'warning' : 'primary') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </p>
                                <p><strong>Escola:</strong> {{ $user->escola->nome ?? 'Não vinculado' }}</p>
                                <p><strong>Criado em:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Atualizado em:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                    <i class="fas fa-trash"></i> Excluir
                                </button>
                            </form>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary float-right">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection