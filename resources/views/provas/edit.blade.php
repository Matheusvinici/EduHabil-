@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Editar Prova</h3>
                <a href="{{ route('provas.professor.index') }}" class="btn btn-light btn-sm">
                    Voltar para Lista
                </a>
            </div>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('provas.professor.update', $prova) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome" class="form-label">Nome da Prova</label>
                            <input type="text" class="form-control" id="nome" name="nome" 
                                   value="{{ old('nome', $prova->nome) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="data" class="form-label">Data</label>
                            <input type="date" class="form-control" id="data" name="data" 
                                   value="{{ old('data', $prova->data->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ano_id" class="form-label">Ano</label>
                            <select class="form-select" id="ano_id" name="ano_id" required>
                                <option value="">Selecione o Ano</option>
                                @foreach($anos as $ano)
                                    <option value="{{ $ano->id }}" 
                                        {{ old('ano_id', $prova->ano_id) == $ano->id ? 'selected' : '' }}>
                                        {{ $ano->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="disciplina_id" class="form-label">Disciplina</label>
                            <select class="form-select" id="disciplina_id" name="disciplina_id" required>
                                <option value="">Selecione a Disciplina</option>
                                @foreach($disciplinas as $disciplina)
                                    <option value="{{ $disciplina->id }}" 
                                        {{ old('disciplina_id', $prova->disciplina_id) == $disciplina->id ? 'selected' : '' }}>
                                        {{ $disciplina->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="habilidade_id" class="form-label">Habilidade</label>
                            <select class="form-select" id="habilidade_id" name="habilidade_id" required>
                                <option value="">Selecione a Habilidade</option>
                                @foreach($habilidades as $habilidade)
                                    <option value="{{ $habilidade->id }}" 
                                        {{ old('habilidade_id', $prova->habilidade_id) == $habilidade->id ? 'selected' : '' }}>
                                        {{ $habilidade->descricao }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $prova->observacoes) }}</textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">Atualizar Prova</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection