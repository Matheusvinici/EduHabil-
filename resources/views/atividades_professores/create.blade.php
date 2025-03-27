@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0">Gerar Atividade</h2>
                </div>
                
                <div class="card-body">
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    
                    <form action="{{ route('atividades_professores.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="disciplina_id" class="form-label">Disciplina</label>
                                    <select name="disciplina_id" id="disciplina_id" class="form-select" required>
                                        <option value="" disabled selected>Selecione uma disciplina</option>
                                        @foreach($disciplinas as $disciplina)
                                        <option value="{{ $disciplina->id }}" @if(old('disciplina_id')==$disciplina->id) selected @endif>
                                            {{ $disciplina->nome }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ano_id" class="form-label">Ano</label>
                                    <select name="ano_id" id="ano_id" class="form-select" required>
                                        <option value="" disabled selected>Selecione o ano/série</option>
                                        @foreach($anos as $ano)
                                        <option value="{{ $ano->id }}" @if(old('ano_id')==$ano->id) selected @endif>
                                            {{ $ano->nome }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="habilidade_id" class="form-label">Habilidade</label>
                                    <select name="habilidade_id" id="habilidade_id" class="form-select" required>
                                        <option value="" disabled selected>Selecione a habilidade</option>
                                        @foreach($habilidades as $habilidade)
                                        <option value="{{ $habilidade->id }}" @if(old('habilidade_id')==$habilidade->id) selected @endif>
                                            {{ $habilidade->descricao }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-plus-circle me-2"></i> Gerar Atividade
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection