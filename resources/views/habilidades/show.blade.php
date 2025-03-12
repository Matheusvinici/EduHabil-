@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalhes da Habilidade</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID:</strong> {{ $habilidade->id }}</p>
                    <p><strong>Ano:</strong> {{ $habilidade->ano->nome }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Disciplina:</strong> {{ $habilidade->disciplina->nome }}</p>
                    <p><strong>Descrição:</strong> {{ $habilidade->descricao }}</p>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('habilidades.index') }}" class="btn btn-secondary">Voltar</a>
            </div>
        </div>
    </div>
</div>
@endsection