@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Estatísticas da Escola: {{ $escola->nome }}</h1>
    </div>

    <div class="card mb-4">
        <div class="card-body text-center">
            <h5 class="card-title">Total de Adaptações nesta Escola</h5>
            <p class="display-4">{{ $totalAdaptacoes }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3>Usuários com Adaptações</h3>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Perfil</th>
                        <th>Total Adaptações</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuariosComAdaptacoes as $usuario)
                    <tr>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ ucfirst($usuario->role) }}</td>
                        <td>{{ $usuario->adaptacoes_count }}</td>
                        <td>
                        <a href="{{ route('adaptacoes.coordenador.professor', $usuario) }}" 
                        class="btn btn-sm btn-info">
                            Ver Adaptações
                        </a>
                    </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection