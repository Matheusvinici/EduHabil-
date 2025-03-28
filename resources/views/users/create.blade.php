@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
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
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Preencha os dados do usuário</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('users.store') }}" method="POST">
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
                                                <option value="professor" {{ old('role') == 'professor' ? 'selected' : '' }}>Professor</option>
                                                <option value="aee" {{ old('role') == 'aee' ? 'selected' : '' }}>Professor do AEE</option>
                                                <option value="inclusiva" {{ old('role') == 'inclusiva' ? 'selected' : '' }}>Diretoria Inclusiva</option>
                                                <option value="coordenador" {{ old('role') == 'coordenador' ? 'selected' : '' }}>Coordenador</option>
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
                                                    <option value="{{ $escola->id }}" {{ old('escola_id') == $escola->id ? 'selected' : '' }}>
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

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Senha *</label>
                                            <input type="password" name="password" id="password" 
                                                   class="form-control @error('password') is-invalid @enderror" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">Confirmar Senha *</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                                   class="form-control" required>
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

            // Executa ao carregar a página e quando muda o papel
            toggleEscolaField();
            $('#role').change(toggleEscolaField);
        });
    </script>
    @endsection
@endsection