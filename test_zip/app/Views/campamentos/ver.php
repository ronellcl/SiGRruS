<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .consent-box { background: rgba(255,255,255,0.05); padding: 2rem; border-radius: 12px; border: 1px solid var(--glass-border); line-height: 1.6; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--glass-border); font-size:0.9rem; }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem;">
            <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver</a>
            <h1 style="margin-top:0.5rem;"><?= htmlspecialchars($camp['nombre']) ?></h1>
            <p><span class="badge" style="background:var(--color-primary)"><?= htmlspecialchars($camp['tipo']) ?></span> | <?= date('d/m/Y', strtotime($camp['fecha_inicio'])) ?> al <?= date('d/m/Y', strtotime($camp['fecha_fin'])) ?></p>
        </header>

        <div class="dashboard-grid">
            <!-- INFO GENERAL -->
            <div class="glass-card">
                <h3>Información del Campamento</h3>
                <hr style="border:0; border-top:1px solid var(--glass-border); margin:1rem 0;">
                <p><strong>Lugar:</strong> <?= htmlspecialchars($camp['lugar']) ?></p>
                <p><strong>Costo:</strong> $<?= number_format($camp['costo_cuota'], 0, ',', '.') ?></p>
                
                <h4 style="margin-top:1.5rem; color:var(--color-secondary);">Objetivos:</h4>
                <p style="font-size:0.9rem; opacity:0.8;"><?= nl2br(htmlspecialchars($camp['objetivos'])) ?></p>
                
                <h4 style="margin-top:1.5rem; color:var(--color-secondary);">Programa Resumen:</h4>
                <p style="font-size:0.9rem; opacity:0.8;"><?= nl2br(htmlspecialchars($camp['programa_resumen'])) ?></p>
            </div>

            <!-- SECCIÓN SEGÚN ROL -->
            <?php if ($user['rol'] === 'Apoderado'): ?>
                <!-- CONSENTIMIENTO INFORMADO PARA EL APODERADO -->
                <div class="glass-card">
                    <h3>Consentimiento Informado</h3>
                    <hr style="border:0; border-top:1px solid var(--glass-border); margin:1rem 0;">
                    
                    <?php 
                    // Obtener hijos del apoderado
                    $benefModel = new \App\Models\Beneficiario();
                    $hijos = $benefModel->getByApoderado($user['id']);
                    
                    foreach($hijos as $h): 
                        $campModel = new \App\Models\Campamento();
                        $auth = $campModel->getAutorizacion($camp['id'], $h['id']);
                    ?>
                        <div class="consent-box" style="margin-bottom:1.5rem;">
                            <h4>Beneficiario: <?= htmlspecialchars($h['nombre_completo']) ?></h4>
                            <?php if ($auth): ?>
                                <div style="background:rgba(40,167,69,0.2); color:#28a745; padding:1rem; border-radius:8px; margin-top:1rem; font-weight:bold;">
                                    ✅ AUTORIZADO el <?= date('d/m/Y H:i', strtotime($auth['fecha_autorizacion'])) ?>
                                </div>
                            <?php else: ?>
                                <p style="font-size:0.85rem; margin-top:1rem; opacity:0.9;">
                                    Yo, <strong><?= htmlspecialchars($user['nombre']) ?></strong>, en mi calidad de apoderado legal, autorizo a mi pupilo/a a participar en el campamento 
                                    mencionado, declarando conocer los riesgos inherentes y confirmando que la información de salud en la <strong>Ficha Médica</strong> se encuentra actualizada.
                                </p>
                                <form action="/campamentos/autorizar/<?= $camp['id'] ?>" method="POST" style="margin-top:1rem;">
                                    <input type="hidden" name="beneficiario_id" value="<?= $h['id'] ?>">
                                    <textarea name="observaciones_apoderado" placeholder="Observaciones adicionales o indicaciones de último minuto..." style="width:100%; padding:0.5rem; margin-bottom:1rem; background:rgba(255,255,255,0.1); border:1px solid var(--glass-border); color:white; border-radius:4px;"></textarea>
                                    <button type="submit" class="btn btn-primary" style="width:100%; background:#28a745;">Firmar Digitalmente y Autorizar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- LISTADO DE PARTICIPANTES PARA DIRIGENTES -->
                <div class="glass-card">
                    <h3>Control de Participantes y Autorizaciones</h3>
                    <hr style="border:0; border-top:1px solid var(--glass-border); margin:1rem 0;">
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Participante</th>
                                <th>Unidad</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($participantes)): ?>
                                <tr><td colspan="3" style="text-align:center; opacity:0.6;">Ningún apoderado ha firmado aún.</td></tr>
                            <?php else: ?>
                                <?php foreach($participantes as $p): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($p['nombre_completo']) ?></strong><br><small><?= htmlspecialchars($p['rut']) ?></small></td>
                                    <td><?= htmlspecialchars($p['unidad_nombre']) ?></td>
                                    <td>
                                        <span class="badge" style="background:#28a745; color:white;">AUTORIZADO</span>
                                        <?php if($p['observaciones_apoderado']): ?><br><small>Obs: <?= htmlspecialchars($p['observaciones_apoderado']) ?></small><?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
