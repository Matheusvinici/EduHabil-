<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Usuários - {{ now()->format('d/m/Y') }}</title>
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
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
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
        .badge-aee { background-color: #6610f2; color: white; }
        .badge-inclusiva { background-color: #20c997; color: white; }
        .badge-gestor { background-color: #fd7e14; color: white; }
        .badge-aluno { background-color: #6c757d; color: white; }
        .badge-default { background-color: #6c757d; color: white; }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .escola-list {
            margin: 0;
            padding-left: 15px;
        }

        .escola-item {
            margin-bottom: 3px;
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
    <h1>Relatório de Usuários</h1>

    <!-- Filtros Aplicados -->
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

    <!-- Tabela de Usuários -->
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Papel</th>
                <th>Escola(s)</th>
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
                        ($user->role == 'aee' ? 'aee' :
                        ($user->role == 'inclusiva' ? 'inclusiva' :
                        ($user->role == 'gestor' ? 'gestor' :
                        ($user->role == 'aluno' ? 'aluno' : 'default'))))))
                    }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td>
                    @if($user->role === 'aluno')
                        {{-- Para alunos, mostra a escola através da turma --}}
                        @if($user->turma && $user->turma->escola)
                            {{ $user->turma->escola->nome }}
                        @else
                            N/A
                        @endif
                    @else
                        {{-- Para outros usuários, mostra através do relacionamento escolas --}}
                        @if($user->escolas->isNotEmpty())
                            <ul class="escola-list">
                                @foreach($user->escolas as $escola)
                                    <li class="escola-item">
                                        {{ $escola->nome }}
                                        @if($escola->pivot->created_at)
                                            <small class="text-muted">({{ $escola->pivot->created_at->format('d/m/Y') }})</small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            N/A
                        @endif
                    @endif
                </td>
                <td>{{ $user->cpf ? mask($user->cpf, '###.###.###-##') : 'N/A' }}</td>
                <td>{{ $user->codigo_acesso ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Rodapé -->
    <div class="footer">
        <p>Total de registros: {{ $users->count() }}</p>
        <p>Sistema Eduhabil+ - Prefeitura Municipal de Juazeiro</p>
    </div>
</body>
</html>