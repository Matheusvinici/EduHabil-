<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Turma - {{ $turma->nome_turma }}</title>
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
        
        .turma-info {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
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
        
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .badge-primary {
            background-color: #0066cc;
            color: white;
        }
        
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
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
    <h1>Relatório da Turma: {{ $turma->nome_turma }}</h1>
    
    <!-- Informações da Turma -->
    <div class="turma-info">
        <p><strong>Escola:</strong> {{ $turma->escola->nome ?? 'Não informada' }}</p>
        <p><strong>Código da Turma:</strong> <span class="badge badge-primary">{{ $turma->codigo_turma }}</span></p>
        <p><strong>Total de Alunos:</strong> {{ $turma->alunos->count() }}</p>
    </div>
    
    <!-- Lista de Alunos -->
    <h2>Alunos</h2>
    <table>
        <thead>
            <tr>
                <th width="25%">Nome</th>
                <th width="25%">Email</th>
                <th width="15%">Código</th>
                <th width="20%">Deficiência</th>
            </tr>
        </thead>
        <tbody>
            @foreach($turma->alunos as $aluno)
            <tr>
                <td>{{ $aluno->name }}</td>
                <td>{{ $aluno->email }}</td>
                <td><span class="badge badge-secondary">{{ $aluno->codigo_acesso }}</span></td>
                <td>
                    <span class="badge badge-info">
                        {{ strtoupper($aluno->deficiencia) ?? 'NÃO INFORMADA' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Rodapé -->
    <div class="footer">
        <p>EduHabil+ - Sistema de Gestão Educacional</p>
        <p>Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
    </div>
</body>
</html>