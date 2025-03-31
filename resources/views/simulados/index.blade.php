@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Simulados</h3>
            <div class="card-tools">
                <a href="{{ route('simulados.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Criar Simulado
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Ações</th>
                        <th>Simulados com Gabarito</th>
                        <th>Simulados sem Gabarito</th>
                       
                    </tr>
                </thead>
                <tbody>
                    @foreach ($simulados as $simulado)
                        <tr>
                            <td>{{ $simulado->id }}</td>
                            <td>{{ $simulado->nome }}</td>
                            <td>
                                <a href="{{ route('simulados.show', $simulado->id) }}" class="btn btn-info btn-sm" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('simulados.edit', $simulado->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('simulados.destroy', $simulado->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este simulado?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <a href="{{ route('simulados.gerarPdf', $simulado->id) }}" class="btn btn-success btn-sm" title="Baixar PDF">
                                    Simulado
                                </a>
                                
                            </td>
                            
                            <td>
                                <a href="{{ route('simulados.gerarPdfEscolas', $simulado->id) }}" class="btn btn-success btn-sm" title="Baixar PDF">
                                    Simulado
                                </a>
                                <a href="{{ route('simulados.baixa-visao-escola', $simulado->id) }}" class="btn btn-primary btn-sm">
                                    Prova Ampliada
                                </a>
                                <a href="{{ route('simulados.gerar-pdf-braille', $simulado->id) }}" class="btn btn-secondary btn-sm">Prova em Braille</a>

                            </td>
                           
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                {{ $simulados->links() }}
            </div>
        </div>
    </div>
</div>
@endsection