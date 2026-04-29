<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--glass-border); }
        .table th { font-weight: 600; color: var(--color-primary); }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
                <h1 style="margin-top:0.5rem;">Hojas de Ruta - <?= htmlspecialchars($unidad['nombre']) ?> (<?= $anio_actual ?>)</h1>
            </div>
            <?php if (!\App\Core\Auth::isReadOnly()): ?>
                <a href="/unidades/<?= $unidad['id'] ?>/hoja-ruta/crear" class="btn btn-primary">Nueva Hoja de Ruta</a>
            <?php endif; ?>
        </header>

        <div class="glass-card">
            <?php if (empty($hojas)): ?>
                <p style="text-align:center; padding: 2rem; opacity:0.7;">No hay hojas de ruta registradas.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Actividad</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($hojas as $h): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($h['nombre_ciclo'] ?: $h['nombre_actividad_manual']) ?></strong>
                            </td>
                            <td>
                                <?php if ($h['actividad_id']): ?>
                                    <span class="badge" style="background:#28a745; color:white;">Del Ciclo</span>
                                <?php else: ?>
                                    <span class="badge" style="background:var(--color-secondary); color:white;">Fuera de Ciclo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/unidades/<?= $unidad['id'] ?>/hoja-ruta/ver/<?= $h['id'] ?>" class="btn btn-primary btn-sm">Ver</a>
                <?php if (!\App\Core\Auth::isReadOnly()): ?>
                    <a href="/unidades/<?= $unidad['id'] ?>/hoja-ruta/editar/<?= $h['id'] ?>" class="btn btn-primary btn-sm" style="background:var(--color-secondary)">Editar</a>
                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
