<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .acta-content {
            background: rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            min-height: 300px;
            white-space: pre-wrap;
            font-family: inherit;
            line-height: 1.6;
        }
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--glass-border); }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <a href="/grupo/consejo" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Consejo</a>
                <h1 style="margin-top:0.5rem;">Acta de Reunión</h1>
            </div>
            <div style="display:flex; gap:0.5rem;">
                <?php if ($reunion['tipo_organo'] === 'Consejo'): ?>
                    <?php if ($modificacion): ?>
                        <?php if ($isResponsable || $isSecretario): ?>
                            <a href="/grupo/revisarModificacion/<?= $modificacion['id'] ?>" class="btn btn-primary" style="background:#ffc107; color:#000;">⚠️ Revisar Modificación</a>
                        <?php else: ?>
                            <button class="btn btn-primary" disabled style="background:#6c757d; cursor:not-allowed;">Modificación en Revisión</button>
                        <?php endif; ?>
                    <?php else: ?>
                        <button onclick="document.getElementById('modal-editar').style.display='block'" class="btn btn-primary">Proponer Modificación</button>
                    <?php endif; ?>
                <?php endif; ?>
                
                <a href="/grupo/imprimirActa/<?= $reunion['id'] ?>" target="_blank" class="btn btn-primary" style="background:var(--color-secondary)">🖨️ Ver PDF / Imprimir</a>
            </div>
        </header>

        <?php if ($modificacion && !$isResponsable && !$isSecretario): ?>
            <div class="glass-card" style="background:rgba(255, 193, 7, 0.2); border-color:#ffc107; margin-bottom:2rem; padding:1rem;">
                <strong>⚠️ Hay una modificación propuesta en revisión.</strong><br>
                <small>Secretaría o Responsable de Grupo deben aprobar o rechazar la propuesta antes de poder solicitar nuevos cambios.</small>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="glass-card" style="background:rgba(40,167,69,0.2); border-color:#28a745; margin-bottom:2rem; padding:1rem; text-align:center;">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="glass-card" style="background:rgba(220,53,69,0.2); border-color:#dc3545; margin-bottom:2rem; padding:1rem; text-align:center; color:#ffb3b3;">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="glass-card" style="grid-column: span 2;">
                <div style="display:flex; justify-content:space-between; margin-bottom:2rem; padding-bottom:1rem; border-bottom:1px solid var(--glass-border);">
                    <div>
                        <strong>Tema:</strong><br>
                        <span style="font-size:1.2rem;"><?= htmlspecialchars($reunion['tema']) ?></span>
                    </div>
                    <div style="text-align:right;">
                        <strong>Fecha de la Reunión:</strong><br>
                        <span style="font-size:1.2rem;"><?= date('d/m/Y H:i', strtotime($reunion['fecha'])) ?></span>
                    </div>
                </div>

                <h3>Contenido del Acta</h3>
                <div class="acta-content"><?= htmlspecialchars($reunion['acta']) ?></div>
            </div>

            <div class="glass-card" style="grid-column: span 1;">
                <h3>Registro de Asistencia</h3>
                <?php if (empty($asistencia)): ?>
                    <p style="opacity:0.6; padding:1rem 0;">No se registró asistencia para esta reunión.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Integrante</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($asistencia as $a): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($a['nombre']) ?></strong><br>
                                    <small style="opacity:0.6;"><?= htmlspecialchars($a['rol']) ?></small>
                                </td>
                                <td>
                                    <?php if($a['asiste']): ?>
                                        <span class="badge" style="background:#28a745;">PRESENTE</span>
                                    <?php else: ?>
                                        <span class="badge" style="background:#dc3545;">AUSENTE</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal Editar Acta -->
    <div id="modal-editar" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div class="glass-card" style="max-width:800px; margin: 2rem auto; position:relative; background:var(--color-bg); max-height:90vh; overflow-y:auto;">
            <button onclick="document.getElementById('modal-editar').style.display='none'" style="position:absolute; top:1rem; right:1rem; background:none; border:none; cursor:pointer; font-size:1.5rem; color:var(--color-text);">&times;</button>
            <h2 style="margin-bottom:1.5rem;">Proponer Modificación al Acta</h2>
            <p style="font-size:0.9rem; opacity:0.8; margin-bottom:1.5rem;">Tu propuesta será enviada al Secretario(a) y Responsable de Grupo para su revisión y aprobación.</p>
            <form action="/grupo/proponerModificacion" method="POST">
                <input type="hidden" name="reunion_id" value="<?= $reunion['id'] ?>">
                
                <div style="margin-bottom:1.5rem;">
                    <label>Tema Principal</label>
                    <input type="text" name="nuevo_tema" value="<?= htmlspecialchars($reunion['tema']) ?>" required style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border);">
                </div>
                
                <div style="margin-bottom:1.5rem;">
                    <label>Contenido del Acta (Edita lo que necesites)</label>
                    <textarea name="nueva_acta" rows="15" required style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border);"><?= htmlspecialchars($reunion['acta']) ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width:100%;">Enviar Propuesta</button>
            </form>
        </div>
    </div>
</body>
</html>
