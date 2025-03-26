@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Editar Pergunta</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('perguntas.update', $pergunta->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Exibição de erros de validação -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ano_id">Ano</label>
                            <select name="ano_id" id="ano_id" class="form-control" required>
                                <option value="">Selecione o Ano</option>
                                @foreach ($anos as $ano)
                                    <option value="{{ $ano->id }}" {{ $pergunta->ano_id == $ano->id ? 'selected' : '' }}>{{ $ano->nome }}</option>
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
                                    <option value="{{ $disciplina->id }}" {{ $pergunta->disciplina_id == $disciplina->id ? 'selected' : '' }}>{{ $disciplina->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Habilidade field -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="habilidade_id">Habilidade</label>
                            <select name="habilidade_id" id="habilidade_id" class="form-control" required>
                                <option value="">Selecione a Habilidade</option>
                                @foreach ($habilidades as $habilidade)
                                    <option value="{{ $habilidade->id }}" {{ (int)$pergunta->habilidade_id === (int)$habilidade->id ? 'selected' : '' }}>{{ $habilidade->descricao }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="enunciado">Enunciado</label>
                    <textarea name="enunciado" id="enunciado" class="form-control" rows="4" required>{{ old('enunciado', $pergunta->enunciado) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_a">Alternativa A</label>
                            <input type="text" name="alternativa_a" id="alternativa_a" class="form-control" value="{{ old('alternativa_a', $pergunta->alternativa_a) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_b">Alternativa B</label>
                            <input type="text" name="alternativa_b" id="alternativa_b" class="form-control" value="{{ old('alternativa_b', $pergunta->alternativa_b) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_c">Alternativa C</label>
                            <input type="text" name="alternativa_c" id="alternativa_c" class="form-control" value="{{ old('alternativa_c', $pergunta->alternativa_c) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_d">Alternativa D</label>
                            <input type="text" name="alternativa_d" id="alternativa_d" class="form-control" value="{{ old('alternativa_d', $pergunta->alternativa_d) }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="resposta_correta">Resposta Correta</label>
                    <select name="resposta_correta" id="resposta_correta" class="form-control" required>
                        <option value="A" {{ old('resposta_correta', $pergunta->resposta_correta) == 'A' ? 'selected' : '' }}>A</option>
                        <option value="B" {{ old('resposta_correta', $pergunta->resposta_correta) == 'B' ? 'selected' : '' }}>B</option>
                        <option value="C" {{ old('resposta_correta', $pergunta->resposta_correta) == 'C' ? 'selected' : '' }}>C</option>
                        <option value="D" {{ old('resposta_correta', $pergunta->resposta_correta) == 'D' ? 'selected' : '' }}>D</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="imagem">Imagem</label>
                    <input type="file" name="imagem" id="imagem" class="form-control" onchange="previewImage()">
                    @if ($pergunta->imagem)
                        <p>Imagem atual: <a href="{{ asset('storage/' . $pergunta->imagem) }}" target="_blank">Ver imagem</a></p>
                    @endif
                    <!-- Exibição da pré-visualização da imagem -->
                    <img id="image_preview" src="#" alt="Pré-visualização da Imagem" style="display: none; max-width: 100%; margin-top: 10px;">
                </div>

                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">Atualizar Pergunta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para pré-visualizar a imagem -->
<script>
    function previewImage() {
        var file = document.getElementById('imagem').files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            var imagePreview = document.getElementById('image_preview');
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
        }
        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
