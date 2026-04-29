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
        .login-card { max-width: 450px; width: 100%; }
    </style>
</head>
<body>
    <div class="login-card glass-card">
        <h1 style="text-align:center; color:#ffc107; margin-bottom:1rem;">⚠️ Cambio de Contraseña</h1>
        <p style="text-align:center; opacity:0.8; margin-bottom:1.5rem;">Por seguridad, debes actualizar tu contraseña temporal antes de continuar.</p>
        
        <?php if (isset($error)): ?>
            <div style="background:rgba(239,68,68,0.2); color:#ef4444; padding:1rem; border-radius:8px; margin-bottom:1rem; text-align:center;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="/auth/changePassword" method="POST">
            <div style="margin-bottom:1.2rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nueva Contraseña</label>
                <input type="password" name="password" required minlength="6" autofocus style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:white;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Confirmar Nueva Contraseña</label>
                <input type="password" name="confirm_password" required minlength="6" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:white;">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; background:#ffc107; color:#000; font-weight:bold;">Actualizar Contraseña y Continuar</button>
        </form>
        
        <div style="text-align:center; margin-top:1.5rem; opacity:0.6; font-size:0.8rem;">
            <p>Una vez cambiada, podrás acceder a todas las funciones del sistema.</p>
        </div>
    </div>
</body>
</html>
