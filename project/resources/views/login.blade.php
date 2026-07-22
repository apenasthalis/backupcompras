<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background-color: #add8e6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        }
        .login-card h1 {
            text-align: center;
            color: #333;
            margin-bottom: 24px;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            border-color: #68BD4F;
        }
        .form-group input:read-only {
            background: #f5f5f5;
            cursor: not-allowed;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #68BD4F;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-login:hover {
            background-color: #5aa844;
        }
        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .btn-secondary {
            width: 100%;
            padding: 12px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn-secondary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: #999;
            font-size: 13px;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #ddd;
        }
        .btn-google {
            width: 100%;
            padding: 12px;
            background: white;
            color: #444;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background-color 0.2s;
            text-decoration: none;
        }
        .btn-google:hover {
            background-color: #f5f5f5;
        }
        .btn-google svg {
            width: 20px;
            height: 20px;
        }
        .error-msg {
            color: #e74c3c;
            font-size: 13px;
            text-align: center;
            margin-top: 12px;
            display: none;
        }
        .error-msg.visible {
            display: block;
        }
        .success-msg {
            color: #27ae60;
            font-size: 13px;
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
            font-size: 14px;
            color: #888;
            display: none;
        }
        .loading.visible {
            display: block;
        }
        .register-link {
            text-align: center;
            margin-top: 16px;
            font-size: 14px;
            color: #888;
        }
        .register-link a {
            color: #68BD4F;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .forgot-link {
            text-align: right;
            margin-top: -8px;
            margin-bottom: 16px;
            font-size: 13px;
        }
        .forgot-link a {
            color: #888;
            text-decoration: none;
        }
        .forgot-link a:hover {
            color: #68BD4F;
            text-decoration: underline;
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
    </style>
</head>
<body>
    <div class="login-card">
        <h1 id="formTitle">Entrar</h1>

        <div class="error-msg" id="errorMsg"></div>
        <div class="success-msg" id="successMsg"></div>

        <!-- LOGIN VIEW -->
        <div id="loginView">
            <form id="loginForm">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" placeholder="seu@email.com" required>
                </div>
                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" placeholder="Sua senha" required>
                </div>
                <div class="forgot-link">
                    <a href="#" onclick="showForgot()">Esqueceu a senha?</a>
                </div>
                <button type="submit" class="btn-login" id="btnLogin">Entrar</button>
            </form>

            <div class="divider">ou</div>

            <a class="btn-google" id="btnGoogle" href="#">
                <svg viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.14.76-4.59l-7.98-6.19A23.99 23.99 0 0 0 0 24c0 3.77.87 7.35 2.56 10.56l7.97-5.97z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 5.97C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Entrar com Google
            </a>

            <div class="register-link">
                Não tem conta? <a href="#" onclick="showRegister()">Cadastre-se</a>
            </div>
        </div>

        <!-- REGISTER VIEW -->
        <div id="registerView" class="hidden">
            <form id="registerForm">
                <div class="form-group">
                    <label for="regName">Nome</label>
                    <input type="text" id="regName" placeholder="Seu nome" required>
                </div>
                <div class="form-group">
                    <label for="regEmail">E-mail</label>
                    <input type="email" id="regEmail" placeholder="seu@email.com" required>
                </div>
                <div class="form-group">
                    <label for="regPassword">Senha</label>
                    <input type="password" id="regPassword" placeholder="Mínimo 6 caracteres" required minlength="6">
                </div>
                <button type="submit" class="btn-login" id="btnRegister">Cadastrar</button>
            </form>
            <div class="register-link">
                Já tem conta? <a href="#" onclick="showLogin()">Entrar</a>
            </div>
        </div>

        <!-- FORGOT VIEW -->
        <div id="forgotView" class="hidden">
            <p style="color: #555; font-size: 14px; margin-bottom: 20px; text-align: center;">
                Digite seu email e enviaremos um token para recuperar sua senha.
            </p>
            <form id="forgotForm">
                <div class="form-group">
                    <label for="forgotEmail">E-mail</label>
                    <input type="email" id="forgotEmail" placeholder="seu@email.com" required>
                </div>
                <button type="submit" class="btn-login" id="btnForgot">Enviar Token</button>
            </form>
            <div class="register-link">
                <a href="#" onclick="showLogin()">Voltar para o login</a>
            </div>
        </div>

        <!-- RESET VIEW -->
        <div id="resetView" class="hidden">
            <p style="color: #555; font-size: 14px; margin-bottom: 20px; text-align: center;">
                Use o token recebido por email para criar uma nova senha.
            </p>
            <form id="resetForm">
                <div class="form-group">
                    <label for="resetEmail">E-mail</label>
                    <input type="email" id="resetEmail" readonly>
                </div>
                <div class="form-group">
                    <label for="resetToken">Token de recuperação</label>
                    <input type="text" id="resetToken" placeholder="Cole o token recebido" required>
                </div>
                <div class="form-group">
                    <label for="resetPassword">Nova senha</label>
                    <input type="password" id="resetPassword" placeholder="Mínimo 6 caracteres" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="resetPasswordConfirm">Confirmar nova senha</label>
                    <input type="password" id="resetPasswordConfirm" placeholder="Repita a nova senha" required minlength="6">
                </div>
                <button type="submit" class="btn-login" id="btnReset">Redefinir Senha</button>
            </form>
            <div class="register-link">
                <a href="#" onclick="showLogin()">Voltar para o login</a>
            </div>
        </div>

        <div class="loading" id="loading">Aguarde...</div>
    </div>

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
            var titles = { login: 'Entrar', register: 'Cadastrar', forgot: 'Recuperar Senha', reset: 'Nova Senha' };
            document.getElementById('formTitle').textContent = titles[view] || 'Entrar';
        }

        function showLogin() { showView('login'); }
        function showRegister() { showView('register'); }
        function showForgot() { showView('forgot'); }

        function showResetView(email, token) {
            document.getElementById('resetEmail').value = email || '';
            document.getElementById('resetToken').value = token || '';
            showView('reset');
        }

        // Check URL params on page load
        (function () {
            var params = new URLSearchParams(window.location.search);
            var token = params.get('token');
            var error = params.get('error');

            if (token) {
                storeToken(token);
                window.location.href = '/menu';
                return;
            }

            if (error === 'google_auth_failed') {
                showError('Falha na autenticação com Google. Tente novamente.');
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
                window.location.href = '/menu';
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
            var btn = document.getElementById('btnRegister');

            btn.disabled = true;
            setLoading(true);
            hideMessages();

            try {
                var res = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ name, email, password }),
                });

                var data = await res.json();

                if (!res.ok) {
                    var msgs = data.errors ? Object.values(data.errors).flat().join(', ') : (data.message || data.error);
                    showError(msgs || 'Erro ao cadastrar');
                    return;
                }

                storeToken(data.access_token);
                window.location.href = '/menu';
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
                // Auto-advance to reset view with the token
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

        // === GOOGLE BUTTON ===
        document.getElementById('btnGoogle').addEventListener('click', async function (e) {
            e.preventDefault();
            setLoading(true);
            hideMessages();

            try {
                var res = await fetch('/api/auth/google');
                var data = await res.json();
                if (data.url) {
                    window.location.href = data.url;
                } else {
                    showError((data.message || data.error) || 'Erro ao conectar com Google');
                    setLoading(false);
                }
            } catch (err) {
                showError('Erro ao conectar com Google.');
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
