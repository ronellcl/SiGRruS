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
        .table th, .table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--glass-border); font-size:0.9rem; }
        .status-badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
        .status-updated { background: #28a745; color: white; }
        .status-pending { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem;">
            <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
            <h1 style="margin-top:0.5rem;">Fichas Médicas del Grupo</h1>
            <p>Visión global de salud de todos los beneficiarios.</p>
        </header>

        <div class="glass-card" style="margin-bottom: 2rem;">
            <form action="/grupo/fichas" method="GET" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
                <div>
                    <label style="font-size:0.8rem; opacity:0.8;">Filtrar por Unidad</label>
                    <select name="unidad_id" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                        <option value="">Todas las Unidades</option>
                        <?php foreach($unidades as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= $filters['unidad_id'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="font-size:0.8rem; opacity:0.8;">Nombre Beneficiario</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($filters['nombre']) ?>" placeholder="Buscar por nombre..." style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                </div>
                <div>
                    <label style="font-size:0.8rem; opacity:0.8;">RUT</label>
                    <input type="text" name="rut" value="<?= htmlspecialchars($filters['rut']) ?>" placeholder="Buscar por RUT..." style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                </div>
                <div>
                    <label style="font-size:0.8rem; opacity:0.8;">Apoderado Responsable</label>
                    <input type="text" name="apoderado" value="<?= htmlspecialchars($filters['apoderado']) ?>" placeholder="Nombre apoderado..." style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                </div>
                <div style="display:flex; gap:0.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex:1;">Filtrar</button>
                    <a href="/grupo/fichas" class="btn btn-primary" style="background:var(--color-secondary); flex:1; text-align:center; text-decoration:none; display:flex; align-items:center; justify-content:center;">Limpiar</a>
                </div>
            </form>
        </div>

        <div class="glass-card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Unidad</th>
                        <th>Beneficiario</th>
                        <th>Apoderado</th>
                        <th>Estado Ficha</th>
                        <th>Tipo Sangre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($beneficiarios as $b): 
                        $ficha = $fichas[$b['id']] ?? null;
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($b['unidad_nombre'] ?? 'Sin Unidad') ?></strong></td>
                        <td><strong><?= htmlspecialchars($b['nombre_completo']) ?></strong><br><small><?= htmlspecialchars($b['rut']) ?></small></td>
                        <td><?= htmlspecialchars($b['apoderado_nombre'] ?? 'Sin asignar') ?></td>
                        <td>
                            <?php if ($ficha): ?>
                                <span class="status-badge status-updated">Actualizada</span>
                            <?php else: ?>
                                <span class="status-badge status-pending">Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $ficha ? htmlspecialchars($ficha['tipo_sangre']) : '-' ?></td>
                        <td><?= $ficha ? htmlspecialchars($ficha['alergias']) : '-' ?></td>
                        <td>
                            <?php if ($ficha): ?>
                                <a href="/fichas/ver/<?= $b['id'] ?>" class="btn btn-primary btn-sm">Ver Detalle</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
