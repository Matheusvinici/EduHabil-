@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Editar Servidor') }}</h1>
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
                            <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="name">Nome</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="role">Papel</label>
                                    <select name="role" id="role" class="form-control" required>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="professor" {{ $user->role === 'professor' ? 'selected' : '' }}>Professor</option>
                                        <option value="aluno" {{ $user->role === 'aluno' ? 'selected' : '' }}>Aluno</option>
                                        <option value="aee" {{ $user->role === 'aee' ? 'selected' : '' }}>Professor do AEE</option>
                                        <option value="inclusiva" {{ $user->role === 'inclusiva' ? 'selected' : '' }}>Diretoria Inclusiva</option>
                                        <option value="coordenador" {{ $user->role === 'coordenador' ? 'selected' : '' }}>Coordenador</option>
                                    </select>
                                </div>

                                <div class="form-group" id="escola-field">
                                    <label for="escola_id">Escola</label>
                                    <select name="escola_id" id="escola_id" class="form-control">
                                        <option value="">Selecione uma escola</option>
                                        @foreach($escolas as $escola)
                                            <option value="{{ $escola->id }}" {{ $user->escola_id === $escola->id ? 'selected' : '' }}>
                                                {{ $escola->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Salvar alterações</button>
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
                if (role === 'professor' || role === 'aee') {
                    $('#escola-field').show();
                    $('#escola_id').prop('required', true); // Adiciona a obrigatoriedade
                } else {
                    $('#escola-field').hide();
                    $('#escola_id').val('').prop('required', false); // Remove a obrigatoriedade
                }
            });

            // Dispara o evento ao carregar a página
            $('#role').trigger('change');
        });
    </script>
@endsection