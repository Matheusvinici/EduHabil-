<!DOCTYPE html>
<html>
<head>
    <title>Atividade Adaptada - Prefeitura de Juazeiro-BA</title>
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
            font-size: 22px;
            margin-top: 25px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        
        .info-aluno {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            color: #0066cc;
            font-size: 16px;
            margin-bottom: 8px;
            border-left: 4px solid #0066cc;
            padding-left: 10px;
        }
        
        .section-content {
            padding-left: 14px;
            text-align: justify;
        }
        
        .badge-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 10px 0;
        }
        
        .badge {
            background-color: #e9ecef;
            color: #495057;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .badge-primary {
            background-color: #d1e7ff;
            color: #0066cc;
        }
        
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        
        .elaborado-por {
            font-style: italic;
            color: #666;
            text-align: right;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('images/logoprefeitura.png') }}" class="logo" alt="Prefeitura de Juazeiro-BA">
            <img src="{{ public_path('images/logoseduc.png') }}" class="logo" alt="Secretaria de Educação">
            <div>
                <h2>Secretaria Municipal de Educação</h2>
                <p class="municipio">Prefeitura de Juazeiro-BA, presente no futuro da gente.</p>
            </div>
        </div>
        <div class="header-text">
            <p class="sistema">Gerado pelo sistema de Adaptações Educacionais</p>
            <p>{{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
    
    <!-- Título da Atividade -->
    <h1>Atividade Adaptada</h1>
    
    <!-- Informações do Recurso -->
    <div class="section">
        <div class="section-title">Recurso Educacional</div>
        <div class="section-content">
            <h3>{{ $adaptacao->recurso->nome }}</h3>
            <p><strong>Descrição:</strong> {{ $adaptacao->recurso->descricao }}</p>
            <p><strong>Como Trabalhar:</strong></p>
            <p>{!! nl2br(e($adaptacao->recurso->como_trabalhar)) !!}</p>
            
            <p><strong>Direcionamentos:</strong></p>
            <p>{!! nl2br(e($adaptacao->recurso->direcionamentos)) !!}</p>
        </div>
    </div>
    
    <!-- Deficiências -->
    <div class="section">
        <div class="section-title">Deficiências</div>
        <div class="section-content">
            <div class="badge-container">
                @foreach ($adaptacao->deficiencias as $deficiencia)
                    <span class="badge badge-primary">{{ $deficiencia->nome }}</span>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Características -->
    <div class="section">
        <div class="section-title">Características</div>
        <div class="section-content">
            <div class="badge-container">
                @foreach ($adaptacao->caracteristicas as $caracteristica)
                    <span class="badge badge-success">{{ $caracteristica->nome }}</span>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Rodapé -->
    <div class="elaborado-por">
        Elaborado pela Diretoria de Educação Inclusiva
    </div>
    
    <div class="footer">
        <p>Sistema EduHabil+</p>
        <p>Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
    </div>
</body>
</html>