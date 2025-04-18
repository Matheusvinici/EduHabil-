@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">
                <i class="fas fa-question-circle mr-2"></i>Cadastrar Nova Pergunta
            </h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('perguntas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Linha 1: Dados Básicos -->
                <div class="row">
                    <!-- Coluna 1: Ano e Disciplina -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ano_id" class="font-weight-bold">
                                <i class="fas fa-graduation-cap mr-1"></i> Ano/Série
                            </label>
                            <select name="ano_id" id="ano_id" class="form-control" required>
                                <option value="" selected disabled>Selecione o ano/série</option>
                                @foreach ($anos as $ano)
                                    <option value="{{ $ano->id }}" {{ old('ano_id') == $ano->id ? 'selected' : '' }}>{{ $ano->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Coluna 2: Disciplina e Habilidade -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="disciplina_id" class="font-weight-bold">
                                <i class="fas fa-book mr-1"></i> Disciplina
                            </label>
                            <select name="disciplina_id" id="disciplina_id" class="form-control" required>
                                <option value="" selected disabled>Selecione a disciplina</option>
                                @foreach ($disciplinas as $disciplina)
                                    <option value="{{ $disciplina->id }}" {{ old('disciplina_id') == $disciplina->id ? 'selected' : '' }}>{{ $disciplina->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Coluna 3: Habilidade e Peso -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="habilidade_id" class="font-weight-bold">
                                <i class="fas fa-brain mr-1"></i> Habilidade
                            </label>
                            <select name="habilidade_id" id="habilidade_id" class="form-control" required>
                                <option value="" selected disabled>Selecione a habilidade</option>
                                @foreach ($habilidades as $habilidade)
                                    <option value="{{ $habilidade->id }}" {{ old('habilidade_id') == $habilidade->id ? 'selected' : '' }}>{{ $habilidade->descricao }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="peso" class="font-weight-bold">
                                <i class="fas fa-weight-hanging mr-1"></i> Peso
                            </label>
                            <select name="peso" id="peso" class="form-control" required>
                                <option value="" selected disabled>Selecione o peso</option>
                                <option value="1" {{ old('peso') == 1 ? 'selected' : '' }}>1 - Baixo</option>
                                <option value="2" {{ old('peso') == 2 ? 'selected' : '' }}>2 - Médio</option>
                                <option value="3" {{ old('peso') == 3 ? 'selected' : '' }}>3 - Alto</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Linha 2: Parâmetros TRI -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-line mr-2"></i>Parâmetros TRI
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Poder Discriminativo -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">
                                                <i class="fas fa-bullseye mr-1"></i> Poder Discriminativo
                                            </label>
                                            <select name="tri_a" class="form-control" required>
                                                <option value="" selected disabled>Selecione o nível</option>
                                                <option value="0.5" {{ old('tri_a') == 0.5 ? 'selected' : '' }}>
                                                    Baixo (0.5)
                                                </option>
                                                <option value="1.0" {{ old('tri_a', 1.0) == 1.0 ? 'selected' : '' }}>
                                                    Moderado (1.0)
                                                </option>
                                                <option value="1.5" {{ old('tri_a') == 1.5 ? 'selected' : '' }}>
                                                    Alto (1.5)
                                                </option>
                                            </select>
                                            <small class="text-muted">Capacidade de diferenciar alunos</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Dificuldade -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">
                                                <i class="fas fa-tachometer-alt mr-1"></i> Nível de Dificuldade
                                            </label>
                                            <select name="tri_b" class="form-control" required>
                                                <option value="" selected disabled>Selecione o nível</option>
                                                <option value="-2.0" {{ old('tri_b') == -2.0 ? 'selected' : '' }}>
                                                    Muito Fácil (-2.0)
                                                </option>
                                                <option value="-1.0" {{ old('tri_b') == -1.0 ? 'selected' : '' }}>
                                                    Fácil (-1.0)
                                                </option>
                                                <option value="0.0" {{ old('tri_b', 0.0) == 0.0 ? 'selected' : '' }}>
                                                    Médio (0.0)
                                                </option>
                                                <option value="1.0" {{ old('tri_b') == 1.0 ? 'selected' : '' }}>
                                                    Difícil (1.0)
                                                </option>
                                            </select>
                                            <small class="text-muted">Probabilidade média de acerto</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Chance de Acerto Casual -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">
                                                <i class="fas fa-dice mr-1"></i> Chance de Acerto Casual
                                            </label>
                                            <select name="tri_c" class="form-control" required>
                                                <option value="0.25" {{ old('tri_c', 0.25) == 0.25 ? 'selected' : '' }}>
                                                    25% (4 alternativas)
                                                </option>
                                                
                                            </select>
                                            <small class="text-muted">Probabilidade de acerto por chute</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Linha 3: Enunciado e Imagem -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="enunciado" class="font-weight-bold">
                                <i class="fas fa-paragraph mr-1"></i> Enunciado da Questão
                            </label>
                            <textarea name="enunciado" id="enunciado" class="form-control" rows="5" 
                                      placeholder="Digite aqui o enunciado completo da questão..." required>{{ old('enunciado') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="imagem" class="font-weight-bold">
                                <i class="fas fa-image mr-1"></i> Imagem (Opcional)
                            </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="imagem" name="imagem" accept="image/*">
                                <label class="custom-file-label" for="imagem">Selecione uma imagem</label>
                            </div>
                            <small class="text-muted">Formatos: JPG, PNG (Máx. 2MB)</small>
                            <div class="mt-2 text-center">
                                <img id="imagem-preview" src="#" alt="Pré-visualização" class="img-fluid d-none mt-2 border rounded" style="max-height: 150px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Linha 4: Alternativas -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_a" class="font-weight-bold text-success">
                                <i class="fas fa-circle mr-1"></i> Alternativa A
                            </label>
                            <input type="text" name="alternativa_a" id="alternativa_a" class="form-control" 
                                   placeholder="Digite o texto da alternativa A" value="{{ old('alternativa_a') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="alternativa_b" class="font-weight-bold text-success">
                                <i class="fas fa-circle mr-1"></i> Alternativa B
                            </label>
                            <input type="text" name="alternativa_b" id="alternativa_b" class="form-control" 
                                   placeholder="Digite o texto da alternativa B" value="{{ old('alternativa_b') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alternativa_c" class="font-weight-bold text-success">
                                <i class="fas fa-circle mr-1"></i> Alternativa C
                            </label>
                            <input type="text" name="alternativa_c" id="alternativa_c" class="form-control" 
                                   placeholder="Digite o texto da alternativa C" value="{{ old('alternativa_c') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="alternativa_d" class="font-weight-bold text-success">
                                <i class="fas fa-circle mr-1"></i> Alternativa D
                            </label>
                            <input type="text" name="alternativa_d" id="alternativa_d" class="form-control" 
                                   placeholder="Digite o texto da alternativa D" value="{{ old('alternativa_d') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Linha 5: Resposta Correta e Botão -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="resposta_correta" class="font-weight-bold">
                                <i class="fas fa-check-circle mr-1"></i> Resposta Correta
                            </label>
                            <select name="resposta_correta" id="resposta_correta" class="form-control" required>
                                <option value="" selected disabled>Selecione a correta</option>
                                <option value="A" {{ old('resposta_correta') == 'A' ? 'selected' : '' }}>Alternativa A</option>
                                <option value="B" {{ old('resposta_correta') == 'B' ? 'selected' : '' }}>Alternativa B</option>
                                <option value="C" {{ old('resposta_correta') == 'C' ? 'selected' : '' }}>Alternativa C</option>
                                <option value="D" {{ old('resposta_correta') == 'D' ? 'selected' : '' }}>Alternativa D</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8 text-right align-self-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-save mr-2"></i> Salvar Pergunta
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Mostrar nome do arquivo e preview da imagem
document.getElementById('imagem').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagem-preview');
    const label = this.nextElementSibling;
    
    if (file) {
        label.textContent = file.name;
        preview.classList.remove('d-none');
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(file);
    } else {
        label.textContent = 'Selecione uma imagem';
        preview.classList.add('d-none');
    }
});
</script>

<style>
.form-group {
    margin-bottom: 1.5rem;
}
.card-header {
    padding: 0.75rem 1.25rem;
}
.custom-file-label::after {
    content: "Procurar";
}
</style>
@endsection