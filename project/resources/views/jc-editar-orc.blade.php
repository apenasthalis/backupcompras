<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            justify-content: center;
            padding: 10px;
            overflow: hidden;
        }
        .quadro-add {
            background: white;
            border: 1px solid gray;
            border-radius: 8px;
            width: 90%;
            padding: 10px 16px;
            margin-bottom: 8px;
            flex-shrink: 0;
        }
        .quadro-add label {
            font-weight: bold;
            margin-right: 8px;
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
            width: 90%;
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
        .quadro2 input {
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
        .quadro2 .item-num {
            color: #666;
            font-weight: bold;
        }
        .vazio {
            padding: 20px;
            text-align: center;
            color: #888;
        }
        .botoes {
            display: flex;
            gap: 12px;
            flex-shrink: 0;
            margin-bottom: 6px;
        }
        .btn-salvar {
            padding: 10px 32px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-salvar:hover { background-color: orange; }
        .btn-salvar:disabled { opacity: 0.6; cursor: not-allowed; }
        .btn-cobrar {
            padding: 10px 32px;
            background-color: #f59e0b;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.2s;
        }
        .btn-cobrar:hover { background-color: orange; }
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
        .mensagem {
            text-align: center;
            font-size: 16px;
            color: orange;
            flex-shrink: 0;
            min-height: 22px;
            margin-top: 8px;
        }
        .mensagem.error { color: #e74c3c; }
        .mensagem.success { color: #27ae60; }
        @media (max-width: 768px) {
            .quadro-add, .quadro2-wrapper { width: 95%; }
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
        <div>EMPRESA: {{ $orcamento->empnome ?? '—' }}</div>
        <div class="nome-cliente">CLIENTE: {{ $orcamento->clinome ?? 'CLIENTE PADRÃO' }}</div>
        <div class="endereco-cliente">
            ENDEREÇO DE ENTREGA:
            {{ $orcamento->cliendereco ?? 'Endereço não informado' }}
            @if ($orcamento->clicidade ?? false), {{ $orcamento->clicidade }}@endif
            @if ($orcamento->cliestado ?? false) - {{ $orcamento->cliestado }}@endif
        </div>
    </div>
    <div class="faixa2">
        ORÇAMENTO EM EDIÇÃO #{{ $orcamento->idorc }}
    </div>

    <div class="corpo">
        <div class="quadro-add">
            <label for="inputProduto">Adicionar produto:</label>
            <div class="produto-search">
                <input type="text" id="inputProduto" placeholder="Digite para pesquisar o produto..." autocomplete="off">
                <div class="produto-sugestoes" id="produtoSugestoes"></div>
            </div>
        </div>

        <div class="quadro2-wrapper">
            <table class="quadro2" id="tabelaItens">
                <thead>
                    <tr>
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
            <button class="btn-salvar" id="btnSalvar" onclick="salvar()">Salvar Alterações</button>
            <form method="POST" action="{{ route('editar-orc.cobrar', ['orcamento' => $orcamento->idorc]) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-cobrar">Cobrar Orçamento</button>
            </form>
            <a href="{{ route('orc-abertos') }}" class="btn-voltar">Voltar</a>
        </div>

        <div class="mensagem {{ session('success') ? 'success' : (session('error') ? 'error' : '') }}" id="mensagem">
            {{ session('success') ?? session('error') ?? '' }}
        </div>
    </div>

    <form id="formSalvar" method="POST" action="{{ route('editar-orc.salvar', ['orcamento' => $orcamento->idorc]) }}" style="display:none;">
        @csrf
        <div id="camposItens"></div>
        <div id="camposDeletar"></div>
    </form>

    <script>
        let itens = [];
        let deletar = [];
        let produtoSearchTimer = null;
        let sugestoesProdutos = [];
        let sugestaoAtiva = -1;
        const inputProduto = document.getElementById('inputProduto');
        const boxSugestoes = document.getElementById('produtoSugestoes');

        @php
            $itensIniciais = $orcamento->itens->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'brand' => $item->brand,
                    'unit' => $item->unit,
                    'qty' => rtrim(rtrim(number_format((float)$item->quantity, 2, '.', ''), '0'), '.'),
                ];
            })->values()->all();
        @endphp
        const itensIniciais = @json($itensIniciais);

        itensIniciais.forEach(function (item) {
            itens.push(item);
        });
        renderizarTabela();

        function getToken() {
            return localStorage.getItem('jwt_token');
        }

        function setMessage(msg, type) {
            const el = document.getElementById('mensagem');
            el.textContent = msg;
            el.className = 'mensagem' + (type === 'error' ? ' error' : type === 'success' ? ' success' : '');
            if (type === 'error') showModal(msg);
        }

        function fecharSugestoes() {
            boxSugestoes.style.display = 'none';
            boxSugestoes.innerHTML = '';
            sugestoesProdutos = [];
            sugestaoAtiva = -1;
        }

        function renderizarSugestoes() {
            boxSugestoes.innerHTML = '';
            if (sugestoesProdutos.length === 0) {
                boxSugestoes.style.display = 'none';
                return;
            }
            sugestoesProdutos.forEach(function (p, i) {
                const div = document.createElement('div');
                div.className = 'produto-sugestao' + (i === sugestaoAtiva ? ' ativa' : '');
                div.textContent = p.name;
                div.addEventListener('mousedown', function (e) {
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

        async function buscarProdutos(termo) {
            const token = getToken();
            if (!token) return;
            try {
                const res = await fetch('/api/products/search?q=' + encodeURIComponent(termo), {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                sugestoesProdutos = await res.json();
                sugestaoAtiva = -1;
                renderizarSugestoes();
            } catch (err) {}
        }

        inputProduto.addEventListener('input', function () {
            clearTimeout(produtoSearchTimer);
            const termo = this.value.trim();
            if (termo.length === 0) {
                fecharSugestoes();
                return;
            }
            produtoSearchTimer = setTimeout(function () {
                buscarProdutos(termo);
            }, 1200);
        });

        inputProduto.addEventListener('keydown', function (e) {
            const aberto = boxSugestoes.style.display === 'block' && sugestoesProdutos.length > 0;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (aberto) {
                    sugestaoAtiva = (sugestaoAtiva + 1) % sugestoesProdutos.length;
                    renderizarSugestoes();
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
                if (aberto && sugestoesProdutos.length > 0) {
                    selecionarSugestao(sugestaoAtiva >= 0 ? sugestaoAtiva : 0);
                } else if (this.value.trim()) {
                    adicionarItemManual(this.value.trim());
                }
                return;
            }
            if (e.key === 'Escape') {
                fecharSugestoes();
            }
        });

        inputProduto.addEventListener('blur', function () {
            setTimeout(fecharSugestoes, 200);
        });

        function adicionarItem(product) {
            if (!product) return;
            itens.push({
                id: null,
                product_id: product.id,
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
            itens.push({
                id: null,
                product_id: null,
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

            if (itens.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="vazio">Nenhum item no orçamento.</td></tr>';
                return;
            }

            itens.forEach(function (item, index) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="item-num">${index + 1}</td>
                    <td><input type="text" value="${(item.description || '').replace(/"/g, '&quot;')}" onchange="itens[${index}].description = this.value"></td>
                    <td><input type="text" value="${(item.brand || '').replace(/"/g, '&quot;')}" onchange="itens[${index}].brand = this.value"></td>
                    <td><input type="text" value="${(item.unit || '').replace(/"/g, '&quot;')}" style="width:60px;" onchange="itens[${index}].unit = this.value"></td>
                    <td><input type="number" class="qty" value="${item.qty}" min="0" step="0.01" onchange="itens[${index}].qty = this.value"></td>
                    <td><button type="button" class="btn-del" onclick="deletarItem(${index})" title="Excluir item">&times;</button></td>
                `;
                tbody.appendChild(tr);
            });
        }

        function deletarItem(index) {
            const item = itens[index];
            if (item.id) deletar.push(item.id);
            itens.splice(index, 1);
            renderizarTabela();
            setMessage('');
        }

        function salvar() {
            const valid = itens.filter(function (i) {
                const qty = parseFloat(i.qty);
                return !isNaN(qty) && qty > 0;
            });

            if (valid.length === 0) {
                setMessage('Adicione ou mantenha pelo menos um item com quantidade maior que 0!', 'error');
                return;
            }

            const form = document.getElementById('formSalvar');
            const containerItens = document.getElementById('camposItens');
            const containerDeletar = document.getElementById('camposDeletar');
            containerItens.innerHTML = '';
            containerDeletar.innerHTML = '';

            valid.forEach(function (item) {
                containerItens.insertAdjacentHTML('beforeend',
                    '<input type="hidden" name="itens[' + item.id + '][id]" value="' + (item.id || '') + '">' +
                    '<input type="hidden" name="itens[' + item.id + '][product_id]" value="' + (item.product_id || '') + '">' +
                    '<input type="hidden" name="itens[' + item.id + '][description]" value="' + (item.description || '').replace(/"/g, '&quot;') + '">' +
                    '<input type="hidden" name="itens[' + item.id + '][brand]" value="' + (item.brand || '').replace(/"/g, '&quot;') + '">' +
                    '<input type="hidden" name="itens[' + item.id + '][unit]" value="' + (item.unit || '').replace(/"/g, '&quot;') + '">' +
                    '<input type="hidden" name="itens[' + item.id + '][quantity]" value="' + item.qty + '">'
                );
            });

            deletar.forEach(function (id) {
                containerDeletar.insertAdjacentHTML('beforeend',
                    '<input type="hidden" name="deletar[]" value="' + id + '">'
                );
            });

            document.getElementById('btnSalvar').disabled = true;
            form.submit();
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
        document.getElementById('errorModal').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });
        window.addEventListener('unhandledrejection', function (event) {
            event.preventDefault();
            var msg = 'Erro inesperado. Tente novamente.';
            if (event.reason) { msg = event.reason.message || event.reason || msg; }
            showModal('Erro: ' + msg);
        });
        window.onerror = function (msg) { showModal('Erro inesperado: ' + msg); return true; };
    </script>
</body>
</html>