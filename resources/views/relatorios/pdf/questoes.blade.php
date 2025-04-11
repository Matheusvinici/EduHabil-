<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Questões</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin-bottom: 5px; }
        .header p { font-size: 12px; color: #666; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th { background-color: #f8f9fa; text-align: left; padding: 8px; border: 1px solid #dee2e6; }
        .table td { padding: 8px; border: 1px solid #dee2e6; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: rgba(0,0,0,.05); }
        .text-center { text-align: center; }
        .badge { padding: 3px 6px; border-radius: 3px; font-size: 11px; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .page-break { page-break-after: always; }
        .footer { font-size: 10px; text-align: center; margin-top: 20px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Estatísticas por Questão</h1>
        <p>Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
        
        @if(!empty($filtros['simulado_id']))
        <p>Filtros aplicados: 
            Simulado ID {{ $filtros['simulado_id'] }}
            @if(!empty($filtros['disciplina_id'])) | Disciplina ID {{ $filtros['disciplina_id'] }} @endif
            @if(!empty($filtros['ano_id'])) | Ano ID {{ $filtros['ano_id'] }} @endif
        </p>
        @endif
    </div>

    <h3>Resumo por Disciplina</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Disciplina</th>
                <th class="text-center">Questões</th>
                <th class="text-center">Média Simples</th>
                <th class="text-center">Média Ponderada</th>
                <th class="text-center">% Acerto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($estatisticasPorDisciplina as $disciplina)
            <tr>
                <td>{{ $disciplina->disciplina }}</td>
                <td class="text-center">{{ $disciplina->total_questoes }}</td>
                <td class="text-center">{{ number_format($disciplina->media_simples, 2) }}</td>
                <td class="text-center">{{ number_format($disciplina->media_ponderada, 1) }} /10</td>
                <td class="text-center">{{ number_format($disciplina->percentual_acerto, 2) }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Nenhum dado encontrado</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Detalhamento por Questão</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Disciplina</th>
                <th>Questão (resumo)</th>
                <th class="text-center">Peso</th>
                <th class="text-center">Habilidade</th>
                <th class="text-center">Respostas</th>
                <th class="text-center">Acertos</th>
                <th class="text-center">% Acerto</th>
                <th class="text-center">Média Ponderada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estatisticasPorQuestao as $questao)
            <tr>
                <td>{{ $questao->disciplina }}</td>
                <td>{{ Str::limit(strip_tags($questao->enunciado), 50) }}</td>
                <td class="text-center">{{ $questao->peso }}</td>
                <td>{{ $questao->habilidade }}</td>
                <td class="text-center">{{ $questao->total_respostas }}</td>
                <td class="text-center">{{ $questao->acertos }}</td>
                <td class="text-center">{{ number_format($questao->percentual_acerto, 2) }}%</td>
                <td class="text-center">{{ number_format($questao->media_ponderada, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Relatório gerado pelo sistema em {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>