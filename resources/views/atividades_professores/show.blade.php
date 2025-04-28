@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detalhes da Atividade</h1>
        <div>
            <a href="{{ route('atividades_professores.index') }}" class="btn btn-secondary">
                Voltar para Minhas Atividades
            </a>
            <a href="{{ route('atividades_professores.download', $atividadeProfessor->id) }}" class="btn btn-success">
                Baixar PDF
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">{{ $atividadeProfessor->atividade->titulo }}</h3>
        </div>
        <div class="card-body">
            <!-- Informações Básicas -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <h5 class="text-primary">Disciplina</h5>
                    <p>
                                @foreach($atividadeProfessor->atividade->disciplinas as $disciplina)
                                    {{ $disciplina->nome }}<br>
                                @endforeach
                            </p>   
                </div>
                <div class="col-md-4">
                    <h5 class="text-primary">Ano/Série</h5>
                    <p>{{ $atividadeProfessor->atividade->ano->nome }}</p>
                </div>
                <div class="col-md-4">
                    <h5 class="text-primary">Habilidade</h5>
                    <p>
                    @foreach($atividadeProfessor->atividade->habilidades as $habilidade)
                                    {{ $habilidade->descricao }}<br>
                                @endforeach
                    </p>
                </div>
            </div>

            <!-- Objetivo -->
            <div class="mb-4">
                <h4 class="text-primary border-bottom pb-2">Objetivo</h4>
                <div class="p-3 bg-light rounded">
                    {!! nl2br(e($atividadeProfessor->atividade->objetivo)) !!}
                </div>
            </div>

            <!-- Conteúdo em 2 colunas -->
            <div class="row">
                <!-- Metodologia -->
                <div class="col-md-6 mb-4">
                    <h4 class="text-primary border-bottom pb-2">Etapas da Aula</h4>
                    <div class="p-3 bg-light rounded">
                        {!! nl2br(e($atividadeProfessor->atividade->metodologia)) !!}
                    </div>
                </div>

                <!-- Materiais Necessários -->
                <div class="col-md-6 mb-4">
                    <h4 class="text-primary border-bottom pb-2">Materiais Necessários</h4>
                    <div class="p-3 bg-light rounded">
                        {!! nl2br(e($atividadeProfessor->atividade->materiais)) !!}
                    </div>
                </div>
            </div>

            <!-- Resultados Esperados -->
            <div class="mb-4">
                <h4 class="text-primary border-bottom pb-2">Atividade Proposta</h4>
                <div class="p-3 bg-light rounded">
                    {!! nl2br(e($atividadeProfessor->atividade->resultados_esperados)) !!}
                </div>
            </div>
            <div class="mb-4">
                <h4 class="text-primary border-bottom pb-2">Materiais de Sugestão</h4>
                <div class="p-3 bg-light rounded">
                    {!! nl2br(e($atividadeProfessor->atividade->links_sugestoes)) !!}
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            Atividade gerada em: {{ $atividadeProfessor->created_at->format('d/m/Y H:i') }} | 
            Última atualização: {{ $atividadeProfessor->updated_at->format('d/m/Y H:i') }}
        </div>
    </div>
</div>
@endsection