<?php
session_start();
// Si ya existe el config, redirigir al dashboard
if (file_exists('../app/config.php')) {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_dir = str_replace('/install/index.php', '', $script_name);
    if ($base_dir === $script_name) {
        $base_dir = dirname($script_name);
    }
    $base_dir = rtrim($base_dir, '/\\');
    
    header("Location: " . $base_dir . "/public/");
    exit;
}

$step = $_GET['step'] ?? 1;
$error = $_SESSION['install_error'] ?? null;
unset($_SESSION['install_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador de SiGRruS</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-bg: #f4f6f9;
            --color-text: #2c3e50;
            --color-primary: #002D62;
            --color-secondary: #E3000F;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
        }
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #002D62 0%, #001530 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 2rem;
        }
        .install-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 3rem;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.3);
        }
        .form-control {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1.5rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            color: white;
            font-family: 'Nunito', sans-serif;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.5); }
        .btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background: var(--color-secondary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
        }
        .btn:hover {
            background: #ff1a2b;
            transform: translateY(-2px);
        }
        .error-box {
            background: rgba(227, 0, 15, 0.2);
            border: 1px solid #E3000F;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            color: #ffb3b3;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            padding-bottom: 1rem;
        }
        .step-indicator span {
            opacity: 0.5;
            font-weight: 600;
        }
        .step-indicator span.active {
            opacity: 1;
            color: #4ade80;
        }
        label { display: block; margin-bottom: 0.5rem; font-size: 0.9rem; opacity: 0.9; }
    </style>
</head>
<body>

<div class="install-card">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h1 style="margin:0; font-size: 2.5rem; letter-spacing: -1px;">SiGRruS</h1>
        <p style="opacity: 0.7; margin-top: 0.5rem;">Instalación y Configuración Inicial</p>
    </div>

    <div class="step-indicator">
        <span class="<?= $step == 1 ? 'active' : '' ?>">1. Requisitos</span>
        <span class="<?= $step == 2 ? 'active' : '' ?>">2. Base de Datos</span>
        <span class="<?= $step == 3 ? 'active' : '' ?>">3. Grupo y Admin</span>
    </div>

    <?php if ($error): ?>
        <div class="error-box"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($step == 1): ?>
        <?php
            $php_ok = version_compare(PHP_VERSION, '8.0.0', '>=');
            $pdo_ok = extension_loaded('pdo_mysql');
            $dir_ok = is_writable('../app/');
            $all_ok = $php_ok && $pdo_ok && $dir_ok;
        ?>
        <h2 style="margin-bottom: 1.5rem;">Verificación del Sistema</h2>
        <ul style="list-style: none; padding: 0; margin-bottom: 2rem;">
            <li style="margin-bottom: 1rem;">
                <?= $php_ok ? '✅' : '❌' ?> PHP Version 8.0 o superior (Actual: <?= PHP_VERSION ?>)
            </li>
            <li style="margin-bottom: 1rem;">
                <?= $pdo_ok ? '✅' : '❌' ?> Extensión PDO MySQL instalada
            </li>
            <li style="margin-bottom: 1rem;">
                <?= $dir_ok ? '✅' : '❌' ?> Permisos de escritura en directorio <code>/app</code>
            </li>
        </ul>
        
        <?php if ($all_ok): ?>
            <a href="?step=2" class="btn">Continuar a Base de Datos</a>
        <?php else: ?>
            <p style="color: #ffb3b3;">Por favor, resuelve los problemas indicados arriba y recarga la página.</p>
        <?php endif; ?>

    <?php elseif ($step == 2): ?>
        <h2 style="margin-bottom: 1.5rem;">Conexión a Base de Datos</h2>
        <p style="font-size: 0.9rem; opacity: 0.8; margin-bottom: 1.5rem;">El sistema instalará automáticamente las tablas en los entornos especificados.</p>
        <form action="process.php" method="POST">
            <input type="hidden" name="action" value="setup_db">
            
            <label>Host de Base de Datos</label>
            <input type="text" name="db_host" class="form-control" value="localhost" required>
            
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label>Usuario DB</label>
                    <input type="text" name="db_user" class="form-control" placeholder="Ej: root" required>
                </div>
                <div>
                    <label>Contraseña DB</label>
                    <input type="password" name="db_pass" class="form-control" placeholder="Opcional">
                </div>
            </div>

            <label>Nombre de la Base de Datos (Producción)</label>
            <input type="text" name="db_name" class="form-control" placeholder="Ej: sigrrus_prod" required>

            <label>Nombre de la Base de Datos (Entrenamiento)</label>
            <input type="text" name="db_test_name" class="form-control" placeholder="Ej: sigrrus_test" required>
            
            <button type="submit" class="btn">Conectar e Instalar Tablas</button>
        </form>

    <?php elseif ($step == 3): ?>
        <h2 style="margin-bottom: 1.5rem;">Información del Grupo</h2>
        <form action="process.php" method="POST">
            <input type="hidden" name="action" value="setup_group">
            
            <label>Nombre de tu Grupo Scout</label>
            <input type="text" name="grupo_nombre" class="form-control" placeholder="Ej: Grupo Scout Antilelfün" required>

            <hr style="border:0; border-top:1px solid rgba(255,255,255,0.2); margin: 2rem 0;">
            <h3 style="margin-bottom: 1.5rem;">Cuenta de Superusuario (Administrador)</h3>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label>Nombre Completo</label>
                    <input type="text" name="admin_nombre" class="form-control" required>
                </div>
                <div>
                    <label>RUT</label>
                    <input type="text" name="admin_rut" class="form-control" placeholder="12345678-9" required>
                </div>
            </div>

            <label>Correo Electrónico (Login)</label>
            <input type="email" name="admin_email" class="form-control" placeholder="admin@grupo.cl" required>

            <label>Contraseña</label>
            <input type="password" name="admin_pass" class="form-control" required>
            
            <button type="submit" class="btn" style="background:#4ade80; color:#000;">Finalizar Instalación</button>
        </form>

    <?php elseif ($step == 'done'): ?>
        <div style="text-align: center;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
            <h2 style="margin-bottom: 1rem;">¡Instalación Completada!</h2>
            <p style="margin-bottom: 2rem; opacity: 0.9;">SiGRruS está listo para ser utilizado en tu grupo.</p>
            
            <div class="error-box" style="background: rgba(255, 193, 7, 0.2); border-color: #ffc107; color: #ffeb3b; text-align: left;">
                <strong>⚠️ POR SEGURIDAD:</strong> Es absolutamente necesario que <strong>ELIMINES LA CARPETA <code>/install</code></strong> de tu servidor antes de continuar.
            </div>

            <a href="../public/" class="btn" style="background:var(--color-primary);">Ir al Panel de Control</a>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
