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
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .header-faixa1 .nome-usuario {
            font-size: 14px;
            margin-top: 4px;
        }
        .header-faixa1 .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.2s;
        }
        .header-faixa1 .logout-btn:hover {
            background: rgba(255,255,255,0.3);
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
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .menu-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            width: 100%;
            max-width: 400px;
        }
        .btn-menu {
            width: 100%;
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
        @media (max-width: 768px) {
            .header-faixa2 { font-size: 24px; }
            .btn-menu { width: 100%; }
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
    <div class="header-faixa1">
        <div>
            <div>{{ auth()->user()->plataforma ?? '' }}</div>
            <div class="nome-usuario">{{ auth()->user()->name }}</div>
        </div>
        <div>
            <button class="logout-btn" onclick="logout()">Sair</button>
        </div>
    </div>
    <div class="header-faixa2">
        {{ auth()->user()->sistema ?? 'SISTEMA DE ORÇAMENTOS' }}
    </div>

    <div class="container">
        <div class="menu-center">
            <a href="jc-cadastro.php" class="btn-menu">Dados Cadastrais</a>
            <a href="{{ route('criarorc') }}" class="btn-menu">Criar Orçamentos</a>
            <a href="jc-orc-abertos.php" class="btn-menu">Orçamentos Abertos</a>
            <a href="jc-orc-prontos.php" class="btn-menu">Orçamentos Prontos</a>
            <a href="jc-orc-aprovados.php" class="btn-menu">Orçamentos Aprovados</a>
            <a href="jc-orc-entregues.php" class="btn-menu">Orçamentos Entregues</a>
            <a href="/login" class="btn-menu">Voltar</a>
        </div>
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
