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
        @media (prefers-color-scheme: dark) { .table th { color: #fff; } }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
                <h1 style="margin-top:0.5rem;">Beneficiarios - <?= htmlspecialchars($unidad['nombre']) ?> (Año <?= $anio_actual ?>)</h1>
            </div>
            <?php if (!\App\Core\Auth::isReadOnly() && ($user['rol'] === 'Superusuario' || $user['unidad_id'] == $unidad['id'])): ?>
                <button onclick="document.getElementById('modal-add').style.display='block'" class="btn btn-primary">Añadir Beneficiario</button>
            <?php endif; ?>
        </header>

        <div class="glass-card">
            <?php if (empty($beneficiarios)): ?>
                <p style="text-align:center; opacity:0.7; padding: 2rem;">No hay beneficiarios registrados en esta unidad.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>RUT</th>
                            <th>Nombre Completo</th>
                            <th>Edad</th>
                            <th>Subgrupo</th>
                            <th>Apoderado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($beneficiarios as $b): ?>
                        <tr>
                            <td><?= htmlspecialchars($b['rut']) ?></td>
                            <td><strong><?= htmlspecialchars($b['nombre_completo']) ?></strong></td>
                            <td>
                                <?php 
                                    $nac = new DateTime($b['fecha_nacimiento']);
                                    $hoy = new DateTime();
                                    echo $hoy->diff($nac)->y . " años";
                                ?>
                            </td>
                            <td><span class="badge"><?= htmlspecialchars($b['subgrupo'] ?: '-') ?></span></td>
                            <td><?= htmlspecialchars($b['apoderado_nombre'] ?: 'No asignado') ?></td>
                            <td>
                                <a href="/beneficiarios/ver/<?= $b['id'] ?>" class="btn btn-primary btn-sm" style="background:var(--color-secondary)">Ver Perfil</a>
                                <?php if ($user['rol'] === 'Superusuario' || $user['rol'] === 'Responsable de Grupo' || ($user['rol'] === 'Responsable de Unidad' && $user['unidad_id'] == $unidad['id'])): ?>
                                    <a href="/beneficiarios/eliminar/<?= $b['id'] ?>" class="btn btn-sm" style="background:#ef4444; color:white;" onclick="return confirm('¿Estás seguro de eliminar a este beneficiario? Se borrará su ficha médica e historial.')">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal Añadir Beneficiario -->
    <div id="modal-add" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div class="glass-card" style="max-width:600px; margin: 2rem auto; position:relative; background:var(--color-bg); max-height:90vh; overflow-y:auto;">
            <button onclick="document.getElementById('modal-add').style.display='none'" style="position:absolute; top:1rem; right:1rem; background:none; border:none; cursor:pointer; font-size:1.5rem; color:var(--color-text);">&times;</button>
            <h2 style="margin-bottom:1.5rem; color:var(--color-primary)">Registrar Nuevo Beneficiario</h2>
            
            <form action="/unidades/<?= $unidad['id'] ?>/beneficiarios/crear" method="POST">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Tipo de Documento</label>
                        <select name="tipo_documento" onchange="toggleOtroDoc(this, 'bene_otro_doc')" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:var(--color-bg); color:var(--color-text);">
                            <option value="RUT">RUT (Chile)</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="DNI">DNI</option>
                            <option value="CI">Cédula de Identidad</option>
                            <option value="Otro">Otro...</option>
                        </select>
                        <input type="text" id="bene_otro_doc" name="tipo_documento_otro" placeholder="Especifique..." style="display:none; width:100%; margin-top:0.5rem; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nacionalidad</label>
                        <input type="text" name="nacionalidad" value="Chilena" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div style="margin-bottom:1rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nombre Completo</label>
                        <input type="text" name="nombre_completo" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Número de Documento / RUT</label>
                        <input type="text" name="rut" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div style="margin-bottom:1rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Fecha Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Subgrupo (Patrulla/Seisena)</label>
                        <input type="text" name="subgrupo" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>
                </div>

                <hr style="border:0; border-top:1px solid var(--glass-border); margin:1.5rem 0;">
                <h3 style="margin-bottom:1rem; color:var(--color-secondary);">Datos del Apoderado</h3>
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Tipo Doc Apoderado</label>
                        <select name="apoderado_tipo_doc" onchange="toggleOtroDoc(this, 'bene_apo_otro_doc')" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:var(--color-bg); color:var(--color-text);">
                            <option value="RUT">RUT (Chile)</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="DNI">DNI</option>
                            <option value="Otro">Otro...</option>
                        </select>
                        <input type="text" id="bene_apo_otro_doc" name="apoderado_tipo_doc_otro" placeholder="Especifique..." style="display:none; width:100%; margin-top:0.5rem; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nacionalidad Apod.</label>
                        <input type="text" name="apoderado_nacionalidad" value="Chilena" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div style="margin-bottom:1rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nombre del Apoderado</label>
                        <input type="text" name="apoderado_nombre" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nº Documento Apoderado</label>
                        <input type="text" name="apoderado_rut" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div style="margin-bottom:1rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Email</label>
                        <input type="email" name="apoderado_email" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Teléfono</label>
                        <input type="text" name="apoderado_telefono" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Dirección de Hogar</label>
                    <input type="text" name="apoderado_direccion" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                </div>

                <hr style="border:0; border-top:1px solid var(--glass-border); margin:1.5rem 0;">
                <h3 style="margin-bottom:1rem; color:var(--color-secondary); font-size:1.1rem;">Apoderados Suplentes (Opcional)</h3>
                <p style="font-size:0.8rem; opacity:0.7; margin-bottom:1rem;">Personas autorizadas en caso de que el titular no esté ubicable.</p>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.5rem;">
                    <div>
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Suplente 1</label>
                        <select name="suplente_1_id" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:var(--color-bg); color:var(--color-text);">
                            <option value="">-- Seleccionar --</option>
                            <?php foreach($apoderados as $apo): ?>
                                <option value="<?= $apo['id'] ?>"><?= htmlspecialchars($apo['nombre_completo']) ?> (<?= $apo['rut'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Suplente 2</label>
                        <select name="suplente_2_id" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:var(--color-bg); color:var(--color-text);">
                            <option value="">-- Seleccionar --</option>
                            <?php foreach($apoderados as $apo): ?>
                                <option value="<?= $apo['id'] ?>"><?= htmlspecialchars($apo['nombre_completo']) ?> (<?= $apo['rut'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">Inscribir Beneficiario</button>
            </form>
        </div>
    </div>
    <script>
        function toggleOtroDoc(select, inputId) {
            const input = document.getElementById(inputId);
            if (select.value === 'Otro') {
                input.style.display = 'block';
                input.required = true;
            } else {
                input.style.display = 'none';
                input.required = false;
            }
        }
    </script>
</body>
</html>
