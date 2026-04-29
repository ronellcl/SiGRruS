<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .login-container { max-width: 400px; margin: 5rem auto; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid var(--glass-border); border-radius: 8px; background: rgba(255,255,255,0.5); }
        @media (prefers-color-scheme: dark) { .form-control { background: rgba(0,0,0,0.2); color: white; } }
    </style>
</head>
<body>
    <main class="login-container">
        <div class="glass-card" style="width:100%; max-width:400px; padding:2rem;">
            <div style="text-align:center; margin-bottom:2rem;">
                <?php 
                    $isTest = isset($_SESSION['sigrrus_env']) && $_SESSION['sigrrus_env'] === 'testing';
                    $envLabel = $isTest ? 'ENTRENAMIENTO' : 'PRODUCCIÓN';
                    $envColor = $isTest ? '#10b981' : '#6366f1';
                ?>
                <a href="<?= rtrim(APP_URL, '/') ?>/?switch_env=true" style="text-decoration:none;">
                    <span style="font-size:0.7rem; font-weight:bold; color:<?= $envColor ?>; letter-spacing:0.2em;"><?= $envLabel ?></span>
                </a>
                <h2 style="margin-top:0.5rem;">Iniciar Sesión</h2>
                <p style="font-size:0.9rem; opacity:0.7;">SiGRruS Identity Framework</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div style="background:var(--color-secondary); color:white; padding:1rem; border-radius:8px; margin-bottom:1.5rem; text-align:center;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="/auth/login" method="POST">
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>
                <div class="form-group">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <label>Contraseña</label>
                        <a href="/auth/forgotPassword" style="font-size:0.8rem; color:var(--color-primary); text-decoration:none;">¿Olvidaste tu contraseña?</a>
                    </div>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Ingresar</button>
            </form>
            <div style="text-align:center; margin-top: 1.5rem;">
                <a href="<?= APP_URL ?>" style="color:var(--color-text); text-decoration:none;">Volver al Inicio</a>
            </div>
        </div>
    </div>
</body>
</html>
