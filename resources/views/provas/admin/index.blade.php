@extends('layouts.app')

@section('title', 'Estatísticas de Provas')

@section('header', 'Estatísticas de Provas')

@section('content')
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Filtros</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('provas.admin.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label for="escola_id" class="form-label">Escola:</label>
                    <select name="escola_id" id="escola_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($escolas as $escola)
                            <option value="{{ $escola->id }}" {{ request('escola_id') == $escola->id ? 'selected' : '' }}>{{ $escola->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Provas Geradas -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Provas Geradas</h5>
        </div>
        <div class="card-body">
            @if($provas->isEmpty())
                <div class="alert alert-warning" role="alert">
                    Nenhuma prova encontrada para a escola selecionada.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Escola</th>
                                <th>Ano</th>
                                <th>Disciplina</th>
                                <th>Data de Criação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($provas as $prova)
                                <tr>
                                    <td>{{ $prova->escola->nome }}</td>
                                    <td>{{ $prova->ano->nome }}</td>
                                    <td>{{ $prova->disciplina->nome }}</td>
                                    <td>{{ \Carbon\Carbon::parse($prova->created_at)->format('d/m/Y H:i:s') }}</td>
                                    <td>
                                        <a href="{{ route('provas.show', $prova->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a href="{{ route('provas.gerarPDF', $prova->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-download"></i> Baixar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $provas->appends(['escola_id' => request('escola_id')])->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Dados Gerais -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Dados Gerais</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Total de Provas:</strong> {{ $totalProvas }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Total de Professores:</strong> {{ $totalProfessores }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Total de Escolas:</strong> {{ $totalEscolas }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Escolas sem Provas:</strong> {{ $totalEscolasSemProvas }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Botões para gerar PDFs -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('provas.admin.pdf.escolas.sem.provas') }}" class="btn btn-danger btn-block">
                <i class="fas fa-file-pdf"></i> Gerar PDF de Escolas sem Provas
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('provas.admin.pdf.escolas.com.provas') }}" class="btn btn-success btn-block">
                <i class="fas fa-file-pdf"></i> Gerar PDF de Escolas com Provas
            </a>
        </div>
    </div>
@endsection