<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .timeline { border-left: 2px solid var(--color-primary); padding-left: 1.5rem; margin-left: 0.5rem; }
        .timeline-item { margin-bottom: 2rem; position: relative; }
        .timeline-item::before { content: ""; position: absolute; left: -1.95rem; top: 0.25rem; width: 12px; height: 12px; border-radius: 50%; background: var(--color-primary); }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem;">
            <a href="/grupo/dirigentes" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver a Gestión de Dirigentes</a>
            <h1 style="margin-top:0.5rem;">Historial y Perfil: <?= htmlspecialchars($dirigente['nombre']) ?></h1>
        </header>

        <div style="display:grid; grid-template-columns: 1fr 2fr; gap:2rem;">
            <!-- INFO BÁSICA -->
            <div>
                <div class="glass-card" style="margin-bottom:2rem;">
                    <h3 style="color:var(--color-primary); margin-bottom:1rem;">Datos Personales</h3>
                    <p><strong>Nombre:</strong> <?= htmlspecialchars($dirigente['nombre'] ?? '') ?></p>
                    <p><strong><?= htmlspecialchars($dirigente['tipo_documento'] ?? 'RUT') ?>:</strong> <?= htmlspecialchars($dirigente['rut'] ?? '-') ?></p>
                    <p><strong>Nacionalidad:</strong> <?= htmlspecialchars($dirigente['nacionalidad'] ?? 'Chilena') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($dirigente['email'] ?? '') ?></p>
                </div>

                <div class="glass-card">
                    <h3 style="color:var(--color-secondary); margin-bottom:1rem;">Certificados</h3>
                    <?php if (empty($certificados)): ?>
                        <p style="opacity:0.5; font-size:0.9rem;">No hay certificados registrados.</p>
                    <?php else: ?>
                        <ul style="list-style:none; padding:0; font-size:0.9rem;">
                            <?php foreach($certificados as $c): ?>
                                <li style="margin-bottom:0.75rem; padding-bottom:0.75rem; border-bottom:1px solid var(--glass-border);">
                                    <strong><?= htmlspecialchars($c['tipo']) ?></strong> (<?= $c['anio'] ?>)<br>
                                    <small style="opacity:0.7;">Emitido: <?= date('d/m/Y', strtotime($c['fecha_emision'])) ?></small><br>
                                    <a href="<?= $c['archivo_path'] ?>" target="_blank" style="color:var(--color-primary);">Ver Documento</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- HISTORIAL DE ROLES -->
            <div class="glass-card">
                <h3 style="color:var(--color-primary); margin-bottom:2rem;">Trayectoria y Roles</h3>
                
                <div class="timeline">
                    <?php if (empty($historial)): ?>
                        <p style="opacity:0.5;">No hay historial de cargos registrado.</p>
                    <?php else: ?>
                        <?php foreach($historial as $h): ?>
                            <div class="timeline-item">
                                <div style="font-weight:bold; color:var(--color-primary); font-size:1.1rem;"><?= htmlspecialchars($h['rol']) ?></div>
                                <div style="font-size:0.9rem; margin-bottom:0.5rem;">
                                    <?= htmlspecialchars($h['unidad_nombre'] ?: 'Nivel Grupo') ?> 
                                    <?php if ($h['anio']): ?> • Año Scout <?= $h['anio'] ?><?php endif; ?>
                                </div>
                                <div style="font-size:0.8rem; opacity:0.6;">
                                    Desde: <?= date('d/m/Y', strtotime($h['fecha_inicio'])) ?>
                                    <?php if ($h['fecha_fin']): ?>
                                        Hasta: <?= date('d/m/Y', strtotime($h['fecha_fin'])) ?>
                                    <?php else: ?>
                                        <span class="badge" style="background:#28a745; color:white; font-size:0.7rem; padding:0.1rem 0.3rem; margin-left:0.5rem;">Activo</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
