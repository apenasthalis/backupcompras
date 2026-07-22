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
            gap: 12px;
            font-weight: 500;
            color: #334155;
        }
        .top-bar .logout-btn {
            background: none;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 13px;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
        }
        .top-bar .logout-btn:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
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
        @media (max-width: 600px) {
            .top-bar { flex-direction: column; gap: 4px; align-items: flex-start; padding: 12px 20px; }
            .bottom-bar { font-size: 18px; padding: 20px; }
            .corpo { padding: 20px; }
            .btn-menu { width: 100%; padding: 14px 18px; }
        }
        .modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5); z-index: 9999;
            display: none; align-items: center; justify-content: center;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: white; border-radius: 12px; max-width: 420px;
            width: 90%; box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            overflow: hidden; animation: modalIn 0.2s ease;
        }
        @keyframes modalIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .modal-header { background: #e74c3c; color: white; padding: 16px 24px; font-size: 18px; font-weight: bold; }
        .modal-body { padding: 24px; font-size: 15px; color: #333; line-height: 1.5; }
        .modal-footer { padding: 12px 24px; text-align: right; border-top: 1px solid #eee; }
        .modal-btn { padding: 8px 24px; background: #e74c3c; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
        .modal-btn:hover { background: #c0392b; }
    </style>
</head>
<body>
    <header>
        <div class="top-bar">
            <span>{{ auth()->user()->plataforma ?? '' }}</span>
            <span class="usuario">
                {{ auth()->user()->name }}
                <button class="logout-btn" onclick="logout()">Sair</button>
            </span>
        </div>
        <div class="bottom-bar">{{ auth()->user()->sistema ?? 'SISTEMA DE ORÇAMENTOS' }}</div>
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
        <a href="/login" class="btn-menu btn-voltar">
            <span class="icone">&#8592;</span>
            <span class="label">Voltar</span>
            <span class="seta">&#8594;</span>
        </a>
    </div>

    <script>
        function logout() {
            localStorage.removeItem('jwt_token');
            document.cookie = 'jwt_token=; path=/; max-age=0';
            window.location.href = '/login';
        }
    </script>
    <div id="errorModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">Erro</div>
            <div class="modal-body" id="modalMessage"></div>
            <div class="modal-footer"><button class="modal-btn" onclick="closeModal()">OK</button></div>
        </div>
    </div>
    <script>
        function showModal(msg) {
            document.getElementById('modalMessage').textContent = msg;
            document.getElementById('errorModal').classList.add('active');
        }
        function closeModal() {
            document.getElementById('errorModal').classList.remove('active');
        }
        document.getElementById('errorModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
        window.addEventListener('unhandledrejection', function(event) {
            event.preventDefault();
            var msg = 'Erro inesperado. Tente novamente.';
            if (event.reason) { msg = event.reason.message || event.reason || msg; }
            showModal('Erro: ' + msg);
        });
        window.onerror = function(msg) { showModal('Erro inesperado: ' + msg); return true; };
    </script>
</body>
</html>
