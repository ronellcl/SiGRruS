<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .activity-card { display: flex; justify-content: space-between; align-items: center; padding: 1rem; margin-bottom: 1rem; border-left: 4px solid var(--color-primary); }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem;">
            <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver</a>
            <h1 style="margin-top:0.5rem;">Programa de Actividades</h1>
            <p>Beneficiario: <strong><?= htmlspecialchars($beneficiario['nombre_completo']) ?></strong></p>
        </header>

        <div class="glass-card">
            <?php if (empty($actividades)): ?>
                <p style="text-align:center; opacity:0.6;">No hay actividades programadas para este ciclo.</p>
            <?php else: ?>
                <?php foreach($actividades as $a): ?>
                    <div class="glass-card activity-card">
                        <div>
                            <span style="font-size:0.8rem; opacity:0.7;"><?= date('d/m/Y', strtotime($a['fecha'])) ?></span>
                            <h3 style="margin:0.2rem 0;"><?= htmlspecialchars($a['nombre_actividad']) ?></h3>
                            <p style="font-size:0.9rem;">📍 <?= htmlspecialchars($a['lugar']) ?></p>
                        </div>
                        <div>
                            <?php if ($a['hoja_ruta_id']): ?>
                                <a href="/hoja-ruta/imprimir/<?= $a['hoja_ruta_id'] ?>" target="_blank" class="btn btn-primary btn-sm">Ver Detalle (PDF)</a>
                            <?php else: ?>
                                <span class="badge" style="background:rgba(255,255,255,0.1);">Info no disponible</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
