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
        <h1 style="text-align:center; color:var(--color-primary); margin-bottom:1.5rem;">Nueva Contraseña</h1>
        <p style="text-align:center; opacity:0.8; margin-bottom:1.5rem;">Crea una nueva contraseña para tu cuenta.</p>
        
        <form action="/auth/resetPassword/<?= $token ?>" method="POST">
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nueva Contraseña</label>
                <input type="password" name="password" required minlength="6" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:white;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Confirmar Contraseña</label>
                <input type="password" name="confirm_password" required minlength="6" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:white;">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Restablecer Contraseña</button>
        </form>
    </div>
</body>
</html>
