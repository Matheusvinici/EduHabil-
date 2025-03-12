@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalhes da Disciplina</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID:</strong> {{ $disciplina->id }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Nome:</strong> {{ $disciplina->nome }}</p>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('disciplinas.index') }}" class="btn btn-secondary">Voltar</a>
            </div>
        </div>
    </div>
</div>
@endsection