<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--color-primary); }
        textarea { width: 100%; min-height: 100px; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: var(--color-text); font-family: inherit; resize: vertical; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem;">
            <a href="/unidades/<?= $unidad['id'] ?>/hoja-ruta" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Listado</a>
            <h1 style="margin-top:0.5rem;">Confección de Hoja de Ruta</h1>
            <p style="opacity:0.7;">Actividad: <strong><?= htmlspecialchars($hoja['nombre_ciclo'] ?: ($hoja['nombre_actividad_manual'] ?? 'Sin nombre')) ?></strong></p>
        </header>

        <form action="/unidades/<?= $unidad['id'] ?>/hoja-ruta/editar/<?= $hoja['id'] ?? '' ?>" method="POST" class="glass-card">
            <?php if (isset($hoja['actividad_id'])): ?>
                <input type="hidden" name="actividad_id" value="<?= $hoja['actividad_id'] ?>">
            <?php endif; ?>
            <div class="form-group">
                <label>1. Motivación y Objetivos</label>
                <textarea name="motivacion" placeholder="¿Por qué hacemos esta actividad?"><?= htmlspecialchars($hoja['motivacion'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>2. Detalles del Lugar</label>
                <textarea name="lugar_detalles" placeholder="Dirección exacta, clima, terreno..."><?= htmlspecialchars($hoja['lugar_detalles'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>3. Fases de la Actividad (Confección)</label>
                <textarea name="fases" placeholder="Inicio, Desarrollo, Cierre..."><?= htmlspecialchars($hoja['fases'] ?? '') ?></textarea>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>4. Variantes</label>
                    <textarea name="variantes" placeholder="Plan B en caso de lluvia, etc."><?= htmlspecialchars($hoja['variantes'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>5. Participación</label>
                    <textarea name="participacion" placeholder="Quiénes participan y roles específicos."><?= htmlspecialchars($hoja['participacion'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>6. Recursos Humanos</label>
                    <textarea name="recursos_humanos" placeholder="Dirigentes responsables, invitados..."><?= htmlspecialchars($hoja['recursos_humanos'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>7. Materiales Necesarios</label>
                    <textarea name="materiales" placeholder="Lista de materiales por subgrupo."><?= htmlspecialchars($hoja['materiales'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>8. Costos y Financiamiento</label>
                    <textarea name="costos" placeholder="Presupuesto estimado y de dónde sale el dinero."><?= htmlspecialchars($hoja['costos'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>9. Seguridad y Riesgos</label>
                    <textarea name="seguridad" placeholder="Riesgos identificados y medidas de mitigación."><?= htmlspecialchars($hoja['seguridad'] ?? '') ?></textarea>
                </div>
            </div>

            <div style="margin-top: 2rem; display:flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Guardar Hoja de Ruta</button>
                <a href="/unidades/<?= $unidad['id'] ?>/ciclo" class="btn btn-primary" style="background:var(--color-secondary); flex:0.3; text-align:center;">Cancelar</a>
            </div>
        </form>
    </main>
</body>
</html>
