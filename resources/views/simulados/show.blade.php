@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $simulado->nome }}</h1>
    <p>Ano: {{ $simulado->ano->nome }}</p>
    <p>Descrição: {{ $simulado->descricao }}</p>

    <h2>Questões</h2>
    <form action="#" method="POST">
        @csrf
        @foreach ($simulado->perguntas as $index => $pergunta)
            <div class="mb-4">
                <p><strong>{{ $index + 1 }}. {{ $pergunta->enunciado }}</strong></p>

                <!-- Verifica se a pergunta tem imagem associada -->
                @if ($pergunta->imagem)
                    <div class="mb-3">
                        <img src="{{ Storage::url($pergunta->imagem) }}" alt="Imagem da Pergunta" class="img-fluid">
                    </div>
                @endif

                <div>
                    <input type="radio" id="q{{ $index }}_a" name="respostas[{{ $pergunta->id }}]" value="A">
                    <label for="q{{ $index }}_a">A) {{ $pergunta->alternativa_a }}</label>
                </div>
                <div>
                    <input type="radio" id="q{{ $index }}_b" name="respostas[{{ $pergunta->id }}]" value="B">
                    <label for="q{{ $index }}_b">B) {{ $pergunta->alternativa_b }}</label>
                </div>
                <div>
                    <input type="radio" id="q{{ $index }}_c" name="respostas[{{ $pergunta->id }}]" value="C">
                    <label for="q{{ $index }}_c">C) {{ $pergunta->alternativa_c }}</label>
                </div>
                <div>
                    <input type="radio" id="q{{ $index }}_d" name="respostas[{{ $pergunta->id }}]" value="D">
                    <label for="q{{ $index }}_d">D) {{ $pergunta->alternativa_d }}</label>
                </div>
                <div>
                    <input type="radio" id="q{{ $index }}_e" name="respostas[{{ $pergunta->id }}]" value="E">
                    <label for="q{{ $index }}_e">E) {{ $pergunta->alternativa_e }}</label>
                </div>
            </div>
        @endforeach
        <button type="submit" class="btn btn-success">Enviar Respostas</button>
    </form>
</div>
@endsection
