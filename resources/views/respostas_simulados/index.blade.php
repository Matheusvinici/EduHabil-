@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Meus Simulados Respondidos</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Simulado</th>
                <th>Total de Acertos</th>
                <th>Total de Questões</th>
                <th>Porcentagem de Acertos</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($simulados as $simulado)
                @php
                    $respostas = $simulado->respostas()->where('user_id', Auth::id())->get();
                    $acertos = $respostas->where('correta', true)->count();
                    $total = $respostas->count();
                    $porcentagem = $total > 0 ? ($acertos / $total) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $simulado->nome }}</td>
                    <td>{{ $acertos }}</td>
                    <td>{{ $total }}</td>
                    <td>{{ number_format($porcentagem, 2) }}%</td>
                    <td>
                        <a href="{{ route('respostas_simulados.show', $simulado) }}" class="btn btn-info">Ver Detalhes</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection