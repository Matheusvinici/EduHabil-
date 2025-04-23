@extends('layouts.app')

@section('title', 'Provas do Professor')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Provas do Professor: {{ $professor->name }}</h2>
            <a href="{{ route('provas.estatisticas-escola', $escola->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar para estatísticas
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Ano</th>
                            <th>Disciplina</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($provas as $prova)
                        <tr>
                            <td>{{ $prova->nome }}</td>
                            <td>{{ $prova->ano->nome }}</td>
                            <td>{{ $prova->disciplina->nome }}</td>
                            <td>{{ $prova->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('provas.show', $prova->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="{{ route('provas.gerarPDF', $prova->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i> PDF
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $provas->links() }}
        </div>
    </div>
</div>
@endsection