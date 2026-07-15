<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Completo</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #0f172a;
        }
        header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
        }
        .top-bar {
            padding: 16px 32px;
            font-size: 13px;
            color: #64748b;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
        }
        .top-bar .usuario {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            color: #334155;
        }
        .bottom-bar {
            padding: 28px 32px;
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .corpo {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 32px;
        }
        .btn-menu {
            width: 340px;
            max-width: 90%;
            padding: 16px 24px;
            background: #fff;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            text-align: left;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
        }
        .btn-menu:hover {
            border-color: #22c55e;
            box-shadow: 0 4px 16px rgba(34,197,94,0.12);
            transform: scale(1.02);
        }
        .btn-menu .icone {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            background: #f0fdf4;
            color: #16a34a;
        }
        .btn-menu .label {
            flex: 1;
        }
        .btn-menu .seta {
            color: #94a3b8;
            font-size: 14px;
            transition: transform 0.2s ease;
        }
        .btn-menu:hover .seta {
            transform: translateX(4px);
            color: #22c55e;
        }
        .btn-voltar {
            margin-top: 8px;
        }
        .btn-voltar .icone {
            background: #f1f5f9;
            color: #64748b;
        }
        .btn-voltar:hover {
            border-color: #64748b;
            box-shadow: 0 4px 16px rgba(100,116,139,0.12);
        }
        .btn-voltar:hover .seta {
            color: #64748b;
        }
        .mensagem {
            text-align: center;
            font-size: 14px;
            color: #ea580c;
            margin-top: 16px;
        }
        @media (max-width: 600px) {
            .top-bar { flex-direction: column; gap: 4px; align-items: flex-start; padding: 12px 20px; }
            .bottom-bar { font-size: 18px; padding: 20px; }
            .corpo { padding: 20px; }
            .btn-menu { width: 100%; padding: 14px 18px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="top-bar">
            <span>{{ session('plataforma') }}</span>
            <span class="usuario">{{ session('nome') }}</span>
        </div>
        <div class="bottom-bar">{{ session('sistema') }}</div>
    </header>

    <div class="corpo">
        <a href="jc-cadastro.php" class="btn-menu">
            <span class="icone">&#128100;</span>
            <span class="label">Dados Cadastrais</span>
            <span class="seta">&#8594;</span>
        </a>
        <a href="jc-criarorc.php" class="btn-menu">
            <span class="icone">&#128221;</span>
            <span class="label">Criar Orçamentos</span>
            <span class="seta">&#8594;</span>
        </a>
        <a href="jc-orc-abertos.php" class="btn-menu">
            <span class="icone">&#128203;</span>
            <span class="label">Orçamentos Abertos</span>
            <span class="seta">&#8594;</span>
        </a>
        <a href="jc-orc-prontos.php" class="btn-menu">
            <span class="icone">&#9989;</span>
            <span class="label">Orçamentos Prontos</span>
            <span class="seta">&#8594;</span>
        </a>
        <a href="jc-orc-aprovados.php" class="btn-menu">
            <span class="icone">&#128077;</span>
            <span class="label">Orçamentos Aprovados</span>
            <span class="seta">&#8594;</span>
        </a>
        <a href="jc-orc-entregues.php" class="btn-menu">
            <span class="icone">&#128230;</span>
            <span class="label">Orçamentos Entregues</span>
            <span class="seta">&#8594;</span>
        </a>
        <a href="index.php" class="btn-menu btn-voltar">
            <span class="icone">&#8592;</span>
            <span class="label">Voltar</span>
            <span class="seta">&#8594;</span>
        </a>

        @if(session('mensagem'))
            <div class="mensagem">{{ session('mensagem') }}</div>
        @endif
    </div>
</body>
</html>
