<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .doc-section { margin-bottom: 2rem; }
        .doc-section h3 { color: var(--color-primary); border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; }
        .doc-content { line-height: 1.6; opacity: 0.9; white-space: pre-wrap; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
        @media print { .navbar, .btn, .no-print { display: none; } .glass-card { border: none; box-shadow: none; background: none; color: black; } body { background: white; } .doc-section h3 { color: black; } }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <div class="glass-card">
            <header style="margin-bottom: 3rem; text-align:center;">
                <h1 style="color:var(--color-primary);">HOJA DE RUTA</h1>
                <h2><?= htmlspecialchars($hoja['nombre_ciclo'] ?: ($hoja['nombre_actividad_manual'] ?? 'Sin nombre')) ?></h2>
                <p>Unidad: <strong><?= htmlspecialchars($unidad['nombre']) ?></strong></p>
                <div class="no-print" style="margin-top: 1rem;">
                    <a href="/unidades/<?= $unidad['id'] ?>/hoja-ruta/editar/<?= $hoja['id'] ?>" class="btn btn-primary">Editar Hoja</a>
                    <button onclick="window.print()" class="btn btn-primary" style="background:var(--color-secondary)">Imprimir / PDF</button>
                </div>
            </header>

            <div class="doc-section">
                <h3>1. Motivación y Objetivos</h3>
                <div class="doc-content"><?= nl2br(htmlspecialchars($hoja['motivacion'] ?? 'No especificado')) ?></div>
            </div>

            <div class="doc-section">
                <h3>2. Detalles del Lugar</h3>
                <div class="doc-content"><?= nl2br(htmlspecialchars($hoja['lugar_detalles'] ?? 'No especificado')) ?></div>
            </div>

            <div class="doc-section">
                <h3>3. Fases de la Actividad</h3>
                <div class="doc-content"><?= nl2br(htmlspecialchars($hoja['fases'] ?? 'No especificado')) ?></div>
            </div>

            <div class="grid-2">
                <div class="doc-section">
                    <h3>4. Variantes</h3>
                    <div class="doc-content"><?= nl2br(htmlspecialchars($hoja['variantes'] ?? 'No especificado')) ?></div>
                </div>
                <div class="doc-section">
                    <h3>5. Participación</h3>
                    <div class="doc-content"><?= nl2br(htmlspecialchars($hoja['participacion'] ?? 'No especificado')) ?></div>
                </div>
            </div>

            <div class="grid-2">
                <div class="doc-section">
                    <h3>6. Recursos Humanos</h3>
                    <div class="doc-content"><?= nl2br(htmlspecialchars($hoja['recursos_humanos'] ?? 'No especificado')) ?></div>
                </div>
                <div class="doc-section">
                    <h3>7. Materiales Necesarios</h3>
                    <div class="doc-content"><?= nl2br(htmlspecialchars($hoja['materiales'] ?? 'No especificado')) ?></div>
                </div>
            </div>

            <div class="grid-2">
                <div class="doc-section">
                    <h3>8. Costos y Financiamiento</h3>
                    <div class="doc-content"><?= nl2br(htmlspecialchars($hoja['costos'] ?? 'No especificado')) ?></div>
                </div>
                <div class="doc-section">
                    <h3>9. Seguridad y Riesgos</h3>
                    <div class="doc-content"><?= nl2br(htmlspecialchars($hoja['seguridad'] ?? 'No especificado')) ?></div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
