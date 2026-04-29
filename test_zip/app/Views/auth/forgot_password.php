<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #0f172a; margin: 0; }
        .login-card { max-width: 400px; width: 100%; }
    </style>
</head>
<body>
    <div class="login-card glass-card">
        <h1 style="text-align:center; color:var(--color-primary); margin-bottom:1.5rem;">Recuperar Contraseña</h1>
        
        <?php if (isset($success)): ?>
            <div style="background:rgba(16,185,129,0.2); color:#10b981; padding:1rem; border-radius:8px; margin-bottom:1rem; text-align:center;">
                <?= $success ?>
            </div>
            <a href="/auth/login" class="btn btn-primary" style="width:100%; text-align:center; display:block;">Volver al Login</a>
        <?php else: ?>
            <p style="text-align:center; opacity:0.8; margin-bottom:1.5rem;">Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>
            
            <form action="/auth/sendResetLink" method="POST">
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Correo Electrónico</label>
                    <input type="email" name="email" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:white;">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Enviar Enlace</button>
                <div style="text-align:center; margin-top:1rem;">
                    <a href="/auth/login" style="color:var(--color-text); text-decoration:none; opacity:0.7; font-size:0.9rem;">&larr; Volver al Login</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
