@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Criar Nova Atividade</h1>
    <form action="{{ route('atividades.store') }}" method="POST">
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
        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control">
        </div>
        <div class="form-group">
            <label for="objetivo">Objetivo</label>
            <textarea name="objetivo" id="objetivo" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="metodologia">Etapas da Aula</label>
            <textarea name="metodologia" id="metodologia" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="materiais">Materiais Necessários</label>
            <textarea name="materiais" id="materiais" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="resultados_esperados">Atividade Proposta</label>
            <textarea name="resultados_esperados" id="resultados_esperados" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Criar Atividade</button>
    </form>
</div>
@endsection