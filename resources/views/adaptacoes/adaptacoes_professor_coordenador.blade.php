@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Adaptações do Professor: {{ $professor->name }}</h1>
        <a href="{{ route('adaptacoes.coordenador.estatisticas', $professor->escolas->first()) }}" 
           class="btn btn-secondary">
            Voltar
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Recurso</th>
                    <th>Deficiências</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($adaptacoes as $adaptacao)
                <tr>
                    <td>{{ $adaptacao->recurso->nome ?? 'N/A' }}</td>
                    <td>
                        @foreach($adaptacao->deficiencias as $deficiencia)
                            <span class="badge bg-primary">{{ $deficiencia->nome }}</span>
                        @endforeach
                    </td>
                    <td>{{ $adaptacao->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('adaptacoes.show', $adaptacao) }}" 
                           class="btn btn-sm btn-info">
                            Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Nenhuma adaptação encontrada</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $adaptacoes->links() }}
    </div>
</div>
@endsection