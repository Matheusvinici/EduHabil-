@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="text-primary-blue">
                Provas do Professor: {{ $professor->name }}
                <small class="text-muted"> - Escola: {{ $escola->nome }}</small>
            </h2>
            <a href="{{ route('provas.coordenador.estatisticas-escola', ['escola' => $escola->id]) }}" 
               class="btn btn-outline-primary-blue">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="bg-primary-blue text-white">
                        <tr>
                            <th>Nome da Prova</th>
                            <th>Data</th>
                            <th>Ano</th>
                            <th>Disciplina</th>
                            <th>Habilidade</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($provas as $prova)
                        <tr>
                            <td class="text-primary-blue">{{ $prova->nome }}</td>
                            <td>{{ $prova->data->format('d/m/Y') }}</td>
                            <td>{{ $prova->ano->nome }}</td>
                            <td>{{ $prova->disciplina->nome }}</td>
                            <td>{{ $prova->habilidade->descricao }}</td>
                            <td>
                                <a href="{{ route('provas.show', $prova->id) }}" 
                                   class="btn btn-sm btn-outline-primary-blue">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('provas.gerarPDF', $prova->id) }}" 
                                   class="btn btn-sm btn-outline-primary-blue">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-primary-blue">
                                Nenhuma prova encontrada
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $provas->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-blue: #2c6ecb;
    }
    
    .text-primary-blue {
        color: var(--primary-blue);
    }
    
    .bg-primary-blue {
        background-color: var(--primary-blue);
    }
    
    .btn-outline-primary-blue {
        color: var(--primary-blue);
        border-color: var(--primary-blue);
    }
    
    .btn-outline-primary-blue:hover {
        background-color: var(--primary-blue);
        color: white;
    }
</style>
@endsection