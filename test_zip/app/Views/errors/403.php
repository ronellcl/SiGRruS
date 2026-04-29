<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Restringido - SiGRruS</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            text-align: center;
        }
        .error-container {
            max-width: 500px;
            padding: 3rem;
            animation: fadeIn 0.8s ease-out;
        }
        .error-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            display: block;
        }
        h1 { font-size: 2.5rem; margin-bottom: 1rem; color: var(--color-primary); }
        p { opacity: 0.8; font-size: 1.1rem; margin-bottom: 2rem; line-height: 1.6; }
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="glass-card error-container">
        <span class="error-icon">🔒</span>
        <h1>Acceso Restringido</h1>
        <p><?= htmlspecialchars($message ?? 'Lo sentimos, no tienes los permisos necesarios para acceder a esta sección con tu perfil actual.') ?></p>
        
        <div style="display:flex; flex-direction:column; gap:1rem;">
            <a href="/dashboard" class="btn btn-primary">Volver al Dashboard</a>
            <button onclick="history.back()" class="btn" style="background:rgba(255,255,255,0.1); color:#fff; border:1px solid rgba(255,255,255,0.2);">Regresar a la página anterior</button>
        </div>
    </div>
</body>
</html>
