<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório por Ano de Ensino - {{ $simulado->nome }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 16px; font-weight: bold; }
        .subtitle { font-size: 12px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f8f9fa; text-align: left; padding: 6px; border: 1px solid #ddd; }
        td { padding: 6px; border: 1px solid #ddd; }
        .text-center { text-align: center; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 10px; }
        .success { background-color: #28a745; color: white; }
        .danger { background-color: #dc3545; color: white; }
        .segment-title { font-weight: bold; background-color: #e9ecef; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Relatório de Desempenho por Ano de Ensino</div>
        <div class="subtitle">
            Simulado: {{ $simulado->nome }}<br>
            Filtro: {{ $filtroAno }}<br>
            Emitido em: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    @if($consolidado)
    <div style="margin-bottom: 20px;">
        <div class="segment-title">Visão Consolidada</div>
        <table>
            <thead>
                <tr>
                    <th>Segmento</th>
                    <th class="text-center">Alunos</th>
                    <th class="text-center">Média Ponderada</th>
                    <th class="text-center">Média TRI</th>
                    <th class="text-center">Projeção IDEB</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($consolidado as $item)
                    <tr>
                        <td>{{ $item->segmento }}</td>
                        <td class="text-center">{{ $item->total_alunos }}</td>
                        <td class="text-center">{{ number_format($item->media_ponderada, 2, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($item->media_tri, 2, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($item->projecao_ideb, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="segment-title">Detalhamento por Ano</div>
    <table>
        <thead>
            <tr>
                <th>Ano de Ensino</th>
                <th class="text-center">Alunos</th>
                <th class="text-center">Média Ponderada</th>
                <th class="text-center">Média TRI</th>
                <th class="text-center">Projeção IDEB</th>
                <th class="text-center">Meta</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($estatisticas as $item)
                <tr>
                    <td>{{ $item->ano_ensino }}</td>
                    <td class="text-center">{{ $item->total_alunos }}</td>
                    <td class="text-center">{{ number_format($item->media_ponderada, 2, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($item->media_tri, 2, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($item->projecao_ideb, 2, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $item->atingiu_meta ? 'success' : 'danger' }}">
                            {{ $item->atingiu_meta ? 'Sim' : 'Não' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>