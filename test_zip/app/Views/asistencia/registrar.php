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
        .attendance-opt { display: flex; gap: 1rem; }
        .opt-presente { color: #28a745; font-weight: bold; }
        .opt-ausente { color: #dc3545; font-weight: bold; }
        .opt-justificado { color: #ffc107; font-weight: bold; }
    </style>
</head>
<body>
    <main class="container">
        <header style="margin-bottom: 2rem;">
            <a href="/unidades/<?= $unidad['id'] ?>/ciclo" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Ciclo</a>
            <h1 style="margin-top:0.5rem;">Asistencia: <?= htmlspecialchars($actividad['nombre_actividad']) ?></h1>
            <p style="opacity:0.8;">Fecha: <?= date('d/m/Y', strtotime($actividad['fecha'])) ?> | Lugar: <?= htmlspecialchars($actividad['lugar']) ?></p>
        </header>

        <form action="/unidades/<?= $unidad['id'] ?>/asistencias/registrar/<?= $actividad['id'] ?>" method="POST" class="glass-card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Beneficiario</th>
                        <th>Estado de Asistencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($beneficiarios as $b): ?>
                    <?php $actual = $asistenciaActual[$b['id']] ?? 'Ausente'; ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($b['nombre_completo']) ?></strong><br>
                            <small style="opacity:0.7;"><?= htmlspecialchars($b['subgrupo'] ?: '-') ?></small>
                        </td>
                        <td>
                            <div class="attendance-opt">
                                <label class="opt-presente">
                                    <input type="radio" name="asistencia[<?= $b['id'] ?>]" value="Presente" <?= $actual === 'Presente' ? 'checked' : '' ?> <?= \App\Core\Auth::isReadOnly() ? 'disabled' : '' ?>> Presente
                                </label>
                                <label class="opt-ausente">
                                    <input type="radio" name="asistencia[<?= $b['id'] ?>]" value="Ausente" <?= $actual === 'Ausente' ? 'checked' : '' ?> <?= \App\Core\Auth::isReadOnly() ? 'disabled' : '' ?>> Ausente
                                </label>
                                <label class="opt-justificado">
                                    <input type="radio" name="asistencia[<?= $b['id'] ?>]" value="Justificado" <?= $actual === 'Justificado' ? 'checked' : '' ?> <?= \App\Core\Auth::isReadOnly() ? 'disabled' : '' ?>> Justificado
                                </label>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (!\App\Core\Auth::isReadOnly()): ?>
                <div style="margin-top:2rem;">
                    <button type="submit" class="btn btn-primary" style="width:100%; padding:1rem;">Guardar Asistencia</button>
                </div>
            <?php else: ?>
                <div style="margin-top:2rem; text-align:center; opacity:0.7;">
                    <p>Modo de solo lectura. No puedes modificar la asistencia.</p>
                </div>
            <?php endif; ?>
        </form>
    </main>
</body>
</html>
