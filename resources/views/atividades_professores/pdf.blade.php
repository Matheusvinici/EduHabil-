<!DOCTYPE html>
<html>
<head>
    <title>Sequência Didática - Prefeitura de Juazeiro-BA</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
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
            font-size: 24px;
            margin-top: 25px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            text-align: center;
        }
        
        h2 {
            color: #0066cc;
            font-size: 20px;
            margin-top: 20px;
            border-left: 4px solid #0066cc;
            padding-left: 10px;
        }
        
        .activity-info {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .info-item {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #0066cc;
            display: inline-block;
            width: 120px;
        }
        
        .activity-content {
            margin-top: 30px;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
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
    
    <!-- Título Principal -->
    <h1>SEQUÊNCIA DIDÁTICA</h1>
    
    <!-- Informações da Atividade -->
    <div class="activity-info">
        <div class="info-item">
            <span class="info-label">Professor:</span> {{ $atividade->professor->name ?? 'N/A' }}
        </div>
        <div class="info-item">
            <span class="info-label">Disciplina:</span> {{ $atividade->atividade->disciplina->nome ?? 'N/A' }}
        </div>
        <div class="info-item">
            <span class="info-label">Ano/Série:</span> {{ $atividade->atividade->ano->nome ?? 'N/A' }}
        </div>
        <div class="info-item">
            <span class="info-label">Habilidade:</span> 
            {{ $atividade->atividade->habilidade->codigo ?? 'N/A' }} - 
            {{ $atividade->atividade->habilidade->descricao ?? 'N/A' }}
        </div>
        <div class="info-item">
            <span class="info-label">Data:</span> {{ $atividade->created_at->format('d/m/Y') ?? 'N/A' }}
        </div>
    </div>
    
    <!-- Conteúdo da Sequência Didática -->
    <div class="activity-content">
        <div class="section">
            <h2>OBJETIVOS DE APRENDIZAGEM</h2>
            {!! $atividade->atividade->objetivos ?? '<p>Não especificado</p>' !!}
        </div>
        
        <div class="section">
            <h2>DESENVOLVIMENTO</h2>
            {!! $atividade->atividade->desenvolvimento ?? '<p>Não especificado</p>' !!}
        </div>
        
        <div class="section">
            <h2>RECURSOS DIDÁTICOS</h2>
            {!! $atividade->atividade->recursos ?? '<p>Não especificado</p>' !!}
        </div>
        
        <div class="section">
            <h2>AVALIAÇÃO</h2>
            {!! $atividade->atividade->avaliacao ?? '<p>Não especificado</p>' !!}
        </div>
    </div>
    
    <!-- Rodapé -->
    <div class="footer">
        <p>EduHabil+ - Sistema de Gestão Educacional</p>
        <p>Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
    </div>
</body>
</html>