@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ __('Criar Novo Usuário') }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
                    <li class="breadcrumb-item active">Criar</li>
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
                        <h3 class="card-title">Preencha os dados do usuário</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.store') }}" method="POST" autocomplete="off">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nome Completo *</label>
                                        <input type="text" name="name" id="name" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email *</label>
                                        <input type="email" name="email" id="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               value="{{ old('email') }}" required>
                                        <small id="email-feedback" class="text-warning d-none"></small>
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
                                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                            <option value="aplicador" {{ old('role') == 'aplicador' ? 'selected' : '' }}>Aplicador</option>
                                            <option value="inclusiva" {{ old('role') == 'inclusiva' ? 'selected' : '' }}>Diretoria Inclusiva</option>
                                            <option value="tutor" {{ old('role') == 'tutor' ? 'selected' : '' }}>Tutor Escolar</option>

                                            <option value="professor" {{ old('role') == 'professor' ? 'selected' : '' }}>Professor(a)</option>
                                            <option value="aee" {{ old('role') == 'aee' ? 'selected' : '' }}>Professor(a) do AEE</option>
                                            <option value="coordenador" {{ old('role') == 'coordenador' ? 'selected' : '' }}>Coordenador(a) Pedagógico</option>
                                            <option value="gestor" {{ old('role') == 'gestor' ? 'selected' : '' }}>Gestor(a) Escolar</option>
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
                                                <option value="{{ $escola->id }}" {{ in_array($escola->id, old('escolas', [])) ? 'selected' : '' }}>
                                                    {{ $escola->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Selecione uma ou mais escolas</small>
                                        @error('escolas')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Senha *</label>
                                        <input type="password" name="password" id="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               required autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirmar Senha *</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" 
                                               class="form-control" required autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Cadastrar Usuário
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

    // Executa ao carregar a página e quando muda o papel
    toggleEscolasField();
    $('#role').change(toggleEscolasField);

    // Verificar email ao sair do campo
    $('#email').blur(function() {
        const email = $(this).val();
        if (email) {
            $.get(`/check-email?email=${email}`, function(response) {
                if (response.exists) {
                    $('#email-feedback').removeClass('d-none').addClass('text-warning')
                        .html(`Usuário já existe. <a href="/users/${response.user_id}/edit">Editar usuário existente</a>`);
                } else {
                    $('#email-feedback').addClass('d-none');
                }
            });
        }
    });
});
</script>
@endsection