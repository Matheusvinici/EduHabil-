<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Usuários - Prefeitura de Juazeiro-BA</title>
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
            text-align: center;
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
            margin-top: 20px;
        }
        
        th {
            background-color: #0066cc;
            color: white;
            text-align: left;
            padding: 10px;
        }
        
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .badge-admin {
            background-color: #dc3545;
            color: white;
        }
        
        .badge-coordenador {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-professor {
            background-color: #17a2b8;
            color: white;
        }
        
        .badge-default {
            background-color: #6c757d;
            color: white;
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
    
    <!-- Título do Relatório -->
    <h1>Relatório de Usuários Cadastrados</h1>
    
    <!-- Filtros Aplicados -->
    @if($escola || $role)
    <div class="filters">
        <h3>Filtros Aplicados:</h3>
        @if($escola)
            <p><strong>Escola:</strong> {{ $escola->nome }}</p>
        @endif
        @if($role)
            <p><strong>Papel:</strong> {{ $role }}</p>
        @endif
    </div>
    @endif
    
    <!-- Tabela de Usuários -->
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Papel</th>
                <th>Escola</th>
                <th>CPF</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="badge badge-{{ $user->role == 'admin' ? 'admin' : ($user->role == 'coordenador' ? 'coordenador' : ($user->role == 'professor' ? 'professor' : 'default')) }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td>{{ $user->escola->nome ?? 'N/A' }}</td>
                <td>{{ $user->cpf ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Resumo -->
    <div style="margin-top: 20px; text-align: right;">
        <p><strong>Total de usuários:</strong> {{ $users->count() }}</p>
        <p><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    
    <!-- Rodapé -->
    <div class="footer">
        <p>EduHabil+ - Sistema de Gestão Educacional</p>
        <p>Prefeitura Municipal de Juazeiro - Secretaria de Educação</p>
    </div>
</body>
</html>