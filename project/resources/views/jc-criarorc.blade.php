<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Orçamento</title>
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
        .quadro1 {
            background: white;
            border: 1px solid gray;
            border-radius: 8px;
            width: 70%;
            padding: 10px 16px;
            margin-bottom: 8px;
            flex-shrink: 0;
        }
        .quadro1 label {
            font-weight: bold;
            margin-right: 8px;
        }
        .quadro1 select {
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            width: 70%;
        }
        .quadro2-wrapper {
            width: 70%;
            flex: 1;
            overflow-y: auto;
            border: 1px solid gray;
            border-radius: 8px;
            background: white;
            margin-bottom: 8px;
        }
        .quadro2 {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .quadro2 th {
            background: #e8f5e9;
            padding: 8px 6px;
            text-align: left;
            border-bottom: 2px solid #c8e6c9;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .quadro2 td {
            padding: 4px 6px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .quadro2 td .handle {
            cursor: grab;
            color: #999;
            font-size: 18px;
            user-select: none;
        }
        .quadro2 input, .quadro2 select {
            width: 100%;
            padding: 4px 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
        }
        .quadro2 input.qty {
            text-align: right;
            width: 70px;
        }
        .quadro2 .btn-del {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            font-size: 18px;
        }
        .quadro2 .btn-del:hover {
            color: #dc2626;
        }
        .botoes {
            display: flex;
            gap: 12px;
            flex-shrink: 0;
            margin-bottom: 6px;
        }
        .btn-concluir {
            padding: 10px 32px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-concluir:hover {
            background-color: orange;
        }
        .btn-concluir:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .btn-voltar {
            padding: 10px 32px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-voltar:hover {
            background-color: orange;
        }
        .mensagem {
            text-align: center;
            font-size: 14px;
            color: orange;
            flex-shrink: 0;
            min-height: 20px;
        }
        .error-msg {
            color: #e74c3c;
        }
        .success-msg {
            color: #27ae60;
        }
        @media (max-width: 768px) {
            .quadro1, .quadro2-wrapper { width: 95%; }
            .faixa2 { font-size: 22px; }
            .faixa1 { padding: 12px; font-size: 13px; }
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
        EMPRESA: {{ session('nomeempresa', 'EMPRESA PADRÃO LTDA') }}<br>
        ENDEREÇO: {{ session('endempresa', 'Rua Exemplo, 123') }}<br>
        CLIENTE: {{ session('nomecliente', 'CLIENTE PADRÃO') }}
    </div>
    <div class="faixa2">
        {{ session('sistema', 'SISTEMA DE ORÇAMENTOS') }}
    </div>

    <div class="corpo">
        <div class="quadro1">
            <label for="selectProduto">Produto:</label>
            <select id="selectProduto">
                <option value="">Carregando produtos...</option>
            </select>
        </div>

        <div class="quadro2-wrapper">
            <table class="quadro2" id="tabelaItens">
                <thead>
                    <tr>
                        <th style="width:40px;"></th>
                        <th style="width:40px;">Item</th>
                        <th>Descrição</th>
                        <th>Marca</th>
                        <th>Un</th>
                        <th style="width:90px;">Qtd</th>
                        <th style="width:30px;"></th>
                    </tr>
                </thead>
                <tbody id="corpoTabela">
                </tbody>
            </table>
        </div>

        <div class="botoes">
            <button class="btn-concluir" id="btnConcluir" onclick="concluir()">Concluir</button>
            <a href="{{ route('menu') }}" class="btn-voltar">Voltar</a>
        </div>

        <div class="mensagem" id="mensagem"></div>
    </div>

    <script>
        let itens = [];
        let itemSeq = 0;
        let products = [];

        function getToken() {
            return localStorage.getItem('jwt_token');
        }

        function setMessage(msg, type) {
            const el = document.getElementById('mensagem');
            el.textContent = msg;
            el.className = 'mensagem' + (type === 'error' ? ' error-msg' : type === 'success' ? ' success-msg' : '');
            if (type === 'error') showModal(msg);
        }

        async function loadProducts() {
            const token = getToken();
            if (!token) {
                setMessage('Sessão expirada. Faça login novamente.', 'error');
                return;
            }

            try {
                const res = await fetch('/api/products', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                if (!res.ok) {
                    if (res.status === 401) {
                        window.location.href = '/login';
                        return;
                    }
                    setMessage('Erro ao carregar produtos', 'error');
                    return;
                }
                products = await res.json();
                const select = document.getElementById('selectProduto');
                select.innerHTML = '<option value="">-- Selecione o produto --</option>';
                products.forEach(function(p) {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.name;
                    select.appendChild(opt);
                });
            } catch (err) {
                setMessage('Erro de conexão ao carregar produtos', 'error');
            }
        }

        document.getElementById('selectProduto').addEventListener('change', function() {
            if (this.value) {
                adicionarItem(this.value);
            }
        });

        document.getElementById('selectProduto').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (this.value && !Array.from(this.options).some(o => o.value === this.value)) {
                    adicionarItemManual(this.value);
                }
            }
        });

        function adicionarItem(productId) {
            const product = products.find(function(p) { return p.id == productId; });
            if (!product) return;
            itemSeq++;
            itens.push({
                id: Date.now() + Math.random(),
                product_id: product.id,
                item: itemSeq,
                description: product.name,
                brand: '',
                unit: '',
                qty: ''
            });
            document.getElementById('selectProduto').value = '';
            renderizarTabela();
            setMessage('');
        }

        function adicionarItemManual(texto) {
            itemSeq++;
            itens.push({
                id: Date.now() + Math.random(),
                product_id: null,
                item: itemSeq,
                description: texto,
                brand: '',
                unit: '',
                qty: ''
            });
            document.getElementById('selectProduto').value = '';
            renderizarTabela();
            setMessage('');
        }

        function renderizarTabela() {
            const tbody = document.getElementById('corpoTabela');
            tbody.innerHTML = '';
            itens.forEach(function(item, index) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><span class="handle" draggable="true" data-index="${index}">&#9776;</span></td>
                    <td>${item.item}</td>
                    <td><input type="text" value="${item.description.replace(/"/g, '&quot;')}" onchange="itens[${index}].description = this.value"></td>
                    <td><input type="text" value="${item.brand.replace(/"/g, '&quot;')}" onchange="itens[${index}].brand = this.value"></td>
                    <td><input type="text" value="${item.unit}" style="width:50px;" onchange="itens[${index}].unit = this.value"></td>
                    <td><input type="number" class="qty" value="${item.qty}" min="0" step="0.01" onchange="itens[${index}].qty = this.value; verificarQty(${index})" onkeydown="if(event.key==='Enter'){verificarQty(${index});}"></td>
                    <td><button class="btn-del" onclick="deletarItem(${index})">&times;</button></td>
                `;
                tbody.appendChild(tr);

                const handle = tr.querySelector('.handle');
                handle.addEventListener('dragstart', function(e) {
                    e.dataTransfer.setData('text/plain', index);
                });
                handle.addEventListener('dragend', function(e) {
                    e.preventDefault();
                });
                tr.addEventListener('dragover', function(e) {
                    e.preventDefault();
                });
                tr.addEventListener('drop', function(e) {
                    e.preventDefault();
                    const from = parseInt(e.dataTransfer.getData('text/plain'));
                    const to = index;
                    if (from !== to) {
                        const movido = itens.splice(from, 1)[0];
                        itens.splice(to, 0, movido);
                        renummerarItens();
                        renderizarTabela();
                    }
                });
            });
            setTimeout(function() {
                const ultimoInput = tbody.querySelector('tr:last-child input.qty');
                if (ultimoInput) ultimoInput.focus();
            }, 100);
        }

        function renummerarItens() {
            itens.forEach(function(item, i) { item.item = i + 1; });
        }

        function deletarItem(index) {
            itens.splice(index, 1);
            renummerarItens();
            renderizarTabela();
            setMessage('');
        }

        function verificarQty(index) {
            const qty = parseFloat(itens[index].qty);
            if (isNaN(qty) || qty <= 0) {
                setMessage('Quantidade deve ser maior que 0!', 'error');
                return true;
            }
            setMessage('');
            document.getElementById('selectProduto').focus();
            return false;
        }

        async function concluir() {
            const token = getToken();
            if (!token) {
                setMessage('Sessão expirada. Faça login novamente.', 'error');
                return;
            }

            const valid = itens.filter(function(i) {
                const qty = parseFloat(i.qty);
                return !isNaN(qty) && qty > 0;
            });

            if (valid.length === 0) {
                setMessage('Adicione pelo menos um item com quantidade maior que 0!', 'error');
                return;
            }

            document.getElementById('btnConcluir').disabled = true;
            setMessage('Salvando itens...');

            let errors = 0;
            for (const item of valid) {
                try {
                    const res = await fetch('/api/user-products', {
                        method: 'POST',
                        headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({
                            product_id: item.product_id || 1,
                            description: item.description,
                            brand: item.brand,
                            unit: item.unit,
                            quantity: parseFloat(item.qty),
                        })
                    });
                    if (!res.ok) {
                        errors++;
                    }
                } catch (err) {
                    errors++;
                }
            }

            document.getElementById('btnConcluir').disabled = false;

            if (errors === 0) {
                setMessage(valid.length + ' item(ns) salvo(s) com sucesso!', 'success');
                itens = [];
                itemSeq = 0;
                renderizarTabela();
            } else {
                setMessage(errors + ' item(ns) falhou ao salvar', 'error');
            }
        }

        loadProducts();
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
