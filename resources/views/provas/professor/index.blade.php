@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h2 class="text-primary-blue font-weight-bold">Minhas Provas</h2>
            <a href="{{ route('provas.create') }}" class="btn btn-primary-blue">
                <i class="fas fa-plus mr-2"></i>Nova Prova
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="bg-primary-blue text-white">
                        <tr>
                            <th class="border-0">Nome da Prova</th>
                            <th class="border-0">Ano</th>
                            <th class="border-0">Disciplina</th>
                            <th class="border-0">Habilidade</th>
                            <th class="border-0">Data</th>
                            <th class="border-0 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($provas as $prova)
                        <tr>
                            <td class="align-middle text-primary-blue">
                                <strong>{{ $prova->nome }}</strong>
                            </td>
                            <td class="align-middle text-primary-blue">
                                {{ $prova->ano->nome ?? 'N/A' }}
                            </td>
                            <td class="align-middle text-primary-blue">
                                {{ $prova->disciplina->nome ?? 'N/A' }}
                            </td>
                            <td class="align-middle text-primary-blue">
                                @if($prova->habilidade)
                                    {{ Str::limit($prova->habilidade->descricao, 30) }}...
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="align-middle text-primary-blue">
                                {{ $prova->created_at->format('d/m/Y') }}
                            </td>
                            <td class="align-middle text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('provas.show', $prova->id) }}" 
                                       class="btn btn-sm btn-outline-primary-blue"
                                       data-toggle="tooltip" title="Visualizar">
                                        <i class="fas fa-eye mr-1"></i> Ver
                                    </a>
                                    <a href="{{ route('provas.gerarPDF', $prova->id) }}" 
                                       class="btn btn-sm btn-outline-primary-blue"
                                       data-toggle="tooltip" title="Gerar PDF">
                                        <i class="fas fa-file-pdf mr-1"></i> PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-primary-blue py-4">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <p class="h5">Nenhuma prova encontrada</p>
                                <a href="{{ route('provas.create') }}" class="btn btn-primary-blue mt-2">
                                    <i class="fas fa-plus mr-2"></i>Criar Primeira Prova
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($provas->count())
            <div class="d-flex justify-content-center mt-4">
                {{ $provas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    :root {
        --primary-blue: #1a5cb0;
        --primary-light: #e8f0fe;
        --primary-lighter: #f5f9ff;
    }
    
    .text-primary-blue {
        color: var(--primary-blue);
    }
    
    .bg-primary-blue {
        background-color: var(--primary-blue);
    }
    
    .btn-primary-blue {
        background-color: var(--primary-blue);
        color: white;
        border-color: var(--primary-blue);
        transition: all 0.3s ease;
    }
    
    .btn-primary-blue:hover {
        background-color: #134a8e;
        border-color: #134a8e;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .btn-outline-primary-blue {
        color: var(--primary-blue);
        border-color: var(--primary-blue);
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary-blue:hover {
        background-color: var(--primary-blue);
        color: white;
        transform: translateY(-1px);
    }
    
    .table-hover tbody tr:hover {
        background-color: var(--primary-lighter);
    }
    
    .pagination .page-item.active .page-link {
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
    }
    
    .pagination .page-link {
        color: var(--primary-blue);
    }
    
    .card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }
    
    thead {
        border-radius: 10px 10px 0 0;
        overflow: hidden;
    }
    
    .alert-success {
        border-left: 4px solid #28a745;
    }
</style>

<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection