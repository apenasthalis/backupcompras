<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Senha</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 40px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; padding: 32px;">
        <h2 style="color: #333;">Recuperação de Senha</h2>
        <p style="color: #555; line-height: 1.6;">
            Olá, {{ $user->name }}!
        </p>
        <p style="color: #555; line-height: 1.6;">
            Recebemos uma solicitação para redefinir sua senha do Sistema de Compras.
        </p>
        <p style="color: #555; line-height: 1.6;">
            Seu token de recuperação é:
        </p>
        <div style="background: #f0fdf4; border: 1px solid #22c55e; border-radius: 8px; padding: 16px; text-align: center; margin: 16px 0;">
            <code style="font-size: 18px; color: #16a34a; font-weight: bold;">{{ $token }}</code>
        </div>
        <p style="color: #555; line-height: 1.6;">
            Use este token na página de login para definir uma nova senha.
            Este token expira em 60 minutos.
        </p>
        <p style="color: #555; line-height: 1.6;">
            Se não solicitou a recuperação de senha, ignore este email.
        </p>
        <p style="color: #999; font-size: 12px; margin-top: 32px;">
            Equipe Sistema de Compras
        </p>
    </div>
</body>
</html>
