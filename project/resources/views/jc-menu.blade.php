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
        .corpo {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
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
        .mensagem {
            text-align: center;
            font-size: 18px;
            color: orange;
            margin-top: 10px;
        }
        @media (max-width: 600px) {
            .header-faixa2 { font-size: 24px; }
            .btn-menu { width: 90%; }
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

    <div class="corpo">
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
</body>
</html>
