<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background-color: #add8e6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header-faixa1 {
            background-color: #68BD4F;
            color: white;
            padding: 25px;
            font-size: 16px;
            text-align: left;
        }
        .header-faixa1 .nome-usuario {
            font-size: 14px;
            margin-top: 4px;
        }
        .header-faixa2 {
            background-color: green;
            color: white;
            padding: 5px;
            font-size: 35px;
            font-weight: 550;
            text-align: center;
        }
        .container {
            flex: 1;
            display: flex;
        }
        .lado-esquerdo {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 20px;
            border-right: 2px solid rgba(0,0,0,0.1);
        }
        .lado-direito {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .btn-menu {
            width: 320px;
            max-width: 90%;
            padding: 14px 0;
            background-color: green;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
        }
        .btn-menu:hover {
            background-color: orange;
        }
        .btn-imagens {
            width: 320px;
            max-width: 90%;
            padding: 40px 0;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
            transition: transform 0.2s;
        }
        .btn-imagens:hover {
            background-color: orange;
            transform: scale(1.05);
        }
        .mensagem {
            text-align: center;
            font-size: 18px;
            color: orange;
            margin-top: 10px;
        }
        @media (max-width: 768px) {
            .header-faixa2 { font-size: 24px; }
            .container { flex-direction: column; }
            .lado-esquerdo { border-right: none; border-bottom: 2px solid rgba(0,0,0,0.1); }
            .btn-menu { width: 90%; }
            .btn-imagens { width: 90%; }
        }
    </style>
</head>
<body>
    <div class="header-faixa1">
        {{ session('plataforma') }}
        <div class="nome-usuario">{{ session('nome') }}</div>
    </div>
    <div class="header-faixa2">
        {{ session('sistema') }}
    </div>

    <div class="container">
        <div class="lado-esquerdo">
            <a href="jc-cadastro.php" class="btn-menu">Dados Cadastrais</a>
            <a href="jc-criarorc.php" class="btn-menu">Criar Orçamentos</a>
            <a href="jc-orc-abertos.php" class="btn-menu">Orçamentos Abertos</a>
            <a href="jc-orc-prontos.php" class="btn-menu">Orçamentos Prontos</a>
            <a href="jc-orc-aprovados.php" class="btn-menu">Orçamentos Aprovados</a>
            <a href="jc-orc-entregues.php" class="btn-menu">Orçamentos Entregues</a>
            <a href="index.php" class="btn-menu">Voltar</a>

            <div class="mensagem">
                @if(session('mensagem'))
                    {{ session('mensagem') }}
                @endif
            </div>
        </div>

        <div class="lado-direito">
            <a href="{{ route('imagens') }}" class="btn-imagens">&#128247; Gerar Imagens</a>
        </div>
    </div>
</body>
</html>
