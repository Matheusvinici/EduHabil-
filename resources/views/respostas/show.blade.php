@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalhes da Prova: {{ $prova->nome }}</h1>
    <p>Total de Acertos: {{ $acertos }} de {{ $total }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>Questão</th>
                <th>Sua Resposta</th>
                <th>Correta?</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($respostas as $resposta)
                <tr>
                    <td>{{ $resposta->questao->enunciado }}</td>
                    <td>{{ $resposta->resposta }}</td>
                    <td>{{ $resposta->correta ? 'Sim' : 'Não' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection