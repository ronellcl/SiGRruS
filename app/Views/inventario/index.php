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
        .table th { color: var(--color-primary); }
        @media print { .navbar, .btn, .no-print, .form-section { display: none !important; } .glass-card { border: none; background: none; color: black; box-shadow: none; } body { background: white; } }
    </style>
</head>
<body>
    <nav class="navbar no-print">
        <a href="/dashboard" class="navbar-brand">SiGRruS - Dashboard</a>
        <div class="navbar-links">
            <span>Hola, <strong><?= htmlspecialchars($user['nombre']) ?></strong></span>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <a href="/dashboard" class="no-print" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
                <h1 style="margin-top:0.5rem;">Inventario de Activos - <?= htmlspecialchars($unidad['nombre']) ?></h1>
            </div>
            <div class="no-print" style="display:flex; gap:0.5rem;">
                <button onclick="window.print()" class="btn btn-primary" style="background:var(--color-secondary)">Imprimir PDF</button>
                <?php if (!\App\Core\Auth::isReadOnly()): ?>
                    <button onclick="document.getElementById('modal-item').style.display='block'" class="btn btn-primary">+ Nuevo Item</button>
                <?php endif; ?>
            </div>
        </header>

        <div class="glass-card">
            <?php if (empty($items)): ?>
                <p style="text-align:center; padding: 2rem; opacity:0.7;">No hay activos registrados en esta unidad.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Nombre Item</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                            <th class="no-print">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $i): ?>
                        <tr>
                            <td><span class="badge" style="background:rgba(255,255,255,0.1);"><?= htmlspecialchars($i['categoria']) ?></span></td>
                            <td>
                                <strong><?= htmlspecialchars($i['nombre_item']) ?></strong>
                                <?php if($i['observaciones']): ?><br><small style="opacity:0.6;"><?= htmlspecialchars($i['observaciones']) ?></small><?php endif; ?>
                            </td>
                            <td><?= $i['cantidad'] ?></td>
                            <td>
                                <span class="badge" style="background:<?= $i['estado'] === 'Bueno' ? '#28a745' : ($i['estado'] === 'Regular' ? '#ffc107' : '#dc3545') ?>; color:white;">
                                    <?= htmlspecialchars($i['estado']) ?>
                                </span>
                            </td>
                            <td class="no-print">
                                <?php if (!\App\Core\Auth::isReadOnly()): ?>
                                    <button onclick="editItem(<?= htmlspecialchars(json_encode($i)) ?>)" class="btn btn-primary btn-sm">Editar</button>
                                    <a href="/unidades/<?= $unidad['id'] ?>/inventario/eliminar/<?= $i['id'] ?>" class="btn btn-primary btn-sm" style="background:#dc3545" onclick="return confirm('¿Eliminar item?')">X</a>
                                <?php else: ?>
                                    <span style="opacity:0.5;">Solo Vista</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal Item -->
    <div id="modal-item" class="modal no-print" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div class="glass-card" style="max-width:500px; margin: 5rem auto; position:relative; background:var(--color-bg);">
            <button onclick="closeModal()" style="position:absolute; top:1rem; right:1rem; background:none; border:none; cursor:pointer; font-size:1.5rem; color:var(--color-text);">&times;</button>
            <h2 id="modal-title" style="margin-bottom:1.5rem;">Nuevo Item</h2>
            <form action="/unidades/<?= $unidad['id'] ?>/inventario/guardar" method="POST">
                <input type="hidden" name="id" id="item-id">
                <div style="margin-bottom:1rem;">
                    <label>Nombre del Item</label>
                    <input type="text" name="nombre_item" id="item-nombre" required placeholder="Ej: Carpa Doite 4P" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label>Categoría</label>
                        <select name="categoria" id="item-categoria" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                            <option value="Camping">Camping</option>
                            <option value="Cocina">Cocina</option>
                            <option value="Herramientas">Herramientas</option>
                            <option value="Progresión">Progresión</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                    <div>
                        <label>Cantidad</label>
                        <input type="number" name="cantidad" id="item-cantidad" value="1" required style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                </div>
                <div style="margin-bottom:1rem;">
                    <label>Estado</label>
                    <select name="estado" id="item-estado" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                        <option value="Bueno">Bueno</option>
                        <option value="Regular">Regular</option>
                        <option value="Malo">Malo</option>
                    </select>
                </div>
                <div style="margin-bottom:1.5rem;">
                    <label>Observaciones</label>
                    <textarea name="observaciones" id="item-obs" rows="2" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Guardar Item</button>
            </form>
        </div>
    </div>

    <script>
        function closeModal() { document.getElementById('modal-item').style.display = 'none'; }
        function editItem(item) {
            document.getElementById('modal-title').innerText = "Editar Item";
            document.getElementById('item-id').value = item.id;
            document.getElementById('item-nombre').value = item.nombre_item;
            document.getElementById('item-categoria').value = item.categoria;
            document.getElementById('item-cantidad').value = item.cantidad;
            document.getElementById('item-estado').value = item.estado;
            document.getElementById('item-obs').value = item.observaciones;
            document.getElementById('modal-item').style.display = 'block';
        }
    </script>
</body>
</html>
