@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Minhas Atividades</h1>
    <a href="{{ route('atividades_professores.create') }}" class="btn btn-primary mb-3">Gerar Nova Atividade</a>
    <table class="table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Disciplina</th>
                <th>Ano</th>
                <th>Habilidade</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($atividadesProfessores as $atividadeProfessor)
            <tr>
                <td>{{ $atividadeProfessor->atividade->titulo }}</td>
                <td>{{ $atividadeProfessor->atividade->disciplina->nome }}</td>
                <td>{{ $atividadeProfessor->atividade->ano->nome }}</td>
                <td>{{ $atividadeProfessor->atividade->habilidade->descricao }}</td>
                <td>
                    <a href="{{ route('atividades_professores.show', $atividadeProfessor->id) }}" class="btn btn-info">Ver</a>
                    <a href="{{ route('atividades_professores.download', $atividadeProfessor->id) }}" class="btn btn-success">Download PDF</a>
                    <form action="{{ route('atividades_professores.destroy', $atividadeProfessor->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Remover</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection