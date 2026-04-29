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
                <h3>Información General</h3>
                <hr style="border:0; border-top:1px solid var(--glass-border); margin:1rem 0;">
                <p><strong><?= htmlspecialchars($beneficiario['tipo_documento'] ?? 'RUT') ?>:</strong> <?= htmlspecialchars($beneficiario['rut'] ?? '') ?></p>
                <p><strong>Nacionalidad:</strong> <?= htmlspecialchars($beneficiario['nacionalidad'] ?? 'Chilena') ?></p>
                <p><strong>Fecha Nacimiento:</strong> <?= date('d/m/Y', strtotime($beneficiario['fecha_nacimiento'])) ?></p>
                <p><strong>Unidad:</strong> <?= htmlspecialchars($unidad['nombre']) ?></p>
                <p><strong>Subgrupo:</strong> <?= htmlspecialchars($beneficiario['subgrupo'] ?: 'No asignado') ?></p>
            </div>

            <div class="glass-card">
                <h3>Resumen de Salud</h3>
                <hr style="border:0; border-top:1px solid var(--glass-border); margin:1rem 0;">
                <p>Para ver el detalle completo, presione el botón <strong>Ficha Médica</strong> en el menú superior.</p>
                <div style="margin-top:1rem; padding:1rem; background:rgba(255,255,255,0.05); border-radius:8px;">
                    <p style="font-size:0.9rem; opacity:0.8;">La información de salud es confidencial y solo accesible por los dirigentes a cargo de la unidad y personal médico del grupo.</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
