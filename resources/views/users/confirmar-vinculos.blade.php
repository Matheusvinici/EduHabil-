@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card border-warning shadow">
        <div class="card-header bg-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Confirmação de Vínculos Existentes
                </h3>
            </div>
        </div>

        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                Os seguintes usuários já possuem vínculos com outras escolas e serão também vinculados à escola <strong>{{ $novaEscola->nome }}</strong>.
            </div>

            @if(empty($usuarios))
                <div class="alert alert-danger">
                    Nenhum usuário para confirmar. Por favor, volte e tente novamente.
                </div>
            @else
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Escolas Atuais</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario['name'] }}</td>
                                <td>{{ $usuario['email'] }}</td>
                                <td>
                                    <ul class="mb-0">
                                        @foreach($usuario['escolas'] as $escola)
                                            <li>{{ $escola }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <form action="{{ route('users.store-lote') }}" method="POST" id="confirmForm">
                    @csrf
                    <input type="hidden" name="confirmar_vinculos" value="1">
                    
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary btn-lg mr-2" id="confirmButton">
                            <i class="fas fa-check mr-1"></i> Confirmar e Continuar
                        </button>
                        <a href="{{ route('users.create-lote') }}" class="btn btn-secondary btn-lg" id="cancelButton">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('confirmForm');
    const confirmButton = document.getElementById('confirmButton');
    const cancelButton = document.getElementById('cancelButton');
    let isProcessing = false;
    
    form.addEventListener('submit', function(e) {
        if (isProcessing) {
            e.preventDefault();
            return;
        }
        
        isProcessing = true;
        confirmButton.disabled = true;
        confirmButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processando...';
        cancelButton.classList.add('disabled');
        
        // Timeout para evitar travamento indefinido
        setTimeout(function() {
            if (isProcessing) {
                alert('O processamento está demorando mais que o normal. Por favor, verifique sua conexão e tente novamente.');
                confirmButton.disabled = false;
                confirmButton.innerHTML = '<i class="fas fa-check mr-1"></i> Tentar novamente';
                cancelButton.classList.remove('disabled');
                isProcessing = false;
            }
        }, 30000); // 30 segundos
    });
});
</script>
@endsection