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
                <h1 style="margin-top:0.5rem;">Gestión de Asistencias - <?= htmlspecialchars($unidad['nombre']) ?> (<?= $anio_actual ?>)</h1>
            </div>
            <?php if (!\App\Core\Auth::isReadOnly()): ?>
                <button onclick="document.getElementById('modal-extra').style.display='block'" class="btn btn-primary">Asistencia Flexible (Fuera de Ciclo)</button>
            <?php endif; ?>
        </header>

        <div class="glass-card">
            <?php if (empty($actividades)): ?>
                <p style="text-align:center; padding: 2rem; opacity:0.7;">No hay actividades registradas.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Actividad</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($actividades as $a): ?>
                        <tr>
                            <td><strong><?= date('d/m/Y', strtotime($a['fecha'])) ?></strong></td>
                            <td><?= htmlspecialchars($a['nombre_actividad']) ?></td>
                            <td>
                                <?php if ($a['es_extra']): ?>
                                    <span class="badge" style="background:var(--color-secondary); color:white;">Flexible</span>
                                <?php else: ?>
                                    <span class="badge" style="background:var(--color-primary); color:white;">Del Ciclo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/unidades/<?= $unidad['id'] ?>/asistencias/registrar/<?= $a['id'] ?>" class="btn btn-primary btn-sm"><?= \App\Core\Auth::isReadOnly() ? 'Ver Asistencia' : 'Pasar Lista / Ver' ?></a>
                                <?php if ($a['es_extra'] && !\App\Core\Auth::isReadOnly()): ?>
                                    <a href="/unidades/<?= $unidad['id'] ?>/hoja-ruta/editar?actividad_id=<?= $a['id'] ?>" class="btn btn-primary btn-sm" style="background:#28a745">Asociar Hoja de Ruta</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal para actividad extra -->
    <div id="modal-extra" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div class="glass-card" style="max-width:500px; margin: 5rem auto; position:relative; background:var(--color-bg);">
            <button onclick="document.getElementById('modal-extra').style.display='none'" style="position:absolute; top:1rem; right:1rem; background:none; border:none; cursor:pointer; font-size:1.5rem; color:var(--color-text);">&times;</button>
            <h2 style="margin-bottom:1.5rem; color:var(--color-primary)">Nueva Asistencia Flexible</h2>
            <form action="/unidades/<?= $unidad['id'] ?>/asistencias/crearExtra" method="POST">
                <div style="margin-bottom:1rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nombre de la Actividad</label>
                    <input type="text" name="nombre_actividad" required placeholder="Ej: Reunión de Apoderados" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                </div>
                <div style="margin-bottom:1rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Fecha</label>
                    <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                </div>
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Lugar</label>
                    <input type="text" name="lugar" required placeholder="Lugar" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Crear y Pasar Lista</button>
            </form>
        </div>
    </div>
</body>
</html>
