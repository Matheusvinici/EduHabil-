@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Painel do Coordenador</h5>
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
                        <a href="{{ route('coordenador.selecionar.escola') }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-exchange-alt"></i> Mudar Escola
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p>Bem-vindo, {{ Auth::user()->name }}!</p>
                    @if ($escola)
                        <p>Você está atualmente coordenando a escola <strong>{{ $escola->nome }}</strong>.</p>

                        <div class="mt-4">
                            <h5>Professores nesta escola</h5>
                            @php
                                $professores = \App\Models\User::where('role', 'professor')
                                                    ->whereHas('escolas', function ($query) use ($escola) {
                                                        $query->where('escolas.id', $escola->id);
                                                    })
                                                    ->get();
                            @endphp

                            @if($professores->count() > 0)
                                <ul class="list-group">
                                    @foreach($professores as $professor)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $professor->name }}
                                            <span class="badge bg-info rounded-pill">
                                                {{ $professor->turmasLecionadas()->where('escola_id', $escola->id)->count() }} turmas
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="alert alert-info">
                                    Nenhum professor cadastrado nesta escola.
                                </div>
                            @endif
                        </div>

                        <div class="mt-4">
                            <h5>Outras funcionalidades (em breve)</h5>
                            <p>Aqui você poderá gerenciar turmas, alunos, relatórios e outras funcionalidades da coordenação.</p>
                            </div>
                    @else
                        <div class="alert alert-info">
                            Por favor, selecione uma escola para visualizar as informações da coordenação.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection