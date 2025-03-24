@extends('layouts.guest')

@section('content')
    <div class="card-body login-card-body">
        <p class="login-box-msg">{{ __('Realize o seu cadastro e tenha acesso ao EduHabil+') }}</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Nome do usuário -->
            <div class="input-group mb-3">
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       placeholder="{{ __('Digite seu nome completo') }}" required autocomplete="name" autofocus>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user"></span>
                    </div>
                </div>
                @error('name')
                <span class="error invalid-feedback">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <!-- E-mail -->
            <div class="input-group mb-3">
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       placeholder="{{ __('Informe um e-mail válido') }}" required autocomplete="email">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
                @error('email')
                <span class="error invalid-feedback">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <!-- Seleção da Escola -->
            <div class="input-group mb-3">
                <select name="escola_id" class="form-control @error('escola_id') is-invalid @enderror" required>
                    <option value="" disabled selected>Selecione uma escola</option>
                    @foreach ($escolas as $escola)
                        <option value="{{ $escola->id }}" {{ old('escola_id') == $escola->id ? 'selected' : '' }}>
                            {{ $escola->nome }}
                        </option>
                    @endforeach
                </select>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-school"></span>
                    </div>
                </div>
                @error('escola_id')
                <span class="error invalid-feedback">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <!-- Senha -->
            <div class="input-group mb-3">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="{{ __('Crie a sua senha') }}" required autocomplete="new-password">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
                @error('password')
                <span class="error invalid-feedback">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <!-- Confirmação de Senha -->
            <div class="input-group mb-3">
                <input type="password" name="password_confirmation"
                       class="form-control @error('password_confirmation') is-invalid @enderror"
                       placeholder="{{ __('Confirme a senha') }}" required autocomplete="new-password">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>

            <!-- Botão de Cadastro -->
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-block">{{ __('Cadastre-se') }}</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Inicializa o Select2 para o campo escola
            $('select[name="escola_id"]').select2({
                placeholder: "Selecione ou digite o nome da escola",
                allowClear: true
            });
        });
    </script>
@endsection