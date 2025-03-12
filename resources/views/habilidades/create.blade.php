@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Cadastrar Habilidade</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('habilidades.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ano_id">Ano</label>
                            <select name="ano_id" id="ano_id" class="form-control" required>
                                <option value="">Selecione o Ano</option>
                                @foreach ($anos as $ano)
                                    <option value="{{ $ano->id }}">{{ $ano->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="disciplina_id">Disciplina</label>
                            <select name="disciplina_id" id="disciplina_id" class="form-control" required>
                                <option value="">Selecione a Disciplina</option>
                                @foreach ($disciplinas as $disciplina)
                                    <option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea name="descricao" id="descricao" class="form-control" rows="4" placeholder="Digite a descrição da habilidade" required></textarea>
                </div>

                <div class="form-group text-right">
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection