@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Editar Questão</h3>
        </div>
        <div class="card-body">
      
        <form action="{{ route('questoes.update', $questao) }}" method="POST">
                @csrf
                @method('PUT')


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ano_id">Ano</label>
                            <select name="ano_id" id="ano_id" class="form-control" required>
                                <option value="">Selecione o Ano</option>
                                @foreach ($anos as $ano)
                                    <option value="{{ $ano->id }}" {{ $questao->ano_id == $ano->id ? 'selected' : '' }}>{{ $ano->nome }}</option>
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
                                    <option value="{{ $disciplina->id }}" {{ $questao->disciplina_id == $disciplina->id ? 'selected' : '' }}>{{ $disciplina->nome }}</option>
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
                                    <option value="{{ $habilidade->id }}" {{ $questao->habilidade_id == $habilidade->id ? 'selected' : '' }}>{{ $habilidade->descricao }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="resposta_correta">Resposta Correta</label>
                            <select name="resposta_correta" id="resposta_correta" class="form-control" required>
                                <option value="">Selecione a Resposta Correta</option>
                                <option value="A" {{ $questao->resposta_correta == 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ $questao->resposta_correta == 'B' ? 'selected' : '' }}>B</option>
                                <option value="C" {{ $questao->resposta_correta == 'C' ? 'selected' : '' }}>C</option>
                                <option value="D" {{ $questao->resposta_correta == 'D' ? 'selected' : '' }}>D</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="enunciado">Enunciado</label>
                    <textarea name="enunciado" id="enunciado" class="form-control" rows="4" placeholder="Digite o enunciado da questão" required>{{ $questao->enunciado }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_a">Alternativa A</label>
                            <input type="text" name="alternativa_a" id="alternativa_a" class="form-control" placeholder="Digite a alternativa A" value="{{ $questao->alternativa_a }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_b">Alternativa B</label>
                            <input type="text" name="alternativa_b" id="alternativa_b" class="form-control" placeholder="Digite a alternativa B" value="{{ $questao->alternativa_b }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_c">Alternativa C</label>
                            <input type="text" name="alternativa_c" id="alternativa_c" class="form-control" placeholder="Digite a alternativa C" value="{{ $questao->alternativa_c }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_d">Alternativa D</label>
                            <input type="text" name="alternativa_d" id="alternativa_d" class="form-control" placeholder="Digite a alternativa D" value="{{ $questao->alternativa_d }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">Atualizar Questão</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection