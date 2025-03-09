@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Questão</h1>
    <form action="{{ route('questoes.update', $questao->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="enunciado">Enunciado</label>
            <textarea name="enunciado" id="enunciado" class="form-control">{{ $questao->enunciado }}</textarea>
        </div>
        <div class="form-group">
            <label for="alternativa_a">Alternativa A</label>
            <input type="text" name="alternativa_a" id="alternativa_a" class="form-control" value="{{ $questao->alternativa_a }}">
        </div>
        <div class="form-group">
            <label for="alternativa_b">Alternativa B</label>
            <input type="text" name="alternativa_b" id="alternativa_b" class="form-control" value="{{ $questao->alternativa_b }}">
        </div>
        <div class="form-group">
            <label for="alternativa_c">Alternativa C</label>
            <input type="text" name="alternativa_c" id="alternativa_c" class="form-control" value="{{ $questao->alternativa_c }}">
        </div>
        <div class="form-group">
            <label for="alternativa_d">Alternativa D</label>
            <input type="text" name="alternativa_d" id="alternativa_d" class="form-control" value="{{ $questao->alternativa_d }}">
        </div>
        <div class="form-group">
            <label for="alternativa_e">Alternativa E</label>
            <input type="text" name="alternativa_e" id="alternativa_e" class="form-control" value="{{ $questao->alternativa_e }}">
        </div>
        <div class="form-group">
            <label for="resposta_correta">Resposta Correta</label>
            <select name="resposta_correta" id="resposta_correta" class="form-control">
                <option value="A" {{ $questao->resposta_correta == 'A' ? 'selected' : '' }}>A</option>
                <option value="B" {{ $questao->resposta_correta == 'B' ? 'selected' : '' }}>B</option>
                <option value="C" {{ $questao->resposta_correta == 'C' ? 'selected' : '' }}>C</option>
                <option value="D" {{ $questao->resposta_correta == 'D' ? 'selected' : '' }}>D</option>
                <option value="E" {{ $questao->resposta_correta == 'E' ? 'selected' : '' }}>E</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Atualizar Questão</button>
    </form>
</div>
@endsection