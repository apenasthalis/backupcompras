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
        .quadro1 .empresa-info {
            font-size: 14px;
            margin-bottom: 10px;
        }
        .quadro1 .empresa-info .nome-empresa {
            font-weight: bold;
            color: #111;
        }
        .quadro1 .trocar-empresa {
            font-size: 13px;
            color: #19703a;
            cursor: pointer;
            text-decoration: underline;
            margin-left: 8px;
        }
        .produto-search {
            position: relative;
            display: inline-block;
            width: 70%;
            vertical-align: middle;
        }
        .produto-search input {
            width: 100%;
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .produto-search input:focus {
            outline: none;
            border-color: #68BD4F;
        }
        .produto-sugestoes {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ccc;
            border-top: none;
            border-radius: 0 0 4px 4px;
            max-height: 220px;
            overflow-y: auto;
            z-index: 100;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .produto-sugestao {
            padding: 8px 10px;
            font-size: 14px;
            cursor: pointer;
        }
        .produto-sugestao:hover,
        .produto-sugestao.ativa {
            background: #e8f5e9;
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
        EMPRESA: <span id="lblEmpresa">CARREGANDO...</span><br>
        ENDEREÇO: <span id="lblEndereco">...</span><br>
        CLIENTE: {{ auth()->user()->name ?? 'CLIENTE PADRÃO' }}<br>
        ENDEREÇO DE ENTREGA: {{ (auth()->user()->endereco ?? 'Endereço não informado') . (auth()->user()->cidade ? ', ' . auth()->user()->cidade : '') . (auth()->user()->estado ? ' - ' . auth()->user()->estado : '') }}
    </div>
    <div class="faixa2">
        {{ auth()->user()->sistema ?? 'SISTEMA DE ORÇAMENTOS' }}
    </div>

    <div class="corpo">
        <div class="quadro1">
            <div class="empresa-info">
                <label>Empresa:</label>
                <span class="nome-empresa" id="lblNomeEmpresa">CARREGANDO...</span>
                <span class="trocar-empresa" onclick="trocarEmpresa()">Trocar Empresa</span>
                <br>
                <span id="lblEnderecoEmpresa" style="font-size:13px; color:#666;"></span>
            </div>
            <label for="inputProduto">Produto:</label>
            <div class="produto-search">
                <input type="text" id="inputProduto" placeholder="Digite para pesquisar o produto..." autocomplete="off">
                <div class="produto-sugestoes" id="produtoSugestoes"></div>
            </div>
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
        let empresas = [];
        let empresaSelecionada = null;
        let produtoSearchTimer = null;
        let sugestoesProdutos = [];
        let sugestaoAtiva = -1;
        const inputProduto = document.getElementById('inputProduto');
        const boxSugestoes = document.getElementById('produtoSugestoes');

        function getToken() {
            return localStorage.getItem('jwt_token');
        }

        function setMessage(msg, type) {
            const el = document.getElementById('mensagem');
            el.textContent = msg;
            el.className = 'mensagem' + (type === 'error' ? ' error-msg' : type === 'success' ? ' success-msg' : '');
            if (type === 'error') showModal(msg);
        }

        function atualizarHeaderEmpresa() {
            const empresa = empresas.find(function(e) { return e.empcontad === empresaSelecionada; });
            document.getElementById('lblEmpresa').textContent = empresa ? empresa.empnome : '—';
            document.getElementById('lblEndereco').textContent = empresa
                ? (empresa.empendereco + (empresa.empcidade ? ', ' + empresa.empcidade : '') + (empresa.empestado ? ' - ' + empresa.empestado : ''))
                : '—';
            document.getElementById('lblNomeEmpresa').textContent = empresa ? empresa.empnome : '—';
            document.getElementById('lblEnderecoEmpresa').textContent = empresa
                ? (empresa.empendereco + (empresa.empcidade ? ', ' + empresa.empcidade : '') + (empresa.empestado ? ' - ' + empresa.empestado : ''))
                : '—';
        }

        function getEmpresaSelecionada() {
            try {
                const raw = localStorage.getItem('empresa_selecionada');
                return raw ? JSON.parse(raw) : null;
            } catch (err) {
                return null;
            }
        }

        function trocarEmpresa() {
            window.location.href = '/jc-euprecisode';
        }

        function loadEmpresaSelecionada() {
            const salva = getEmpresaSelecionada();
            if (!salva || !salva.empcontad) {
                window.location.href = '/jc-euprecisode';
                return;
            }

            empresas = [salva];
            empresaSelecionada = salva.empcontad;
            atualizarHeaderEmpresa();
        }

        async function buscarProdutos(termo) {
            const token = getToken();
            if (!token) {
                setMessage('Sessão expirada. Faça login novamente.', 'error');
                return;
            }

            const empresa = getEmpresaSelecionada();
            let url = '/api/products/search?q=' + encodeURIComponent(termo);
            if (empresa && empresa.segmento_id) {
                url += '&segmento_id=' + empresa.segmento_id;
            }

            try {
                const res = await fetch(url, {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                if (!res.ok) {
                    if (res.status === 401) {
                        window.location.href = '/login';
                    }
                    return;
                }
                sugestoesProdutos = await res.json();
                sugestaoAtiva = -1;
                renderizarSugestoes();
            } catch (err) {
                // falha de rede não deve interromper a digitação
            }
        }

        function renderizarSugestoes() {
            boxSugestoes.innerHTML = '';
            if (sugestoesProdutos.length === 0) {
                boxSugestoes.style.display = 'none';
                return;
            }
            sugestoesProdutos.forEach(function(p, i) {
                const div = document.createElement('div');
                div.className = 'produto-sugestao' + (i === sugestaoAtiva ? ' ativa' : '');
                div.textContent = p.name;
                div.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    selecionarSugestao(i);
                });
                boxSugestoes.appendChild(div);
            });
            boxSugestoes.style.display = 'block';
        }

        function selecionarSugestao(i) {
            const product = sugestoesProdutos[i];
            fecharSugestoes();
            if (product) adicionarItem(product);
        }

        function fecharSugestoes() {
            boxSugestoes.style.display = 'none';
            boxSugestoes.innerHTML = '';
            sugestoesProdutos = [];
            sugestaoAtiva = -1;
        }

        function dispararBuscaImediata() {
            clearTimeout(produtoSearchTimer);
            const termo = inputProduto.value.trim();
            if (termo) buscarProdutos(termo);
        }

        inputProduto.addEventListener('input', function() {
            clearTimeout(produtoSearchTimer);
            const termo = this.value.trim();
            if (termo.length === 0) {
                fecharSugestoes();
                return;
            }
            produtoSearchTimer = setTimeout(function() {
                buscarProdutos(termo);
            }, 1500);
        });

        inputProduto.addEventListener('keydown', function(e) {
            const aberto = boxSugestoes.style.display === 'block' && sugestoesProdutos.length > 0;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (aberto) {
                    sugestaoAtiva = (sugestaoAtiva + 1) % sugestoesProdutos.length;
                    renderizarSugestoes();
                } else {
                    dispararBuscaImediata();
                }
                return;
            }
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (aberto) {
                    sugestaoAtiva = (sugestaoAtiva - 1 + sugestoesProdutos.length) % sugestoesProdutos.length;
                    renderizarSugestoes();
                }
                return;
            }
            if (e.key === 'Enter') {
                e.preventDefault();
                if (aberto && sugestaoAtiva >= 0) {
                    selecionarSugestao(sugestaoAtiva);
                } else if (aberto) {
                    selecionarSugestao(0);
                } else if (this.value.trim()) {
                    adicionarItemManual(this.value.trim());
                }
                return;
            }
            if (e.key === 'Escape') {
                fecharSugestoes();
            }
        });

        inputProduto.addEventListener('blur', function() {
            setTimeout(fecharSugestoes, 200);
        });

        function adicionarItem(product) {
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
            inputProduto.value = '';
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
            inputProduto.value = '';
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
            inputProduto.focus();
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
            setMessage('Salvando orçamento...');

            let errors = 0;

            let idorc = null;
            try {
                const resOrc = await fetch('/api/orcamentos', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ idempresa: empresaSelecionada })
                });
                if (!resOrc.ok) {
                    errors++;
                } else {
                    const orcData = await resOrc.json();
                    idorc = orcData.orcamento ? orcData.orcamento.idorc : null;
                }
            } catch (err) {
                errors++;
            }

            if (!errors && idorc === null) {
                errors++;
            }

            for (const item of valid) {
                try {
                    const res = await fetch('/api/user-products', {
                        method: 'POST',
                        headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({
                            product_id: item.product_id || 1,
                            orcamento_id: idorc,
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
                setMessage('Orçamento ' + (idorc !== null ? '#' + idorc : '') + ' criado com ' + valid.length + ' item(ns) salvo(s) com sucesso!', 'success');
                itens = [];
                itemSeq = 0;
                renderizarTabela();
            } else {
                setMessage(errors + ' erro(s) ao salvar orçamento. Tente novamente.', 'error');
            }
        }

        loadEmpresaSelecionada();
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
