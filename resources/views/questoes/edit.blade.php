@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Editar Questão</h1>

        <form action="{{ route('questoes.update', $questao->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="ano_id">Ano</label>
                <select name="ano_id" id="ano_id" class="form-control">
                    @foreach ($anos as $ano)
                        <option value="{{ $ano->id }}" @if ($ano->id == $questao->ano_id) selected @endif>{{ $ano->nome }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="disciplina_id">Disciplina</label>
                <select name="disciplina_id" id="disciplina_id" class="form-control">
                    @foreach ($disciplinas as $disciplina)
                        <option value="{{ $disciplina->id }}" @if ($disciplina->id == $questao->disciplina_id) selected @endif>{{ $disciplina->nome }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="habilidade_id">Habilidade</label>
                <select name="habilidade_id" id="habilidade_id" class="form-control">
                    @foreach ($habilidades as $habilidade)
                        <option value="{{ $habilidade->id }}" @if ($habilidade->id == $questao->habilidade_id) selected @endif>{{ $habilidade->descricao }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="enunciado">Enunciado</label>
                <textarea name="enunciado" id="enunciado" class="form-control" rows="3" required>{{ $questao->enunciado }}</textarea>
            </div>

            <div class="form-group">
                <label for="resposta_correta">Resposta Correta</label>
                <input type="text" name="resposta_correta" id="resposta_correta" class="form-control" value="{{ $questao->resposta_correta }}" required>
            </div>

            <button type="submit" class="btn btn-success mt-3">Atualizar Questão</button>
        </form>
    </div>
@endsection
