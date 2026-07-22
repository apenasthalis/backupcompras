<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 40px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; padding: 32px;">
        <h2 style="color: #333;">Bem-vindo, {{ $user->name }}!</h2>
        <p style="color: #555; line-height: 1.6;">
            Sua conta no Sistema de Compras foi criada com sucesso.
        </p>
        <p style="color: #555; line-height: 1.6;">
            Seu email de acesso: <strong>{{ $user->email }}</strong>
        </p>
        <p style="color: #555; line-height: 1.6;">
            Caso não tenha criado esta conta, ignore este email.
        </p>
        <p style="color: #999; font-size: 12px; margin-top: 32px;">
            Equipe Sistema de Compras
        </p>
    </div>
</body>
</html>
