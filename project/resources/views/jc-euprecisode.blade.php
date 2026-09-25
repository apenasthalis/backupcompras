<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eu Preciso De</title>
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
        .faixa1 .endereco-cliente {
            margin-top: 2px;
            font-size: 13px;
            opacity: 0.95;
        }
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
            padding: 10px;
            overflow: hidden;
        }
        .filtro-box {
            background: white;
            border: 1px solid gray;
            border-radius: 8px;
            width: 70%;
            padding: 10px 16px;
            margin-bottom: 8px;
            flex-shrink: 0;
        }
        .filtro-box label {
            font-weight: bold;
            margin-right: 8px;
        }
        .filtro-box select {
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            width: 60%;
        }
        .lista-wrapper {
            width: 70%;
            flex: 1;
            overflow-y: auto;
            border: 1px solid gray;
            border-radius: 8px;
            background: white;
            margin-bottom: 8px;
        }
        .empresa-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background 0.15s;
        }
        .empresa-card:hover {
            background: #e8f5e9;
        }
        .empresa-card:last-child {
            border-bottom: none;
        }
        .empresa-card .info .nome {
            font-weight: bold;
            font-size: 15px;
            color: #111;
        }
        .empresa-card .info .detalhe {
            font-size: 13px;
            color: #666;
            margin-top: 2px;
        }
        .empresa-card .segmento {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }
        .mensagem {
            text-align: center;
            font-size: 14px;
            color: orange;
            flex-shrink: 0;
            min-height: 20px;
        }
        .error-msg { color: #e74c3c; }
        .empty {
            padding: 24px;
            text-align: center;
            color: #888;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .filtro-box, .lista-wrapper { width: 95%; }
            .faixa2 { font-size: 22px; }
            .faixa1 { padding: 12px; font-size: 13px; }
            .filtro-box select { width: 100%; }
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

    <div class="faixa1">
        <div class="nome-cliente">{{ auth()->user()->name ?? 'CLIENTE PADRÃO' }}</div>
        <div class="endereco-cliente">
            {{ (auth()->user()->endereco ?? '') . (auth()->user()->cidade ? ', ' . auth()->user()->cidade : '') . (auth()->user()->estado ? ' - ' . auth()->user()->estado : '') }}
        </div>
    </div>
    <div class="faixa2">
        EU PRECISO DE
    </div>

    <div class="corpo">
        <div class="filtro-box">
            <label for="selectSegmento">Eu preciso de:</label>
            <select id="selectSegmento">
                <option value="">Todos os segmentos</option>
            </select>
        </div>

        <div class="lista-wrapper" id="listaWrapper">
            <div class="empty">Carregando empresas...</div>
        </div>

        <div class="mensagem" id="mensagem"></div>
    </div>

    <script>
        let empresas = [];
        let segmentos = [];

        function getToken() {
            return localStorage.getItem('jwt_token');
        }

        function setMessage(msg, type) {
            const el = document.getElementById('mensagem');
            el.textContent = msg;
            el.className = 'mensagem' + (type === 'error' ? ' error-msg' : '');
            if (type === 'error') showModal(msg);
        }

        function montarCidade() {
            const el = document.querySelector('.endereco-cliente');
            if (el) {
                const txt = el.textContent.trim();
                if (txt) return txt;
            }
            return '';
        }

        async function loadSegmentos() {
            const token = getToken();
            if (!token) {
                setMessage('Sessão expirada. Faça login novamente.', 'error');
                return;
            }
            try {
                const res = await fetch('/api/empresas/segmentos', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                if (!res.ok) {
                    if (res.status === 401) { window.location.href = '/login'; return; }
                    return;
                }
                segmentos = await res.json();
                const select = document.getElementById('selectSegmento');
                select.innerHTML = '<option value="">Todos os segmentos</option>';
                segmentos.forEach(function (s) {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.name;
                    select.appendChild(opt);
                });
            } catch (err) {
                setMessage('Erro de conexão ao carregar segmentos', 'error');
            }
        }

        async function loadEmpresas(segmento) {
            const token = getToken();
            if (!token) {
                setMessage('Sessão expirada. Faça login novamente.', 'error');
                return;
            }

            try {
                const url = '/api/empresas' + (segmento ? '?segmento_id=' + encodeURIComponent(segmento) : '');
                const res = await fetch(url, {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                if (!res.ok) {
                    if (res.status === 401) { window.location.href = '/login'; return; }
                    setMessage('Erro ao carregar empresas', 'error');
                    return;
                }
                empresas = await res.json();
                renderizarLista();
            } catch (err) {
                setMessage('Erro de conexão ao carregar empresas', 'error');
            }
        }

        function renderizarLista() {
            const wrapper = document.getElementById('listaWrapper');
            wrapper.innerHTML = '';

            if (empresas.length === 0) {
                const div = document.createElement('div');
                div.className = 'empty';
                div.textContent = 'Nenhuma empresa encontrada na sua cidade.';
                wrapper.appendChild(div);
                return;
            }

            empresas.forEach(function (e) {
                const card = document.createElement('div');
                card.className = 'empresa-card';

                const info = document.createElement('div');
                info.className = 'info';

                const nome = document.createElement('div');
                nome.className = 'nome';
                nome.textContent = e.empnome;
                info.appendChild(nome);

                const detalhe = document.createElement('div');
                detalhe.className = 'detalhe';
                const endereco = e.empendereco || '';
                const cidade = e.empcidade ? (endereco ? ', ' : '') + e.empcidade : '';
                const estado = e.empestado ? (cidade ? ' - ' : '') + e.empestado : '';
                detalhe.textContent = endereco + cidade + estado;
                info.appendChild(detalhe);

                card.appendChild(info);

                if (e.segmento) {
                    const seg = document.createElement('span');
                    seg.className = 'segmento';
                    seg.textContent = e.segmento;
                    card.appendChild(seg);
                }

                card.addEventListener('click', function () {
                    selecionarEmpresa(e);
                });

                wrapper.appendChild(card);
            });
        }

        function selecionarEmpresa(empresa) {
            localStorage.setItem('empresa_selecionada', JSON.stringify(empresa));
            window.location.href = '/jc-criarorc';
        }

        document.getElementById('selectSegmento').addEventListener('change', function () {
            const segmento = this.value;
            if (segmento) {
                loadEmpresas(segmento);
            } else {
                loadEmpresas(null);
            }
        });

        (function init() {
            loadSegmentos();
            loadEmpresas(null);
        })();
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