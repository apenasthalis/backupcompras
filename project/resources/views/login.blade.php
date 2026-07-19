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
    </style>
</head>
<body>
    <div class="login-card">
        <h1>Entrar</h1>

        <div class="error-msg" id="errorMsg"></div>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" placeholder="seu@email.com" required>
            </div>
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" placeholder="Sua senha" required>
            </div>
            <button type="submit" class="btn-login" id="btnLogin">Entrar</button>
        </form>

        <div class="divider">ou</div>

        <a class="btn-google" id="btnGoogle" href="/api/auth/google">
            <svg viewBox="0 0 48 48">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.14.76-4.59l-7.98-6.19A23.99 23.99 0 0 0 0 24c0 3.77.87 7.35 2.56 10.56l7.97-5.97z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 5.97C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            Entrar com Google
        </a>

        <div class="loading" id="loading">Entrando...</div>

        <div class="register-link">
            Não tem conta? <a href="#" onclick="showRegister()">Cadastre-se</a>
        </div>
    </div>

    <script>
        const token = new URLSearchParams(window.location.search).get('token');
        if (token) {
            localStorage.setItem('jwt_token', token);
            window.location.href = '/menu';
        }

        if (new URLSearchParams(window.location.search).get('error') === 'google_auth_failed') {
            document.getElementById('errorMsg').textContent = 'Falha na autenticação com Google. Tente novamente.';
            document.getElementById('errorMsg').classList.add('visible');
        }

        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const btn = document.getElementById('btnLogin');
            const loading = document.getElementById('loading');
            const errorMsg = document.getElementById('errorMsg');

            btn.disabled = true;
            loading.classList.add('visible');
            errorMsg.classList.remove('visible');

            try {
                const res = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email, password }),
                });

                const data = await res.json();

                if (!res.ok) {
                    errorMsg.textContent = data.error || data.errors?.email?.[0] || 'Erro ao fazer login';
                    errorMsg.classList.add('visible');
                    return;
                }

                localStorage.setItem('jwt_token', data.access_token);
                window.location.href = '/menu';
            } catch (err) {
                errorMsg.textContent = 'Erro de conexão. Tente novamente.';
                errorMsg.classList.add('visible');
            } finally {
                btn.disabled = false;
                loading.classList.remove('visible');
            }
        });

        document.getElementById('btnGoogle').addEventListener('click', async function (e) {
            e.preventDefault();
            const loading = document.getElementById('loading');
            loading.classList.add('visible');

            try {
                const res = await fetch('/api/auth/google');
                const data = await res.json();
                if (data.url) {
                    window.location.href = data.url;
                }
            } catch (err) {
                document.getElementById('errorMsg').textContent = 'Erro ao conectar com Google.';
                document.getElementById('errorMsg').classList.add('visible');
                loading.classList.remove('visible');
            }
        });

        function showRegister() {
            document.querySelector('.login-card h1').textContent = 'Cadastrar';
            document.getElementById('loginForm').innerHTML = `
                <div class="form-group">
                    <label for="name">Nome</label>
                    <input type="text" id="name" placeholder="Seu nome" required>
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
            `;

            document.querySelector('.register-link').innerHTML = 'Já tem conta? <a href="#" onclick="showLogin()">Entrar</a>';

            document.getElementById('loginForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const name = document.getElementById('name').value;
                const email = document.getElementById('regEmail').value;
                const password = document.getElementById('regPassword').value;
                const btn = document.getElementById('btnRegister');
                const loading = document.getElementById('loading');
                const errorMsg = document.getElementById('errorMsg');

                btn.disabled = true;
                loading.classList.add('visible');
                errorMsg.classList.remove('visible');

                try {
                    const res = await fetch('/api/auth/register', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ name, email, password }),
                    });

                    const data = await res.json();

                    if (!res.ok) {
                        const msgs = data.errors ? Object.values(data.errors).flat().join(', ') : data.error;
                        errorMsg.textContent = msgs || 'Erro ao cadastrar';
                        errorMsg.classList.add('visible');
                        return;
                    }

                    localStorage.setItem('jwt_token', data.access_token);
                    window.location.href = '/menu';
                } catch (err) {
                    errorMsg.textContent = 'Erro de conexão. Tente novamente.';
                    errorMsg.classList.add('visible');
                } finally {
                    btn.disabled = false;
                    loading.classList.remove('visible');
                }
            });
        }

        function showLogin() {
            location.reload();
        }
    </script>
</body>
</html>
