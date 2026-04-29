<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--glass-border); }
        .table th { color: var(--color-primary); }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem;">
            <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
            <h1 style="margin-top:0.5rem;">Fichas Médicas de la Unidad: <?= htmlspecialchars($unidad['nombre']) ?></h1>
            <p style="opacity:0.8;">Listado de beneficiarios y estado de su ficha de salud.</p>
        </header>

        <div class="glass-card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Beneficiario</th>
                        <th>Estado Ficha</th>
                        <th>Última Actualización</th>
                        <th>Ingresado Por</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($beneficiarios as $b): 
                        $f = $fichas[$b['id']];
                        $has_data = isset($f['ultima_actualizacion']) && $f['tipo_sangre'];
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($b['nombre_completo']) ?></strong></td>
                        <td>
                            <span class="badge" style="background:<?= $has_data ? '#28a745' : '#dc3545' ?>; color:white;">
                                <?= $has_data ? '✅ Completa' : '⚠️ Pendiente' ?>
                            </span>
                        </td>
                        <td><?= $has_data ? date('d/m/Y H:i', strtotime($f['ultima_actualizacion'])) : '-' ?></td>
                        <td style="font-size:0.85rem; opacity:0.8;">
                            <?= !empty($f['autor_nombre']) ? htmlspecialchars($f['autor_nombre']) : ($has_data ? 'Apoderado' : '-') ?>
                        </td>
                        <td>
                            <a href="/fichas/ver/<?= $b['id'] ?>" class="btn btn-primary btn-sm">Ver Ficha</a>
                            <?php if(!\App\Core\Auth::isReadOnly() && ($user['rol'] === 'Superusuario' || $user['rol'] === 'Responsable de Unidad')): ?>
                                <a href="/fichas/editar/<?= $b['id'] ?>" class="btn btn-primary btn-sm" style="background:var(--color-secondary)">Editar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
