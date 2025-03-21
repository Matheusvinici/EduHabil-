@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Criar Novo Simulado</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('simulados.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- Ano -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="ano_id">Ano:</label>
                    <select name="ano_id" id="ano_id" class="form-control" required>
                        <option value="">Selecione o Ano</option>
                        @foreach($anos as $ano)
                            <option value="{{ $ano->id }}">{{ $ano->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Nome do Simulado -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nome">Nome do Simulado:</label>
                    <input type="text" name="nome" id="nome" class="form-control" placeholder="Digite o nome do simulado" required>
                </div>
            </div>

            <!-- Data -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="data">Data:</label>
                    <input type="date" name="data" id="data" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Observações -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="observacoes">Observações:</label>
                    <textarea name="observacoes" id="observacoes" class="form-control" placeholder="Digite observações, se necessário"></textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4>Disciplinas e Habilidades</h4>
                <div id="disciplinas-container">
                    <div class="disciplina-block">
                        <div class="form-group">
                            <label for="disciplina_id">Disciplina:</label>
                            <select name="disciplinas[]" class="form-control disciplina-select" required>
                                <option value="">Selecione a Disciplina</option>
                                @foreach($disciplinas as $disciplina)
                                    <option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="habilidade_id">Habilidade:</label>
                            <select name="habilidades[]" class="form-control habilidade-select" required>
                                <option value="">Selecione a Habilidade</option>
                                @foreach($habilidades as $habilidade)
                                    <option value="{{ $habilidade->id }}">{{ $habilidade->descricao }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="btn btn-success add-question">Adicionar Pergunta</button>
                        <div class="perguntas-container"></div>
                    </div>
                </div>
                <button type="button" id="add-disciplina" class="btn btn-primary">Adicionar Disciplina</button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Criar Simulado</button>
    </form>
</div>

<script>
    document.getElementById('add-disciplina').addEventListener('click', function () {
        let disciplinaBlock = document.querySelector('.disciplina-block').cloneNode(true);
        disciplinaBlock.querySelector('.perguntas-container').innerHTML = ''; // Limpar perguntas clonadas
        document.getElementById('disciplinas-container').appendChild(disciplinaBlock);
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('add-question')) {
            let perguntasContainer = e.target.nextElementSibling;
            let perguntaHtml = `
                <div class="form-group">
                    <label>Enunciado:</label>
                    <textarea name="perguntas[]" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label>Imagem:</label>
                    <input type="file" name="imagens[]" class="form-control">
                </div>
                <div class="form-group">
                    <label>Alternativas:</label>
                    <input type="text" name="alternativa_a[]" class="form-control" placeholder="Alternativa A" required>
                    <input type="text" name="alternativa_b[]" class="form-control" placeholder="Alternativa B" required>
                    <input type="text" name="alternativa_c[]" class="form-control" placeholder="Alternativa C" required>
                    <input type="text" name="alternativa_d[]" class="form-control" placeholder="Alternativa D" required>
                    <select name="resposta_correta[]" class="form-control" required>
                        <option value="">Resposta Correta</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>
            `;
            let perguntaDiv = document.createElement('div');
            perguntaDiv.classList.add('pergunta-block');
            perguntaDiv.innerHTML = perguntaHtml;
            perguntasContainer.appendChild(perguntaDiv);
        }
    });
</script>
@endsection
