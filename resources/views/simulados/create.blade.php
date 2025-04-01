@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Criar Simulado</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('simulados.store') }}" method="POST" id="form-simulado">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome">Nome do Simulado</label>
                            <input type="text" name="nome" id="nome" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="tempo_limite">Tempo Limite (minutos)</label>
                            <input type="number" name="tempo_limite" id="tempo_limite" class="form-control" min="1" placeholder="Deixe em branco para sem limite">
                        </div>
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <textarea name="descricao" id="descricao" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="pergunta_id">Selecione uma Pergunta</label>
                            <select name="pergunta_id" id="pergunta_id" class="form-control">
                                <option value="">Selecione uma pergunta</option>
                                @foreach ($perguntas as $pergunta)
                                    <option value="{{ $pergunta->id }}"
                                            data-enunciado="{{ $pergunta->enunciado }}"
                                            data-imagem="{{ $pergunta->imagem ? asset('storage/' . $pergunta->imagem) : '' }}"
                                            data-peso="{{ $pergunta->peso }}">
                                        {{ $pergunta->enunciado }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="button" id="adicionar-pergunta" class="btn btn-secondary">
                                <i class="fas fa-plus"></i> Adicionar Pergunta
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h5>Perguntas Adicionadas</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Peso</th>
                                    <th>Enunciado</th>
                                    <th>Imagem</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody id="lista-perguntas"></tbody>
                        </table>
                        <input type="hidden" name="perguntas" id="perguntas-selecionadas">
                    </div>
                </div>
                <div class="form-group text-right mt-3">
                    <button type="submit" class="btn btn-primary">Criar Simulado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectPergunta = document.getElementById('pergunta_id');
        const btnAdicionar = document.getElementById('adicionar-pergunta');
        const listaPerguntas = document.getElementById('lista-perguntas');
        const inputPerguntasSelecionadas = document.getElementById('perguntas-selecionadas');
        const perguntasAdicionadas = new Set();

        btnAdicionar.addEventListener('click', function () {
            const selectedOption = selectPergunta.options[selectPergunta.selectedIndex];
            if (selectedOption.value && !perguntasAdicionadas.has(selectedOption.value)) {
                const perguntaId = selectedOption.value;
                const enunciado = selectedOption.getAttribute('data-enunciado');
                const imagem = selectedOption.getAttribute('data-imagem');
                const peso = selectedOption.getAttribute('data-peso');

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${peso}</td>
                    <td>${enunciado}</td>
                    <td>${imagem ? `<img src="${imagem}" alt="Imagem da pergunta" style="max-width: 100px;">` : 'Sem imagem'}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remover-pergunta" data-id="${perguntaId}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;

                listaPerguntas.appendChild(tr);
                perguntasAdicionadas.add(perguntaId);
                inputPerguntasSelecionadas.value = Array.from(perguntasAdicionadas).join(',');
                selectPergunta.selectedIndex = 0;
            }
        });

        listaPerguntas.addEventListener('click', function (event) {
            if (event.target.classList.contains('remover-pergunta')) {
                const perguntaId = event.target.getAttribute('data-id');
                const tr = event.target.closest('tr');
                tr.remove();
                perguntasAdicionadas.delete(perguntaId);
                inputPerguntasSelecionadas.value = Array.from(perguntasAdicionadas).join(',');
            }
        });
    });
</script>
@endsection
