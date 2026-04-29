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
            <a href="/unidades/<?= $unidad['id'] ?>/hoja-ruta" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Cancelar</a>
            <h1 style="margin-top:0.5rem;">Nueva Hoja de Ruta</h1>
        </header>

        <form action="/unidades/<?= $unidad['id'] ?>/hoja-ruta/editar" method="POST" class="glass-card" style="max-width:800px; margin: 0 auto;">
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600;">¿Vincular a una actividad del Ciclo de Programa?</label>
                <select name="actividad_id" onchange="toggleManual(this.value)" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
                    <option value="">-- No vincular (Actividad fuera de ciclo) --</option>
                    <?php foreach($actividadesCycle as $act): ?>
                        <option value="<?= $act['id'] ?>"><?= date('d/m/Y', strtotime($act['fecha'])) ?> - <?= htmlspecialchars($act['nombre_actividad']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="manual-name-div" style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Nombre de la Actividad (Manual)</label>
                <input type="text" name="nombre_actividad_manual" placeholder="Ej: Salida especial de fin de semana" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);">
            </div>

            <script>
                function toggleManual(val) {
                    const div = document.getElementById('manual-name-div');
                    const input = div.querySelector('input');
                    if (val === "") {
                        div.style.display = 'block';
                        input.required = true;
                    } else {
                        div.style.display = 'none';
                        input.required = false;
                    }
                }
            </script>

            <h3 style="margin-bottom:1rem; color:var(--color-secondary);">Detalles Técnicos</h3>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Motivación / Objetivo</label>
                <textarea name="motivacion" rows="3" style="width:100%; padding:0.75rem; border-radius:8px; border:1px solid var(--glass-border); background:rgba(255,255,255,0.1); color:var(--color-text);"></textarea>
            </div>

            <div style="text-align:center; padding:1rem; background:rgba(255,255,255,0.05); border-radius:8px; margin-bottom:1.5rem;">
                <p>Una vez creada, podrás completar el resto de campos pormenorizados (Fases, RRHH, Materiales, etc.)</p>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; padding:1rem;">Crear Hoja de Ruta</button>
        </form>
    </main>
</body>
</html>
