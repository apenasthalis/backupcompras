<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aprovar Orçamento</title>
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
        .faixa2 {
            background-color: green;
            color: white;
            padding: 5px;
            font-size: 35px;
            font-weight: 550;
            text-align: center;
            flex-shrink: 0;
        }
        .aprovacao {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        .corpo {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 12px;
            overflow: hidden;
        }
        .informacoes {
            background: white;
            border: 1px solid gray;
            border-radius: 8px;
            width: 80%;
            padding: 12px 20px;
            font-size: 15px;
            margin-bottom: 8px;
            flex-shrink: 0;
        }
        .informacoes strong { color: #166534; }
        .quadro {
            background: white;
            border: 1px solid gray;
            border-radius: 8px;
            width: 80%;
            flex: 1;
            min-height: 0;
            overflow: auto;
        }
        .quadro table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .quadro th {
            background: #e8f5e9;
            padding: 6px 4px;
            text-align: left;
            border-bottom: 2px solid #c8e6c9;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .quadro td {
            padding: 4px;
            border-bottom: 1px solid #eee;
        }
        .quadro input[type="text"],
        .quadro input[type="number"] {
            width: 100%;
            padding: 4px 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 13px;
        }
        .quadro input:focus {
            outline: none;
            border-color: #68BD4F;
        }
        .quadro input.num {
            text-align: right;
        }
        .desconto-bloco {
            background: white;
            border: 1px solid gray;
            border-radius: 8px;
            width: 80%;
            padding: 10px 20px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            font-size: 14px;
            flex-shrink: 0;
        }
        .desconto-bloco input {
            width: 100px;
            padding: 6px 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-align: right;
        }
        .totais {
            display: flex;
            gap: 24px;
            margin-left: auto;
            flex-wrap: wrap;
        }
        .totais span.valor {
            font-weight: bold;
            color: #166534;
        }
        .totais .destaque span.valor {
            font-size: 16px;
        }
        .botoes {
            display: flex;
            gap: 12px;
            margin-top: 10px;
            flex-shrink: 0;
        }
        .btn-salvar {
            padding: 10px 32px;
            background-color: #68BD4F;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-salvar:hover { background-color: orange; }
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
        .btn-voltar:hover { background-color: orange; }
        .mensagem {
            text-align: center;
            font-size: 15px;
            color: orange;
            flex-shrink: 0;
            min-height: 20px;
            margin-top: 6px;
        }
        .mensagem.error { color: #e74c3c; }
        .mensagem.success { color: #27ae60; }
        .vazio { padding: 20px; text-align: center; color: #888; }
        @media (max-width: 768px) {
            .quadro, .informacoes, .desconto-bloco { width: 95%; }
            .faixa2 { font-size: 22px; }
            .faixa1 { padding: 12px; font-size: 13px; }
            .btn-salvar, .btn-voltar { padding: 10px 18px; font-size: 14px; }
        }
    </style>
</head>
<body>

    <div class="faixa1">
        <div>{{ $usuario->sistema ?? 'SISTEMA DE ORÇAMENTOS' }}</div>
        <div class="nome-cliente">{{ $usuario->name ?? 'CLIENTE PADRÃO' }}</div>
    </div>
    <div class="faixa2">
        APROVAR ORÇAMENTO
    </div>

    <form method="POST" action="{{ route('orc-prontos.salvar', ['orcamento' => $orcamento->idorc]) }}" class="aprovacao">
        @csrf
        <div class="corpo">
            <div class="informacoes">
                <strong>Orçamento #{{ $orcamento->idorc }}</strong> — {{ $orcamento->empnome ?? 'Sem empresa' }}
                ({{ Str::limit($orcamento->empendereco ?? '', 15) }})<br>
                Cliente: {{ $orcamento->clinome ?? '—' }}<br>
                Entrega: {{ $orcamento->cliendereco ?? '—' }}
                @if ($orcamento->clicidade ?? false), {{ $orcamento->clicidade }}@endif
                @if ($orcamento->cliestado ?? false) - {{ $orcamento->cliestado }}@endif
            </div>

            <div class="quadro">
                <table>
                    <thead>
                        <tr>
                            <th style="width:40px;">Item</th>
                            <th>DESCRIÇÃO</th>
                            <th style="width:120px;">MARCA</th>
                            <th style="width:60px;">UN</th>
                            <th style="width:70px;">QTD</th>
                            <th style="width:110px;">PREÇO LOJISTA</th>
                            <th style="width:110px;">PREÇO CLIENTE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orcamento->itens as $index => $item)
                            <tr class="item-orcamento">
                                <td>
                                    <input type="hidden" name="itens[{{ $index }}][id]" value="{{ $item->id }}">
                                </td>
                                <td>
                                    <input type="text" name="itens[{{ $index }}][description]" value="{{ $item->description }}" required>
                                </td>
                                <td>
                                    <input type="text" name="itens[{{ $index }}][brand]" value="{{ $item->brand }}">
                                </td>
                                <td>
                                    <input type="text" name="itens[{{ $index }}][unit]" value="{{ $item->unit }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0.01" class="js-qtd num" name="itens[{{ $index }}][quantity]" value="{{ number_format((float) $item->quantity, 2, '.', '') }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" class="js-preco-lojista num" name="itens[{{ $index }}][preco_lojista]" value="{{ number_format((float) $item->preco_lojista, 2, '.', '') }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" class="js-preco-cliente num" name="itens[{{ $index }}][preco_cliente]" value="{{ number_format((float) $item->preco_cliente, 2, '.', '') }}" required>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="vazio">Nenhum item vinculado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="desconto-bloco">
                <label for="desconto">Desconto (%):</label>
                <input type="number" id="desconto" name="desconto" step="0.01" min="0" max="100" value="{{ number_format((float) $orcamento->desconto, 2, '.', '') }}">

                <div class="totais">
                    <div>Total lojista: <span class="valor" id="totalLojista">{{ number_format($orcamento->totalLojista(), 2, ',', '.') }}</span></div>
                    <div>Total cliente: <span class="valor" id="totalCliente">{{ number_format($orcamento->totalCliente(), 2, ',', '.') }}</span></div>
                    <div class="destaque">Total final: R$ <span class="valor" id="totalFinal">{{ number_format($orcamento->totalFinal(), 2, ',', '.') }}</span></div>
                </div>
            </div>

            <div class="botoes">
                <button type="submit" class="btn-salvar">Salvar Alterações</button>
                <a href="{{ route('orc-prontos') }}" class="btn-voltar">Voltar</a>
            </div>

            <div class="mensagem {{ session('success') ? 'success' : (session('error') || $errors->any() ? 'error' : '') }}" id="mensagem">
                {{ session('success') ?? session('error') ?? '' }}
                @if ($errors->any()) {{ $errors->first() }} @endif
            </div>
        </div>
    </form>

    <script>
        var MARGEM_CLIENTE = 0.03;

        function numero(valor) {
            return parseFloat(String(valor).replace(',', '.')) || 0;
        }

        function moeda(valor) {
            return valor.toFixed(2).replace('.', ',');
        }

        function precoClienteSugerido(lojista) {
            return Math.round(lojista * (1 + MARGEM_CLIENTE) * 100) / 100;
        }

        function recalcular() {
            var totalLojista = 0;
            var totalCliente = 0;

            document.querySelectorAll('tr.item-orcamento').forEach(function (tr) {
                var quantidade = numero(tr.querySelector('.js-qtd').value);
                totalLojista += quantidade * numero(tr.querySelector('.js-preco-lojista').value);
                totalCliente += quantidade * numero(tr.querySelector('.js-preco-cliente').value);
            });

            var desconto = Math.min(Math.max(numero(document.getElementById('desconto').value), 0), 100);

            document.getElementById('totalLojista').textContent = moeda(totalLojista);
            document.getElementById('totalCliente').textContent = moeda(totalCliente);
            document.getElementById('totalFinal').textContent = moeda(totalCliente * (1 - desconto / 100));
        }

        document.querySelectorAll('.js-preco-lojista').forEach(function (input) {
            input.addEventListener('change', function () {
                var cliente = input.closest('tr').querySelector('.js-preco-cliente');
                if (cliente.dataset.manualizado === '1') return;
                cliente.value = precoClienteSugerido(numero(input.value)).toFixed(2);
                recalcular();
            });
        });

        document.querySelectorAll('.js-preco-cliente').forEach(function (input) {
            input.addEventListener('input', function () {
                input.dataset.manualizado = '1';
            });
        });

        document.querySelectorAll('tr.item-orcamento input, #desconto').forEach(function (input) {
            input.addEventListener('input', recalcular);
            input.addEventListener('change', recalcular);
        });
    </script>

</body>
</html>