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
            <a href="/beneficiarios/ver/<?= $beneficiario['id'] ?>" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Beneficiario</a>
            <h1 style="margin-top:0.5rem;">Ficha de Salud: <?= htmlspecialchars($beneficiario['nombre_completo']) ?></h1>
            <p style="opacity:0.7;">
                <?php if ($ficha && isset($ficha['ultima_actualizacion'])): ?>
                    Última actualización: <?= date('d/m/Y H:i', strtotime($ficha['ultima_actualizacion'])) ?>
                <?php else: ?>
                    <span style="color:#dc3545;">⚠️ Ficha aún no completada por el apoderado</span>
                <?php endif; ?>
            </p>
        </header>

        <div class="glass-card">
            <?php if (!$ficha): ?>
                <div style="text-align:center; padding:3rem; opacity:0.7;">
                    <div style="font-size:3rem; margin-bottom:1rem;">📋</div>
                    <h3>No existe información médica registrada</h3>
                    <p>El apoderado aún no ha completado la ficha de salud de este beneficiario.</p>
                </div>
            <?php else: ?>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:2rem;">
                    <div style="border-right: 1px solid var(--glass-border); padding-right: 2rem;">
                        <h3 style="color:var(--color-primary); margin-bottom:1rem;">Información Crítica</h3>
                        <div style="background:rgba(220,53,69,0.1); padding:1rem; border-radius:8px; margin-bottom:1rem; border:1px solid #dc3545;">
                            <label style="font-size:0.8rem; display:block; color:#dc3545; font-weight:bold;">ALERGIAS</label>
                            <p style="font-size:1.1rem;"><?= nl2br(htmlspecialchars(($ficha['alergias'] ?? '') ?: 'Sin alergias informadas')) ?></p>
                        </div>
                        <div style="background:rgba(255,193,7,0.1); padding:1rem; border-radius:8px; border:1px solid #ffc107;">
                            <label style="font-size:0.8rem; display:block; color:#ffc107; font-weight:bold;">ENFERMEDADES CRÓNICAS</label>
                            <p><?= nl2br(htmlspecialchars(($ficha['enfermedades_cronicas'] ?? '') ?: 'Ninguna')) ?></p>
                        </div>
                    </div>

                    <div>
                        <h3 style="color:var(--color-secondary); margin-bottom:1rem;">Detalles Médicos</h3>
                        <p><strong>Tipo de Sangre:</strong> <span class="badge" style="background:var(--color-primary);"><?= htmlspecialchars(($ficha['tipo_sangre'] ?? '') ?: 'No informado') ?></span></p>
                        <p><strong>Previsión:</strong> <?= htmlspecialchars(($ficha['prevision_salud'] ?? '') ?: 'No informada') ?></p>
                        <p><strong>Vacunas al día:</strong> <?= ($ficha['vacunas_al_dia'] ?? 0) ? 'Sí' : 'No' ?></p>
                        
                        <hr style="border:0; border-top:1px solid var(--glass-border); margin:1rem 0;">
                        
                        <label style="font-weight:bold; display:block;">Medicamentos:</label>
                        <p style="opacity:0.8;"><?= nl2br(htmlspecialchars(($ficha['medicamentos'] ?? '') ?: 'Ninguno')) ?></p>
                        
                        <label style="font-weight:bold; display:block; margin-top:1rem;">Restricciones Alimenticias:</label>
                        <p style="opacity:0.8;"><?= nl2br(htmlspecialchars(($ficha['restricciones_alimenticias'] ?? '') ?: 'Ninguna')) ?></p>
                    </div>
                </div>

                <div style="margin-top:2rem; padding-top:1rem; border-top: 1px solid var(--glass-border);">
                    <label style="font-weight:bold; display:block;">Otras Observaciones:</label>
                    <p style="opacity:0.8;"><?= nl2br(htmlspecialchars(($ficha['observaciones_medicas'] ?? '') ?: 'Sin observaciones adicionales.')) ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top:2rem; text-align:center;">
            <button onclick="window.print()" class="btn btn-primary">Imprimir Ficha para Botiquín</button>
        </div>
    </main>
</body>
</html>
