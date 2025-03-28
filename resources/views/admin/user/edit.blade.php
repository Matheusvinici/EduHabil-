@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Editar Usuário</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.user.update', $user->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="name">Nome</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Nova Senha (deixe em branco para manter a atual)</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>

                            <div class="form-group">
                                <label for="role">Papel</label>
                                <select class="form-control" id="role" name="role" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="codigo_acesso">Código de Acesso</label>
                                <input type="text" class="form-control" id="codigo_acesso" name="codigo_acesso" value="{{ old('codigo_acesso', $user->codigo_acesso) }}">
                            </div>

                            <div class="form-group">
                                <label for="escola_id">Escola</label>
                                <select class="form-control" id="escola_id" name="escola_id">
                                    <option value="">Selecione uma escola</option>
                                    @foreach($escolas as $escola)
                                        <option value="{{ $escola->id }}" {{ old('escola_id', $user->escola_id) == $escola->id ? 'selected' : '' }}>{{ $escola->nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Atualizar Usuário</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Mostra/oculta o campo escola_id baseado no papel selecionado
    document.getElementById('role').addEventListener('change', function() {
        const escolaField = document.getElementById('escola_id');
        if (['coordenador', 'professor'].includes(this.value)) {
            escolaField.setAttribute('required', 'required');
        } else {
            escolaField.removeAttribute('required');
        }
    });
</script>
@endsection