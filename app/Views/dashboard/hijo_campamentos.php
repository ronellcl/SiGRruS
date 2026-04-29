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
            <h1 style="margin-top:0.5rem;">Campamentos Disponibles</h1>
            <p style="opacity:0.8;">Beneficiario: <strong><?= htmlspecialchars($beneficiario['nombre_completo']) ?></strong></p>
        </header>

        <div class="dashboard-grid">
            <?php if (empty($campamentos)): ?>
                <div class="glass-card">No hay campamentos activos para la unidad de su hijo/a en este momento.</div>
            <?php else: ?>
                <?php foreach($campamentos as $c): 
                    $campModel = new \App\Models\Campamento();
                    $auth = $campModel->getAutorizacion($c['id'], $beneficiario['id']);
                ?>
                <div class="glass-card" style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <span class="badge" style="background:rgba(255,255,255,0.1);"><?= htmlspecialchars($c['tipo']) ?></span>
                        <h3 style="margin: 0.5rem 0;"><?= htmlspecialchars($c['nombre']) ?></h3>
                        <p style="font-size:0.9rem; opacity:0.8;">📅 <?= date('d/m', strtotime($c['fecha_inicio'])) ?> al <?= date('d/m/Y', strtotime($c['fecha_fin'])) ?></p>
                    </div>
                    <div>
                        <?php if ($auth): ?>
                            <span class="badge" style="background:#28a745; padding:0.6rem 1rem;">✅ Autorizado</span>
                            <a href="/campamentos/ver/<?= $c['id'] ?>" class="btn btn-primary btn-sm" style="margin-left:0.5rem;">Ver Info</a>
                        <?php else: ?>
                            <a href="/campamentos/ver/<?= $c['id'] ?>" class="btn btn-primary" style="background:#dc3545;">Firmar Autorización</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
