<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.5; padding: 2cm; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 1rem; margin-bottom: 2rem; }
        .logo { max-height: 80px; }
        .group-info { text-align: right; }
        h1 { text-align: center; text-transform: uppercase; font-size: 1.5rem; margin-bottom: 2rem; }
        .metadata { margin-bottom: 2rem; }
        .content { min-height: 15cm; text-align: justify; white-space: pre-wrap; }
        .signatures { margin-top: 3rem; display: flex; justify-content: space-around; }
        .sig-box { border-top: 1px solid #000; width: 6cm; text-align: center; padding-top: 0.5rem; margin-top: 2rem; }
        .attendance { margin-top: 2rem; page-break-before: auto; }
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { border: 1px solid #000; padding: 0.5rem; font-size: 0.9rem; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background:#f4f4f4; padding:1rem; margin-bottom:2rem; text-align:center;">
        <button onclick="window.print()" style="padding:0.7rem 1.5rem; cursor:pointer;">🖨️ Imprimir Acta (PDF)</button>
        <p><small>Usa "Guardar como PDF" en las opciones de impresión.</small></p>
    </div>

    <div class="header">
        <?php if($config['logo_path']): ?>
            <img src="<?= htmlspecialchars($config['logo_path']) ?>" class="logo">
        <?php else: ?>
            <div style="font-weight:bold;">SCOUTS</div>
        <?php endif; ?>
        <div class="group-info">
            <strong><?= htmlspecialchars($config['nombre_grupo']) ?></strong><br>
            <?= htmlspecialchars($config['institucion_patrocinante']) ?>
        </div>
    </div>

    <h1>Acta de Consejo de Grupo</h1>

    <div class="metadata">
        <p><strong>FECHA:</strong> <?= date('d/m/Y', strtotime($reunion['fecha'])) ?></p>
        <p><strong>HORA:</strong> <?= date('H:i', strtotime($reunion['fecha'])) ?> hrs.</p>
        <p><strong>TEMA:</strong> <?= htmlspecialchars($reunion['tema']) ?></p>
    </div>

    <div class="content">
        <?= htmlspecialchars($reunion['acta']) ?>
    </div>

    <div class="attendance">
        <h3>Asistencia de Dirigentes</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($asistencia as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['nombre']) ?></td>
                    <td><?= htmlspecialchars($a['rol']) ?></td>
                    <td><?= $a['asiste'] ? 'PRESENTE' : 'AUSENTE' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="signatures">
        <div class="sig-box">
            Responsable de Grupo
        </div>
        <div class="sig-box">
            Secretario(a) de Consejo
        </div>
    </div>
</body>
</html>
