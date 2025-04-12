@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ __('Editar Usuário') }}: {{ $user->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
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
                        <h3 class="card-title">Editar dados do usuário</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.update', $user->id) }}" method="POST" autocomplete="off">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nome Completo *</label>
                                        <input type="text" name="name" id="name" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email *</label>
                                        <input type="email" name="email" id="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role">Papel *</label>
                                        <select name="role" id="role" 
                                                class="form-control @error('role') is-invalid @enderror" required>
                                            <option value="">Selecione um papel</option>
                                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                                            <option value="professor" {{ old('role', $user->role) == 'professor' ? 'selected' : '' }}>Professor</option>
                                            <option value="aee" {{ old('role', $user->role) == 'aee' ? 'selected' : '' }}>Professor do AEE</option>
                                            <option value="inclusiva" {{ old('role', $user->role) == 'inclusiva' ? 'selected' : '' }}>Diretoria Inclusiva</option>
                                            <option value="coordenador" {{ old('role', $user->role) == 'coordenador' ? 'selected' : '' }}>Coordenador</option>
                                            <option value="gestor" {{ old('role', $user->role) == 'gestor' ? 'selected' : '' }}>Gestor</option>
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group" id="escolas-field" style="display: none;">
                                        <label for="escolas">Escolas Vinculadas *</label>
                                        <select name="escolas[]" id="escolas" 
                                                class="form-control select2 @error('escolas') is-invalid @enderror" multiple>
                                            @foreach($escolas as $escola)
                                                <option value="{{ $escola->id }}" 
                                                    {{ in_array($escola->id, old('escolas', $user->escolas->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                    {{ $escola->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('escolas')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="change_password" name="change_password">
                                    <label class="form-check-label" for="change_password">
                                        Alterar senha?
                                    </label>
                                </div>
                            </div>

                            <div id="password_fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Nova Senha</label>
                                            <input type="password" name="password" id="password" 
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   autocomplete="new-password">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">Confirmar Senha</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                                   class="form-control" autocomplete="new-password">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Atualizar Usuário
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--multiple {
    border: 1px solid #ced4da;
    padding: .375rem .75rem;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializa Select2 para múltiplas escolas
    $('.select2').select2({
        placeholder: "Selecione as escolas",
        allowClear: true,
        width: '100%'
    });

    // Mostra/oculta o campo escolas conforme o papel selecionado
    function toggleEscolasField() {
        const role = $('#role').val();
        const escolasField = $('#escolas-field');
        
        if (['professor', 'aee', 'coordenador', 'gestor'].includes(role)) {
            escolasField.show();
            $('#escolas').prop('required', true);
        } else {
            escolasField.hide();
            $('#escolas').prop('required', false);
        }
    }

    // Mostra/oculta campos de senha
    $('#change_password').change(function() {
        $('#password_fields').toggle(this.checked);
        $('#password').prop('required', this.checked);
        $('#password_confirmation').prop('required', this.checked);
    });

    // Executa ao carregar a página
    toggleEscolasField();
    $('#role').change(toggleEscolasField);

    // Verifica se deve mostrar a escola ao carregar (para edição)
    @if(in_array($user->role, ['professor', 'aee', 'coordenador', 'gestor']))
        $('#escolas-field').show();
    @endif
});
</script>
@endsection