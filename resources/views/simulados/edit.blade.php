@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Editar Simulado: {{ $simulado->nome }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('simulados.update', $simulado->id) }}" method="POST" id="form-simulado">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="nome">Nome do Simulado</label>
                    <input type="text" name="nome" id="nome" class="form-control" value="{{ $simulado->nome }}" required>
                </div>
                <div class="form-group">
                    <label for="tempo_limite">Tempo Limite (minutos)</label>
                    <input type="number" name="tempo_limite" id="tempo_limite" class="form-control" 
                           min="1" placeholder="Deixe em branco para sem limite" value="{{ $simulado->tempo_limite }}">
                </div>
                <div class="form-group">
                    <label for="ano_id">Ano:</label>
                    <select name="ano_id" id="ano_id" class="form-control" required>
                        <option value="">Selecione o Ano</option>
                        @foreach($anos as $ano)
                            <option value="{{ $ano->id }}" {{ $simulado->ano_id == $ano->id ? 'selected' : '' }}>
                                {{ $ano->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea name="descricao" id="descricao" class="form-control" rows="3">{{ $simulado->descricao }}</textarea>
                </div>

                <div class="form-group">
                    <label for="pergunta_id">Selecione uma Pergunta</label>
                    <select name="pergunta_id" id="pergunta_id" class="form-control">
                        <option value="">Selecione uma pergunta</option>
                        @foreach ($perguntas as $pergunta)
                            <option value="{{ $pergunta->id }}" data-enunciado="{{ $pergunta->enunciado }}" data-imagem="{{ $pergunta->imagem ? asset('storage/' . $pergunta->imagem) : '' }}">
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

                <div class="form-group">
                    <h5>Perguntas Adicionadas</h5>
                    <ul id="lista-perguntas" class="list-group">
                        @foreach ($simulado->perguntas as $pergunta)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $pergunta->enunciado }}</strong>
                                    @if ($pergunta->imagem)
                                        <br><img src="{{ asset('storage/' . $pergunta->imagem) }}" alt="Imagem da pergunta" style="max-width: 100px;">
                                    @endif
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remover-pergunta" data-id="{{ $pergunta->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <input type="hidden" name="perguntas" id="perguntas-selecionadas" value="{{ $simulado->perguntas->pluck('id')->implode(',') }}">

                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">Atualizar Simulado</button>
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
        const perguntasAdicionadas = new Set(inputPerguntasSelecionadas.value.split(',').filter(id => id)); // Inicializa com os IDs existentes

        // Função para adicionar uma pergunta à lista
        btnAdicionar.addEventListener('click', function () {
            const selectedOption = selectPergunta.options[selectPergunta.selectedIndex];

            if (selectedOption.value && !perguntasAdicionadas.has(selectedOption.value)) {
                const perguntaId = selectedOption.value;
                const enunciado = selectedOption.getAttribute('data-enunciado');
                const imagem = selectedOption.getAttribute('data-imagem');

                // Cria o item da lista
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
                    <div>
                        <strong>${enunciado}</strong>
                        ${imagem ? `<br><img src="${imagem}" alt="Imagem da pergunta" style="max-width: 100px;">` : ''}
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remover-pergunta" data-id="${perguntaId}">
                        <i class="fas fa-trash"></i>
                    </button>
                `;

                // Adiciona o item à lista
                listaPerguntas.appendChild(li);

                // Adiciona o ID ao conjunto de perguntas selecionadas
                perguntasAdicionadas.add(perguntaId);

                // Atualiza o campo oculto com os IDs das perguntas selecionadas
                inputPerguntasSelecionadas.value = Array.from(perguntasAdicionadas).join(',');

                // Limpa a seleção
                selectPergunta.selectedIndex = 0;
            }
        });

        // Função para remover uma pergunta da lista
        listaPerguntas.addEventListener('click', function (event) {
            if (event.target.classList.contains('remover-pergunta')) {
                const perguntaId = event.target.getAttribute('data-id');
                const li = event.target.closest('li');

                // Remove o item da lista
                li.remove();

                // Remove o ID do conjunto de perguntas selecionadas
                perguntasAdicionadas.delete(perguntaId);

                // Atualiza o campo oculto com os IDs das perguntas selecionadas
                inputPerguntasSelecionadas.value = Array.from(perguntasAdicionadas).join(',');
            }
        });
    });
</script>
@endsection