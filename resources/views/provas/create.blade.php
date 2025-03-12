@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Criar Nova Prova</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('provas.store') }}" method="POST">
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

            <!-- Disciplina -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="disciplina_id">Disciplina:</label>
                    <select name="disciplina_id" id="disciplina_id" class="form-control" required>
                        <option value="">Selecione a Disciplina</option>
                        @foreach($disciplinas as $disciplina)
                            <option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Habilidade -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="habilidade_id">Habilidade:</label>
                    <select name="habilidade_id" id="habilidade_id" class="form-control" required>
                        <option value="">Selecione a Habilidade</option>
                        @foreach($habilidades as $habilidade)
                            <option value="{{ $habilidade->id }}">{{ $habilidade->descricao }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Nome da Prova -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nome">Nome da Prova:</label>
                    <input type="text" name="nome" id="nome" class="form-control" placeholder="Digite o nome da prova" required>
                </div>
            </div>

            <!-- Data -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="data">Data:</label>
                    <input type="date" name="data" id="data" class="form-control" placeholder="Selecione a data" required>
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

        <button type="submit" class="btn btn-primary">Criar Prova</button>
    </form>
</div>
@endsection