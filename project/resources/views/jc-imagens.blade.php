<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Imagens</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background-color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        h1 {
            color: white;
            margin: 20px 0;
            font-size: 28px;
        }
        .btn-gerar {
            padding: 16px 48px;
            background-color: #22c55e;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            margin-bottom: 30px;
        }
        .btn-gerar:hover {
            background-color: orange;
            transform: scale(1.05);
        }
        .btn-voltar {
            padding: 10px 32px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            margin-bottom: 20px;
            transition: background 0.2s;
        }
        .btn-voltar:hover {
            background-color: orange;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 16px;
            width: 100%;
            max-width: 1200px;
        }
        .grid img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            transition: transform 0.2s;
        }
        .grid img:hover {
            transform: scale(1.03);
        }
        @media (max-width: 600px) {
            h1 { font-size: 22px; }
            .grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
            .grid img { height: 150px; }
        }
    </style>
</head>
<body>
    <a href="{{ route('menu') }}" class="btn-voltar">&larr; Voltar ao Menu</a>
    <h1>&#128247; Gerador de Imagens Aleatórias</h1>
    <button class="btn-gerar" onclick="gerarImagens()">&#128260; Gerar Imagens</button>
    <div class="grid" id="grid"></div>

    <script>
        function gerarImagens() {
            const grid = document.getElementById('grid');
            grid.innerHTML = '';
            for (let i = 0; i < 8; i++) {
                const img = document.createElement('img');
                const w = 400 + Math.floor(Math.random() * 200);
                const h = 250 + Math.floor(Math.random() * 100);
                img.src = 'https://picsum.photos/' + w + '/' + h + '?random=' + Date.now() + i;
                img.alt = 'Imagem aleatória ' + (i + 1);
                grid.appendChild(img);
            }
        }
        gerarImagens();
    </script>
</body>
</html>