@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Provas da Escola</h1>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Ano</th>
                <th>Disciplina</th>
                <th>Professor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($provas as $prova)
                <tr>
                    <td>{{ $prova->nome }}</td>
                    <td>{{ $prova->ano->nome }}</td>
                    <td>{{ $prova->disciplina->nome }}</td>
                    <td>{{ $prova->user->name }}</td>
                    <td>
                        <a href="{{ route('provas.gerarPDF', $prova) }}" class="btn btn-info">Baixar PDF</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection