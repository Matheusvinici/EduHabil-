@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">
                <i class="fas fa-question-circle"></i> Cadastrar Nova Pergunta
            </h3>
        </div>
        <div class="card-body">
        <form action="{{ route('perguntas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

                <!-- Informações Básicas -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ano_id" class="font-weight-bold">Ano/Série</label>
                            <select name="ano_id" id="ano_id" class="form-control" required>
                                <option value="">Selecione o Ano</option>
                                @foreach ($anos as $ano)
                                    <option value="{{ $ano->id }}">{{ $ano->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="disciplina_id" class="font-weight-bold">Disciplina</label>
                            <select name="disciplina_id" id="disciplina_id" class="form-control" required>
                                <option value="">Selecione a Disciplina</option>
                                @foreach ($disciplinas as $disciplina)
                                    <option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="peso" class="font-weight-bold">Peso da Pergunta</label>
                            <select class="form-control" id="peso" name="peso" required>
                                @for($i = 1; $i <= 3; $i++)
                                    <option value="{{ $i }}">
                                        Nível {{ $i }} - {{ ['Baixo', 'Médio', 'Alto'][$i-1] }}
                                    </option>
                                @endfor
                            </select>
                            <small class="text-muted">Define a importância da questão na avaliação</small>
                        </div>
                    </div>
                </div>

               <!-- Seção TRI - Versão Super Didática -->
                <div class="card mb-4 border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-microscope"></i> Parâmetros de Qualidade da Questão
                            <small class="float-right">Teoria de Resposta ao Item (TRI)</small>
                        </h5>
                    </div>
                    
                    <div class="card-body">
                        <!-- Poder Discriminativo -->
                        <div class="form-group">
                            <label for="tri_a" class="font-weight-bold">
                                <i class="fas fa-fingerprint text-primary"></i> PODER DISCRIMINATIVO
                            </label>
                            <select name="tri_a" id="tri_a" class="form-control" required>
                                <option value="0.5">Baixo (0.5) - Ex: Questão que quase todos acertam ou erram</option>
                                <option value="1.0" selected>Moderado (1.0) - Ex: Diferencia alunos medianos dos bons</option>
                                <option value="1.5">Alto (1.5) - Ex: Só os melhores alunos acertam</option>
                                <option value="2.0">Excelente (2.0) - Ex: Identifica precisamente quem domina o conteúdo</option>
                            </select>
                            <div class="alert alert-light mt-2 p-2">
                                <small>
                                    <i class="fas fa-lightbulb text-info"></i> 
                                    <strong>Como avaliar:</strong> Questões com enunciado claro e alternativas bem elaboradas 
                                    tendem a ter maior poder discriminativo. Valores abaixo de 0.8 indicam que a questão 
                                    não está ajudando a diferenciar os alunos.
                                </small>
                            </div>
                        </div>

                        <!-- Dificuldade -->
                        <div class="form-group mt-4">
                            <label for="tri_b" class="font-weight-bold">
                                <i class="fas fa-chart-line text-warning"></i> NÍVEL DE DIFICULDADE
                            </label>
                            <select name="tri_b" id="tri_b" class="form-control" required>
                                <option value="-2.0">Muito Fácil (-2.0) - Ex: Questão básica que 90% acertam</option>
                                <option value="-1.0">Fácil (-1.0) - Ex: Cerca de 70% de acertos</option>
                                <option value="0.0" selected>Médio (0.0) - Ex: 50% dos alunos acertam</option>
                                <option value="1.0">Difícil (1.0) - Ex: Apenas 30% acertam</option>
                                <option value="2.0">Muito Difícil (2.0) - Ex: Menos de 10% acertam</option>
                            </select>
                            <div class="alert alert-light mt-2 p-2">
                                <small>
                                    <i class="fas fa-lightbulb text-info"></i> 
                                    <strong>Escala prática:</strong> 
                                    -3 (fácil para iniciantes) até +3 (desafio para especialistas). 
                                    A maioria das questões deve ficar entre -1.0 e 1.0 para uma avaliação balanceada.
                                </small>
                            </div>
                        </div>

                        <!-- Acerto Casual -->
                        <div class="form-group mt-4">
                            <label for="tri_c" class="font-weight-bold">
                                <i class="fas fa-dice text-success"></i> CHANCE DE ACERTO ALEATÓRIO
                            </label>
                            <select name="tri_c" id="tri_c" class="form-control" required>
                            <option value="0.0">0% - Ex: Questão discursiva ou sem alternativas</option>
                            <option value="0.25" selected>25% - Padrão para 4 alternativas bem elaboradas</option>
                            <option value="0.2">20% - Para 5 alternativas</option>
                            <option value="0.33">33% - Quando há 3 alternativas plausíveis</option>
                            <option value="0.5">50% - Evitar (questões Verdadeiro/Falso)</option>
                            </select>
                            <div class="alert alert-light mt-2 p-2">
                                <small>
                                    <i class="fas fa-lightbulb text-info"></i> 
                                    <strong>Dica pedagógica:</strong> Quanto menor esse valor, melhor a questão está construída. 
                                    Se precisar usar valores acima de 0.3, considere revisar as alternativas para eliminar 
                                    opções muito óbvias.
                                </small>
                            </div>
                        </div>

                        <!-- Exemplo Prático -->
                        <div class="alert alert-secondary mt-3">
                            <h6><i class="fas fa-book-open"></i> <strong>Exemplo Completo:</strong></h6>
                            <p class="mb-1"><strong>Questão bem construída:</strong></p>
                            <ul class="mb-1">
                                <li>Poder Discriminativo: 1.5 (identifica bem quem aprendeu)</li>
                                <li>Dificuldade: 0.5 (desafiadora mas possível)</li>
                                <li>Acerto Casual: 0.2 (alternativas bem elaboradas)</li>
                            </ul>
                            <p class="mb-0"><strong>Resultado:</strong> Uma questão que diferencia alunos preparados, com dificuldade 
                            adequada e baixo acerto por chute.</p>
                        </div>
                    </div>
                </div>

                <!-- Habilidade e Resposta Correta -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="habilidade_id" class="font-weight-bold">
                                <i class="fas fa-brain"></i> Habilidade Avaliada
                            </label>
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
                            <label for="resposta_correta" class="font-weight-bold">
                                <i class="fas fa-check-circle"></i> Resposta Correta
                            </label>
                            <select name="resposta_correta" id="resposta_correta" class="form-control" required>
                                <option value="">Selecione...</option>
                                <option value="A">Alternativa A</option>
                                <option value="B">Alternativa B</option>
                                <option value="C">Alternativa C</option>
                                <option value="D">Alternativa D</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Enunciado -->
                <div class="form-group mt-3">
                    <label for="enunciado" class="font-weight-bold">
                        <i class="fas fa-paragraph"></i> Enunciado da Questão
                    </label>
                    <textarea name="enunciado" id="enunciado" class="form-control" rows="5" 
                              placeholder="Digite o enunciado de forma clara e objetiva..." required></textarea>
                </div>

                <!-- Alternativas -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_a" class="font-weight-bold">Alternativa A</label>
                            <input type="text" name="alternativa_a" id="alternativa_a" class="form-control" 
                                   placeholder="Texto da alternativa A" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_b" class="font-weight-bold">Alternativa B</label>
                            <input type="text" name="alternativa_b" id="alternativa_b" class="form-control" 
                                   placeholder="Texto da alternativa B" required>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_c" class="font-weight-bold">Alternativa C</label>
                            <input type="text" name="alternativa_c" id="alternativa_c" class="form-control" 
                                   placeholder="Texto da alternativa C" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_d" class="font-weight-bold">Alternativa D</label>
                            <input type="text" name="alternativa_d" id="alternativa_d" class="form-control" 
                                   placeholder="Texto da alternativa D" required>
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                <label for="imagem">Imagem (Opcional - Máx 2MB)</label>
                <input type="file" name="imagem" id="imagem" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif">
                <small class="text-muted">Formatos aceitos: JPEG, PNG, JPG, GIF</small>
            </div>

                

                <!-- Botão de Submissão -->
                <div class="form-group text-right mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Salvar Pergunta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para tooltips -->
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
@endsection    