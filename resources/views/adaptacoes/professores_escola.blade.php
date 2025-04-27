@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Professores da Escola: {{ $escola->nome }}</h1>
        <a href="{{ route('adaptacoes.estatisticas') }}" class="btn btn-secondary">
            Voltar para Estatísticas
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Professor</th>
                        <th>Total Adaptações</th>
                        <th>Últimas Adaptações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($professores as $professor)
                    <tr>
                        <td>{{ $professor->name }}</td>
                        <td>{{ $professor->adaptacoes_count }}</td>
                        <td>
                            @if($professor->ultimas_adaptacoes->count() > 0)
                                <ul>
                                    @foreach($professor->ultimas_adaptacoes as $adaptacao)
                                    <li>
                                        {{ $adaptacao->recurso->nome }} - 
                                        <small class="text-muted">
                                            {{ $adaptacao->created_at->format('d/m/Y') }}
                                        </small>
                                    </li>
                                    @endforeach
                                </ul>
                            @else
                                Nenhuma adaptação registrada
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection