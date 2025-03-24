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

                <div class="form-group">
                    <label for="nome">Nome do Simulado</label>
                    <input type="text" name="nome" id="nome" class="form-control" required>
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
           


                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea name="descricao" id="descricao" class="form-control" rows="3"></textarea>
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

                <!-- Lista de perguntas adicionadas -->
                <div class="form-group">
                    <h5>Perguntas Adicionadas</h5>
                    <ul id="lista-perguntas" class="list-group">
                        <!-- As perguntas adicionadas serão inseridas aqui via JavaScript -->
                    </ul>
                </div>

                <!-- Campo oculto para enviar as perguntas selecionadas -->
                <input type="hidden" name="perguntas" id="perguntas-selecionadas">

                <div class="form-group text-right">
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
        const perguntasAdicionadas = new Set(); // Armazena os IDs das perguntas adicionadas

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