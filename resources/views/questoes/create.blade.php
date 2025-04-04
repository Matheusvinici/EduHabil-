@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Cadastrar Questão</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('questoes.store') }}" method="POST">
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

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="habilidade_id">Habilidade</label>
                            <select name="habilidade_id" id="habilidade_id" class="form-control" required>
                                <option value="">Selecione a Habilidade</option>
                                @foreach ($habilidades as $habilidade)
                                    <option value="{{ $habilidade->id }}">{{ $habilidade->descricao }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="resposta_correta">Resposta Correta</label>
                            <select name="resposta_correta" id="resposta_correta" class="form-control" required>
                                <option value="">Selecione a Resposta Correta</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="enunciado">Enunciado</label>
                    <textarea name="enunciado" id="enunciado" class="form-control" rows="4" placeholder="Digite o enunciado da questão" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_a">Alternativa A</label>
                            <input type="text" name="alternativa_a" id="alternativa_a" class="form-control" placeholder="Digite a alternativa A" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_b">Alternativa B</label>
                            <input type="text" name="alternativa_b" id="alternativa_b" class="form-control" placeholder="Digite a alternativa B" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_c">Alternativa C</label>
                            <input type="text" name="alternativa_c" id="alternativa_c" class="form-control" placeholder="Digite a alternativa C" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_d">Alternativa D</label>
                            <input type="text" name="alternativa_d" id="alternativa_d" class="form-control" placeholder="Digite a alternativa D" required>
                        </div>
                    </div>
                </div>

                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">Cadastrar Questão</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection