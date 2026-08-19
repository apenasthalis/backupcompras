<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Orçamentos Abertos</title>
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
        #formCobrar {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            min-height: 0;
            width: 100%;
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
        .btn-cobrar {
            padding: 10px 32px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-cobrar:hover {
            background-color: orange;
        }
        .btn-cobrar:disabled {
            opacity: 0.5;
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
            font-size: 16px;
            color: orange;
            flex-shrink: 0;
            min-height: 22px;
            margin-top: 8px;
        }
        .mensagem.error {
            color: #e74c3c;
        }
        .mensagem.success {
            color: #27ae60;
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
            .btn-cobrar, .btn-voltar { padding: 10px 18px; font-size: 14px; }
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
        .modal-header.warning { background: #f39c12; }
        .modal-body { padding: 24px; font-size: 15px; color: #333; line-height: 1.5; }
        .modal-footer { padding: 12px 24px; text-align: right; border-top: 1px solid #eee; }
        .modal-btn { padding: 8px 24px; background: #e74c3c; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
        .modal-btn:hover { background: #c0392b; }
        .modal-btn.secondary { background: #95a5a6; margin-right: 8px; }
        .modal-btn.secondary:hover { background: #7f8c8d; }
    </style>
</head>
<body>

    <div class="faixa1">
        <div class="nome-cliente">{{ $usuario->name ?? 'CLIENTE PADRÃO' }}</div>
        <div class="endereco-cliente">
            {{ $usuario->endereco ?? 'Endereço não informado' }}
            @if ($usuario->cidade ?? false), {{ $usuario->cidade }}@endif
            @if ($usuario->estado ?? false) - {{ $usuario->estado }}@endif
        </div>
    </div>
    <div class="faixa2">
        ORÇAMENTOS ABERTOS
    </div>

    <div class="corpo">
        <form id="formCobrar" method="POST" action="{{ route('orc-abertos.cobrar') }}">
            @csrf
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
                                    <input type="checkbox" name="orcamentos[]" value="{{ $orcamento->idorc }}">
                                </td>
                                <td>{{ $orcamento->idorc }}</td>
                                <td>{{ $orcamento->empnome ?? '—' }}</td>
                                <td>{{ Str::limit($orcamento->empendereco ?? '', 15) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="vazio">Nenhum orçamento aberto.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="botoes">
                <button type="submit" class="btn-cobrar" id="btnCobrar" disabled>Cobrar Orçamentos</button>
                <a href="{{ route('menu') }}" class="btn-voltar">Voltar</a>
            </div>
        </form>

        <div class="mensagem {{ session('success') ? 'success' : (session('error') ? 'error' : '') }}" id="mensagem">
            {{ session('success') ?? session('error') ?? '' }}
        </div>
    </div>

    <script>
        var btnCobrar = document.getElementById('btnCobrar');
        var checkboxes = document.querySelectorAll('#formCobrar input[name="orcamentos[]"]');

        checkboxes.forEach(function (cb) {
            cb.addEventListener('change', function () {
                btnCobrar.disabled = document.querySelectorAll('#formCobrar input[name="orcamentos[]"]:checked').length === 0;
            });
        });

        document.querySelectorAll('tr.linha').forEach(function (tr) {
            tr.addEventListener('click', function (e) {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'A' || e.target.tagName === 'BUTTON') return;
                var id = tr.getAttribute('data-id');
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ url('/orc-abertos') }}/" + id + "/editar";
                var token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = "{{ csrf_token() }}";
                form.appendChild(token);
                document.body.appendChild(form);
                form.submit();
            });
        });
    </script>

</body>
</html>