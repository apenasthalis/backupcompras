<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            padding: 16px 20px;
            font-size: 15px;
            margin-bottom: 10px;
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
            overflow-y: auto;
        }
        .quadro table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .quadro th {
            background: #e8f5e9;
            padding: 8px 6px;
            text-align: left;
            border-bottom: 2px solid #c8e6c9;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .quadro td {
            padding: 6px;
            border-bottom: 1px solid #eee;
        }
        .botoes { margin-top: 10px; flex-shrink: 0; }
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
        .vazio { padding: 20px; text-align: center; color: #888; }
        @media (max-width: 768px) {
            .quadro, .informacoes { width: 95%; }
            .faixa2 { font-size: 22px; }
            .faixa1 { padding: 12px; font-size: 13px; }
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
                        <th style="width:80px;">ID</th>
                        <th>DESCRIÇÃO</th>
                        <th>MARCA</th>
                        <th>UN</th>
                        <th style="width:90px;">QTD</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orcamento->itens as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->brand }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ $item->quantity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="vazio">Nenhum item vinculado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="botoes">
            <a href="{{ route('orc-prontos') }}" class="btn-voltar">Voltar</a>
        </div>
    </div>

</body>
</html>