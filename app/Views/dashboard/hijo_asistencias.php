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
            <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver</a>
            <h1 style="margin-top:0.5rem;">Registro de Asistencia</h1>
            <p>Beneficiario: <strong><?= htmlspecialchars($beneficiario['nombre_completo']) ?></strong></p>
        </header>

        <div class="glass-card">
            <?php if (empty($asistencias)): ?>
                <p style="text-align:center; opacity:0.6;">Aún no hay registros de asistencia para este beneficiario.</p>
            <?php else: ?>
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="text-align:left; border-bottom:1px solid var(--glass-border);">
                            <th style="padding:1rem;">Fecha</th>
                            <th style="padding:1rem;">Actividad</th>
                            <th style="padding:1rem;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($asistencias as $a): ?>
                        <tr style="border-bottom:1px solid var(--glass-border);">
                            <td style="padding:1rem;"><?= date('d/m/Y', strtotime($a['fecha'])) ?></td>
                            <td style="padding:1rem;"><?= htmlspecialchars($a['nombre_actividad']) ?></td>
                            <td style="padding:1rem;">
                                <span class="badge" style="background:<?= $a['estado'] === 'Presente' ? '#28a745' : ($a['estado'] === 'Ausente' ? '#dc3545' : '#ffc107') ?>; color:#fff;">
                                    <?= htmlspecialchars($a['estado']) ?>
                                </span>
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
