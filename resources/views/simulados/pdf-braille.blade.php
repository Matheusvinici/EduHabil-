<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url({{ storage_path('fonts/DejaVuSans.ttf') }}) format('truetype');
        }
        
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12pt;
            line-height: 1.4;
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
        .pergunta {
            margin-bottom: 15px;
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .braille-text {
            white-space: pre-wrap;
            word-wrap: break-word;
            margin: 3px 0;
        }
        .alternativas {
            margin-left: 15px;
        }
        .imagem-pergunta {
            max-width: 200px;
            max-height: 150px;
            display: block;
            margin: 8px auto;
            page-break-inside: avoid;
        }
        .resposta {
            margin-top: 8px;
            font-weight: bold;
        }
        .numero-pergunta {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="header">
        <div class="logo-container">
            <img src="{{ public_path('images/logoprefeitura.png') }}" class="logo" alt="Prefeitura de Juazeiro-BA">
            <div>
                <h2>Secretaria Municipal de Educação</h2>
                <p class="municipio">Prefeitura de Juazeiro-BA, presente no futuro da gente.</p>
            </div>
        </div>
       
    </div>
    <div class="header">
        <h1 class="braille-text">{{ $converterParaBraille('SIMULADO: ' . $simulado->nome) }}</h1>
        @if($simulado->descricao)
            <p class="braille-text">{{ $converterParaBraille($simulado->descricao) }}</p>
        @endif
    </div>
    
    @foreach ($simulado->perguntas as $pergunta)
        <div class="pergunta">
            <p class="braille-text">
                <span class="numero-pergunta">{{ $converterParaBraille($loop->iteration . '.') }}</span>
                {{ $converterParaBraille($pergunta->enunciado) }}
            </p>
            
            @if (!empty($pergunta->imagem) && file_exists(storage_path('app/public/' . $pergunta->imagem)))
                <img src="{{ storage_path('app/public/' . $pergunta->imagem) }}" 
                     alt="{{ $converterParaBraille('Imagem referente a pergunta ' . $loop->iteration) }}"
                     class="imagem-pergunta">
            @endif
            
            <div class="alternativas">
                @foreach(['a', 'b', 'c', 'd'] as $alt)
                    <p class="braille-text">
                        <strong>{{ $converterParaBraille(strtoupper($alt) . ') ') }}</strong>
                        {{ $converterParaBraille($pergunta->{'alternativa_'.$alt}) }}
                    </p>
                @endforeach
            </div>
            
            
        </div>
    @endforeach
</body>
</html>