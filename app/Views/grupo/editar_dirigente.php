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
            <a href="/grupo/dirigentes" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Listado</a>
            <h1 style="margin-top:0.5rem;">Editar Dirigente: <?= htmlspecialchars($dirigente['nombre']) ?></h1>
        </header>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:2rem;">
            <!-- DATOS BÁSICOS Y ROL -->
            <div class="glass-card">
                <h3 style="color:var(--color-primary); margin-bottom:1.5rem;">Información y Perfil</h3>
                <form action="/grupo/actualizarDirigente" method="POST">
                    <input type="hidden" name="id" value="<?= $dirigente['id'] ?>">
                    
                    <div style="margin-bottom:1rem;">
                        <label style="display:block; margin-bottom:0.5rem;">Nombre Completo</label>
                        <input type="text" name="nombre" value="<?= htmlspecialchars($dirigente['nombre'] ?? '') ?>" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:white;">
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                        <div>
                            <label style="display:block; margin-bottom:0.5rem;">RUT</label>
                            <input type="text" name="rut" value="<?= htmlspecialchars($dirigente['rut'] ?? '') ?>" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:white;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:0.5rem;">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($dirigente['email'] ?? '') ?>" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:white;">
                        </div>
                    </div>

                    <?php
                    $hasRole = function($rol, $unitId = null) use ($dirigente) {
                        if (!isset($dirigente['roles'])) return false;
                        foreach ($dirigente['roles'] as $idx => $r) {
                            $uId = $dirigente['unidades'][$idx] ?? null;
                            if ($r === $rol && $uId == $unitId) return true;
                        }
                        return false;
                    };
                    ?>

                    <div style="margin-bottom:1.5rem;">
                        <label style="display:block; margin-bottom:1rem; font-weight:bold; color:var(--color-primary);">Roles de Nivel Grupo</label>
                        <div style="display:grid; grid-template-columns:1fr; gap:0.5rem; background:rgba(255,255,255,0.05); padding:1rem; border-radius:8px;">
                            <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                                <input type="checkbox" name="roles_grupo[]" value="Superusuario" <?= $hasRole('Superusuario') ? 'checked' : '' ?>> Superusuario (Acceso Total)
                            </label>
                            <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                                <input type="checkbox" name="roles_grupo[]" value="Responsable de Grupo" <?= $hasRole('Responsable de Grupo') ? 'checked' : '' ?>> Responsable de Grupo
                            </label>
                            <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                                <input type="checkbox" name="roles_grupo[]" value="Asistente de Grupo" <?= $hasRole('Asistente de Grupo') ? 'checked' : '' ?>> Asistente de Grupo
                            </label>
                            <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; color:var(--color-secondary); border-top:1px solid var(--glass-border); padding-top:0.5rem; margin-top:0.5rem;">
                                <input type="checkbox" name="es_apoderado" value="1" <?= $hasRole('Apoderado') ? 'checked' : '' ?>> 🏠 También es Apoderado (Padre/Madre/Tutor)
                            </label>
                        </div>
                    </div>

                    <div style="margin-bottom:1.5rem;">
                        <label style="display:block; margin-bottom:1rem; font-weight:bold; color:var(--color-secondary);">Asignación por Unidades</label>
                        <div style="max-height:300px; overflow-y:auto; display:flex; flex-direction:column; gap:1rem;">
                            <?php foreach($unidades as $u): ?>
                                <div style="background:rgba(255,255,255,0.03); padding:1rem; border-radius:8px; border-left:4px solid var(--color-primary);">
                                    <strong style="display:block; margin-bottom:0.5rem;"><?= htmlspecialchars($u['nombre']) ?></strong>
                                    <div style="display:flex; gap:1.5rem; font-size:0.9rem;">
                                        <label style="display:flex; align-items:center; gap:0.4rem; cursor:pointer;">
                                            <input type="checkbox" name="roles_unidad[<?= $u['id'] ?>][]" value="Responsable de Unidad" <?= $hasRole('Responsable de Unidad', $u['id']) ? 'checked' : '' ?>> Responsable
                                        </label>
                                        <label style="display:flex; align-items:center; gap:0.4rem; cursor:pointer;">
                                            <input type="checkbox" name="roles_unidad[<?= $u['id'] ?>][]" value="Asistente de Unidad" <?= $hasRole('Asistente de Unidad', $u['id']) ? 'checked' : '' ?>> Asistente
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;">Guardar Cambios</button>
                </form>
            </div>

            <!-- CERTIFICADOS -->
            <div class="glass-card">
                <h3 style="color:var(--color-secondary); margin-bottom:1.5rem;">Certificados y Documentación</h3>
                
                <form action="/grupo/subirCertificado" method="POST" enctype="multipart/form-data" style="margin-bottom:2rem; padding:1rem; background:rgba(255,255,255,0.05); border-radius:8px;">
                    <input type="hidden" name="usuario_id" value="<?= $dirigente['id'] ?>">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem; margin-bottom:1rem;">
                        <div>
                            <label style="font-size:0.8rem;">Tipo de Documento</label>
                            <select name="tipo" required style="width:100%; padding:0.5rem; background:var(--color-bg); color:white; border:1px solid var(--glass-border);">
                                <option value="Antecedentes">Antecedentes</option>
                                <option value="Inhabilidad">Inhabilidad (Registro de Ofensores)</option>
                                <option value="Formacion">Certificado de Formación</option>
                                <option value="Salud">Certificado de Salud</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:0.8rem;">Fecha Emisión</label>
                            <input type="date" name="fecha_emision" required style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border);">
                        </div>
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label style="font-size:0.8rem;">Archivo (PDF/Imagen)</label>
                        <input type="file" name="archivo" required style="width:100%;">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="background:var(--color-secondary); width:100%;">Subir Certificado</button>
                </form>

                <div style="max-height: 300px; overflow-y:auto;">
                    <table style="width:100%; font-size:0.8rem; border-collapse:collapse;">
                        <thead>
                            <tr style="border-bottom:1px solid var(--glass-border);">
                                <th style="text-align:left; padding:0.5rem;">Tipo</th>
                                <th style="text-align:left; padding:0.5rem;">Fecha</th>
                                <th style="text-align:right; padding:0.5rem;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($certificados)): ?>
                                <tr><td colspan="3" style="text-align:center; padding:1rem; opacity:0.5;">No hay certificados cargados.</td></tr>
                            <?php else: ?>
                                <?php foreach($certificados as $c): ?>
                                    <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                        <td style="padding:0.5rem;"><strong><?= htmlspecialchars($c['tipo']) ?></strong></td>
                                        <td style="padding:0.5rem;"><?= date('d/m/Y', strtotime($c['fecha_emision'])) ?></td>
                                        <td style="padding:0.5rem; text-align:right;">
                                            <a href="<?= $c['archivo_path'] ?>" target="_blank" style="color:var(--color-primary); text-decoration:none;">Ver Documento</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
