@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Resultado Geral da Prova</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Aluno</th>
                <th>Acertos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($acertosPorAluno as $user_id => $acertos)
                <tr>
                    <td>{{ $respostas->firstWhere('user_id', $user_id)->user->name }}</td>
                    <td>{{ $acertos }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection