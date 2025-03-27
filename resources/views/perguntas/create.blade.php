@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Cadastrar Pergunta</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('perguntas.store') }}" method="POST" enctype="multipart/form-data">
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
                    <textarea name="enunciado" id="enunciado" class="form-control" rows="4" placeholder="Digite o enunciado da pergunta" required></textarea>
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

                <div class="form-group">
                    <label for="imagem">Imagem (Opcional)</label>
                    <input type="file" name="imagem" id="imagem" class="form-control" accept="image/*" onchange="previewImage()">
                </div>

               
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">Cadastrar Pergunta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewImage() {
        var file = document.getElementById("imagem").files[0];
        var preview = document.getElementById("preview");

        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = "block";
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = "none";
        }
    }

    function updateImageSize() {
        var preview = document.getElementById("preview");
        var tamanho = document.getElementById("imagem_tamanho").value;

        preview.className = tamanho + " mt-2"; // Atualiza a classe da imagem para refletir o tamanho escolhido
    }
</script>
@endsection
