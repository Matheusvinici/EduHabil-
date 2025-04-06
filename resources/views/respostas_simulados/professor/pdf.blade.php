<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Desempenho - Professor</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 15px;
            color: #333;
            line-height: 1.4;
            font-size: 10px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 10px;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
        }
        
        .logo {
            height: 60px;
            margin-right: 10px;
        }
        
        .header-text {
            text-align: right;
        }
        
        .municipio {
            font-size: 11px;
            color: #555;
        }
        
        .sistema {
            font-size: 10px;
            color: #777;
            margin-top: 3px;
        }
        
        h1 {
            color: #0066cc;
            font-size: 16px;
            margin: 15px 0 10px 0;
            text-align: center;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        
        h2 {
            color: #0066cc;
            font-size: 14px;
            margin: 12px 0 8px 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .filters {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            font-size: 10px;
        }
        
        .filters p {
            margin: 3px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 9px;
            page-break-inside: avoid;
        }
        
        th {
            background-color: #0066cc;
            color: white;
            text-align: left;
            padding: 5px;
            font-weight: bold;
        }
        
        td {
            padding: 4px;
            border: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .stat-box {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px;
            text-align: center;
            background-color: #f8f9fa;
        }
        
        .stat-title {
            font-size: 9px;
            color: #666;
            margin-bottom: 3px;
        }
        
        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #0066cc;
        }
        
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            color: white;
            font-weight: bold;
            font-size: 9px;
            display: inline-block;
            min-width: 30px;
            text-align: center;
        }
        
        .success { background-color: #28a745; }
        .warning { background-color: #ffc107; }
        .danger { background-color: #dc3545; }
        .primary { background-color: #007bff; }
        .secondary { background-color: #6c757d; }
        
        /* Evitar quebra de página dentro de elementos importantes */
        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('images/logoprefeitura.png') }}" class="logo" alt="Prefeitura">
            <div>
                <h2>Secretaria Municipal de Educação</h2>
                <p class="municipio">Prefeitura de Juazeiro-BA</p>
            </div>
        </div>
        <div class="header-text">
            <p class="sistema">Relatório do Professor</p>
            <p>{{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
    
    <!-- Título do Relatório -->
    <h1>Relatório de Desempenho da Turma</h1>
    
    <!-- Filtros Aplicados -->
    <div class="filters no-break">
        <h3>Filtros Aplicados:</h3>
        @if($filtros['turma_id'] ?? false)
            <p><strong>Turma:</strong> {{ $turmaSelecionada->nome_turma ?? 'N/A' }}</p>
        @endif
        @if($filtros['habilidade_id'] ?? false)
            <p><strong>Habilidade:</strong> {{ $habilidades->firstWhere('id', $filtros['habilidade_id'])->descricao ?? 'N/A' }}</p>
        @endif
    </div>
    
    <!-- Dados Gerais -->
    <div class="stats-grid no-break">
        <div class="stat-box">
            <div class="stat-title">Alunos na Turma</div>
            <div class="stat-value">{{ $totalAlunosTurma }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-title">Responderam</div>
            <div class="stat-value">{{ count($estatisticas) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-title">Não Responderam</div>
            <div class="stat-value">{{ count($alunosSemResposta) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-title">Com Deficiência</div>
            <div class="stat-value">{{ $alunosComDeficiencia }}</div>
        </div>
    </div>
    
    <!-- Resultados Detalhados -->
    <div class="no-break">
        <h2>Resultados Detalhados</h2>
        <table>
            <thead>
                <tr>
                    <th>Aluno</th>
                    <th>Turma</th>
                    <th>Simulado</th>
                    <th>Questões</th>
                    <th>Acertos</th>
                    <th>%</th>
                    <th>Média</th>
                    <th>Deficiência</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estatisticas as $est)
                <tr>
                    <td>{{ $est['aluno'] }}</td>
                    <td>{{ $est['turma'] }}</td>
                    <td>{{ Str::limit($est['simulado'], 15) }}</td>
                    <td>{{ $est['total_questoes'] }}</td>
                    <td>{{ $est['acertos'] }}</td>
                    <td>
                        <span class="badge {{ $est['desempenho_class'] }}">
                            {{ number_format($est['porcentagem'], 1) }}%
                        </span>
                    </td>
                    <td>
                        <span class="badge primary">
                            {{ number_format($est['media'], 1) }}
                        </span>
                    </td>
                    <td>
                        @if($est['deficiencia'])
                            <span class="badge danger">Sim</span>
                        @else
                            <span class="badge secondary">Não</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Alunos sem resposta -->
    @if(count($alunosSemResposta) > 0)
    <div class="no-break">
        <h2>Alunos que ainda não responderam</h2>
        <table>
            <thead>
                <tr>
                    <th>Aluno</th>
                    <th>Turma</th>
                    <th>Deficiência</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alunosSemResposta as $aluno)
                <tr>
                    <td>{{ $aluno->name }}</td>
                    <td>{{ $turmaSelecionada->nome_turma }}</td>
                    <td>
                        @if($aluno->deficiencia)
                            <span class="badge danger">Sim</span>
                        @else
                            <span class="badge secondary">Não</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Estatísticas por Habilidade -->
    @if(!empty($estatisticasHabilidades))
    <div class="no-break">
        <h2>Desempenho por Habilidade</h2>
        <table>
            <thead>
                <tr>
                    <th>Habilidade</th>
                    <th>Respostas</th>
                    <th>Acertos</th>
                    <th>% Acertos</th>
                    <th>Média</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estatisticasHabilidades as $estHab)
                <tr>
                    <td>{{ Str::limit($estHab['habilidade'], 30) }}</td>
                    <td>{{ $estHab['total_respostas'] }}</td>
                    <td>{{ $estHab['acertos'] }}</td>
                    <td>
                        <span class="badge {{ $estHab['desempenho_class'] }}">
                            {{ number_format($estHab['porcentagem'], 1) }}%
                        </span>
                    </td>
                    <td>
                        <span class="badge primary">
                            {{ number_format($estHab['media'], 1) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Rodapé -->
    <div class="footer">
        <p>EduHabil+ - Sistema de Gestão Educacional</p>
        <p>Relatório gerado automaticamente</p>
    </div>
</body>
</html>