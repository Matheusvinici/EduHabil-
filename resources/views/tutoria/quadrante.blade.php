@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-{{ $quadranteColor }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Escolas no Quadrante {{ ucfirst($quadrante) }}</h4>
                        <a href="{{ route('tutoria.dashboard') }}" class="btn btn-light btn-sm">
                            Voltar ao Dashboard
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Escola</th>
                                <th>Média</th>
                                <th>Última Avaliação</th>
                                <th>Tutor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($escolas as $escola)
                            <tr>
                                <td>{{ $escola->nome }}</td>
                                <td>
                                    <span class="badge bg-{{ $quadranteColor }}">
                                        {{ number_format($escola->media_avaliacao, 1) }}
                                    </span>
                                </td>
                                <td>
                                @if($escola->ultima_avaliacao)
                                    {{ \Carbon\Carbon::parse($escola->ultima_avaliacao->data_visita)->format('d/m/Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                                <td>
                                    @if($escola->ultima_avaliacao && $escola->ultima_avaliacao->tutor)
                                        {{ $escola->ultima_avaliacao->tutor->name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('tutoria.avaliacoes.create', ['escola_id' => $escola->id]) }}" 
                                    class="btn btn-sm btn-outline-primary">
                                        Nova Avaliação
                                    </a>
                                    <a href="{{ route('tutoria.acompanhamento.escola', $escola->id) }}" 
                                    class="btn btn-sm btn-outline-info">
                                        Acompanhamento
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Nenhuma escola encontrada neste quadrante</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection