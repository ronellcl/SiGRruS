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
        .table th { font-weight: 600; color: var(--color-primary); }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
                <h1 style="margin-top:0.5rem;">Gestión Global de Apoderados</h1>
            </div>
        </header>

        <div class="glass-card">
            <?php if (empty($apoderados)): ?>
                <p style="text-align:center; padding: 2rem; opacity:0.7;">No hay apoderados registrados en el sistema.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre Apoderado</th>
                            <th>RUT</th>
                            <th>Vínculos (Hijos)</th>
                            <th>Contacto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($apoderados as $a): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($a['nombre_completo']) ?></strong></td>
                            <td><?= htmlspecialchars($a['rut']) ?></td>
                            <td><span class="badge" style="background:var(--color-primary); color:white;"><?= htmlspecialchars($a['hijos'] ?: 'Sin hijos registrados') ?></span></td>
                            <td>
                                📞 <?= htmlspecialchars($a['telefono']) ?><br>
                                📧 <?= htmlspecialchars($a['email']) ?>
                            </td>
                            <td>
                                <a href="/apoderados/editar/<?= $a['id'] ?>?referer=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-primary btn-sm" style="background:var(--color-secondary)">Editar</a>
                                <a href="/apoderados/vincular/<?= $a['id'] ?>" class="btn btn-primary btn-sm" style="background:var(--color-primary)">Vincular Hijo</a>
                                <?php if ($user['rol'] === 'Superusuario' || $user['rol'] === 'Responsable de Grupo'): ?>
                                    <a href="/apoderados/eliminar/<?= $a['id'] ?>" class="btn btn-sm" style="background:#ef4444; color:white;" onclick="return confirm('¿Estás seguro de eliminar a este apoderado? Sus hijos quedarán sin apoderado asignado.')">Eliminar</a>
                                <?php endif; ?>
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
