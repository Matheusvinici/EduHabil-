<!DOCTYPE html>
<html>
<head>
    <title>{{ $atividadeProfessor->atividade->titulo }} - Prefeitura de Juazeiro-BA</title>
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
        
        .info-aluno label {
            display: inline-block;
            width: 80px;
            font-weight: bold;
        }
        
        .info-aluno input {
            border: none;
            border-bottom: 1px solid #ccc;
            width: 200px;
            margin-right: 30px;
            background-color: transparent;
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
        
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-after: always;
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
    
    <!-- Dados do Aluno -->
    <div class="info-aluno">
       
        
        <label for="turma">Turma:</label>
        <input type="text" id="turma" name="turma">
        
        <label for="ano">Ano:</label>
        <input type="text" id="ano" name="ano" value="{{ $atividadeProfessor->atividade->ano->nome }}">
    </div>
    
    <!-- Título da Atividade -->
    <h1>{{ $atividadeProfessor->atividade->titulo }}</h1>
    
    <!-- Informações Básicas -->
    <div class="section">
        <div class="section-title">Informações Básicas</div>
        <div class="section-content">
            <p><strong>Disciplina:</strong> {{ $atividadeProfessor->atividade->disciplina->nome }}</p>
            <p><strong>Habilidade:</strong> {{ $atividadeProfessor->atividade->habilidade->descricao }}</p>
        </div>
    </div>
    
    <!-- Objetivo -->
    <div class="section">
        <div class="section-title">Objetivo</div>
        <div class="section-content">
            <p>{{ $atividadeProfessor->atividade->objetivo }}</p>
        </div>
    </div>
    
    <!-- Etapas da Aula -->
    <div class="section">
        <div class="section-title">Etapas da Aula</div>
        <div class="section-content">
            <p>{!! nl2br(e($atividadeProfessor->atividade->metodologia)) !!}</p>
        </div>
    </div>
    
    <!-- Materiais Necessários -->
    <div class="section">
        <div class="section-title">Materiais Necessários</div>
        <div class="section-content">
            <p>{!! nl2br(e($atividadeProfessor->atividade->materiais)) !!}</p>
        </div>
    </div>
    
    <!-- Atividade Proposta -->
    <div class="section">
        <div class="section-title">Atividade Proposta</div>
        <div class="section-content">
            <p>{!! nl2br(e($atividadeProfessor->atividade->resultados_esperados)) !!}</p>
        </div>
    </div>
    
    <!-- Rodapé -->
    <div class="footer">
        <p>EduHabil+ - Sistema de Geração de Atividades Educacionais</p>
        <p>Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
    </div>
</body>
</html>