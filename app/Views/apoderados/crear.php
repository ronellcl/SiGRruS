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

    <main class="container" style="max-width:900px;">
        <header style="margin-bottom: 2rem;">
            <a href="/grupo/apoderados" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Listado</a>
            <h1 style="margin-top:0.5rem;">Registro de Apoderado y Beneficiarios</h1>
        </header>

        <form action="/apoderados/guardar" method="POST">
            <div class="dashboard-grid">
                <!-- DATOS DEL APODERADO -->
                <div class="glass-card" style="grid-column: 1 / -1;">
                    <h3 style="color:var(--color-primary); margin-bottom:1.5rem;">Datos del Apoderado Responsable</h3>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem;">
                        <div>
                            <label>Nombre Completo</label>
                            <input type="text" name="nombre_completo" required style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                        </div>
                        <div>
                            <label>Tipo de Documento</label>
                            <select name="tipo_documento" onchange="toggleOtroDoc(this, 'apo_otro_doc')" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                                <option value="RUT">RUT (Chile)</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="DNI">DNI</option>
                                <option value="CI">Cédula de Identidad</option>
                                <option value="Otro">Otro...</option>
                            </select>
                            <input type="text" id="apo_otro_doc" name="tipo_documento_otro" placeholder="Especifique..." style="display:none; width:100%; margin-top:0.5rem; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                        </div>
                        <div>
                            <label>Número de Documento / RUT</label>
                            <input type="text" name="rut" required placeholder="Número de identificación" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                        </div>
                        <div>
                            <label>Nacionalidad</label>
                            <input type="text" name="nacionalidad" value="Chilena" required style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                        </div>
                        <div>
                            <label>Email de Contacto</label>
                            <input type="email" name="email" required style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                        </div>
                        <div>
                            <label>Teléfono</label>
                            <input type="text" name="telefono" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label>Dirección Particular</label>
                            <input type="text" name="direccion" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                        </div>

                        <!-- SUPLENTE 1 -->
                        <div style="grid-column: 1 / -1; margin-top:1rem; padding:1rem; background:rgba(255,255,255,0.05); border-radius:8px;">
                            <h4 style="color:var(--color-secondary); margin-bottom:1rem;">Apoderado Suplente 1 (Opcional)</h4>
                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                                <div>
                                    <label>Nombre Completo</label>
                                    <input type="text" name="s1_nombre" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                </div>
                                <div>
                                    <label>RUT / Documento</label>
                                    <input type="text" name="s1_rut" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                </div>
                                <div>
                                    <label>Email</label>
                                    <input type="email" name="s1_email" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                </div>
                                <div>
                                    <label>Teléfono</label>
                                    <input type="text" name="s1_telefono" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                </div>
                            </div>
                        </div>

                        <!-- SUPLENTE 2 -->
                        <div style="grid-column: 1 / -1; margin-top:0.5rem; padding:1rem; background:rgba(255,255,255,0.05); border-radius:8px;">
                            <h4 style="color:var(--color-secondary); margin-bottom:1rem;">Apoderado Suplente 2 (Opcional)</h4>
                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                                <div>
                                    <label>Nombre Completo</label>
                                    <input type="text" name="s2_nombre" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                </div>
                                <div>
                                    <label>RUT / Documento</label>
                                    <input type="text" name="s2_rut" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                </div>
                                <div>
                                    <label>Email</label>
                                    <input type="email" name="s2_email" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                </div>
                                <div>
                                    <label>Teléfono</label>
                                    <input type="text" name="s2_telefono" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BENEFICIARIOS ASOCIADOS -->
                <div class="glass-card" style="grid-column: 1 / -1;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                        <h3 style="color:var(--color-secondary);">Beneficiarios a Cargo (Hijos/Pupilos)</h3>
                        <button type="button" onclick="addHijo()" class="btn btn-primary" style="font-size:0.8rem;">+ Añadir Hijo</button>
                    </div>
                    
                    <div id="hijos-container">
                        <!-- Primer hijo por defecto -->
                        <div class="glass-card hijo-form" style="background:rgba(255,255,255,0.05); margin-bottom:1rem; padding:1.5rem; position:relative;">
                            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:1rem; margin-bottom:1rem;">
                                <div>
                                    <label>Nombre del Beneficiario</label>
                                    <input type="text" name="hijos_nombre[]" required style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                </div>
                                <div>
                                    <label>Tipo Documento</label>
                                    <select name="hijos_tipo_doc[]" onchange="toggleOtroDocHijo(this)" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                        <option value="RUT">RUT (Chile)</option>
                                        <option value="Pasaporte">Pasaporte</option>
                                        <option value="DNI">DNI</option>
                                        <option value="CI">Cédula de Identidad</option>
                                        <option value="Otro">Otro...</option>
                                    </select>
                                    <input type="text" name="hijos_tipo_doc_otro[]" placeholder="Especifique..." style="display:none; width:100%; margin-top:0.4rem; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                </div>
                                <div>
                                    <label>Número de Documento / RUT</label>
                                    <input type="text" name="hijos_rut[]" required placeholder="Número" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                </div>
                            </div>
                            <div style="display:grid; grid-template-columns: 1fr 1fr 2fr; gap:1rem;">
                                <div>
                                    <label>Nacionalidad</label>
                                    <input type="text" name="hijos_nacionalidad[]" value="Chilena" required style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                </div>
                                <div>
                                    <label>F. Nacimiento</label>
                                    <input type="date" name="hijos_fecha[]" required style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                </div>
                                <div>
                                    <label>Unidad de Destino</label>
                                    <select name="hijos_unidad[]" required style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                        <?php foreach($unidades as $u): ?>
                                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?> (<?= htmlspecialchars($u['rama']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top:2rem;">
                <button type="submit" class="btn btn-primary" style="width:100%; padding:1rem; font-size:1.1rem; background:#28a745;">Finalizar Registro Masivo</button>
            </div>
        </form>
    </main>

    <script>
        function toggleOtroDoc(select, inputId) {
            const input = document.getElementById(inputId);
            if (select.value === 'Otro') {
                input.style.display = 'block';
                input.required = true;
            } else {
                input.style.display = 'none';
                input.required = false;
            }
        }

        function toggleOtroDocHijo(select) {
            const input = select.nextElementSibling;
            if (select.value === 'Otro') {
                input.style.display = 'block';
                input.required = true;
            } else {
                input.style.display = 'none';
                input.required = false;
            }
        }

        function addHijo() {
            const container = document.getElementById('hijos-container');
            const div = document.createElement('div');
            div.className = 'glass-card hijo-form';
            div.style = 'background:rgba(255,255,255,0.05); margin-bottom:1rem; padding:1.5rem; position:relative;';
            div.innerHTML = `
                <button type="button" onclick="this.parentElement.remove()" style="position:absolute; top:0.5rem; right:0.5rem; background:none; border:none; color:#dc3545; cursor:pointer; font-size:1.2rem;">&times;</button>
                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label>Nombre del Beneficiario</label>
                        <input type="text" name="hijos_nombre[]" required style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                    </div>
                    <div>
                        <label>Tipo Documento</label>
                        <select name="hijos_tipo_doc[]" onchange="toggleOtroDocHijo(this)" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                            <option value="RUT">RUT (Chile)</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="DNI">DNI</option>
                            <option value="CI">Cédula de Identidad</option>
                            <option value="Otro">Otro...</option>
                        </select>
                        <input type="text" name="hijos_tipo_doc_otro[]" placeholder="Especifique..." style="display:none; width:100%; margin-top:0.4rem; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                    </div>
                    <div>
                        <label>Número de Documento / RUT</label>
                        <input type="text" name="hijos_rut[]" required placeholder="Número" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr 2fr; gap:1rem;">
                    <div>
                        <label>Nacionalidad</label>
                        <input type="text" name="hijos_nacionalidad[]" value="Chilena" required style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                    </div>
                    <div>
                        <label>F. Nacimiento</label>
                        <input type="date" name="hijos_fecha[]" required style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                    </div>
                    <div>
                        <label>Unidad de Destino</label>
                        <select name="hijos_unidad[]" required style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                            <?php foreach($unidades as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?> (<?= htmlspecialchars($u['rama']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            `;
            container.appendChild(div);
        }
    </script>
</body>
</html>
