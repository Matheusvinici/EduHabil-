@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Cadastrar Questão</h1>
    <form action="{{ route('questoes.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="ano_id">Ano:</label>
            <select name="ano_id" id="ano_id" class="form-control" required>
                @foreach ($anos as $ano)
                    <option value="{{ $ano->id }}">{{ $ano->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="disciplina_id">Disciplina:</label>
            <select name="disciplina_id" id="disciplina_id" class="form-control" required>
                @foreach ($disciplinas as $disciplina)
                    <option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="unidade_id">Unidade:</label>
            <select name="unidade_id" id="unidade_id" class="form-control" required>
                @foreach ($unidades as $unidade)
                    <option value="{{ $unidade->id }}">{{ $unidade->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="habilidade_id">Habilidade:</label>
            <select name="habilidade_id" id="habilidade_id" class="form-control" required>
                @foreach ($habilidades as $habilidade)
                    <option value="{{ $habilidade->id }}">{{ $habilidade->descricao }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="enunciado">Enunciado:</label>
            <textarea name="enunciado" id="enunciado" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label for="alternativa_a">Alternativa A:</label>
            <input type="text" name="alternativa_a" id="alternativa_a" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="alternativa_b">Alternativa B:</label>
            <input type="text" name="alternativa_b" id="alternativa_b" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="alternativa_c">Alternativa C:</label>
            <input type="text" name="alternativa_c" id="alternativa_c" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="alternativa_d">Alternativa D:</label>
            <input type="text" name="alternativa_d" id="alternativa_d" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="resposta_correta">Resposta Correta:</label>
            <select name="resposta_correta" id="resposta_correta" class="form-control" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Cadastrar Questão</button>
    </form>
</div>
@endsection