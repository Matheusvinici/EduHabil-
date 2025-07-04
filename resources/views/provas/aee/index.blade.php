@extends('layouts.app')

@section('title', 'Provas - AEE')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Provas - AEE - {{ auth()->user()->escolas->first()->nome }}</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Todas as Provas da Escola</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Ano</th>
                            <th>Disciplina</th>
                            <th>Professor</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(App\Models\Prova::with(['ano', 'disciplina', 'professor'])
                            ->where('escola_id', auth()->user()->escolas->first()->id)
                            ->orderBy('created_at', 'desc')
                            ->paginate(10) as $prova)
                        <tr>
                            <td>{{ $prova->nome }}</td>
                            <td>{{ $prova->ano->nome }}</td>
                            <td>{{ $prova->disciplina->nome }}</td>
                            <td>{{ $prova->professor->name }}</td>
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
                {{ App\Models\Prova::where('escola_id', auth()->user()->escolas->first()->id)->paginate(10)->links() }}
            </div>
        </div>
    </div>
</div>
@endsection