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
        @media (max-width: 768px) {
            .quadro1, .quadro2-wrapper { width: 95%; }
            .faixa2 { font-size: 22px; }
            .faixa1 { padding: 12px; font-size: 13px; }
        }
    </style>
</head>
<body>

    @php
        $produtos = [
            ['descricao' => 'Parafuso Aço Inox M6', 'marca' => 'Telas', 'unidade' => 'UN'],
            ['descricao' => 'Porca Sextavada M8', 'marca' => 'Telas', 'unidade' => 'UN'],
            ['descricao' => 'Arruela Lisa 1/2', 'marca' => 'Ciser', 'unidade' => 'UN'],
            ['descricao' => 'Chave Inglesa 12"', 'marca' => 'Vonder', 'unidade' => 'UN'],
            ['descricao' => 'Fita Veda Rosca 18mm', 'marca' => 'Tigre', 'unidade' => 'PC'],
            ['descricao' => 'Lixa d\'Água #220', 'marca' => 'Norton', 'unidade' => 'UN'],
            ['descricao' => 'Tinta Esmalte Branco', 'marca' => 'Suvinil', 'unidade' => 'LT'],
            ['descricao' => 'Disco Corte 4.1/2"', 'marca' => 'Bosch', 'unidade' => 'UN'],
            ['descricao' => 'Cano PVC 25mm 3m', 'marca' => 'Tigre', 'unidade' => 'UN'],
            ['descricao' => 'Joelho PVC 25mm', 'marca' => 'Tigre', 'unidade' => 'UN'],
        ];
    @endphp

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
            <select id="selectProduto" onchange="adicionarItem(this)">
                <option value="">-- Digite ou selecione o produto --</option>
                @foreach($produtos as $p)
                    <option value="{{ $p['descricao'] }}|{{ $p['marca'] }}|{{ $p['unidade'] }}">
                        {{ $p['descricao'] }} - {{ $p['marca'] }} - {{ $p['unidade'] }}
                    </option>
                @endforeach
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
                        <th>Un.</th>
                        <th style="width:90px;">Qty</th>
                        <th style="width:30px;"></th>
                    </tr>
                </thead>
                <tbody id="corpoTabela">
                </tbody>
            </table>
        </div>

        <div class="botoes">
            <button class="btn-concluir" onclick="concluir()">Concluir</button>
            <a href="{{ route('menu') }}" class="btn-voltar">Voltar</a>
        </div>

        <div class="mensagem" id="mensagem"></div>
    </div>

    <script>
        let itens = [];
        let itemSeq = 0;

        document.getElementById('selectProduto').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const val = this.value;
                if (val) {
                    const opt = Array.from(this.options).find(o => o.value === val);
                    if (opt) {
                        adicionarItem(this);
                    } else {
                        adicionarItemManual(val);
                    }
                }
            }
        });

        function adicionarItem(select) {
            const val = select.value;
            if (!val) return;
            const [descricao, marca, unidade] = val.split('|');
            itemSeq++;
            itens.push({
                id: Date.now() + Math.random(),
                item: itemSeq,
                descricao: descricao,
                marca: marca,
                unidade: unidade,
                qty: ''
            });
            select.value = '';
            renderizarTabela();
            document.getElementById('mensagem').textContent = '';
        }

        function adicionarItemManual(texto) {
            itemSeq++;
            itens.push({
                id: Date.now() + Math.random(),
                item: itemSeq,
                descricao: texto,
                marca: '',
                unidade: '',
                qty: ''
            });
            document.getElementById('selectProduto').value = '';
            renderizarTabela();
            document.getElementById('mensagem').textContent = '';
        }

        function renderizarTabela() {
            const tbody = document.getElementById('corpoTabela');
            tbody.innerHTML = '';
            itens.forEach((item, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><span class="handle" draggable="true" data-index="${index}">&#9776;</span></td>
                    <td>${item.item}</td>
                    <td><input type="text" value="${item.descricao}" onchange="itens[${index}].descricao = this.value"></td>
                    <td><input type="text" value="${item.marca}" onchange="itens[${index}].marca = this.value"></td>
                    <td><input type="text" value="${item.unidade}" style="width:50px;" onchange="itens[${index}].unidade = this.value"></td>
                    <td><input type="number" class="qty" value="${item.qty}" min="0" onchange="itens[${index}].qty = this.value; verificarQty(${index})" onkeydown="if(event.key==='Enter'){verificarQty(${index});}"></td>
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
                        const [movido] = itens.splice(from, 1);
                        itens.splice(to, 0, movido);
                        renummerarItens();
                        renderizarTabela();
                    }
                });
            });
            setTimeout(() => {
                const ultimoInput = tbody.querySelector('tr:last-child input.qty');
                if (ultimoInput) ultimoInput.focus();
            }, 100);
        }

        function renummerarItens() {
            itens.forEach((item, i) => item.item = i + 1);
        }

        function deletarItem(index) {
            itens.splice(index, 1);
            renummerarItens();
            renderizarTabela();
            document.getElementById('mensagem').textContent = '';
        }

        function verificarQty(index) {
            const qty = itens[index].qty;
            if (qty === '' || parseInt(qty) <= 0) {
                document.getElementById('mensagem').textContent = 'Digite a Qty ou Delete o Item!!!';
                return;
            }
            document.getElementById('mensagem').textContent = '';
            document.getElementById('selectProduto').focus();
        }

        function concluir() {
            const temItem = itens.some(i => i.qty !== '' && parseInt(i.qty) > 0);
            if (!temItem) {
                document.getElementById('mensagem').textContent = 'Adicione pelo menos um item com quantidade válida!';
                return;
            }
            document.getElementById('mensagem').textContent = 'Orçamento simulado com sucesso! (modo estático - sem banco de dados)';
        }
    </script>
</body>
</html>