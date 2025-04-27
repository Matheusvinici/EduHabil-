@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Estatísticas Gerais de Adaptações</h1>

    <div class="row">
        <!-- Deficiências mais atendidas -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>Top 10 Deficiências com Mais Adaptações</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Deficiência</th>
                                <th>Total Adaptações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topDeficiencias as $deficiencia)
                            <tr>
                                <td>{{ $deficiencia->nome }}</td>
                                <td>{{ $deficiencia->adaptacoes_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Características mais buscadas -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3>Top 10 Características Mais Buscadas</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Característica</th>
                                <th>Total Adaptações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCaracteristicas as $caracteristica)
                            <tr>
                                <td>{{ $caracteristica->nome }}</td>
                                <td>{{ $caracteristica->adaptacoes_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Escolas com adaptações -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h3>Escolas com Adaptações</h3>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Escola</th>
                        <th>Total Adaptações</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($escolas as $escola)
                    <tr>
                        <td>{{ $escola->nome }}</td>
                        <td>{{ $escola->adaptacoes_count }}</td>
                        <td>
                            <a href="{{ route('adaptacoes.escola', $escola) }}" 
                               class="btn btn-sm btn-primary">
                                Ver Detalhes
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $escolas->links() }}
        </div>
    </div>
</div>
@endsection