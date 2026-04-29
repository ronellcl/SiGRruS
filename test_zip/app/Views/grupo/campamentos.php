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
        <header style="margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
                <h1>Campamentos de Grupo / Distritales</h1>
            </div>
            <button onclick="document.getElementById('modal-camp').style.display='block'" class="btn btn-primary">Planificar Evento Masivo</button>
        </header>

        <div class="glass-card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Nombre / Tipo</th>
                        <th>Lugar</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($campamentos as $c): ?>
                    <tr>
                        <td><strong><?= date('d/m/Y', strtotime($c['fecha_inicio'])) ?></strong></td>
                        <td>
                            <strong><?= htmlspecialchars($c['nombre']) ?></strong><br>
                            <span class="badge" style="background:rgba(255,255,255,0.1);"><?= htmlspecialchars($c['tipo']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($c['lugar']) ?></td>
                        <td><?= htmlspecialchars($c['estado']) ?></td>
                        <td><a href="/campamentos/ver/<?= $c['id'] ?>" class="btn btn-primary btn-sm">Ver Participantes</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal Nuevo Campamento Grupo -->
    <div id="modal-camp" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div class="glass-card" style="max-width:600px; margin: 5rem auto; position:relative; background:var(--color-bg);">
            <button onclick="document.getElementById('modal-camp').style.display='none'" style="position:absolute; top:1rem; right:1rem; background:none; border:none; cursor:pointer; font-size:1.5rem; color:var(--color-text);">&times;</button>
            <h2 style="margin-bottom:1.5rem;">Planificar Campamento Masivo</h2>
            <form action="/campamentos/guardar" method="POST">
                <div style="margin-bottom:1rem;">
                    <label>Nombre del Evento</label>
                    <input type="text" name="nombre" required style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border);">
                </div>
                <div style="margin-bottom:1rem;">
                    <label>Tipo</label>
                    <select name="tipo" style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border);">
                        <option value="Grupal">Grupal (Todo el Grupo)</option>
                        <option value="Distrital">Distrital</option>
                        <option value="Rama Distrital">Por Rama Distrital</option>
                    </select>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label>Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" required style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white;">
                    </div>
                    <div>
                        <label>Fecha Término</label>
                        <input type="date" name="fecha_fin" required style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white;">
                    </div>
                </div>
                <div style="margin-bottom:1.5rem;">
                    <label>Lugar</label>
                    <input type="text" name="lugar" style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white;">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Lanzar Plan de Campamento</button>
            </form>
        </div>
    </div>
</body>
</html>
