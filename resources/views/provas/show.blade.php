@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Detalhes da Prova</h3>
                <a href="{{ route('provas.professor.index') }}" class="btn btn-light btn-sm">
                    Voltar para Lista
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Informações Básicas</h5>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Nome:</strong> {{ $prova->nome }}
                        </li>
                        <li class="list-group-item">
                            <strong>Escola:</strong> {{ $prova->escola->nome }}
                        </li>
                        <li class="list-group-item">
                            <strong>Ano:</strong> {{ $prova->ano->nome }}
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Detalhes Acadêmicos</h5>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Disciplina:</strong> {{ $prova->disciplina->nome }}
                        </li>
                        <li class="list-group-item">
                            <strong>Habilidade:</strong> {{ $prova->habilidade->descricao }}
                        </li>
                        <li class="list-group-item">
                            <strong>Data:</strong> {{ $prova->data->format('d/m/Y') }}
                        </li>
                    </ul>
                </div>
            </div>

            @if($prova->observacoes)
            <div class="mb-4">
                <h5>Observações</h5>
                <div class="card">
                    <div class="card-body">
                        {{ $prova->observacoes }}
                    </div>
                </div>
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
                    <a href="{{ route('provas.professor.edit', $prova) }}" class="btn btn-warning">
                        Editar Prova
                    </a>
                    <a href="{{ route('provas.professor.pdf', $prova) }}" class="btn btn-info">
                        Baixar PDF
                    </a>
                    <a href="{{ route('provas.professor.pdf-gabarito', $prova) }}" class="btn btn-secondary">
                        Baixar Gabarito
                    </a>
                </div>
                
                <form action="{{ route('provas.professor.destroy', $prova) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Tem certeza que deseja excluir esta prova?')">
                        Excluir Prova
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection