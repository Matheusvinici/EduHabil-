@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Painel do Professor</h5>
                    <div>
                        @if ($escola)
                            <span class="badge bg-primary mr-2">
                                Escola atual: {{ $escola->nome }}
                            </span>
                        @else
                            <span class="badge bg-warning mr-2">
                                Nenhuma escola selecionada
                            </span>
                        @endif
                        <a href="{{ route('selecionar.escola') }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-exchange-alt"></i> Mudar Escola
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p>Bem-vindo, {{ Auth::user()->name }}!</p>
                    @if ($escola)
                        <p>Você está atualmente trabalhando com a escola <strong>{{ $escola->nome }}</strong>.</p>

                        <div class="mt-4">
                            <h5>Turmas nesta escola</h5>
                            @php
                                $turmas = Auth::user()
                                    ->turmasLecionadas() // Use a relação correta para turmas que o professor leciona
                                    ->where('escola_id', $escola->id)
                                    ->get();
                            @endphp

                            @if($turmas->count() > 0)
                                <ul class="list-group">
                                    @foreach($turmas as $turma)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $turma->nome_turma }}
                                            <span class="badge bg-info rounded-pill">
                                                {{ $turma->alunos()->count() }} alunos
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="alert alert-warning">
                                    Você não está lecionando em nenhuma turma nesta escola.
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-info">
                            Por favor, selecione uma escola para visualizar suas turmas.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection