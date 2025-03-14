@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Gerar Atividade</h1>
    <form action="{{ route('atividades_professores.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="disciplina_id">Disciplina</label>
            <select name="disciplina_id" id="disciplina_id" class="form-control">
                @foreach($disciplinas as $disciplina)
                <option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="ano_id">Ano</label>
            <select name="ano_id" id="ano_id" class="form-control">
                @foreach($anos as $ano)
                <option value="{{ $ano->id }}">{{ $ano->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="habilidade_id">Habilidade</label>
            <select name="habilidade_id" id="habilidade_id" class="form-control">
                @foreach($habilidades as $habilidade)
                <option value="{{ $habilidade->id }}">{{ $habilidade->descricao }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Gerar Atividade</button>
    </form>
</div>
@endsection