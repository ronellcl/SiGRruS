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
        .role-badge { padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: bold; background: var(--color-primary); color: white; }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
                <h1 style="margin-top:0.5rem;">Gestión de Dirigentes</h1>
                <p>Listado oficial de líderes del grupo.</p>
            </div>
            <button onclick="document.getElementById('modal-add').style.display='block'" class="btn btn-primary">+ Nuevo Dirigente</button>
        </header>

        <div class="glass-card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>RUT</th>
                        <th>Email</th>
                        <th>Rol Principal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($dirigentes as $d): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($d['nombre'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars($d['rut'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($d['email'] ?? '') ?></td>
                        <td><span class="role-badge"><?= htmlspecialchars($d['rol'] ?? 'Sin Rol') ?></span></td>
                        <td>
                            <a href="/grupo/editarDirigente/<?= $d['id'] ?>" class="btn btn-primary btn-sm" style="background:var(--color-primary)">Editar</a>
                            <a href="/usuarios/perfil/<?= $d['id'] ?>" class="btn btn-primary btn-sm" style="background:var(--color-secondary)">Historial</a>
                            <a href="/dirigentes/eliminar/<?= $d['id'] ?>" class="btn btn-sm" style="background:#ef4444; color:white;" onclick="return confirm('¿Estás seguro de eliminar a este dirigente? Se borrarán sus roles y certificados.')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal simple para añadir -->
    <div id="modal-add" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div class="glass-card" style="max-width:500px; margin: 2rem auto; position:relative; background:var(--color-bg); max-height:90vh; overflow-y:auto;">
            <button onclick="document.getElementById('modal-add').style.display='none'" style="position:absolute; top:1rem; right:1rem; background:none; border:none; cursor:pointer; font-size:1.5rem; color:var(--color-text);">&times;</button>
            <h2 style="margin-bottom:1.5rem; color:var(--color-primary)">Nuevo Dirigente / Usuario</h2>
            <form action="/dirigentes/crear" method="POST">
                <div style="margin-bottom:1rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nombre Completo</label>
                    <input type="text" name="nombre" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Tipo de Documento</label>
                        <select name="tipo_documento" onchange="toggleOtroDoc(this, 'global_dir_otro_doc')" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:var(--color-bg); color:var(--color-text);">
                            <option value="RUT">RUT (Chile)</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="DNI">DNI</option>
                            <option value="CI">Cédula de Identidad</option>
                            <option value="Otro">Otro...</option>
                        </select>
                        <input type="text" id="global_dir_otro_doc" name="tipo_documento_otro" placeholder="Especifique..." style="display:none; width:100%; margin-top:0.5rem; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nacionalidad</label>
                        <input type="text" name="nacionalidad" value="Chilena" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>
                </div>
                <div style="margin-bottom:1rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Número de Documento / RUT</label>
                    <input type="text" name="rut" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                </div>
                <div style="margin-bottom:1rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Correo Electrónico</label>
                    <input type="email" name="email" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                </div>
                <div style="margin-bottom:1rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Contraseña Temporal</label>
                    <input type="text" name="password" required value="scout123" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                </div>
                <div style="margin-bottom:1rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Rol en el Sistema</label>
                    <select name="rol" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:var(--color-bg); color:var(--color-text);">
                        <?php if ($user['rol'] === 'Superusuario'): ?>
                            <option value="Superusuario">Superusuario</option>
                            <option value="Responsable de Grupo">Responsable de Grupo</option>
                            <option value="Asistente de Grupo">Asistente de Grupo</option>
                        <?php endif; ?>
                        <!-- Todos (incluido Resp. de Grupo) pueden crear estos: -->
                        <option value="Responsable de Unidad">Responsable de Unidad</option>
                        <option value="Asistente de Unidad">Asistente de Unidad</option>
                    </select>
                </div>
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Unidad Asignada (Si aplica)</label>
                    <select name="unidad_id" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:var(--color-bg); color:var(--color-text);">
                        <option value="">Ninguna (A nivel de Grupo)</option>
                        <?php foreach($unidades as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Crear Dirigente</button>
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
