@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $atividadeProfessor->atividade->titulo }}</h1>
    <p><strong>Disciplina:</strong> {{ $atividadeProfessor->atividade->disciplina->nome }}</p>
    <p><strong>Ano:</strong> {{ $atividadeProfessor->atividade->ano->nome }}</p>
    <p><strong>Habilidade:</strong> {{ $atividadeProfessor->atividade->habilidade->nome }}</p>
    <p><strong>Objetivo:</strong> {{ $atividadeProfessor->atividade->objetivo }}</p>
    <p><strong>Metodologia:</strong> {{ $atividadeProfessor->atividade->metodologia }}</p>
    <p><strong>Materiais Necessários:</strong> {{ $atividadeProfessor->atividade->materiais }}</p>
    <p><strong>Resultados Esperados:</strong> {{ $atividadeProfessor->atividade->resultados_esperados }}</p>


    <!-- Botão para voltar -->
    <a href="{{ route('atividades_professores.index') }}" class="btn btn-secondary">
        Voltar
    </a>
</div>
@endsection