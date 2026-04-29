<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .diff-box {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid var(--glass-border);
            white-space: pre-wrap;
            font-family: inherit;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .diff-original { border-left: 4px solid #dc3545; opacity:0.8;}
        .diff-nuevo { border-left: 4px solid #28a745; }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem;">
            <a href="/grupo/verActa/<?= $reunion['id'] ?>" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Acta</a>
            <h1 style="margin-top:0.5rem;">Revisar Modificación Propuesta</h1>
        </header>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="glass-card" style="background:rgba(220,53,69,0.2); border-color:#dc3545; margin-bottom:2rem; padding:1rem; text-align:center; color:#ffb3b3;">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="glass-card" style="grid-column: span 2;">
                <h2>Comparación de Cambios</h2>
                
                <h3>Tema</h3>
                <div style="display:flex; gap:1rem; margin-bottom:2rem;">
                    <div style="flex:1;">
                        <strong>Original:</strong>
                        <div class="diff-box diff-original"><?= htmlspecialchars($reunion['tema']) ?></div>
                    </div>
                    <div style="flex:1;">
                        <strong>Propuesto:</strong>
                        <div class="diff-box diff-nuevo"><?= htmlspecialchars($modificacion['nuevo_tema']) ?></div>
                    </div>
                </div>

                <h3>Contenido del Acta</h3>
                <div style="display:flex; gap:1rem;">
                    <div style="flex:1;">
                        <strong>Original:</strong>
                        <div class="diff-box diff-original"><?= htmlspecialchars($reunion['acta']) ?></div>
                    </div>
                    <div style="flex:1;">
                        <strong>Propuesto:</strong>
                        <div class="diff-box diff-nuevo"><?= htmlspecialchars($modificacion['nueva_acta']) ?></div>
                    </div>
                </div>
            </div>

            <div class="glass-card" style="grid-column: span 1;">
                <h3>Estado de Aprobación</h3>
                <p style="font-size:0.9rem; opacity:0.8; margin-bottom:1rem;">Esta modificación requiere la firma del Secretario(a) y del Responsable de Grupo.</p>
                
                <ul style="list-style:none; padding:0; margin-bottom:2rem;">
                    <li style="margin-bottom:0.5rem;">
                        <?php if ($modificacion['aprobado_secretario']): ?>
                            ✅ <strong>Secretario(a):</strong> Aprobado
                        <?php else: ?>
                            ⏳ <strong>Secretario(a):</strong> Pendiente
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if ($modificacion['aprobado_responsable']): ?>
                            ✅ <strong>Responsable Grupo:</strong> Aprobado
                        <?php else: ?>
                            ⏳ <strong>Responsable Grupo:</strong> Pendiente
                        <?php endif; ?>
                    </li>
                </ul>

                <hr style="border-color:var(--glass-border); margin-bottom:1.5rem;">

                <form action="/grupo/procesarModificacion/<?= $modificacion['id'] ?>" method="POST">
                    
                    <?php if ($isSecretario && !$modificacion['aprobado_secretario']): ?>
                        <div style="margin-bottom:1rem;">
                            <input type="hidden" name="aprobar_como" value="Secretario">
                            <button type="submit" name="accion" value="aprobar" class="btn btn-primary" style="width:100%; background:#28a745;">Aprobar como Secretario(a)</button>
                        </div>
                    <?php endif; ?>

                    <?php if ($isResponsable && !$modificacion['aprobado_responsable']): ?>
                        <div style="margin-bottom:1rem;">
                            <input type="hidden" name="aprobar_como" value="Responsable">
                            <button type="submit" name="accion" value="aprobar" class="btn btn-primary" style="width:100%; background:#28a745;">Aprobar como Responsable</button>
                        </div>
                    <?php endif; ?>
                    
                    <div style="margin-top:2rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-size:0.9rem; color:#ffb3b3;">¿Deseas rechazar esta propuesta?</label>
                        <textarea name="motivo" placeholder="Escribe el motivo del rechazo aquí..." rows="3" style="width:100%; padding:0.5rem; margin-bottom:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border);"></textarea>
                        <button type="submit" name="accion" value="rechazar" class="btn btn-primary" style="width:100%; background:#dc3545;" onclick="return confirm('¿Estás seguro de rechazar esta modificación? Se cancelará la propuesta por completo.')">Rechazar Propuesta</button>
                    </div>

                </form>
            </div>
        </div>
    </main>
</body>
</html>
