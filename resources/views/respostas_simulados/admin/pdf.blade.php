<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Estatísticas - Admin</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
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
        
        h1 {
            color: #0066cc;
            font-size: 22px;
            margin-top: 25px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            text-align: center;
        }
        
        h2 {
            color: #0066cc;
            font-size: 18px;
            margin-top: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        
        .filters {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .filters p {
            margin: 5px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 12px;
        }
        
        th {
            background-color: #0066cc;
            color: white;
            text-align: left;
            padding: 8px;
            font-weight: bold;
        }
        
        td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
            background-color: #f8f9fa;
        }
        
        .stat-title {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #0066cc;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('images/logoprefeitura.png') }}" class="logo" alt="Prefeitura de Juazeiro-BA">
            <div>
                <h2>Secretaria Municipal de Educação</h2>
                <p class="municipio">Prefeitura de Juazeiro-BA, presente no futuro da gente.</p>
            </div>
        </div>
        <div class="header-text">
            <p class="sistema">Gerado pelo sistema EduHabil+</p>
            <p>{{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
    
    <!-- Título do Relatório -->
    <h1>Relatório de Estatísticas - Administrador</h1>
    
    <!-- Filtros Aplicados -->
    <div class="filters">
        <h3>Filtros Aplicados:</h3>
        @if($request->simulado_id)
            <p><strong>Simulado:</strong> {{ $simulados->firstWhere('id', $request->simulado_id)?->nome ?? 'N/A' }}</p>
        @endif
        @if($request->ano_id)
            <p><strong>Ano:</strong> {{ $anos->firstWhere('id', $request->ano_id)?->nome ?? 'N/A' }}</p>
        @endif
        @if($request->escola_id)
            <p><strong>Escola:</strong> {{ $escolas->firstWhere('id', $request->escola_id)?->nome ?? 'N/A' }}</p>
        @endif
        @if($request->habilidade_id)
            <p><strong>Habilidade:</strong> {{ $habilidades->firstWhere('id', $request->habilidade_id)?->descricao ?? 'N/A' }}</p>
        @endif
    </div>
    
    <!-- Dados Gerais (Sempre exibido) -->
    <h2>Dados Gerais</h2>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-title">Total de Simulados</div>
            <div class="stat-value">{{ $totalSimulados }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-title">Total de Professores</div>
            <div class="stat-value">{{ $totalProfessores }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-title">Total de Alunos</div>
            <div class="stat-value">{{ $totalAlunos }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-title">Total de Respostas</div>
            <div class="stat-value">{{ $totalRespostas }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-title">Professores Responderam</div>
            <div class="stat-value">{{ $professoresResponderam }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-title">Alunos Responderam</div>
            <div class="stat-value">{{ $alunosResponderam }}</div>
        </div>
    </div>
    
        <!-- Estatísticas por Raça (Sempre exibido) -->
<h2>Estatísticas por Raça/Cor</h2>
<table>
    <thead>
        <tr>
            <th>Raça/Cor</th>
            <th>Total Respostas</th>
            <th>Acertos</th>
            <th>% Acertos</th>
            <th>Média (0-10)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($estatisticasPorRaca as $estatistica)
        <tr>
            <td>{{ $estatistica['raca'] }}</td>
            <td>{{ $estatistica['total_respostas'] }}</td>
            <td>{{ $estatistica['acertos'] }}</td>
            <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
            <td>{{ number_format($estatistica['media_final'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
    <!-- Médias Gerais (Sempre exibido junto com Dados Gerais) -->
    <h2>Médias Gerais</h2>
    <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr);">
        <div class="stat-box">
            <div class="stat-title">Média Geral (1º ao 5º Ano)</div>
            <div class="stat-value">{{ number_format($mediaGeral1a5, 2) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-title">Média Geral (6º ao 9º Ano)</div>
            <div class="stat-value">{{ number_format($mediaGeral6a9, 2) }}</div>
        </div>
    </div>
    
    <!-- Estatísticas por Escola (Exibido apenas quando filtrado por escola ou quando não há filtro de habilidade) -->
    @if(!$request->habilidade_id || $request->escola_id)
    <h2>Estatísticas por Escola</h2>
    <table>
        <thead>
            <tr>
                <th>Escola</th>
                <th>Total Respostas</th>
                <th>Acertos</th>
                <th>% Acertos</th>
                <th>Média (0-10)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estatisticasPorEscola as $estatistica)
            <tr>
                <td>{{ $estatistica['escola'] }}</td>
                <td>{{ $estatistica['total_respostas'] }}</td>
                <td>{{ $estatistica['acertos'] }}</td>
                <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                <td>{{ number_format($estatistica['media_final'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    <!-- Estatísticas por Ano (Exibido apenas quando filtrado por ano) -->
    @if($request->ano_id)
    <h2>Estatísticas por Ano</h2>
    <table>
        <thead>
            <tr>
                <th>Ano</th>
                <th>Total Respostas</th>
                <th>Acertos</th>
                <th>% Acertos</th>
                <th>Média (0-10)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estatisticasPorAno as $estatistica)
            <tr>
                <td>{{ $estatistica['ano'] }}</td>
                <td>{{ $estatistica['total_respostas'] }}</td>
                <td>{{ $estatistica['acertos'] }}</td>
                <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                <td>{{ number_format($estatistica['media_final'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    <!-- Estatísticas por Habilidade (Exibido apenas quando filtrado por habilidade) -->
    @if($request->habilidade_id)
    <h2>Estatísticas por Habilidade</h2>
    <table>
        <thead>
            <tr>
                <th>Habilidade</th>
                <th>Total Respostas</th>
                <th>Acertos</th>
                <th>% Acertos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estatisticasPorHabilidade as $estatistica)
            <tr>
                <td>{{ $estatistica['habilidade'] }}</td>
                <td>{{ $estatistica['total_respostas'] }}</td>
                <td>{{ $estatistica['acertos'] }}</td>
                <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
     <!-- Estatísticas por Questão (Sempre exibido se houver dados) -->
     @if(isset($estatisticasPorQuestao) && count($estatisticasPorQuestao) > 0)
    <h2>Estatísticas por Questão</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Questão</th>
                <th>Total Respostas</th>
                <th>Acertos</th>
                <th>% Acertos</th>
                <th>Média (0-10)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estatisticasPorQuestao as $index => $estatistica)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $estatistica['enunciado'] }}</td>
                <td>{{ $estatistica['total_respostas'] }}</td>
                <td>{{ $estatistica['acertos'] }}</td>
                <td>{{ number_format($estatistica['porcentagem_acertos'], 2) }}%</td>
                <td>{{ number_format($estatistica['media_final'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    <!-- Rodapé -->
    <div class="footer">
        <p>EduHabil+ - Sistema de Gestão Educacional</p>
        <p>Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
    </div>
</body>
</html>