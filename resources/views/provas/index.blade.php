@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Provas</h1>
    <a href="{{ route('provas.create') }}" class="btn btn-success">Criar Nova Prova</a>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Ano</th>
                <th>Disciplina</th>
           
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($provas as $prova)
                <tr>
                    <td>{{ $prova->nome }}</td>
                    <td>{{ $prova->ano->nome }}</td>
                    <td>{{ $prova->disciplina->nome }}</td>
                    
                    <td>
                        <a href="{{ route('provas.gerarPDF', $prova) }}" class="btn btn-info">Baixar PDF</a>
                        <form action="{{ route('provas.destroy', $prova) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Tem certeza?')">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
