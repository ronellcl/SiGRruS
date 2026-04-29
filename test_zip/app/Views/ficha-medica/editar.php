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
            <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
            <h1 style="margin-top:0.5rem;">Ficha Médica de Beneficiario</h1>
            <p style="opacity:0.8;">Por favor, mantenga estos datos actualizados para la seguridad del niño/a.</p>
        </header>

        <div class="glass-card">
            <h3 style="margin-bottom:1.5rem; color:var(--color-primary);">1. Datos de Identificación (Pre-cargados)</h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1.5rem; margin-bottom:2rem; opacity:0.9;">
                <div>
                    <label style="font-size:0.8rem; display:block;">Nombre Completo</label>
                    <strong><?= htmlspecialchars($beneficiario['nombre_completo']) ?></strong>
                </div>
                <div>
                    <label style="font-size:0.8rem; display:block;">RUT</label>
                    <strong><?= htmlspecialchars($beneficiario['rut']) ?></strong>
                </div>
                <div>
                    <label style="font-size:0.8rem; display:block;">Fecha de Nacimiento</label>
                    <strong><?= date('d/m/Y', strtotime($beneficiario['fecha_nacimiento'])) ?></strong>
                </div>
            </div>

            <form action="/fichas/guardar" method="POST">
                <input type="hidden" name="beneficiario_id" value="<?= $beneficiario['id'] ?>">
                
                <h3 style="margin-bottom:1.5rem; color:var(--color-primary);">2. Información de Salud</h3>
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">
                    <div>
                        <label>Grupo Sanguíneo</label>
                        <select name="tipo_sangre" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                            <option value="">Seleccione...</option>
                            <?php foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $t): ?>
                                <option value="<?= $t ?>" <?= ($ficha['tipo_sangre'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label>Previsión de Salud (Fonasa/Isapre)</label>
                        <input type="text" name="prevision_salud" value="<?= htmlspecialchars($ficha['prevision_salud'] ?? '') ?>" placeholder="Ej: Fonasa Tramo B" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label>Alergias (Alimentos, medicamentos, picaduras, etc.)</label>
                    <textarea name="alergias" rows="3" placeholder="Describa alergias y severidad..." style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;"><?= htmlspecialchars($ficha['alergias'] ?? '') ?></textarea>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label>Enfermedades Crónicas (Asma, Diabetes, Epilepsia, etc.)</label>
                    <textarea name="enfermedades_cronicas" rows="3" placeholder="Indique enfermedades y cuidados especiales..." style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;"><?= htmlspecialchars($ficha['enfermedades_cronicas'] ?? '') ?></textarea>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label>Medicamentos en Uso (Nombre, dosis y horario)</label>
                    <textarea name="medicamentos" rows="3" placeholder="Ej: Inhalador cada 8 horas en caso de crisis..." style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;"><?= htmlspecialchars($ficha['medicamentos'] ?? '') ?></textarea>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label>Restricciones Alimenticias (Vegetariano, Celíaco, Religioso, etc.)</label>
                    <textarea name="restricciones_alimenticias" rows="2" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;"><?= htmlspecialchars($ficha['restricciones_alimenticias'] ?? '') ?></textarea>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="vacunas_al_dia" value="1" <?= ($ficha['vacunas_al_dia'] ?? 1) ? 'checked' : '' ?>>
                        Declaro que el beneficiario tiene sus vacunas al día según el esquema ministerial.
                    </label>
                </div>

                <div style="margin-bottom:2rem;">
                    <label>Otras observaciones médicas de importancia</label>
                    <textarea name="observaciones_medicas" rows="3" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;"><?= htmlspecialchars($ficha['observaciones_medicas'] ?? '') ?></textarea>
                </div>

                <div style="text-align:right;">
                    <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem;">Guardar y Actualizar Ficha</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
