@extends('layouts.app')

@section('content')
<div class="container">
    <div class="header mb-4">
        <div class="logo-container">
            <img src="{{ asset('images/logoprefeitura.png') }}" class="logo" alt="Prefeitura de Juazeiro-BA">
            <div>
                <h2>Secretaria Municipal de Educação</h2>
                <p class="municipio">Prefeitura de Juazeiro-BA, presente no futuro da gente.</p>
            </div>
        </div>
        <div class="header-text">
            <p class="sistema">Sistema EduHabil+</p>
            <p>{{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <h1 class="text-center mb-4" style="color: #0066cc; border-bottom: 2px solid #0066cc; padding-bottom: 10px;">
        Simulados Disponíveis
    </h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-primary">
                <tr>
                    <th>Nome do Simulado</th>
                    <th>Quantidade de Perguntas</th>
                    <th>Quantidade de Acertos</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($simulados as $simulado)
                    <tr>
                        <td>{{ $simulado->nome }}</td>
                        <td>{{ $simulado->perguntas_count }}</td>
                        <td>
                            @if ($simulado->respostas()->where('user_id', auth()->id())->exists())
                                {{ $simulado->respostas()->where('user_id', auth()->id())->where('correta', true)->count() }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if ($simulado->respostas()->where('user_id', auth()->id())->exists())
                                <span class="badge badge-success">Concluído</span>
                            @else
                                <span class="badge badge-warning">Pendente</span>
                            @endif
                        </td>
                        <td>
                            @if ($simulado->respostas()->where('user_id', auth()->id())->exists())
                                <a href="{{ route('respostas_simulados.show', $simulado->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Ver Resultado
                                </a>
                            @else
                                <a href="{{ route('respostas_simulados.create', $simulado->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Responder
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer mt-5 text-center">
        <p>EduHabil+ - Sistema de Gestão Educacional</p>
        <p>Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
    </div>
</div>

<style>
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 3px solid #0066cc;
        padding-bottom: 15px;
    }
    
    .logo-container {
        display: flex;
        align-items: center;
    }
    
    .logo {
        height: 80px;
        margin-right: 15px;
    }
    
    .header-text {
        text-align: right;
    }
    
    .municipio {
        font-size: 14px;
        color: #555;
        font-style: italic;
    }
    
    .sistema {
        font-size: 12px;
        color: #777;
        margin-top: 5px;
    }
    
    .footer {
        text-align: center;
        margin-top: 40px;
        font-size: 12px;
        color: #777;
        border-top: 1px solid #eee;
        padding-top: 10px;
    }
    
    .table th {
        background-color: #0066cc;
        color: white;
    }
    
    .badge-success {
        background-color: #28a745;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
</style>
@endsection