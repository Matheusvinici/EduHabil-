@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Simulados</h1>
    <a href="{{ route('simulados.create') }}" class="btn btn-success">Criar Novo Simulado</a>
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
            @foreach ($simulados as $simulado)
                <tr>
                    <td>{{ $simulado->nome }}</td>
                    <td>{{ $simulado->ano->nome }}</td>
                    <td>{{ $simulado->disciplina->nome }}</td>
                    <td>
                        <a href="{{ route('simulados.gerarPDF', $simulado) }}" class="btn btn-info">Baixar PDF</a>
                        <form action="{{ route('simulados.destroy', $simulado) }}" method="POST" style="display:inline;">
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
