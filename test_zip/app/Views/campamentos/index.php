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
                <h1 style="margin-top:0.5rem;">Gestión de Campamentos - <?= htmlspecialchars($unidad['nombre']) ?></h1>
            </div>
            <?php if (!\App\Core\Auth::isReadOnly()): ?>
                <button onclick="document.getElementById('modal-camp').style.display='block'" class="btn btn-primary">Planificar Nuevo Campamento</button>
            <?php endif; ?>
        </header>

        <div class="glass-card">
            <?php if (empty($campamentos)): ?>
                <p style="text-align:center; padding: 2rem; opacity:0.7;">No hay campamentos planificados.</p>
            <?php else: ?>
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
                            <td>
                                <strong><?= date('d/m/Y', strtotime($c['fecha_inicio'])) ?></strong><br>
                                <small><?= date('d/m/Y', strtotime($c['fecha_fin'])) ?></small>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($c['nombre']) ?></strong><br>
                                <span class="badge" style="font-size:0.7rem; background:rgba(255,255,255,0.1);"><?= htmlspecialchars($c['tipo']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($c['lugar']) ?></td>
                            <td><span class="badge" style="background:var(--color-primary)"><?= htmlspecialchars($c['estado']) ?></span></td>
                            <td>
                                <a href="/campamentos/ver/<?= $c['id'] ?>" class="btn btn-primary btn-sm">Ver Detalle / Autorizaciones</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal Nuevo Campamento -->
    <div id="modal-camp" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div class="glass-card" style="max-width:700px; margin: 2rem auto; position:relative; background:var(--color-bg); max-height:90vh; overflow-y:auto;">
            <button onclick="document.getElementById('modal-camp').style.display='none'" style="position:absolute; top:1rem; right:1rem; background:none; border:none; cursor:pointer; font-size:1.5rem; color:var(--color-text);">&times;</button>
            <h2 style="margin-bottom:1.5rem;">Planificación de Campamento</h2>
            <form action="/campamentos/guardar/<?= $unidad['id'] ?>" method="POST">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label>Nombre del Campamento</label>
                        <input type="text" name="nombre" required placeholder="Ej: Campamento de Verano" style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                    <div>
                        <label>Tipo de Campamento</label>
                        <select name="tipo" style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                            <option value="Unidad">De Unidad</option>
                            <option value="Grupal">Grupal</option>
                            <option value="Distrital">Distrital</option>
                            <option value="Rama Distrital">Por Rama Distrital</option>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label>Inicio</label>
                        <input type="date" name="fecha_inicio" required style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                    <div>
                        <label>Término</label>
                        <input type="date" name="fecha_fin" required style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                    <div>
                        <label>Cuota ($)</label>
                        <input type="number" name="costo_cuota" value="0" style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                </div>

                <div style="margin-bottom:1rem;">
                    <label>Lugar</label>
                    <input type="text" name="lugar" placeholder="Ej: Parque Nacional Conguillio" style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                </div>

                <div style="margin-bottom:1rem;">
                    <label>Vincular a Actividad del Ciclo</label>
                    <select name="ciclo_actividad_id" style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                        <option value="">-- No vincular --</option>
                        <?php foreach($actividades as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= date('d/m', strtotime($a['fecha'])) ?> - <?= htmlspecialchars($a['nombre_actividad']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom:1rem;">
                    <label>Objetivos del Campamento</label>
                    <textarea name="objetivos" rows="3" style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;"></textarea>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label>Resumen del Programa / Actividades Clave</label>
                    <textarea name="programa_resumen" rows="3" style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;"></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">Crear Plan de Campamento</button>
            </form>
        </div>
    </div>
</body>
</html>
