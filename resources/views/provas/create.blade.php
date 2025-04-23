@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Criar Nova Atividade</h3>
                <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                    Voltar
                </a>
            </div>
        </div>
        
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <h5>Por favor, corrija os seguintes erros:</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('provas.store') }}" method="POST">
                @csrf
                
                <div class="row mb-4">
                    <!-- Ano -->
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="ano_id" class="font-weight-bold">Ano</label>
                            <select name="ano_id" id="ano_id" class="form-control form-control-lg" required>
                                <option value="">Selecione o Ano</option>
                                @foreach($anos as $ano)
                                    <option value="{{ $ano->id }}" {{ old('ano_id') == $ano->id ? 'selected' : '' }}>
                                        {{ $ano->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Disciplina -->
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="disciplina_id" class="font-weight-bold">Disciplina</label>
                            <select name="disciplina_id" id="disciplina_id" class="form-control form-control-lg" required>
                                <option value="">Selecione a Disciplina</option>
                                @foreach($disciplinas as $disciplina)
                                    <option value="{{ $disciplina->id }}" {{ old('disciplina_id') == $disciplina->id ? 'selected' : '' }}>
                                        {{ $disciplina->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Habilidade - Modificado para mostrar código ao invés da descrição completa -->
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="habilidade_id" class="font-weight-bold">Habilidade</label>
                            <select name="habilidade_id" id="habilidade_id" class="form-control form-control-lg" required>
                                <option value="">Selecione a Habilidade</option>
                                @foreach($habilidades as $habilidade)
                                    <option value="{{ $habilidade->id }}" {{ old('habilidade_id') == $habilidade->id ? 'selected' : '' }}
                                        data-descricao="{{ $habilidade->descricao }}"
                                        title="{{ $habilidade->descricao }}">
                                        {{ $habilidade->codigo }} - {{ Str::limit($habilidade->descricao, 20) }}
                                    </option>
                                @endforeach
                            </select>
                            <small id="habilidadeDescricao" class="form-text text-muted d-none"></small>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Nome da Prova -->
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="nome" class="font-weight-bold">Nome da Prova</label>
                            <input type="text" name="nome" id="nome" class="form-control form-control-lg" 
                                   placeholder="Digite o nome da prova" value="{{ old('nome') }}" required>
                        </div>
                    </div>

                    <!-- Data -->
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="data" class="font-weight-bold">Data</label>
                            <input type="date" name="data" id="data" class="form-control form-control-lg" 
                                   value="{{ old('data') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div class="form-group mb-4">
                    <label for="observacoes" class="font-weight-bold">Observações</label>
                    <textarea name="observacoes" id="observacoes" class="form-control form-control-lg" 
                              rows="3" placeholder="Digite observações, se necessário">{{ old('observacoes') }}</textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg px-4">
                        Criar Atividade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Mostra a descrição completa da habilidade quando selecionada
    document.getElementById('habilidade_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const descricaoElement = document.getElementById('habilidadeDescricao');
        
        if (selectedOption.value && selectedOption.dataset.descricao) {
            descricaoElement.textContent = selectedOption.dataset.descricao;
            descricaoElement.classList.remove('d-none');
        } else {
            descricaoElement.classList.add('d-none');
        }
    });
</script>
@endsection
@endsection