@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gerenciamento de Usuários</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('users.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Novo Usuário
                </a>
                <a href="{{ route('users.create-lote') }}" class="btn btn-info ml-2">
                    <i class="fas fa-users"></i> Cadastrar em Lote
                </a>
                <a href="{{ route('users.pdf', [
                    'search' => request('search'),
                    'role' => request('role'),
                    'escola_id' => request('escola_id')
                ]) }}" class="btn btn-primary ml-2" title="Gerar relatório em PDF" style="min-width: 120px;">
                    <i class="fas fa-file-pdf mr-2"></i> Exportar PDF
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filtros</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('users.index') }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="search">Nome</label>
                                        <input type="text" id="search" name="search" class="form-control"
                                               placeholder="Pesquisar por nome" value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="role">Papel</label>
                                        <select id="role" name="role" class="form-control">
                                            <option value="">Todos</option>
                                            @foreach(['admin', 'professor', 'aluno', 'aee', 'inclusiva', 'coordenador', 'aplicador'] as $role)
                                                <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                                    {{ ucfirst($role) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="escola_id">Escola</label>
                                        <select id="escola_id" name="escola_id" class="form-control">
                                            <option value="">Todas</option>
                                            @foreach($escolas as $escola)
                                                <option value="{{ $escola->id }}" {{ request('escola_id') == $escola->id ? 'selected' : '' }}>
                                                    {{ $escola->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-filter"></i> Filtrar
                                    </button>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Limpar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Papel</th>
                                    <th>Escola(s)</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{
                                            $user->role == 'admin' ? 'danger' :
                                            ($user->role == 'coordenador' ? 'warning' :
                                            ($user->role == 'professor' ? 'primary' :
                                            ($user->role == 'aee' ? 'info' :
                                            ($user->role == 'inclusiva' ? 'success' :
                                            ($user->role == 'aplicador' ? 'secondary' : 
                                            ($user->role == 'aluno' ? 'dark' : 'light'))))))
                                        }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($user->role === 'aluno')
                                            {{-- Para alunos, mostra a escola através da turma --}}
                                            @if ($user->turma && $user->turma->escola)
                                                {{ $user->turma->escola->nome }}
                                            @else
                                                N/A
                                            @endif
                                        @else
                                            {{-- Para outros usuários, mostra através do relacionamento escolas --}}
                                            @if ($user->escolas->isNotEmpty())
                                                @if ($user->escolas->count() > 1)
                                                    <button type="button" class="btn btn-sm btn-outline-primary escolas-btn" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#escolasModal"
                                                            data-user-name="{{ $user->name }}"
                                                            data-escolas="{{ $user->escolas->pluck('nome')->join('<br>') }}">
                                                        {{ $user->escolas->count() }} escolas
                                                    </button>
                                                @else
                                                    {{ $user->escolas->first()->nome }}
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm">Detalhes</a>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-sm">Editar</a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir"
                                                    onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                                Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Nenhum usuário encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Escolas -->
<div class="modal fade" id="escolasModal" tabindex="-1" aria-labelledby="escolasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="escolasModalLabel">Escolas Vinculadas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 id="modalUserName" class="mb-3"></h6>
                <div id="modalEscolasList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Inicializa o modal de escolas
    const escolasModal = new bootstrap.Modal(document.getElementById('escolasModal'));
    
    $('.escolas-btn').click(function() {
        const userName = $(this).data('user-name');
        const escolas = $(this).data('escolas');
        
        $('#modalUserName').text('Usuário: ' + userName);
        $('#modalEscolasList').html(escolas);
    });
    
    // Inicializa tooltips
    $('[title]').tooltip();
});
</script>
@endsection