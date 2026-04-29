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
            <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
            <h1 style="margin-top:0.5rem;">Finanzas de <?= htmlspecialchars($beneficiario['nombre_completo']) ?></h1>
            <p>Historial de cuotas pagadas en el año.</p>
        </header>

        <div class="glass-card">
            <h3>Cuotas Pagadas</h3>
            <?php if(empty($cuotas)): ?>
                <p style="opacity: 0.7;">No hay registros de cuotas pagadas para este beneficiario.</p>
            <?php else: ?>
                <table style="width:100%; border-collapse:collapse; margin-top:1rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(255,255,255,0.1);">
                            <th style="text-align:left; padding:0.5rem;">Mes / Concepto</th>
                            <th style="text-align:left; padding:0.5rem;">Monto</th>
                            <th style="text-align:left; padding:0.5rem;">Fecha de Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $meses = [
                            0 => 'Inscripción Anual',
                            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                        ];
                        foreach($cuotas as $c): 
                        ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding:0.5rem;"><?= htmlspecialchars($meses[$c['mes']] ?? 'Desconocido') ?></td>
                            <td style="padding:0.5rem;">$<?= number_format($c['monto'], 0, ',', '.') ?></td>
                            <td style="padding:0.5rem;"><?= htmlspecialchars($c['fecha_pago']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
