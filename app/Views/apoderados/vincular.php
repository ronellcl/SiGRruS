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

    <main class="container" style="max-width:800px;">
        <header style="margin-bottom: 2rem;">
            <?php if ($user['rol'] === 'Apoderado'): ?>
                <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver a Mi Espacio</a>
            <?php else: ?>
                <a href="/grupo/apoderados" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver a Apoderados</a>
            <?php endif; ?>
            <h1 style="margin-top:0.5rem;">Vincular Hijo/Beneficiario</h1>
            <p>Apoderado: <strong><?= htmlspecialchars($apoderado['nombre_completo']) ?></strong> (RUT: <?= htmlspecialchars($apoderado['rut']) ?>)</p>
        </header>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:2rem;">
            <!-- Opción A: Vincular Existente -->
            <section class="glass-card">
                <h2 style="color:var(--color-primary); margin-bottom:1.5rem;">Vincular Existente</h2>
                <p style="font-size:0.9rem; margin-bottom:1.5rem; opacity:0.8;">Busca un beneficiario que ya esté registrado en el sistema pero no tenga apoderado asignado (o quieras cambiarlo).</p>
                
                <form action="/apoderados/postVincularExistente" method="POST">
                    <input type="hidden" name="apoderado_id" value="<?= $apoderado['id'] ?>">
                    <div style="margin-bottom:1.5rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Seleccionar Beneficiario</label>
                        <select name="beneficiario_id" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:var(--color-bg); color:var(--color-text);">
                            <option value="">-- Seleccione uno --</option>
                            <?php foreach($beneficiarios as $b): ?>
                                <option value="<?= $b['id'] ?>">
                                    <?= htmlspecialchars($b['nombre_completo']) ?> (<?= htmlspecialchars($b['rut']) ?>) - <?= htmlspecialchars($b['unidad_nombre'] ?: 'Sin Unidad') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Confirmar Vínculo</button>
                </form>
            </section>

            <!-- Opción B: Crear Nuevo -->
            <section class="glass-card">
                <h2 style="color:var(--color-secondary); margin-bottom:1.5rem;">Crear Nuevo</h2>
                <p style="font-size:0.9rem; margin-bottom:1.5rem; opacity:0.8;">Registra un nuevo beneficiario desde cero para este apoderado.</p>
                
                <form action="/apoderados/postVincularNuevo" method="POST">
                    <input type="hidden" name="apoderado_id" value="<?= $apoderado['id'] ?>">
                    
                    <div style="margin-bottom:1rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nombre Completo</label>
                        <input type="text" name="nombre_completo" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                        <div>
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Tipo Doc.</label>
                            <select name="tipo_documento" onchange="toggleOtroDoc(this, 'vinc_otro_doc')" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:var(--color-bg); color:var(--color-text);">
                                <option value="RUT">RUT</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="DNI">DNI</option>
                                <option value="Otro">Otro...</option>
                            </select>
                            <input type="text" id="vinc_otro_doc" name="tipo_documento_otro" placeholder="Especifique..." style="display:none; width:100%; margin-top:0.5rem; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Número / RUT</label>
                            <input type="text" name="rut" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                        <div>
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Fecha Nac.</label>
                            <input type="date" name="fecha_nacimiento" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nacionalidad</label>
                            <input type="text" name="nacionalidad" value="Chilena" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                        </div>
                    </div>

                    <div style="margin-bottom:1.5rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Unidad Destino</label>
                        <select name="unidad_id" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:var(--color-bg); color:var(--color-text);">
                            <option value="">-- Seleccione Unidad --</option>
                            <?php foreach($unidades as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="padding-top:1rem; border-top:1px solid var(--glass-border); margin-bottom:1rem;">
                        <h4 style="color:var(--color-secondary); margin-bottom:1rem;">Suplente 1 (Opcional)</h4>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; margin-bottom:0.5rem;">
                            <input type="text" name="s1_nombre" placeholder="Nombre Completo" style="width:100%; padding:0.5rem; border-radius:4px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.05); color:white;">
                            <input type="text" name="s1_rut" placeholder="RUT / Doc" style="width:100%; padding:0.5rem; border-radius:4px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.05); color:white;">
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem;">
                            <input type="email" name="s1_email" placeholder="Email" style="width:100%; padding:0.5rem; border-radius:4px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.05); color:white;">
                            <input type="text" name="s1_telefono" placeholder="Teléfono" style="width:100%; padding:0.5rem; border-radius:4px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.05); color:white;">
                        </div>
                    </div>

                    <div style="padding-top:1rem; border-top:1px solid var(--glass-border); margin-bottom:1.5rem;">
                        <h4 style="color:var(--color-secondary); margin-bottom:1rem;">Suplente 2 (Opcional)</h4>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; margin-bottom:0.5rem;">
                            <input type="text" name="s2_nombre" placeholder="Nombre Completo" style="width:100%; padding:0.5rem; border-radius:4px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.05); color:white;">
                            <input type="text" name="s2_rut" placeholder="RUT / Doc" style="width:100%; padding:0.5rem; border-radius:4px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.05); color:white;">
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem;">
                            <input type="email" name="s2_email" placeholder="Email" style="width:100%; padding:0.5rem; border-radius:4px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.05); color:white;">
                            <input type="text" name="s2_telefono" placeholder="Teléfono" style="width:100%; padding:0.5rem; border-radius:4px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.05); color:white;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; background:var(--color-secondary);">Crear e Inscribir</button>
                </form>
            </section>
        </div>
    </main>

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
