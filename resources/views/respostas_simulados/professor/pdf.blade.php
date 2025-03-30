<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Estatísticas - Professor</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
            font-size: 12px;
        }
        
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
            height: 60px;
            margin-right: 15px;
        }
        
        .header-text {
            text-align: right;
        }
        
        h1 {
            color: #0066cc;
            font-size: 18px;
            margin-top: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
            text-align: center;
        }
        
        h2 {
            font-size: 16px;
            margin-top: 15px;
            color: #444;
        }
        
        .filters {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
            font-size: 11px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        
        th {
            background-color: #0066cc;
            color: white;
            text-align: left;
            padding: 5px;
        }
        
        td {
            padding: 4px;
            border-bottom: 1px solid #ddd;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
        
        .signature {
            margin-top: 40px;
            border-top: 1px solid #333;
            width: 200px;
            text-align: center;
            padding-top: 5px;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .bg-primary { background-color: #0066cc; color: white; }
        .bg-success { background-color: #28a745; color: white; }
        .bg-warning { background-color: #ffc107; color: black; }
        .bg-danger { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('images/logoprefeitura.png') }}" class="logo" alt="Prefeitura">
            <div>
                <h2>Secretaria Municipal de Educação</h2>
                <p>Relatório de Desempenho dos Alunos</p>
            </div>
        </div>
        <div class="header-text">
            <p>Professor: {{ $professor->name ?? 'N/A' }}</p>
            <p>Gerado em: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
    
    <h1>Relatório de Estatísticas - Professor</h1>
    
    <div class="filters">
        <h3>Filtros Aplicados:</h3>
        @if($request->simulado_id ?? false)
            <p><strong>Simulado:</strong> {{ $simulados->firstWhere('id', $request->simulado_id)?->nome ?? 'N/A' }}</p>
        @endif
        @if($request->ano_id ?? false)
            <p><strong>Ano:</strong> {{ $anos->firstWhere('id', $request->ano_id)?->nome ?? 'N/A' }}</p>
        @endif
        @if($request->habilidade_id ?? false)
            <p><strong>Habilidade:</strong> {{ $habilidades->firstWhere('id', $request->habilidade_id)?->descricao ?? 'N/A' }}</p>
        @endif
    </div>
    
    <h2>Dados Gerais</h2>
    <table>
        <tr>
            <td><strong>Total de Alunos:</strong></td>
            <td>{{ $totalAlunos ?? 0 }}</td>
            <td><strong>Total de Respostas:</strong></td>
            <td>{{ $totalRespostas ?? 0 }}</td>
        </tr>
    </table>
    
    @if(isset($mediaTurmaPorSimulado) && count($mediaTurmaPorSimulado) > 0)
    <h2>Média por Simulado</h2>
    <table>
        <thead>
            <tr>
                <th>Simulado</th>
                <th>Média da Turma (0-10)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mediaTurmaPorSimulado as $media)
            <tr>
                <td>{{ $media['simulado'] ?? 'N/A' }}</td>
                <td>{{ isset($media['media_turma']) ? number_format($media['media_turma'], 2) : '0.00' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    @if(isset($estatisticasPorAluno) && count($estatisticasPorAluno) > 0)
    <h2>Desempenho por Aluno</h2>
    <table>
        <thead>
            <tr>
                <th>Aluno</th>
                <th>Respostas</th>
                <th>Acertos</th>
                <th>% Acertos</th>
                <th>Média (0-10)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estatisticasPorAluno as $aluno)
            <tr>
                <td>{{ $aluno['aluno'] ?? 'N/A' }}</td>
                <td>{{ $aluno['total_respostas'] ?? 0 }}</td>
                <td>{{ $aluno['acertos'] ?? 0 }}</td>
                <td>
                    @php
                        $porcentagem = $aluno['porcentagem_acertos'] ?? 0;
                        $classe = $porcentagem >= 70 ? 'success' : ($porcentagem >= 50 ? 'warning' : 'danger');
                    @endphp
                    <span class="badge bg-{{ $classe }}">
                        {{ number_format($porcentagem, 2) }}%
                    </span>
                </td>
                <td>{{ isset($aluno['media_final']) ? number_format($aluno['media_final'], 2) : '0.00' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    @if(isset($estatisticasPorHabilidade) && count($estatisticasPorHabilidade) > 0)
    <h2>Desempenho por Habilidade</h2>
    <table>
        <thead>
            <tr>
                <th>Habilidade</th>
                <th>Respostas</th>
                <th>Acertos</th>
                <th>% Acertos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estatisticasPorHabilidade as $habilidade)
            <tr>
                <td>{{ $habilidade['habilidade'] ?? 'N/A' }}</td>
                <td>{{ $habilidade['total_respostas'] ?? 0 }}</td>
                <td>{{ $habilidade['acertos'] ?? 0 }}</td>
                <td>
                    @php
                        $porcentagem = $habilidade['porcentagem_acertos'] ?? 0;
                        $classe = $porcentagem >= 70 ? 'success' : ($porcentagem >= 50 ? 'warning' : 'danger');
                    @endphp
                    <span class="badge bg-{{ $classe }}">
                        {{ number_format($porcentagem, 2) }}%
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    <div class="footer">
        <p>EduHabil+ - Sistema de Gestão Educacional</p>
        <p>Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
    </div>
    
    <div class="signature">
        Professor: {{ $professor->name ?? 'N/A' }}
    </div>
</body>
</html>