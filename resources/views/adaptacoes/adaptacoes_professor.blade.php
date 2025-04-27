@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Adaptações do Professor: {{ $professor->name }}</h1>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            Voltar
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Recurso</th>
                        <th>Deficiências</th>
                        <th>Características</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($adaptacoes as $adaptacao)
                    <tr>
                        <td>{{ $adaptacao->created_at->format('d/m/Y') }}</td>
                        <td>{{ $adaptacao->recurso->nome }}</td>
                        <td>
                            @foreach($adaptacao->deficiencias as $deficiencia)
                            <span class="badge bg-primary">{{ $deficiencia->nome }}</span>
                            @endforeach
                        </td>
                        <td>
                            @foreach($adaptacao->caracteristicas as $caracteristica)
                            <span class="badge bg-success">{{ $caracteristica->nome }}</span>
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $adaptacoes->links() }}
        </div>
    </div>
</div>
@endsection