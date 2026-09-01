<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dados Cadastrais</title>
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
            justify-content: center;
            padding: 10px;
            overflow: hidden;
        }
        .quadro {
            background: white;
            border: 1px solid gray;
            border-radius: 8px;
            width: 70%;
            padding: 24px 32px;
            text-align: left;
            color: #333;
            font-size: 15px;
            overflow-y: auto;
        }
        .campo {
            margin-bottom: 16px;
        }
        .campo label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            font-size: 14px;
        }
        .campo input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }
        .campo input:focus {
            outline: none;
            border-color: #68BD4F;
        }
        .campo input[readonly] {
            background: #f0f0f0;
            color: #888;
            cursor: not-allowed;
        }
        .campo .dica {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }
        .botoes {
            display: flex;
            gap: 12px;
            margin-top: 20px;
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
            min-height: 22px;
            margin-top: 10px;
            flex-shrink: 0;
        }
        .mensagem.success { color: #27ae60; }
        .mensagem.error { color: #e74c3c; }
        @media (max-width: 768px) {
            .quadro { width: 95%; }
            .faixa2 { font-size: 22px; }
            .faixa1 { padding: 12px; font-size: 13px; }
        }
    </style>
</head>
<body>

    <div class="faixa1">
        <div class="nome-cliente">{{ $usuario->name ?? 'CLIENTE PADRÃO' }}</div>
    </div>
    <div class="faixa2">
        DADOS CADASTRAIS
    </div>

    <div class="corpo">
        <form class="quadro" method="POST" action="{{ route('cadastro') }}">
            @csrf

            <div class="campo">
                <label for="name">Nome</label>
                <input type="text" id="name" name="name" value="{{ old('name', $usuario->name) }}" required maxlength="255">
            </div>

            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ $usuario->email }}" readonly>
                <div class="dica">O email não pode ser alterado.</div>
            </div>

            <div class="campo">
                <label for="endereco">Endereço</label>
                <input type="text" id="endereco" name="endereco" value="{{ old('endereco', $usuario->endereco) }}" maxlength="255">
            </div>

            <div class="campo">
                <label for="cidade">Cidade</label>
                <input type="text" id="cidade" name="cidade" value="{{ old('cidade', $usuario->cidade) }}" maxlength="255">
            </div>

            <div class="campo">
                <label for="estado">Estado</label>
                <input type="text" id="estado" name="estado" value="{{ old('estado', $usuario->estado) }}" maxlength="255">
            </div>

            @if ($errors->any())
                <div class="mensagem error">
                    @foreach ($errors->all() as $erro)
                        <div>{{ $erro }}</div>
                    @endforeach
                </div>
            @endif

            <div class="botoes">
                <button type="submit" class="btn-salvar">Salvar</button>
                <a href="{{ route('menu') }}" class="btn-voltar">Voltar</a>
            </div>
        </form>

        <div class="mensagem {{ session('success') ? 'success' : 'error' }}" id="mensagem">
            {{ session('success') ?? '' }}
        </div>
    </div>

</body>
</html>