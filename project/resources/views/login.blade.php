<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de Orçamentos - Login</title>

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html, body {
      min-height: 100%;
      width: 100%;
    }

    body {
      font-family: Arial, Helvetica, sans-serif;
      background: #f5f5f5;
      color: #111;
    }

    /* Faixa verde superior */
    .top-strip {
      height: 91px;
      width: 100%;
      background: linear-gradient(to bottom, #0d6f1d, #084b14);
      display: flex;
      align-items: flex-start;
      padding-top: 18px;
    }

    /* Cabeçalho */
    header {
      height: 76px;
      width: 100%;
      background: linear-gradient(to bottom, #111, #171717);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 16px;
    }

    header h1 {
      color: #e6e6e6;
      font-size: clamp(28px, 5vw, 43px);
      font-weight: 700;
      text-shadow: 0 1px 2px rgba(0,0,0,.8);
      text-align: center;
      line-height: 1.2;
      max-width: 100%;
    }

    /* Conteúdo */
    main {
      min-height: calc(100vh - 167px);
      display: flex;
      justify-content: flex-start;
      align-items: center;
      flex-direction: column;
      padding-top: 110px;
      padding-bottom: 40px;
      padding-left: 16px;
      padding-right: 16px;
    }

    /* Card */
    .login-card {
      width: 645px;
      min-height: 520px;
      border: 1px solid #9b9b9b;
      border-radius: 13px;
      background: rgba(255,255,255,.13);
      padding: 46px 56px 40px;
      box-shadow:
        inset 0 0 0 1px rgba(255,255,255,.2),
        0 1px 2px rgba(0,0,0,.08);
    }

    .login-title {
      font-size: 32px;
      line-height: 1;
      font-weight: 700;
      margin-bottom: 22px;
    }

    .green-line {
      height: 3px;
      width: 100%;
      background: #19703a;
      margin-bottom: 29px;
    }

    .field {
      margin-bottom: 25px;
    }

    .field-label {
      display: block;
      font-size: 20px;
      margin-bottom: 11px;
      color: #111;
    }

    .input {
      width: 100%;
      height: 55px;
      border: 1px solid #aaa;
      border-radius: 9px;
      background: rgba(255,255,255,.08);
      padding: 0 20px;
      font-family: Arial, Helvetica, sans-serif;
      font-size: 20px;
      color: #526078;
      outline: none;
      box-shadow: inset 0 1px 2px rgba(0,0,0,.04);
    }

    .input:focus {
      border-color: #19703a;
    }

    .input:read-only {
      background: #eeeeee;
      cursor: not-allowed;
    }

    .input::placeholder {
      color: #56647a;
      opacity: 1;
    }

    .password-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 9px;
      gap: 12px;
    }

    .password-header .field-label {
      margin: 0;
      font-weight: 700;
    }

    .forgot {
      font-size: 18px;
      color: #9d705c;
      text-decoration: none;
      white-space: nowrap;
    }

    .forgot:hover {
      text-decoration: underline;
      color: #7a5545;
    }

    .password-wrap {
      position: relative;
    }

    .password-wrap .input {
      padding-right: 60px;
    }

    .eye {
      position: absolute;
      right: 17px;
      top: 50%;
      transform: translateY(-50%);
      width: 27px;
      height: 17px;
      border: 2px solid #526078;
      border-radius: 50% / 60%;
      opacity: .8;
      cursor: pointer;
    }

    .eye::after {
      content: "";
      position: absolute;
      width: 6px;
      height: 6px;
      border: 2px solid #526078;
      border-radius: 50%;
      top: 3px;
      left: 8px;
    }

    .eye.on {
      opacity: 1;
      border-color: #19703a;
    }

    .eye.on::after {
      border-color: #19703a;
    }

    .buttons {
      display: flex;
      gap: 56px;
      justify-content: center;
      margin-top: 18px;
    }

    .btn {
      width: 211px;
      height: 59px;
      border: 1px solid rgba(0,0,0,.25);
      border-radius: 9px;
      color: #eee;
      font-family: Arial, Helvetica, sans-serif;
      font-size: 23px;
      font-weight: 700;
      text-shadow: 0 1px 1px rgba(0,0,0,.35);
      cursor: pointer;
      box-shadow:
        inset 0 1px 0 rgba(255,255,255,.28),
        0 1px 2px rgba(0,0,0,.25);
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
    }

    .btn:disabled {
      opacity: .6;
      cursor: not-allowed;
    }

    .btn-enter {
      background: linear-gradient(to bottom, #0da53a, #07862d 52%, #087325);
    }

    .btn-back {
      background: linear-gradient(to bottom, #1199df, #087fbe 52%, #0870a8);
    }

    .btn:hover:not(:disabled) {
      filter: brightness(1.07);
    }

    .btn:active:not(:disabled) {
      transform: translateY(1px);
    }

    .register-link {
      text-align: center;
      margin-top: 16px;
      font-size: 16px;
      color: #888;
    }

    .register-link a {
      color: #19703a;
      text-decoration: none;
      font-weight: 600;
    }

    .register-link a:hover {
      text-decoration: underline;
    }

    .error-msg {
      color: #e74c3c;
      font-size: 14px;
      text-align: center;
      margin-top: 12px;
      display: none;
    }

    .error-msg.visible {
      display: block;
    }

    .success-msg {
      color: #27ae60;
      font-size: 14px;
      text-align: center;
      margin-top: 12px;
      display: none;
    }

    .success-msg.visible {
      display: block;
    }

    .loading {
      text-align: center;
      margin-top: 12px;
      font-size: 16px;
      color: #888;
      display: none;
    }

    .loading.visible {
      display: block;
    }

    .hidden {
      display: none !important;
    }

    .info-box {
      background: #f0fdf4;
      border: 1px solid #22c55e;
      border-radius: 6px;
      padding: 12px;
      margin-bottom: 16px;
      font-size: 13px;
      color: #16a34a;
      text-align: center;
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

    @media (max-width: 720px) {
      header h1 {
        font-size: 31px;
      }

      main {
        padding-top: 45px;
      }

      .login-card {
        width: calc(100% - 32px);
        min-height: 500px;
        padding: 35px 28px;
      }

      .buttons {
        gap: 14px;
        flex-direction: column;
        align-items: center;
      }

      .btn {
        width: 100%;
      }

      .login-title {
        font-size: 28px;
      }

      .field-label {
        font-size: 17px;
      }

      .input {
        font-size: 17px;
      }

      .forgot {
        font-size: 16px;
      }
    }
  </style>
</head>

<body>

  <div class="top-strip"></div>

  <header>
    <h1>Sistema de Orçamentos</h1>
  </header>

  <main>
    <section class="login-card">

      <h2 class="login-title" id="formTitle">Login.</h2>

      <div class="green-line"></div>

      <div class="error-msg" id="errorMsg"></div>
      <div class="success-msg" id="successMsg"></div>

      <!-- LOGIN VIEW -->
      <div id="loginView">
        <form id="loginForm">
          <div class="field">
            <label class="field-label" for="email">E-mail</label>
            <input class="input" id="email" type="email" placeholder="exemplo@email.com" required>
          </div>

          <div class="field">
            <div class="password-header">
              <label class="field-label" for="password">Senha</label>
              <a class="forgot" href="#" onclick="showForgot(); return false;">Esqueci Minha Senha</a>
            </div>

            <div class="password-wrap">
              <input class="input" id="password" type="password" placeholder="Digite sua senha" required>
              <span class="eye" id="eyeToggle" title="Mostrar senha"></span>
            </div>
          </div>

          <div class="buttons">
            <button class="btn btn-enter" type="submit" id="btnLogin">Entrar</button>
            <a class="btn btn-back" href="/">Voltar</a>
          </div>
        </form>

        <div class="register-link">
          Não tem conta? <a href="#" onclick="showRegister(); return false;">Cadastre-se</a>
        </div>
      </div>

      <!-- REGISTER VIEW -->
      <div id="registerView" class="hidden">
        <form id="registerForm">
          <div class="field">
            <label class="field-label" for="regName">Nome</label>
            <input class="input" id="regName" type="text" placeholder="Seu nome" required>
          </div>
          <div class="field">
            <label class="field-label" for="regEmail">E-mail</label>
            <input class="input" id="regEmail" type="email" placeholder="seu@email.com" required>
          </div>
          <div class="field">
            <label class="field-label" for="regPassword">Senha</label>
            <input class="input" id="regPassword" type="password" placeholder="Mínimo 6 caracteres" required minlength="6">
          </div>
          <div class="field">
            <label class="field-label" for="regEndereco">Endereço de Entrega</label>
            <input class="input" id="regEndereco" type="text" placeholder="Rua, número, complemento">
          </div>
          <div class="field">
            <label class="field-label" for="regCidade">Cidade</label>
            <input class="input" id="regCidade" type="text" placeholder="Sua cidade">
          </div>
          <div class="field">
            <label class="field-label" for="regEstado">Estado</label>
            <input class="input" id="regEstado" type="text" placeholder="UF">
          </div>
          <div class="buttons">
            <button class="btn btn-enter" type="submit" id="btnRegister">Cadastrar</button>
          </div>
        </form>
        <div class="register-link">
          Já tem conta? <a href="#" onclick="showLogin(); return false;">Entrar</a>
        </div>
      </div>

      <!-- FORGOT VIEW -->
      <div id="forgotView" class="hidden">
        <p style="color: #555; font-size: 16px; margin-bottom: 24px; text-align: center;">
          Digite seu email e enviaremos um token para recuperar sua senha.
        </p>
        <form id="forgotForm">
          <div class="field">
            <label class="field-label" for="forgotEmail">E-mail</label>
            <input class="input" id="forgotEmail" type="email" placeholder="seu@email.com" required>
          </div>
          <div class="buttons">
            <button class="btn btn-enter" type="submit" id="btnForgot">Enviar Token</button>
          </div>
        </form>
        <div class="register-link">
          <a href="#" onclick="showLogin(); return false;">Voltar para o login</a>
        </div>
      </div>

      <!-- RESET VIEW -->
      <div id="resetView" class="hidden">
        <p style="color: #555; font-size: 16px; margin-bottom: 24px; text-align: center;">
          Use o token recebido por email para criar uma nova senha.
        </p>
        <form id="resetForm">
          <div class="field">
            <label class="field-label" for="resetEmail">E-mail</label>
            <input class="input" id="resetEmail" type="email" readonly>
          </div>
          <div class="field">
            <label class="field-label" for="resetToken">Token de recuperação</label>
            <input class="input" id="resetToken" type="text" placeholder="Cole o token recebido" required>
          </div>
          <div class="field">
            <label class="field-label" for="resetPassword">Nova senha</label>
            <input class="input" id="resetPassword" type="password" placeholder="Mínimo 6 caracteres" required minlength="6">
          </div>
          <div class="field">
            <label class="field-label" for="resetPasswordConfirm">Confirmar nova senha</label>
            <input class="input" id="resetPasswordConfirm" type="password" placeholder="Repita a nova senha" required minlength="6">
          </div>
          <div class="buttons">
            <button class="btn btn-enter" type="submit" id="btnReset">Redefinir Senha</button>
          </div>
        </form>
        <div class="register-link">
          <a href="#" onclick="showLogin(); return false;">Voltar para o login</a>
        </div>
      </div>

      <div class="loading" id="loading">Aguarde...</div>
    </section>
  </main>

  <script>
    function storeToken(token) {
      localStorage.setItem('jwt_token', token);
      document.cookie = 'jwt_token=' + token + '; path=/; max-age=' + (24 * 60 * 60);
    }

    function showError(msg) {
      showModal(msg);
      document.getElementById('errorMsg').textContent = msg;
      document.getElementById('errorMsg').classList.add('visible');
      document.getElementById('successMsg').classList.remove('visible');
    }

    function showSuccess(msg) {
      document.getElementById('successMsg').textContent = msg;
      document.getElementById('successMsg').classList.add('visible');
      document.getElementById('errorMsg').classList.remove('visible');
    }

    function hideMessages() {
      document.getElementById('errorMsg').classList.remove('visible');
      document.getElementById('successMsg').classList.remove('visible');
    }

    function setLoading(on) {
      document.getElementById('loading').classList.toggle('visible', on);
    }

    function showView(view) {
      hideMessages();
      ['loginView','registerView','forgotView','resetView'].forEach(function (id) {
        document.getElementById(id).classList.toggle('hidden', id !== view + 'View');
      });
      var titles = { login: 'Login.', register: 'Cadastrar.', forgot: 'Recuperar Senha.', reset: 'Nova Senha.' };
      document.getElementById('formTitle').textContent = titles[view] || 'Login.';
    }

    function showLogin() { showView('login'); }
    function showRegister() { showView('register'); }
    function showForgot() { showView('forgot'); }

    function showResetView(email, token) {
      document.getElementById('resetEmail').value = email || '';
      document.getElementById('resetToken').value = token || '';
      showView('reset');
    }

    function togglePassword() {
      var input = document.getElementById('password');
      var eye = document.getElementById('eyeToggle');
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      eye.classList.toggle('on', show);
      eye.title = show ? 'Ocultar senha' : 'Mostrar senha';
    }

    document.getElementById('eyeToggle').addEventListener('click', togglePassword);

    // Check URL params on page load
    (function () {
      var params = new URLSearchParams(window.location.search);
      var token = params.get('token');
      var view = params.get('view');

      if (token) {
        storeToken(token);
        window.location.href = '/jc-euprecisode';
        return;
      }

      if (view === 'register') {
        showRegister();
      }
    })();

    // === LOGIN FORM ===
    document.getElementById('loginForm').addEventListener('submit', async function (e) {
      e.preventDefault();

      var email = document.getElementById('email').value;
      var password = document.getElementById('password').value;
      var btn = document.getElementById('btnLogin');

      btn.disabled = true;
      setLoading(true);
      hideMessages();

      try {
        var res = await fetch('/api/auth/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ email, password }),
        });

        var data = await res.json();

        if (!res.ok) {
          showError((data.message || data.error) || 'Erro ao fazer login');
          return;
        }

        storeToken(data.access_token);
        window.location.href = '/jc-euprecisode';
      } catch (err) {
        showError('Erro de conexão. Tente novamente.');
      } finally {
        btn.disabled = false;
        setLoading(false);
      }
    });

    // === REGISTER FORM ===
    document.getElementById('registerForm').addEventListener('submit', async function (e) {
      e.preventDefault();

      var name = document.getElementById('regName').value;
      var email = document.getElementById('regEmail').value;
      var password = document.getElementById('regPassword').value;
      var endereco = document.getElementById('regEndereco').value;
      var cidade = document.getElementById('regCidade').value;
      var estado = document.getElementById('regEstado').value;
      var btn = document.getElementById('btnRegister');

      btn.disabled = true;
      setLoading(true);
      hideMessages();

      try {
        var res = await fetch('/api/auth/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ name, email, password, endereco, cidade, estado }),
        });

        var data = await res.json();

        if (!res.ok) {
          var msgs = data.errors ? Object.values(data.errors).flat().join(', ') : (data.message || data.error);
          showError(msgs || 'Erro ao cadastrar');
          return;
        }

        storeToken(data.access_token);
        window.location.href = '/jc-euprecisode';
      } catch (err) {
        showError('Erro de conexão. Tente novamente.');
      } finally {
        btn.disabled = false;
        setLoading(false);
      }
    });

    // === FORGOT FORM ===
    document.getElementById('forgotForm').addEventListener('submit', async function (e) {
      e.preventDefault();

      var email = document.getElementById('forgotEmail').value;
      var btn = document.getElementById('btnForgot');

      btn.disabled = true;
      setLoading(true);
      hideMessages();

      try {
        var res = await fetch('/api/auth/forgot-password', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ email }),
        });

        var data = await res.json();

        if (!res.ok) {
          var msgs = data.errors ? Object.values(data.errors).flat().join(', ') : (data.message || data.error);
          showError(msgs || 'Erro ao solicitar recuperação');
          return;
        }

        showSuccess('Token enviado! Verifique seu email.');
        setTimeout(function () {
          showResetView(email, data.reset_token);
        }, 1500);
      } catch (err) {
        showError('Erro de conexão. Tente novamente.');
      } finally {
        btn.disabled = false;
        setLoading(false);
      }
    });

    // === RESET FORM ===
    document.getElementById('resetForm').addEventListener('submit', async function (e) {
      e.preventDefault();

      var email = document.getElementById('resetEmail').value;
      var token = document.getElementById('resetToken').value;
      var password = document.getElementById('resetPassword').value;
      var confirm = document.getElementById('resetPasswordConfirm').value;
      var btn = document.getElementById('btnReset');

      if (password !== confirm) {
        showError('As senhas não conferem.');
        return;
      }

      btn.disabled = true;
      setLoading(true);
      hideMessages();

      try {
        var res = await fetch('/api/auth/reset-password', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ email, token, password }),
        });

        var data = await res.json();

        if (!res.ok) {
          showError((data.message || data.error) || 'Erro ao redefinir senha');
          return;
        }

        showSuccess('Senha redefinida com sucesso! Redirecionando para o login...');
        setTimeout(function () {
          showLogin();
        }, 2000);
      } catch (err) {
        showError('Erro de conexão. Tente novamente.');
      } finally {
        btn.disabled = false;
        setLoading(false);
      }
    });
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