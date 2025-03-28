@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
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
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Editar dados do usuário</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('users.update', $user->id) }}" method="POST">
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
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group" id="escola-field" style="display: none;">
                                            <label for="escola_id">Escola *</label>
                                            <select name="escola_id" id="escola_id" 
                                                    class="form-control @error('escola_id') is-invalid @enderror">
                                                <option value="">Selecione uma escola</option>
                                                @foreach($escolas as $escola)
                                                    <option value="{{ $escola->id }}" {{ old('escola_id', $user->escola_id) == $escola->id ? 'selected' : '' }}>
                                                        {{ $escola->nome }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('escola_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="change_password" name="change_password">
                                        <label class="form-check-label" for="change_password">
                                            Deseja alterar a senha?
                                        </label>
                                    </div>
                                </div>

                                <div id="password_fields" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Nova Senha</label>
                                                <input type="password" name="password" id="password" 
                                                       class="form-control @error('password') is-invalid @enderror">
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password_confirmation">Confirmar Nova Senha</label>
                                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                                       class="form-control">
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

    @section('scripts')
    <script>
        $(document).ready(function() {
            // Mostra/oculta o campo escola conforme o papel selecionado
            function toggleEscolaField() {
                const role = $('#role').val();
                const escolaField = $('#escola-field');
                
                if (['professor', 'aee', 'coordenador'].includes(role)) {
                    escolaField.show();
                    $('#escola_id').prop('required', true);
                } else {
                    escolaField.hide();
                    $('#escola_id').prop('required', false);
                }
            }

            // Mostra/oculta campos de senha
            $('#change_password').change(function() {
                if ($(this).is(':checked')) {
                    $('#password_fields').show();
                    $('#password').prop('required', true);
                    $('#password_confirmation').prop('required', true);
                } else {
                    $('#password_fields').hide();
                    $('#password').prop('required', false);
                    $('#password_confirmation').prop('required', false);
                }
            });

            // Executa ao carregar a página
            toggleEscolaField();
            $('#role').change(toggleEscolaField);

            // Verifica se deve mostrar a escola ao carregar (para edição)
            @if(in_array($user->role, ['professor', 'aee', 'coordenador']))
                $('#escola-field').show();
            @endif
        });
    </script>
    @endsection
@endsection