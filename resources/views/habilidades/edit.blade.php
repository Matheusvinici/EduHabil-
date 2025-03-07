@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Habilidade</h1>
    <form action="{{ route('habilidades.update', $habilidade) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="ano_id">Ano</label>
            <select name="ano_id" id="ano_id" class="form-control" required>
                <option value="">Selecione o Ano</option>
                @foreach ($anos as $ano)
                    <option value="{{ $ano->id }}" {{ $habilidade->ano_id == $ano->id ? 'selected' : '' }}>{{ $ano->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="disciplina_id">Disciplina</label>
            <select name="disciplina_id" id="disciplina_id" class="form-control" required>
                <option value="">Selecione a Disciplina</option>
                @foreach ($disciplinas as $disciplina)
                    <option value="{{ $disciplina->id }}" {{ $habilidade->disciplina_id == $disciplina->id ? 'selected' : '' }}>{{ $disciplina->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="unidade_id">Unidade</label>
            <select name="unidade_id" id="unidade_id" class="form-control" required>
                <option value="">Selecione a Unidade</option>
                @foreach ($unidades as $unidade)
                    <option value="{{ $unidade->id }}" {{ $habilidade->unidade_id == $unidade->id ? 'selected' : '' }}>{{ $unidade->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição</label>
            <input type="text" name="descricao" id="descricao" class="form-control" value="{{ $habilidade->descricao }}" required>
        </div>

        <button type="submit" class="btn btn-warning mt-3">Atualizar</button>
    </form>
</div>
@endsection
