<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .tabs { display: flex; gap: 1rem; margin-bottom: 2rem; border-bottom: 1px solid var(--glass-border); }
        .tab-btn { padding: 1rem; cursor: pointer; opacity: 0.7; border-bottom: 3px solid transparent; }
        .tab-btn.active { opacity: 1; border-bottom-color: var(--color-primary); font-weight: bold; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--glass-border); font-size: 0.9rem; }
        .table th { color: var(--color-primary); }

        .cuotas-table { overflow-x: auto; }
        .dot { width: 15px; height: 15px; border-radius: 50%; display: inline-block; cursor: pointer; }
        .dot-paid { background: #28a745; }
        .dot-pending { background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border); }

        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .summary-card { padding: 1.5rem; text-align: center; }
        .summary-card h4 { font-size: 0.8rem; text-transform: uppercase; opacity: 0.8; margin-bottom: 0.5rem; }
        .summary-card .amount { font-size: 1.8rem; font-weight: bold; }
    </style>
</head>
<body>
    <?php include APP_PATH . '/Views/partials/navbar.php'; ?>

    <main class="container">
        <header style="margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
                <h1 style="margin-top:0.5rem;">Tesorería de Unidad - <?= htmlspecialchars($unidad['nombre']) ?></h1>
            </div>
            <div style="display:flex; gap:0.5rem;">
                <?php if (!\App\Core\Auth::isReadOnly()): ?>
                    <button onclick="openModal('modal-movimiento')" class="btn btn-primary">+ Movimiento de Caja</button>
                <?php endif; ?>
            </div>
        </header>

        <?php
            $ingresos = 0; $egresos = 0;
            foreach($movimientos as $m) {
                if($m['tipo'] === 'Ingreso') $ingresos += $m['monto']; else $egresos += $m['monto'];
            }
            $balance = $ingresos - $egresos;
        ?>

        <div class="summary-grid">
            <div class="glass-card summary-card">
                <h4>Ingresos Totales</h4>
                <div class="amount" style="color:#28a745">$<?= number_format($ingresos, 0, ',', '.') ?></div>
            </div>
            <div class="glass-card summary-card">
                <h4>Egresos Totales</h4>
                <div class="amount" style="color:#dc3545">$<?= number_format($egresos, 0, ',', '.') ?></div>
            </div>
            <div class="glass-card summary-card" style="background:rgba(255,255,255,0.05)">
                <h4>Saldo en Caja</h4>
                <div class="amount" style="color:var(--color-primary)">$<?= number_format($balance, 0, ',', '.') ?></div>
            </div>
        </div>

        <div class="tabs">
            <div class="tab-btn active" onclick="showTab('tab-cuotas', this)">Cuotas Mensuales</div>
            <div class="tab-btn" onclick="showTab('tab-inscripcion', this)">Inscripción Anual</div>
            <div class="tab-btn" onclick="showTab('tab-movimientos', this)">Historial de Movimientos</div>
        </div>

        <!-- TAB INSCRIPCION -->
        <div id="tab-inscripcion" class="tab-content">
            <div class="glass-card">
                <h3>Registro de Inscripción Anual <?= $anio_actual ?></h3>
                <p style="margin-bottom:1.5rem; opacity:0.8;">Monto definido por el grupo: <strong>$<?= number_format($valor_inscripcion_grupo ?? 0, 0, ',', '.') ?></strong></p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Beneficiario</th>
                            <th>Estado</th>
                            <th>Monto Pagado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cuotas as $bid => $data): 
                            $pagado_insc = isset($data['inscripcion']) && $data['inscripcion']['pagado'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($data['nombre']) ?></td>
                            <td>
                                <span class="badge" style="background:<?= $pagado_insc ? '#28a745' : '#ffc107' ?>; color:white;">
                                    <?= $pagado_insc ? 'Pagado' : 'Pendiente' ?>
                                </span>
                            </td>
                            <td>$<?= number_format($pagado_insc ? $data['inscripcion']['monto'] : 0, 0, ',', '.') ?></td>
                            <td>
                                <?php if(!$pagado_insc && !\App\Core\Auth::isReadOnly()): ?>
                                    <button onclick="openPagoModal(<?= $bid ?>, '<?= addslashes($data['nombre']) ?>', 0, <?= $valor_inscripcion_grupo ?? 0 ?>)" class="btn btn-primary btn-sm">Registrar Pago</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB CUOTAS -->
        <div id="tab-cuotas" class="tab-content active">
            <div class="glass-card cuotas-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Beneficiario</th>
                            <?php for($m=1; $m<=12; $m++): ?>
                                <th style="text-align:center;"><?= substr(['','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'][$m], 0, 3) ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cuotas as $bid => $data): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($data['nombre']) ?></strong></td>
                            <?php for($m=1; $m<=12; $m++): ?>
                                <td style="text-align:center;">
                                    <?php if($data['meses'][$m]['pagado']): ?>
                                        <span class="dot dot-paid" title="Pagado: $<?= number_format($data['meses'][$m]['monto'],0) ?>"></span>
                                    <?php else: ?>
                                        <span class="dot dot-pending" <?php if(!\App\Core\Auth::isReadOnly()): ?>onclick="openPagoModal(<?= $bid ?>, '<?= addslashes($data['nombre']) ?>', <?= $m ?>)"<?php endif; ?> title="Pendiente"></span>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB MOVIMIENTOS -->
        <div id="tab-movimientos" class="tab-content">
            <div class="glass-card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Descripción / Respaldo</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($movimientos as $m): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($m['fecha'])) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($m['descripcion']) ?></strong>
                                <?php if($m['beneficiario']): ?><br><small style="opacity:0.6;">(Benef: <?= htmlspecialchars($m['beneficiario']) ?>)</small><?php endif; ?>
                                <?php if($m['comprobante_archivo']): ?>
                                    <br><a href="<?= $m['comprobante_archivo'] ?>" target="_blank" style="color:var(--color-primary); font-size:0.8rem;">📎 Ver Comprobante/Boleta</a>
                                <?php elseif($m['tipo'] === 'Egreso'): ?>
                                    <br><span style="color:#ffc107; font-size:0.8rem;">⚠️ Sin documento. Justificación: <?= htmlspecialchars($m['justificacion'] ?: 'No informada') ?></span>
                                <?php endif; ?>
                                
                                <?php if($m['estado'] === 'pendiente'): ?>
                                    <br><span class="badge" style="background:#ffc107; color:#000; font-size:0.7rem;">⏳ Pendiente de Aprobación Grupo</span>
                                <?php elseif($m['estado'] === 'rechazado'): ?>
                                    <br><span class="badge" style="background:#dc3545; color:white; font-size:0.7rem;">❌ Rechazado: <?= htmlspecialchars($m['justificacion']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge" style="background:<?= $m['tipo'] === 'Ingreso' ? '#28a745' : '#dc3545' ?>; color:white;"><?= $m['tipo'] ?></span>
                            </td>
                            <td style="font-weight:bold; color:<?= $m['tipo'] === 'Ingreso' ? '#28a745' : '#dc3545' ?>">
                                <?= $m['tipo'] === 'Ingreso' ? '+' : '-' ?>$<?= number_format($m['monto'], 0, ',', '.') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Movimiento -->
    <div id="modal-movimiento" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div class="glass-card" style="max-width:500px; margin: 3rem auto; position:relative; background:var(--color-bg); max-height:90vh; overflow-y:auto;">
            <button onclick="closeModal('modal-movimiento')" style="position:absolute; top:1rem; right:1rem; background:none; border:none; cursor:pointer; font-size:1.5rem; color:var(--color-text);">&times;</button>
            <h2 style="margin-bottom:1.5rem;">Registrar Movimiento</h2>
            <form action="/unidades/<?= $unidad['id'] ?>/finanzas/registrarMovimiento" method="POST" enctype="multipart/form-data">
                <div style="margin-bottom:1rem;">
                    <label>Tipo</label>
                    <select name="tipo" id="mov-tipo" onchange="toggleJustificacion(this.value)" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                        <option value="Ingreso">Ingreso (Pago, Cuota, etc)</option>
                        <option value="Egreso">Egreso (Gasto, Depósito a Grupo)</option>
                    </select>
                </div>
                <div style="margin-bottom:1rem;">
                    <label>Monto</label>
                    <input type="number" name="monto" required style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                </div>
                <div style="margin-bottom:1rem;">
                    <label>Descripción</label>
                    <input type="text" name="descripcion" required placeholder="Ej: Depósito Cuotas a Tesorería Grupo" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                </div>
                <div style="margin-bottom:1rem;">
                    <label>Adjuntar Comprobante / Boleta (Imagen o PDF)</label>
                    <input type="file" name="comprobante" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                </div>
                <div id="div-traspaso" style="margin-bottom:1rem; display:none; background:rgba(40,167,69,0.1); padding:0.5rem; border-radius:8px;">
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="es_traspaso_grupo" value="1">
                        ¿Es un depósito a Tesorería de Grupo? (Requiere aprobación del grupo)
                    </label>
                </div>
                <div id="div-justificacion" style="margin-bottom:1.5rem; display:none;">
                    <label>Justificación (Si no hay boleta)</label>
                    <textarea name="justificacion" placeholder="Explica por qué no hay respaldo legal..." style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;"></textarea>
                </div>
                <div style="margin-bottom:1rem;">
                    <label>Fecha</label>
                    <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Guardar Registro</button>
            </form>
        </div>
    </div>

    <script>
        function toggleJustificacion(tipo) {
            document.getElementById('div-justificacion').style.display = (tipo === 'Egreso') ? 'block' : 'none';
            document.getElementById('div-traspaso').style.display = (tipo === 'Egreso') ? 'block' : 'none';
        }
    </script>

    <!-- Modal Pago Cuota -->
    <div id="modal-pago" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div class="glass-card" style="max-width:400px; margin: 10rem auto; position:relative; background:var(--color-bg);">
            <button onclick="closeModal('modal-pago')" style="position:absolute; top:1rem; right:1rem; background:none; border:none; cursor:pointer; font-size:1.5rem; color:var(--color-text);">&times;</button>
            <h2 style="margin-bottom:1rem;">Registrar Pago</h2>
            <p id="pago-beneficiario" style="margin-bottom:1rem; font-weight:bold;"></p>
            <form action="/unidades/<?= $unidad['id'] ?>/finanzas/pagarCuota" method="POST">
                <input type="hidden" name="beneficiario_id" id="pago-id">
                <input type="hidden" name="mes" id="pago-mes">
                <div style="margin-bottom:1.5rem;">
                    <label>Monto del Pago</label>
                    <input type="number" name="monto" id="pago-monto-input" value="5000" required style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Confirmar Pago</button>
            </form>
        </div>
    </div>

    <script>
        function showTab(tabId, btn) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            btn.classList.add('active');
        }
        function openModal(id) { document.getElementById(id).style.display = 'block'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }

        function openPagoModal(id, nombre, mes, montoDefault = 5000) {
            let label = (mes === 0) ? "Inscripción Anual" : "Mes " + mes;
            document.getElementById('pago-beneficiario').innerText = "Pago " + nombre + " (" + label + ")";
            document.getElementById('pago-id').value = id;
            document.getElementById('pago-mes').value = mes;
            document.getElementById('pago-monto-input').value = montoDefault;
            openModal('modal-pago');
        }
    </script>
</body>
</html>
