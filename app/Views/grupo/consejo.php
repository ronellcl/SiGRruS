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
        
        .consejo-layout {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 2rem;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .consejo-layout {
                grid-template-columns: 1fr;
            }
        }

        .glass-card { min-width: 0; } /* Evita que el contenido ancho rompa el grid */
        .table-container { overflow-x: auto; width: 100%; }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
                <h1 style="margin-top:0.5rem;">Consejo de Grupo</h1>
            </div>
            <button onclick="document.getElementById('modal-acta').style.display='block'" class="btn btn-primary">Nueva Acta de Reunión</button>
        </header>

        <div class="consejo-layout">
            <!-- LISTADO DE ACTAS -->
            <div class="glass-card">
                <h3>Actas de Reunión</h3>
                <?php if (empty($reuniones)): ?>
                    <p style="text-align:center; padding:2rem; opacity:0.6;">No hay actas registradas.</p>
                <?php else: ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tema</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($reuniones as $r): ?>
                            <tr>
                                <td><strong><?= date('d/m/Y', strtotime($r['fecha'])) ?></strong></td>
                                <td><?= htmlspecialchars($r['tema']) ?></td>
                                <td>
                                    <a href="/grupo/verActa/<?= $r['id'] ?>" class="btn btn-primary btn-sm">Ver</a>
                                    <a href="/grupo/imprimirActa/<?= $r['id'] ?>" target="_blank" class="btn btn-primary btn-sm" style="background:var(--color-secondary)">PDF</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- GESTIÓN DE MIEMBROS -->
            <div class="glass-card">
                <h3>Integrantes del Consejo</h3>
                <p style="font-size:0.85rem; opacity:0.8; margin-bottom:1rem;">Solo las personas en esta lista pueden ver y proponer modificaciones a las actas.</p>
                
                <div class="table-container">
                    <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>RUT</th>
                            <th>Función</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($miembros as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['nombre']) ?></td>
                            <td><?= htmlspecialchars($m['rut']) ?></td>
                            <td><small><?= htmlspecialchars($m['rol_especifico']) ?></small></td>
                            <td>
                                <?php if($user['rol'] === 'Superusuario' || $user['rol'] === 'Responsable de Grupo'): ?>
                                    <a href="/grupo/eliminarMiembroConsejo/<?= $m['id'] ?>" style="color:#dc3545; text-decoration:none;" onclick="return confirm('¿Quitar del consejo?')">&times;</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                </div>

                <?php if($user['rol'] === 'Superusuario' || $user['rol'] === 'Responsable de Grupo'): ?>
                <form action="/grupo/agregarMiembroConsejo" method="POST" style="margin-top:1.5rem; display:flex; flex-direction:column; gap:0.75rem; padding:1rem; background:rgba(255,255,255,0.03); border-radius:12px;">
                    <div style="display:flex; gap:0.5rem;">
                        <input type="text" name="rut" placeholder="RUT del miembro" required style="flex:1; padding:0.6rem; border-radius:6px;">
                        <input type="text" name="rol_especifico" placeholder="Función (ej: Asistente)" required style="flex:1; padding:0.6rem; border-radius:6px;">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%; padding:0.6rem;">Añadir al Consejo</button>
                </form>
                <?php endif; ?>
            </div>


        </div>
    </main>

    <!-- Modal Nueva Acta -->
    <div id="modal-acta" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div class="glass-card" style="max-width:800px; margin: 2rem auto; position:relative; background:var(--color-bg); max-height:90vh; overflow-y:auto;">
            <button onclick="document.getElementById('modal-acta').style.display='none'" style="position:absolute; top:1rem; right:1rem; background:none; border:none; cursor:pointer; font-size:1.5rem; color:var(--color-text);">&times;</button>
            <h2 style="margin-bottom:1.5rem;">Registrar Reunión de Consejo</h2>
            <form action="/grupo/crearActa" method="POST">
                <input type="hidden" name="tipo_organo" value="Consejo">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label>Fecha y Hora</label>
                        <input type="datetime-local" name="fecha" required style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border);">
                    </div>
                    <div>
                        <label>Tema Principal</label>
                        <input type="text" name="tema" required style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border);">
                    </div>
                </div>
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Asistencia al Consejo</label>
                    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap:0.5rem; background:rgba(255,255,255,0.05); padding:1rem; border-radius:8px; max-height: 150px; overflow-y:auto;">
                        <?php foreach($miembros as $m): 
                            $tipo = $m['usuario_id'] ? 'Usuario' : 'Apoderado';
                            $id_entidad = $m['usuario_id'] ? $m['usuario_id'] : $m['apoderado_id'];
                            $val = $tipo . '-' . $id_entidad;
                        ?>
                            <label style="display:flex; align-items:center; gap:0.5rem; font-weight:normal; cursor:pointer;">
                                <input type="checkbox" name="asistentes[]" value="<?= $val ?>" checked>
                                <span style="font-size:0.85rem;"><?= htmlspecialchars($m['nombre']) ?> <small style="opacity:0.6;">(<?= htmlspecialchars($m['rol_especifico']) ?>)</small></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div style="margin-bottom:1.5rem;">
                    <label>Contenido del Acta / Acuerdos</label>
                    <textarea name="acta" rows="10" required style="width:100%; padding:0.7rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border);"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Guardar Acta</button>
            </form>
        </div>
    </div>
</body>
</html>
