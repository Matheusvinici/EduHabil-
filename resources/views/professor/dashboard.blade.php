@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Painel do Professor</h5>
                    <div>
                        <span class="badge bg-primary mr-2">
                            Escola atual: {{ $escola->nome }}
                        </span>
                        <a href="{{ route('professor.dashboard', ['mudar_escola' => true]) }}" 
                           class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-exchange-alt"></i> Mudar Escola
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p>Bem-vindo, {{ Auth::user()->name }}!</p>
                    <p>Você está atualmente trabalhando com a escola <strong>{{ $escola->nome }}</strong>.</p>
                    
                    <!-- Conteúdo específico da escola selecionada -->
                    <div class="mt-4">
                        <h5>Turmas nesta escola</h5>
                        @php
                            $turmas = Auth::user()
                                ->turmas()
                                ->where('escola_id', $escola->id)
                                ->get();
                        @endphp
                        
                        @if($turmas->count() > 0)
                            <ul class="list-group">
                                @foreach($turmas as $turma)
                                    <li class="list-group-item">
                                        {{ $turma->nome_turma }}
                                        <span class="badge bg-info float-end">
                                            {{ $turma->alunos->count() }} alunos
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-warning">
                                Você não está vinculado a nenhuma turma nesta escola.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection