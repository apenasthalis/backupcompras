<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio - Sistema de Orçamentos</title>

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html, body {
      width: 100%;
      min-height: 100%;
    }

    body {
      font-family: Arial, Helvetica, sans-serif;
      background: #f5f5f5;
      color: #111;
    }

    /* Faixa superior verde */
    .top-strip {
      min-height: 56px;
      width: 100%;
      background: linear-gradient(to bottom, #178b28, #075b18);
      display: flex;
      align-items: flex-end;
      justify-content: center;
      padding: 10px 16px 8px;
    }

    .top-strip span {
      color: #ddd;
      font-size: 18px;
      text-align: center;
      line-height: 1.4;
      max-width: 100%;
      overflow-wrap: anywhere;
    }

    /* Cabeçalho preto */
    header {
      width: 100%;
      min-height: 70px;
      background: linear-gradient(to bottom, #111, #191919);
      display: flex;
      align-items: center;
      justify-content: center;
      border-top: 1px solid #111;
      border-bottom: 1px solid #111;
      padding: 10px 16px;
    }

    header h1 {
      color: #e8e8e8;
      font-size: clamp(26px, 5vw, 40px);
      font-weight: 700;
      letter-spacing: 0.3px;
      text-shadow: 0 1px 2px rgba(0,0,0,.8);
      text-align: center;
      line-height: 1.2;
      max-width: 100%;
    }

    /* Área principal */
    main {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 69px 16px 40px;
    }

    .intro {
      text-align: center;
      line-height: 1.55;
      font-size: 25px;
      font-weight: 400;
      color: #171717;
    }

    .intro p {
      margin: 0;
    }

    .intro .strong {
      font-weight: 500;
    }

    /* Caixa de acesso */
    .login-box {
      width: 605px;
      min-height: 205px;
      margin-top: 43px;
      border: 1px solid #999;
      border-radius: 11px;
      background: rgba(255,255,255,.12);
      box-shadow:
        inset 0 0 0 1px rgba(255,255,255,.18),
        0 1px 2px rgba(0,0,0,.08);
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 38px 25px 40px;
    }

    .login-box h2 {
      font-size: 31px;
      font-weight: 700;
      margin-bottom: 34px;
      color: #050505;
    }

    .buttons {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 18px;
    }

    .btn {
      width: 181px;
      height: 49px;
      border-radius: 6px;
      border: 1px solid rgba(0,0,0,.25);
      color: white;
      font-size: 22px;
      font-weight: 700;
      font-family: Arial, Helvetica, sans-serif;
      cursor: pointer;
      box-shadow:
        inset 0 1px 0 rgba(255,255,255,.3),
        0 1px 2px rgba(0,0,0,.25);
      text-shadow: 0 1px 1px rgba(0,0,0,.45);
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
    }

    .btn-login {
      background: linear-gradient(to bottom, #0a9e27 0%, #087d1e 50%, #056b18 100%);
    }

    .btn-account {
      background: linear-gradient(to bottom, #1399dd 0%, #087fbd 50%, #066aa0 100%);
    }

    .btn:hover {
      filter: brightness(1.08);
    }

    .btn:active {
      transform: translateY(1px);
    }

    /* Ajuste para telas menores */
    @media (max-width: 700px) {
      header h1 {
        font-size: 26px;
      }

      .top-strip {
        align-items: center;
      }

      .top-strip span {
        font-size: 15px;
      }

      .intro {
        font-size: 20px;
        padding: 0 20px;
      }

      .login-box {
        width: calc(100% - 40px);
        margin-top: 35px;
      }

      .buttons {
        flex-direction: column;
        gap: 12px;
      }

      .btn {
        width: 220px;
      }
    }

    @media (max-width: 420px) {
      .top-strip span {
        font-size: 13px;
      }

      .intro {
        font-size: 17px;
      }

      .login-box {
        padding: 30px 20px 32px;
      }

      .login-box h2 {
        font-size: 26px;
      }

      .btn {
        width: 100%;
        max-width: 240px;
        font-size: 20px;
      }
    }
  </style>
</head>

<body>

  <div class="top-strip">
    <span>Seu sistema de orçamentos, simples e descomplicado.</span>
  </div>

  <header>
    <h1>Sistema de Orçamentos</h1>
  </header>

  <main>
    <section class="intro">
      <p>Crie e gerencie seus orçamentos de forma prática.</p>
      <p class="strong">Orçamentos rápidos, organizados e sempre ao seu alcance!</p>
      <p>Fácil Fácil...</p>
    </section>

    <section class="login-box">
      <h2>Acesse sua conta!!!</h2>

      <div class="buttons">
        <a class="btn btn-login" href="/login">Login</a>
        <a class="btn btn-account" href="/login?view=register">Criar Conta</a>
      </div>
    </section>
  </main>

</body>
</html>
