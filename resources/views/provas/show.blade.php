@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $prova->nome }}</h1>
    <p>Data: {{ $prova->data }}</p>
    <p>Observações: {{ $prova->observacoes }}</p>

    <h2>Questões</h2>
    <ul>
        @foreach ($prova->questoes as $questao)
            <li>
                <p>{{ $questao->enunciado }}</p>
                <p>A) {{ $questao->alternativa_a }}</p>
                <p>B) {{ $questao->alternativa_b }}</p>
                <p>C) {{ $questao->alternativa_c }}</p>
                <p>D) {{ $questao->alternativa_d }}</p>
                <p>E) {{ $questao->alternativa_e }}</p>
                <p>Resposta correta: {{ $questao->resposta_correta }}</p>
                <a href="{{ route('questoes.edit', $questao->id) }}" class="btn btn-primary">Editar Questão</a>
            </li>
        @endforeach
    </ul>
</div>
@endsection