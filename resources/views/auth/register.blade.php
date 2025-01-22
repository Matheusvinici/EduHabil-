@extends('layouts.guest')

@section('content')
    <div class="card-body login-card-body">
        <p class="login-box-msg">{{ __('Realize o seu cadastro e tenha acesso aos encontros formativos e certificados') }}</p>

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

            <!-- Campo de Escola com Autocomplete -->
            <div class="input-group mb-3">
                <select name="escola" id="escola" class="form-control @error('escola') is-invalid @enderror" required>
                    <option value="">Selecione a Escola</option>
                    <option value="Escola Crenildes Brandão">Escola Crenildes Brandão</option>
                    <option value="Escola Paulo VI">Escola Paulo VI</option>
                    <option value="Escola Iracema Pereira">Escola Iracema Pereira</option>
                    <!-- Mais opções podem ser adicionadas aqui -->
                </select>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-school"></span>
                    </div>
                </div>
                @error('escola')
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
                    <button type="submit"
                            class="btn btn-primary btn-block">{{ __('Cadastre-se') }}</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Inicializa o Select2 para o campo escola
            $('#escola').select2({
                placeholder: "Selecione ou digite o nome da escola",
                allowClear: true
            });
        });
    </script>
@endsection
