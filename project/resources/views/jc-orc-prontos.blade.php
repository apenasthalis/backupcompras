<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Orçamentos Prontos</title>
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
        .faixa1 .nome-cliente {
            margin-top: 4px;
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
        .quadro {
            background: white;
            border: 1px solid gray;
            border-radius: 8px;
            width: 80%;
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        .quadro table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }
        .quadro th {
            background: #e8f5e9;
            padding: 10px 8px;
            text-align: left;
            border-bottom: 2px solid #c8e6c9;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .quadro td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .quadro tr.linha {
            cursor: pointer;
            transition: background 0.15s;
        }
        .quadro tr.linha:hover {
            background: #f0fdf4;
        }
        .quadro tr.linha input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .botoes {
            display: flex;
            gap: 12px;
            flex-shrink: 0;
            margin-top: 10px;
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
            font-size: 16px;
            color: orange;
            flex-shrink: 0;
            min-height: 22px;
            margin-top: 8px;
        }
        .vazio {
            padding: 20px;
            text-align: center;
            color: #888;
        }
        @media (max-width: 768px) {
            .quadro { width: 95%; }
            .faixa2 { font-size: 22px; }
            .faixa1 { padding: 12px; font-size: 13px; }
            .btn-voltar { padding: 10px 18px; font-size: 14px; }
        }
    </style>
</head>
<body>

    <div class="faixa1">
        <div>{{ $usuario->sistema ?? 'SISTEMA DE ORÇAMENTOS' }}</div>
        <div class="nome-cliente">{{ $usuario->name ?? 'CLIENTE PADRÃO' }}</div>
    </div>
    <div class="faixa2">
        ORÇAMENTOS PRONTOS
    </div>

    <div class="corpo">
        <div class="quadro">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px;"></th>
                        <th style="width:80px;">ID</th>
                        <th>EMPRESA</th>
                        <th>ENDEREÇO</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orcamentos as $orcamento)
                        <tr class="linha" data-id="{{ $orcamento->idorc }}">
                            <td>
                                <input type="checkbox" value="{{ $orcamento->idorc }}">
                            </td>
                            <td>{{ $orcamento->idorc }}</td>
                            <td>{{ $orcamento->empnome ?? '—' }}</td>
                            <td>{{ Str::limit($orcamento->empendereco ?? '', 15) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="vazio">Nenhum orçamento pronto.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="botoes">
            <a href="{{ route('menu') }}" class="btn-voltar">Voltar</a>
        </div>

        <div class="mensagem" id="mensagem">
            {{ session('success') ?? session('error') ?? '' }}
        </div>
    </div>

    <script>
        document.querySelectorAll('tr.linha').forEach(function (tr) {
            tr.addEventListener('click', function (e) {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'A') return;
                var id = tr.getAttribute('data-id');
                window.location.href = "{{ url('/orc-prontos') }}/" + id + "/aprovar";
            });
        });
    </script>
</body>
</html>