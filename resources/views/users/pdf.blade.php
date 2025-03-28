<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Usuários - {{ now()->format('d/m/Y') }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 15px;
        }
        .header h1 { 
            margin: 0; 
            font-size: 18px;
            color: #0066cc;
        }
        .header p { 
            margin: 5px 0 0; 
            font-size: 14px;
        }
        .filters { 
            margin-bottom: 15px; 
            padding: 15px; 
            background-color: #f8f9fa; 
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .filters h3 {
            margin-top: 0;
            color: #0066cc;
            font-size: 14px;
        }
        .filters p { 
            margin: 5px 0; 
            display: flex;
        }
        .filters strong {
            min-width: 100px;
            display: inline-block;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px;
            font-size: 11px;
        }
        th { 
            background-color: #0066cc;
            color: white;
            text-align: left; 
            padding: 8px;
        }
        td { 
            padding: 7px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer { 
            margin-top: 20px; 
            text-align: right; 
            font-size: 10px; 
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .page-break { 
            page-break-after: always;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-admin { background-color: #dc3545; color: white; }
        .badge-coordenador { background-color: #ffc107; color: #212529; }
        .badge-professor { background-color: #17a2b8; color: white; }
        .badge-aluno { background-color: #28a745; color: white; }
        .badge-default { background-color: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Usuários - Sistema Eduhabil+</h1>
        <p>Gerado em: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="filters">
        <h3>Filtros Aplicados:</h3>
        @if(request('search'))
            <p><strong>Pesquisa por nome:</strong> {{ request('search') }}</p>
        @endif
        @if(request('role'))
            <p><strong>Papel:</strong> {{ ucfirst(request('role')) }}</p>
        @endif
        @if(request('escola_id') && $escola)
            <p><strong>Escola:</strong> {{ $escola->nome }}</p>
        @endif
        @if(auth()->user()->role === 'inclusiva')
            <p><strong>Filtro automático:</strong> Apenas usuários AEE</p>
        @elseif(auth()->user()->role === 'coordenador')
            <p><strong>Filtro automático:</strong> Apenas minha escola</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Papel</th>
                <th>Escola</th>
                <th>CPF</th>
                <th>Código Acesso</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="badge badge-{{ 
                        $user->role == 'admin' ? 'admin' : 
                        ($user->role == 'coordenador' ? 'coordenador' : 
                        ($user->role == 'professor' ? 'professor' :
                        ($user->role == 'aluno' ? 'aluno' : 'default')))
                    }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td>{{ $user->escola->nome ?? 'N/A' }}</td>
                <td>{{ $user->cpf ? mask($user->cpf, '###.###.###-##') : 'N/A' }}</td>
                <td>{{ $user->codigo_acesso ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total de registros: {{ $users->count() }}</p>
        <p>Sistema Eduhabil+ - Prefeitura Municipal de Juazeiro</p>
    </div>
</body>
</html>