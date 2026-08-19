<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Orçamento</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            height: 100%;
            overflow: hidden;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f6faf8;
            display: flex;
            flex-direction: column;
        }
        .faixa1 {
            background-color: #68BD4F;
            color: white;
            padding: 25px;
            font-size: 16px;
            text-align: left;
            flex-shrink: 0;
        }
        .faixa1 .nome-cliente { margin-top: 4px; }
        .faixa2 {
            background-color: green;
            color: white;
            padding: 5px;
            font-size: 35px;
            font-weight: 550;
            text-align: center;
            flex-shrink: 0;
        }
        .corpo {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px;
            overflow: hidden;
        }
        .quadro {
            background: white;
            border: 1px solid gray;
            border-radius: 8px;
            width: 80%;
            padding: 24px;
            text-align: center;
            color: #333;
            font-size: 16px;
        }
        .botoes { margin-top: 12px; }
        .btn-voltar {
            padding: 10px 32px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.2s;
        }
        .btn-voltar:hover { background-color: orange; }
        @media (max-width: 768px) {
            .quadro { width: 95%; }
            .faixa2 { font-size: 22px; }
            .faixa1 { padding: 12px; font-size: 13px; }
        }
    </style>
</head>
<body>

    <div class="faixa1">
        <div>{{ session('sistema', 'SISTEMA DE ORÇAMENTOS') }}</div>
        <div class="nome-cliente">{{ session('nomecliente', 'CLIENTE PADRÃO') }}</div>
    </div>
    <div class="faixa2">
        ORÇAMENTO EM EDIÇÃO
    </div>

    <div class="corpo">
        <div class="quadro">
            <p>Edição do orçamento <strong>#{{ $orcamento }}</strong> (status: Orçamento Aberto).</p>
            <p style="margin-top:8px; color:#888;">Funcionalidade de edição em desenvolvimento.</p>
        </div>
        <div class="botoes">
            <a href="{{ route('orc-abertos') }}" class="btn-voltar">Voltar</a>
        </div>
    </div>

</body>
</html>