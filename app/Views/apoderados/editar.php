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
    <main class="container">
        <header style="margin-bottom: 2rem;">
            <h1>Editar Datos de Apoderado</h1>
        </header>

        <form action="/apoderados/editar/<?= $apoderado['id'] ?>" method="POST" class="glass-card" style="max-width:600px; margin: 0 auto;">
            <input type="hidden" name="referer" value="<?= htmlspecialchars($_GET['referer'] ?? '/dashboard') ?>">
            
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nombre Completo</label>
                <input type="text" name="nombre_completo" value="<?= htmlspecialchars($apoderado['nombre_completo'] ?? '') ?>" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Tipo de Documento</label>
                    <select name="tipo_documento" onchange="toggleOtroDoc(this, 'edit_apo_otro_doc')" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:var(--color-bg); color:var(--color-text);">
                        <?php $tipos = ['RUT', 'Pasaporte', 'DNI', 'CI']; ?>
                        <?php foreach($tipos as $t): ?>
                            <option value="<?= $t ?>" <?= ($apoderado['tipo_documento'] ?? 'RUT') === $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endforeach; ?>
                        <option value="Otro" <?= !in_array($apoderado['tipo_documento'] ?? 'RUT', $tipos) ? 'selected' : '' ?>>Otro...</option>
                    </select>
                    <input type="text" id="edit_apo_otro_doc" name="tipo_documento_otro" 
                           value="<?= !in_array($apoderado['tipo_documento'] ?? 'RUT', $tipos) ? htmlspecialchars($apoderado['tipo_documento']) : '' ?>"
                           placeholder="Especifique..." 
                           style="<?= !in_array($apoderado['tipo_documento'] ?? 'RUT', $tipos) ? 'display:block;' : 'display:none;' ?> width:100%; margin-top:0.5rem; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nacionalidad</label>
                    <input type="text" name="nacionalidad" value="<?= htmlspecialchars($apoderado['nacionalidad'] ?? 'Chilena') ?>" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                </div>
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Número de Documento / RUT</label>
                <input type="text" name="rut" value="<?= htmlspecialchars($apoderado['rut'] ?? '') ?>" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($apoderado['email'] ?? '') ?>" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Teléfono</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($apoderado['telefono'] ?? '') ?>" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
            </div>

            <div style="margin-bottom:2rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Dirección</label>
                <input type="text" name="direccion" value="<?= htmlspecialchars($apoderado['direccion'] ?? '') ?>" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
            </div>

            <div style="margin-bottom:2rem; padding:1rem; background:rgba(255,255,255,0.05); border-radius:8px; border:1px dashed var(--glass-border);">
                <h4 style="margin-bottom:0.5rem; color:var(--color-primary);">Seguridad de Cuenta</h4>
                <label style="display:block; margin-bottom:0.5rem; font-size:0.85rem; opacity:0.8;">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                <input type="password" name="password" placeholder="Mínimo 6 caracteres" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
            </div>

            <div style="display:flex; gap:1rem;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Guardar Cambios</button>
                <a href="<?= htmlspecialchars($_GET['referer'] ?? '/dashboard') ?>" class="btn btn-primary" style="background:var(--color-secondary); flex:0.3; text-align:center;">Cancelar</a>
            </div>
        </form>
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
