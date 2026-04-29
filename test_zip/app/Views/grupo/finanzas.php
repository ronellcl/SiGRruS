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
            <h1 style="margin-top:0.5rem;">Tesorería General de Grupo (<?= $anio ?>)</h1>
        </header>

        <div style="display:grid; grid-template-columns: 1fr 2fr; gap: 2rem; margin-bottom: 2rem;">
            <div class="glass-card" style="text-align:center; padding: 2rem; display:flex; flex-direction:column; justify-content:center;">
                <h3 style="opacity:0.8; text-transform:uppercase; font-size:0.8rem;">Caja Central Grupo</h3>
                <div style="font-size:2.5rem; font-weight:bold; color:var(--color-primary);">$<?= number_format($balance, 0, ',', '.') ?></div>
                <p style="font-size:0.8rem; opacity:0.6; margin-top:1rem;">Recaudado por traspasos de unidades</p>
            </div>

            <div class="glass-card">
                <h3 style="margin-bottom:1rem; font-size:1rem;">Consolidado de Unidades</h3>
                <table class="table" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="text-align:left; border-bottom:1px solid var(--glass-border);">
                            <th style="padding:0.5rem;">Unidad</th>
                            <th style="padding:0.5rem;">Ingresos</th>
                            <th style="padding:0.5rem;">Egresos</th>
                            <th style="padding:0.5rem;">Saldo Local</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($balances_unidades as $bu): 
                            $local = $bu['ingresos'] - $bu['egresos'];
                        ?>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                            <td style="padding:0.5rem;"><strong><?= htmlspecialchars($bu['nombre']) ?></strong></td>
                            <td style="padding:0.5rem; color:#28a745">$<?= number_format($bu['ingresos'], 0, ',', '.') ?></td>
                            <td style="padding:0.5rem; color:#dc3545">$<?= number_format($bu['egresos'], 0, ',', '.') ?></td>
                            <td style="padding:0.5rem; font-weight:bold;">$<?= number_format($local, 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <h2 style="margin-bottom:1.5rem; color:var(--color-secondary);">Traspasos Pendientes de Aprobación</h2>
        
        <div class="dashboard-grid" style="grid-template-columns: 1fr;">
            <?php if (empty($pendientes)): ?>
                <div class="glass-card" style="text-align:center; opacity:0.7;">No hay traspasos pendientes de revisión.</div>
            <?php else: ?>
                <?php foreach($pendientes as $p): ?>
                <div class="glass-card" style="display:flex; justify-content:space-between; align-items:center; border-left: 5px solid #ffc107;">
                    <div>
                        <h4 style="color:var(--color-primary);"><?= htmlspecialchars($p['unidad_nombre']) ?></h4>
                        <p><strong>Monto: $<?= number_format($p['monto'], 0, ',', '.') ?></strong></p>
                        <p style="font-size:0.9rem; opacity:0.8;"><?= htmlspecialchars($p['descripcion']) ?> (<?= date('d/m/Y', strtotime($p['fecha'])) ?>)</p>
                        <?php if($p['comprobante_archivo']): ?>
                            <a href="<?= $p['comprobante_archivo'] ?>" target="_blank" style="color:var(--color-secondary); text-decoration:none; font-size:0.85rem;">📎 Ver Comprobante de Depósito</a>
                        <?php endif; ?>
                    </div>
                    <div style="display:flex; gap:0.5rem;">
                        <a href="/grupo/aprobarTraspaso/<?= $p['id'] ?>" class="btn btn-primary" style="background:#28a745; font-size:0.85rem;">Aprobar Ingreso</a>
                        <button onclick="document.getElementById('rechazo-<?= $p['id'] ?>').style.display='block'" class="btn btn-primary" style="background:#dc3545; font-size:0.85rem;">Rechazar</button>
                    </div>
                </div>
                
                <!-- Modal Rechazo -->
                <div id="rechazo-<?= $p['id'] ?>" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
                    <div class="glass-card" style="max-width:400px; margin: 10rem auto; background:var(--color-bg);">
                        <h3>Rechazar Traspaso</h3>
                        <form action="/grupo/rechazarTraspaso/<?= $p['id'] ?>" method="POST">
                            <textarea name="justificacion" required placeholder="Motivo del rechazo (ej: El comprobante no coincide con el monto)" style="width:100%; padding:0.75rem; margin:1rem 0; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;"></textarea>
                            <button type="submit" class="btn btn-primary" style="width:100%; background:#dc3545;">Confirmar Rechazo</button>
                            <button type="button" onclick="this.parentElement.parentElement.parentElement.style.display='none'" class="btn btn-primary" style="width:100%; margin-top:0.5rem;">Cancelar</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
