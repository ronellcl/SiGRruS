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
            <a href="/beneficiarios/ver/<?= $beneficiario['id'] ?>" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Perfil</a>
            <h1 style="margin-top:0.5rem;">Editar Beneficiario</h1>
        </header>

        <div class="glass-card" style="max-width: 800px; margin: 0 auto;">
            <form action="/beneficiarios/actualizar/<?= $beneficiario['id'] ?>" method="POST">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
                    <div>
                        <h3 style="color:var(--color-primary); margin-bottom:1rem;">Datos Personales</h3>
                        
                        <div style="margin-bottom:1rem;">
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nombre Completo</label>
                            <input type="text" name="nombre_completo" value="<?= htmlspecialchars($beneficiario['nombre_completo']) ?>" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                        </div>

                        <div style="margin-bottom:1rem;">
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">RUT / Documento</label>
                            <input type="text" name="rut" value="<?= htmlspecialchars($beneficiario['rut']) ?>" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                        </div>

                        <div style="margin-bottom:1rem;">
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Tipo Documento</label>
                            <input type="text" name="tipo_documento" value="<?= htmlspecialchars($beneficiario['tipo_documento'] ?? 'RUT') ?>" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                        </div>

                        <div style="margin-bottom:1rem;">
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" value="<?= $beneficiario['fecha_nacimiento'] ?>" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                        </div>

                        <div style="margin-bottom:1rem;">
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nacionalidad</label>
                            <input type="text" name="nacionalidad" value="<?= htmlspecialchars($beneficiario['nacionalidad'] ?? 'Chilena') ?>" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                        </div>
                    </div>

                    <div>
                        <h3 style="color:var(--color-secondary); margin-bottom:1rem;">Apoderados</h3>

                        <div style="margin-bottom:1rem;">
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Apoderado Titular</label>
                            <select name="apoderado_id" required style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:var(--color-bg); color:var(--color-text);">
                                <?php foreach($apoderados as $apo): ?>
                                    <option value="<?= $apo['id'] ?>" <?= $beneficiario['apoderado_id'] == $apo['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($apo['nombre_completo']) ?> (<?= $apo['rut'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="margin-bottom:1rem; padding-top:1rem; border-top:1px solid var(--glass-border);">
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Apoderado Suplente 1</label>
                            <select name="suplente_1_id" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:var(--color-bg); color:var(--color-text);">
                                <option value="">-- Ninguno --</option>
                                <?php foreach($apoderados as $apo): ?>
                                    <option value="<?= $apo['id'] ?>" <?= $beneficiario['apoderado_suplente_1_id'] == $apo['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($apo['nombre_completo']) ?> (<?= $apo['rut'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="margin-bottom:1rem;">
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Apoderado Suplente 2</label>
                            <select name="suplente_2_id" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:var(--color-bg); color:var(--color-text);">
                                <option value="">-- Ninguno --</option>
                                <?php foreach($apoderados as $apo): ?>
                                    <option value="<?= $apo['id'] ?>" <?= $beneficiario['apoderado_suplente_2_id'] == $apo['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($apo['nombre_completo']) ?> (<?= $apo['rut'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="margin-top:2rem; text-align:center;">
                    <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
