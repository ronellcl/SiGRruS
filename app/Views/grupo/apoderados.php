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
        .table th, .table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--glass-border); font-size:0.9rem; }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
                <h1 style="margin-top:0.5rem;">Gestión de Apoderados</h1>
                <p>Base de datos de padres y responsables legales.</p>
            </div>
            <a href="/apoderados/crear" class="btn btn-primary">+ Nuevo Apoderado</a>
        </header>

        <div class="glass-card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>RUT</th>
                        <th>Email / Teléfono</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($apoderados as $a): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($a['nombre_completo']) ?></strong></td>
                        <td><?= htmlspecialchars($a['rut']) ?></td>
                        <td>
                            <?= htmlspecialchars($a['email']) ?><br>
                            <small><?= htmlspecialchars($a['telefono']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($a['direccion']) ?></td>
                        <td>
                            <a href="/apoderados/editar/<?= $a['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
