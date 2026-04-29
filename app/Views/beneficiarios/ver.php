<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem;">
            <h1>Perfil de Beneficiario</h1>
            <h2 style="color:var(--color-primary);"><?= htmlspecialchars($beneficiario['nombre_completo']) ?></h2>
        </header>

        <div class="dashboard-grid">
            <div class="glass-card">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="margin:0;">Información General</h3>
                    <?php if (in_array($user['rol'], ['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad'])): ?>
                        <a href="/beneficiarios/editar/<?= $beneficiario['id'] ?>" class="btn btn-sm" style="background:var(--color-primary); color:white;">Editar Perfil</a>
                    <?php endif; ?>
                </div>
                <hr style="border:0; border-top:1px solid var(--glass-border); margin:1rem 0;">
                <p><strong><?= htmlspecialchars($beneficiario['tipo_documento'] ?? 'RUT') ?>:</strong> <?= htmlspecialchars($beneficiario['rut'] ?? '') ?></p>
                <p><strong>Nacionalidad:</strong> <?= htmlspecialchars($beneficiario['nacionalidad'] ?? 'Chilena') ?></p>
                <p><strong>Fecha Nacimiento:</strong> <?= date('d/m/Y', strtotime($beneficiario['fecha_nacimiento'])) ?></p>
                <p><strong>Unidad:</strong> <?= htmlspecialchars($unidad['nombre']) ?></p>
                <p><strong>Subgrupo:</strong> <?= htmlspecialchars($beneficiario['subgrupo'] ?: 'No asignado') ?></p>
            </div>

            <div class="glass-card">
                <h3>Contactos de Emergencia / Apoderados</h3>
                <hr style="border:0; border-top:1px solid var(--glass-border); margin:1rem 0;">
                <div style="margin-bottom:1rem; padding:0.75rem; background:rgba(99, 102, 241, 0.1); border-radius:8px; border-left:4px solid var(--color-primary);">
                    <p style="margin:0; font-size:0.8rem; text-transform:uppercase; font-weight:bold; color:var(--color-primary);">Titular</p>
                    <p style="margin:0.2rem 0 0 0;"><strong><?= htmlspecialchars($beneficiario['apoderado_nombre']) ?></strong></p>
                    <p style="margin:0; font-size:0.85rem; opacity:0.8;">RUT: <?= htmlspecialchars($beneficiario['apoderado_rut']) ?></p>
                </div>

                <?php if ($beneficiario['suplente1_nombre']): ?>
                <div style="margin-bottom:1rem; padding:0.75rem; background:rgba(255,255,255,0.05); border-radius:8px; border-left:4px solid var(--color-secondary);">
                    <p style="margin:0; font-size:0.8rem; text-transform:uppercase; font-weight:bold; opacity:0.7;">Suplente 1</p>
                    <p style="margin:0.2rem 0 0 0;"><strong><?= htmlspecialchars($beneficiario['suplente1_nombre']) ?></strong></p>
                    <p style="margin:0; font-size:0.85rem; opacity:0.8;">
                        RUT: <?= htmlspecialchars($beneficiario['suplente1_rut']) ?> 
                        <?= $beneficiario['suplente1_telefono'] ? ' | Tel: ' . htmlspecialchars($beneficiario['suplente1_telefono']) : '' ?>
                    </p>
                </div>
                <?php endif; ?>

                <?php if ($beneficiario['suplente2_nombre']): ?>
                <div style="margin-bottom:1rem; padding:0.75rem; background:rgba(255,255,255,0.05); border-radius:8px; border-left:4px solid var(--color-secondary);">
                    <p style="margin:0; font-size:0.8rem; text-transform:uppercase; font-weight:bold; opacity:0.7;">Suplente 2</p>
                    <p style="margin:0.2rem 0 0 0;"><strong><?= htmlspecialchars($beneficiario['suplente2_nombre']) ?></strong></p>
                    <p style="margin:0; font-size:0.85rem; opacity:0.8;">
                        RUT: <?= htmlspecialchars($beneficiario['suplente2_rut']) ?> 
                        <?= $beneficiario['suplente2_telefono'] ? ' | Tel: ' . htmlspecialchars($beneficiario['suplente2_telefono']) : '' ?>
                    </p>
                </div>
                <?php endif; ?>

                <?php if (!$beneficiario['suplente1_nombre'] && !$beneficiario['suplente2_nombre']): ?>
                    <p style="font-size:0.85rem; opacity:0.6; font-style:italic;">No hay apoderados suplentes registrados.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
