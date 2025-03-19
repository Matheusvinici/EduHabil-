@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Criar Novo Usuário') }}</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.user.store') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="name">Nome</label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="password">Senha</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">Confirmar Senha</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="role">Papel</label>
                                    <select name="role" id="role" class="form-control" required>
                                        <option value="admin">Admin</option>
                                        <option value="professor">Professor</option>
                                        <option value="aee">Professor do AEE</option>
                                        <option value="inclusiva">Diretoria Inclusiva</option>
                                        <option value="coordenador">Coordenador</option>
                                    </select>
                                </div>

                                <!-- Campo de escola (inicialmente oculto) -->
                                <div class="form-group" id="escola-field" style="display: none;">
                                    <label for="escola_id">Escola</label>
                                    <select name="escola_id" id="escola_id" class="form-control">
                                        <option value="">Selecione uma escola</option>
                                        @foreach($escolas as $escola)
                                            <option value="{{ $escola->id }}">{{ $escola->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Criar Usuário</button>
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Oculta ou exibe o campo escola_id com base no papel selecionado
            $('#role').change(function() {
                const role = $(this).val();
                if (role === 'professor' || role === 'aee' || role === 'coordenador') {
                    $('#escola-field').show(); // Exibe o campo
                    $('#escola_id').prop('required', true); // Torna o campo obrigatório
                } else {
                    $('#escola-field').hide(); // Oculta o campo
                    $('#escola_id').prop('required', false); // Remove a obrigatoriedade
                }
            });

            // Dispara o evento ao carregar a página
            $('#role').trigger('change');
        });
    </script>
@endsection