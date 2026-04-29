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
        .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold; background: rgba(255,255,255,0.2); }
        .btn-sm { padding: 0.4rem 0.8rem; font-size: 0.85rem; }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
                <h1 style="margin-top:0.5rem;">Ciclo de Programa - <?= htmlspecialchars($unidad['nombre']) ?> (<?= $anio_actual ?>)</h1>
            </div>
            <?php if (!\App\Core\Auth::isReadOnly()): ?>
                <button onclick="document.getElementById('modal-add').style.display='block'" class="btn btn-primary">Nueva Actividad</button>
            <?php endif; ?>
        </header>

        <div class="glass-card">
            <?php if (empty($actividades)): ?>
                <p style="text-align:center; padding: 2rem; opacity:0.7;">No hay actividades registradas para este año.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Actividad</th>
                            <th>Lugar</th>
                            <th>Tipo</th>
                            <th>Hoja de Ruta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($actividades as $a): ?>
                        <tr>
                            <td><strong><?= date('d/m/Y', strtotime($a['fecha'])) ?></strong></td>
                            <td><?= htmlspecialchars($a['nombre_actividad']) ?></td>
                            <td><?= htmlspecialchars($a['lugar']) ?></td>
                            <td>
                                <?php if($a['es_campamento']): ?>
                                    <span class="badge" style="background:#28a745">🏕️ Campamento</span>
                                <?php else: ?>
                                    <span class="badge" style="background:rgba(255,255,255,0.1)">Actividad</span>
                                <?php endif; ?>
                            </td>
                            <td style="display:flex; gap:0.5rem;">
                                <?php if ($a['hoja_ruta_id']): ?>
                                    <a href="/unidades/<?= $unidad['id'] ?>/hoja-ruta/ver/<?= $a['hoja_ruta_id'] ?>" class="btn btn-primary btn-sm" style="background:#28a745">Ver Hoja de Ruta</a>
                                <?php elseif (!\App\Core\Auth::isReadOnly()): ?>
                                    <a href="/unidades/<?= $unidad['id'] ?>/hoja-ruta/editar?actividad_id=<?= $a['id'] ?>" class="btn btn-primary btn-sm" style="background:var(--color-secondary)">Crear Hoja de Ruta</a>
                                <?php endif; ?>
                                <a href="/unidades/<?= $unidad['id'] ?>/asistencias/registrar/<?= $a['id'] ?>" class="btn btn-primary btn-sm" style="background:var(--color-primary)"><?= \App\Core\Auth::isReadOnly() ? 'Ver Asistencia' : 'Tomar Asistencia' ?></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal para planificación por lotes (Batch) -->
    <div id="modal-add" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div class="glass-card" style="max-width:800px; margin: 2rem auto; position:relative; background:var(--color-bg); max-height:90vh; overflow-y:auto;">
            <button onclick="document.getElementById('modal-add').style.display='none'" style="position:absolute; top:1rem; right:1rem; background:none; border:none; cursor:pointer; font-size:1.5rem; color:var(--color-text);">&times;</button>
            <h2 style="margin-bottom:1rem; color:var(--color-primary)">Planificar Ciclo de Actividades</h2>
            <p style="opacity:0.8; margin-bottom:1.5rem;">Ingresa las actividades del ciclo. Puedes agregar tantas filas como necesites.</p>
            
            <form action="/unidades/<?= $unidad['id'] ?>/ciclo/crear" method="POST">
                <table class="table" id="batch-table" style="margin-bottom:1.5rem;">
                    <thead>
                        <tr>
                            <th style="width:40%;">Nombre de la Actividad</th>
                            <th style="width:25%;">Fecha</th>
                            <th style="width:25%;">Lugar</th>
                            <th style="width:10%;">Camp.</th>
                            <th style="width:5%;"></th>
                        </tr>
                    </thead>
                    <tbody id="batch-body">
                        <tr>
                            <td><input type="text" name="actividades[0][nombre_actividad]" required placeholder="Ej: Excursión" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); border:1px solid var(--glass-border); color:white; border-radius:4px;"></td>
                            <td><input type="date" name="actividades[0][fecha]" required style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); border:1px solid var(--glass-border); color:white; border-radius:4px;"></td>
                            <td><input type="text" name="actividades[0][lugar]" required placeholder="Lugar" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); border:1px solid var(--glass-border); color:white; border-radius:4px;"></td>
                            <td style="text-align:center;"><input type="checkbox" name="actividades[0][es_campamento]" value="1"></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                
                <div style="display:flex; gap:1rem; margin-top:1rem;">
                    <button type="button" onclick="addRow()" class="btn btn-primary" style="background:var(--color-secondary); flex:1;">+ Agregar Fila</button>
                    <button type="submit" class="btn btn-primary" style="flex:2;">Guardar Ciclo Completo</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let rowCount = 1;
        function addRow() {
            const body = document.getElementById('batch-body');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="text" name="actividades[${rowCount}][nombre_actividad]" required style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); border:1px solid var(--glass-border); color:white; border-radius:4px;"></td>
                <td><input type="date" name="actividades[${rowCount}][fecha]" required style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); border:1px solid var(--glass-border); color:white; border-radius:4px;"></td>
                <td><input type="text" name="actividades[${rowCount}][lugar]" required style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); border:1px solid var(--glass-border); color:white; border-radius:4px;"></td>
                <td style="text-align:center;"><input type="checkbox" name="actividades[${rowCount}][es_campamento]" value="1"></td>
                <td><button type="button" onclick="this.closest('tr').remove()" style="background:none; border:none; color:#ff4d4d; cursor:pointer; font-size:1.2rem;">&times;</button></td>
            `;
            body.appendChild(newRow);
            rowCount++;
        }
    </script>
</body>
</html>
