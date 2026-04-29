<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top right, #1e1b4b, #0f172a);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .portada-container {
            max-width: 900px;
            width: 90%;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .logos-header {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2rem;
            margin-bottom: 3rem;
            animation: fadeInDown 1s ease-out;
        }

        .logo-box {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            padding: 1.5rem;
            border-radius: 24px;
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }

        .logo-box:hover {
            transform: translateY(-5px);
        }

        .logo-img {
            height: 100px;
            width: auto;
            object-fit: contain;
        }

        .welcome-text {
            margin-bottom: 4rem;
            animation: fadeIn 1.2s ease-out;
        }

        .welcome-text h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin: 0;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-text p {
            font-size: 1.2rem;
            color: #94a3b8;
            margin-top: 1rem;
        }

        .env-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        .env-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            padding: 2.5rem;
            border-radius: 32px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .env-card:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #6366f1;
            transform: scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .env-card.production:hover {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2) 0%, rgba(168, 85, 247, 0.2) 100%);
        }

        .env-card.testing:hover {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.2) 100%);
        }

        .env-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            display: block;
        }

        .env-card h2 {
            font-size: 1.8rem;
            margin: 0 0 0.5rem 0;
        }

        .env-card p {
            font-size: 0.95rem;
            color: #94a3b8;
            line-height: 1.5;
        }

        .tag {
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.25rem 0.75rem;
            border-radius: 100px;
        }

        .tag-prod { background: #6366f1; }
        .tag-test { background: #10b981; }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Decorative blobs */
        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: var(--primary-gradient);
            filter: blur(100px);
            opacity: 0.15;
            border-radius: 50%;
            z-index: 0;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="blob" style="top: -100px; left: -100px;"></div>
    <div class="blob" style="bottom: -100px; right: -100px; background: linear-gradient(135deg, #10b981 0%, #3b82f6 100%);"></div>

    <div class="portada-container">
        <div class="logos-header">
            <?php if (!empty($global_config['logo_path'])): ?>
                <div class="logo-box">
                    <img src="<?= $global_config['logo_path'] ?>" class="logo-img" alt="Logo Grupo">
                </div>
            <?php endif; ?>
            
            <?php if (!empty($global_config['asociacion_logo_path'])): ?>
                <div class="logo-box">
                    <img src="<?= $global_config['asociacion_logo_path'] ?>" class="logo-img" alt="Logo Asociación">
                </div>
            <?php endif; ?>
        </div>

        <div class="welcome-text">
            <?php if (empty($global_config['logo_path']) && empty($global_config['asociacion_logo_path'])): ?>
                <h1>Bienvenido</h1>
                <p>Sistema de Gestión de Guías y Scouts (No Oficial)</p>
            <?php else: ?>
                <h1><?= htmlspecialchars($global_config['nombre_grupo']) ?></h1>
                <p>Gestión Institucional de Guías y Scouts</p>
            <?php endif; ?>
        </div>

        <div class="env-selector">
            <a href="<?= rtrim(APP_URL, '/') ?>/home/setEnv/production" class="env-card production">
                <span class="tag tag-prod">Oficial</span>
                <span class="env-icon">🚀</span>
                <h2>Producción</h2>
                <p>Acceso al sistema real del grupo. Uso oficial para registros, finanzas y gestión activa.</p>
            </a>

            <a href="<?= rtrim(APP_URL, '/') ?>/home/setEnv/testing" class="env-card testing">
                <span class="tag tag-test">Prácticas</span>
                <span class="env-icon">🛠️</span>
                <h2>Entrenamiento</h2>
                <p>Plataforma de pruebas totalmente independiente. Ideal para practicar y capacitar nuevos dirigentes.</p>
            </a>
        </div>

        <div style="margin-top: 4rem; font-size: 0.8rem; opacity: 0.4;">
            SiGRruS Identity Framework &copy; <?= date('Y') ?>
        </div>
    </div>
</body>
</html>
