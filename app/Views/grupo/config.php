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

    <main class="container" style="max-width:800px;">
        <header style="margin-bottom: 2rem;">
            <a href="/dashboard" style="color:var(--color-text); text-decoration:none; opacity:0.8;">&larr; Volver al Dashboard</a>
            <h1 style="margin-top:0.5rem;">Configuración Institucional del Grupo</h1>
        </header>

        <div class="glass-card">
            <form action="/grupo/guardarConfig" method="POST" enctype="multipart/form-data">
                <div style="margin-bottom:1.5rem;">
                    <label>Nombre del Grupo Scout</label>
                    <input type="text" name="nombre_grupo" value="<?= htmlspecialchars($config['nombre_grupo']) ?>" required style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">
                    <div>
                        <label>Logo del Grupo (Subir imagen)</label>
                        <?php if (!empty($config['logo_path'])): ?>
                            <div style="margin-bottom:0.5rem;"><img src="<?= $config['logo_path'] ?>" style="height:50px; border-radius:4px;"></div>
                        <?php endif; ?>
                        <input type="file" name="logo_file" accept="image/*" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                    <div>
                        <label>Logo de la Asociación Nacional (Subir imagen)</label>
                        <?php if (!empty($config['asociacion_logo_path'])): ?>
                            <div style="margin-bottom:0.5rem;"><img src="<?= $config['asociacion_logo_path'] ?>" style="height:50px; border-radius:4px;"></div>
                        <?php endif; ?>
                        <input type="file" name="asociacion_logo_file" accept="image/*" style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">
                    <div>
                        <label>País</label>
                        <select name="pais" id="pais-selector" onchange="updateCities()" style="width:100%; padding:0.75rem; background:var(--color-bg); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                            <option value="Chile" <?= ($config['pais'] ?? 'Chile') === 'Chile' ? 'selected' : '' ?>>Chile</option>
                            <option value="Argentina" <?= ($config['pais'] ?? '') === 'Argentina' ? 'selected' : '' ?>>Argentina</option>
                            <option value="Perú" <?= ($config['pais'] ?? '') === 'Perú' ? 'selected' : '' ?>>Perú</option>
                            <option value="Colombia" <?= ($config['pais'] ?? '') === 'Colombia' ? 'selected' : '' ?>>Colombia</option>
                            <option value="España" <?= ($config['pais'] ?? '') === 'España' ? 'selected' : '' ?>>España</option>
                            <option value="Otro" <?= !in_array($config['pais'] ?? 'Chile', ['Chile', 'Argentina', 'Perú', 'Colombia', 'España']) ? 'selected' : '' ?>>Otro...</option>
                        </select>
                        <input type="text" id="pais-otro" name="pais_otro" placeholder="Escriba el país" 
                               style="display:none; width:100%; margin-top:0.5rem; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                    <div>
                        <label>Ciudad</label>
                        <select name="ciudad" id="ciudad-selector" onchange="checkOtherCity()" style="width:100%; padding:0.75rem; background:var(--color-bg); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                            <!-- Opciones cargadas por JS -->
                        </select>
                        <input type="text" id="ciudad-otro" name="ciudad_otro" placeholder="Escriba la ciudad" 
                               style="display:none; width:100%; margin-top:0.5rem; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">
                    <div>
                        <label>Zona</label>
                        <select name="zona" id="zona-selector" onchange="updateDistricts()" style="width:100%; padding:0.75rem; background:var(--color-bg); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                            <!-- Opciones cargadas por JS -->
                        </select>
                        <input type="text" id="zona-otro" name="zona_otro" value="<?= htmlspecialchars($config['zona'] ?? '') ?>" placeholder="Escriba la zona" 
                               style="display:none; width:100%; margin-top:0.5rem; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                    <div>
                        <label>Distrito</label>
                        <select name="distrito" id="distrito-selector" onchange="checkOtherDistrito()" style="width:100%; padding:0.75rem; background:var(--color-bg); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                            <!-- Opciones cargadas por JS -->
                        </select>
                        <input type="text" id="distrito-otro" name="distrito_otro" value="<?= htmlspecialchars($config['distrito'] ?? '') ?>" placeholder="Escriba el distrito" 
                               style="display:none; width:100%; margin-top:0.5rem; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label>Institución Patrocinante</label>
                    <input type="text" name="institucion_patrocinante" value="<?= htmlspecialchars($config['institucion_patrocinante']) ?>" placeholder="Ej: Parroquia San José" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                </div>

                <hr style="border:0; border-top:1px solid var(--glass-border); margin: 2rem 0;">
                <h3 style="margin-bottom:1rem; color:var(--color-primary);">📧 Configuración de Correo (SMTP)</h3>
                <p style="font-size:0.85rem; opacity:0.7; margin-bottom:1.5rem;">Configura tu propio servidor de correo para el envío de contraseñas y notificaciones.</p>

                <div style="margin-bottom:1.5rem;">
                    <label>Servidor SMTP (Host)</label>
                    <input type="text" name="smtp_host" value="<?= htmlspecialchars($config['smtp_host'] ?? '') ?>" placeholder="ej: smtp.gmail.com" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">
                    <div>
                        <label>Usuario SMTP</label>
                        <input type="text" name="smtp_user" value="<?= htmlspecialchars($config['smtp_user'] ?? '') ?>" placeholder="ej: usuario@empresa.com" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                    <div>
                        <label>Contraseña SMTP</label>
                        <input type="password" name="smtp_pass" value="<?= htmlspecialchars($config['smtp_pass'] ?? '') ?>" placeholder="••••••••" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; margin-bottom:2rem;">
                    <div>
                        <label>Puerto</label>
                        <input type="number" name="smtp_port" value="<?= htmlspecialchars($config['smtp_port'] ?? '587') ?>" style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                    <div>
                        <label>Cifrado</label>
                        <select name="smtp_encryption" style="width:100%; padding:0.75rem; background:var(--color-bg); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                            <option value="tls" <?= ($config['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                            <option value="ssl" <?= ($config['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                            <option value="none" <?= ($config['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>Sin cifrado</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; padding:1rem; font-weight:bold;">Guardar Configuración General</button>
            </form>
        </div>
    </main>

    <script>
        const citiesByCountry = {
            'Chile': ['Santiago', 'Valparaíso', 'Concepción', 'La Serena', 'Antofagasta', 'Temuco', 'Puerto Montt', 'Rancagua', 'Iquique', 'Talca', 'Arica', 'Punta Arenas'],
            'Argentina': ['Buenos Aires', 'Córdoba', 'Rosario', 'Mendoza', 'La Plata'],
            'Perú': ['Lima', 'Arequipa', 'Trujillo', 'Chiclayo'],
            'Colombia': ['Bogotá', 'Medellín', 'Cali', 'Barranquilla'],
            'España': ['Madrid', 'Barcelona', 'Valencia', 'Sevilla']
        };

        const estructuraChile = {
            "Zona Arica": ["Arica"],
            "Zona Iquique": ["Alto Hospicio", "Cavancha", "Cerro Tarapacá"],
            "Zona Antofagasta": ["Coloso", "El Loa", "La Portada"],
            "Zona Atacama": ["Copayapu", "Incaripe", "Valle del Huasco"],
            "Zona Norte Verde": ["Coquimbo", "Diaguitas", "Limarí", "Choapa"],
            "Zona Valparaíso": ["José Francisco Vergara", "Marga-Marga", "Michimalonco", "San Antonio", "Valparaíso", "Villa Alemana", "Viña del Mar"],
            "Zona Aconcagua": ["Llay Llay", "San Felipe"],
            "Zona Cajón del Maipo": ["Camilo Henríquez", "Las Vizcachas", "Puente Alto Poniente"],
            "Zona Santiago Centro": ["Cerro Huelén", "Santiago Centro", "Providencia"],
            "Zona Santiago Cordillera": ["Apoquindo", "Los Leones", "Manquehue", "Vitacura"],
            "Zona Santiago La Florida": ["Bellavista", "Mapurayen", "Peñimahuida"],
            "Zona Santiago Maipo": ["San Bernardo", "El Bosque", "Valle del Maipo"],
            "Zona Santiago Norte": ["Chacabuco", "Conchalí", "La Cañadilla", "Quilicura", "Renca"],
            "Zona Santiago Oeste": ["Cerrillos", "Maipú Nuevo Extremo", "Melipilla", "Pila del Ganso", "Quilamapu", "Quinta Normal-Cerro Navia", "Talakanta"],
            "Zona Santiago Oriente": ["La Reina", "Macul", "Ñuñoa", "Pedro de Valdivia", "Peñalolén"],
            "Zona Santiago Sur": ["La Cisterna", "La Granja", "Pedro Aguirre Cerda", "San Joaquín", "San Miguel", "Santa Rosa"],
            "Zona del Libertador": ["Cachapoal", "Cipreses", "Colchagua", "O'Higgins"],
            "Zona del Maule": ["Curicó", "Linares", "Talca"],
            "Zona Ñuble": ["Ñuble"],
            "Zona Bio Bio": ["Bio Bio", "Concepción", "Nahuelbuta", "Río Andalien", "Talcahuano"],
            "Zona de la Frontera": ["de la Frontera"],
            "Zona de los Ríos": ["Valdivia"],
            "Zona Los Lagos": ["Osorno"],
            "Zona Reloncavi": ["Lago Llanquihue", "Puerto Montt", "Chiloe"],
            "Zona Aysén": ["Coyhaique"],
            "Zona Magallanes": ["Punta Arenas"]
        };

        const currentCity = "<?= htmlspecialchars($config['ciudad'] ?? '') ?>";
        const currentCountry = "<?= htmlspecialchars($config['pais'] ?? 'Chile') ?>";
        const currentZona = "<?= htmlspecialchars($config['zona'] ?? '') ?>";
        const currentDistrito = "<?= htmlspecialchars($config['distrito'] ?? '') ?>";

        function updateCities() {
            const countrySelector = document.getElementById('pais-selector');
            const citySelector = document.getElementById('ciudad-selector');
            const countryOtro = document.getElementById('pais-otro');
            const country = countrySelector.value;

            // Mostrar/Ocultar manual de país
            if (country === 'Otro') {
                countryOtro.style.display = 'block';
                countryOtro.required = true;
            } else {
                countryOtro.style.display = 'none';
                countryOtro.required = false;
            }

            // Actualizar Ciudades
            const cities = (citiesByCountry[country] || []).sort();
            let html = '';
            cities.forEach(city => {
                html += `<option value="${city}" ${city === currentCity ? 'selected' : ''}>${city}</option>`;
            });
            html += `<option value="Otra" ${(!cities.includes(currentCity) && currentCity !== '') ? 'selected' : ''}>Otra...</option>`;
            citySelector.innerHTML = html;
            
            checkOtherCity();
            updateZones(); // Actualizar zonas basado en el país
        }

        function checkOtherCity() {
            const citySelector = document.getElementById('ciudad-selector');
            const cityOtro = document.getElementById('ciudad-otro');
            if (citySelector.value === 'Otra') {
                cityOtro.style.display = 'block';
                cityOtro.required = true;
                if (!citiesByCountry[document.getElementById('pais-selector').value]?.includes(currentCity)) {
                    cityOtro.value = currentCity;
                }
            } else {
                cityOtro.style.display = 'none';
                cityOtro.required = false;
            }
        }

        function updateZones() {
            const country = document.getElementById('pais-selector').value;
            const zonaSelector = document.getElementById('zona-selector');
            const zonaOtro = document.getElementById('zona-otro');

            if (country !== 'Chile') {
                zonaSelector.innerHTML = '<option value="Otra">Otra / No aplica</option>';
                zonaOtro.style.display = 'block';
                zonaOtro.required = false;
                updateDistricts();
                return;
            }

            let html = '<option value="">-- Seleccione Zona --</option>';
            const sortedZones = Object.keys(estructuraChile).sort();
            sortedZones.forEach(zona => {
                html += `<option value="${zona}" ${zona === currentZona ? 'selected' : ''}>${zona}</option>`;
            });
            html += `<option value="Otra" ${(currentZona && !estructuraChile[currentZona]) ? 'selected' : ''}>Otra...</option>`;
            zonaSelector.innerHTML = html;
            
            updateDistricts();
        }

        function updateDistricts() {
            const zona = document.getElementById('zona-selector').value;
            const distritoSelector = document.getElementById('distrito-selector');
            const zonaOtro = document.getElementById('zona-otro');
            const country = document.getElementById('pais-selector').value;

            if (zona === 'Otra' || country !== 'Chile') {
                zonaOtro.style.display = 'block';
                zonaOtro.required = (zona === 'Otra');
                distritoSelector.innerHTML = '<option value="Otra">Otro / No aplica</option>';
            } else {
                zonaOtro.style.display = 'none';
                zonaOtro.required = false;
                
                const distritos = (estructuraChile[zona] || []).sort();
                let html = '<option value="">-- Seleccione Distrito --</option>';
                distritos.forEach(d => {
                    html += `<option value="${d}" ${d === currentDistrito ? 'selected' : ''}>${d}</option>`;
                });
                html += `<option value="Otra" ${(currentDistrito && !distritos.includes(currentDistrito)) ? 'selected' : ''}>Otro...</option>`;
                distritoSelector.innerHTML = html;
            }
            checkOtherDistrito();
        }

        function checkOtherDistrito() {
            const distritoSelector = document.getElementById('distrito-selector');
            const distritoOtro = document.getElementById('distrito-otro');
            const zona = document.getElementById('zona-selector').value;

            if (distritoSelector.value === 'Otra') {
                distritoOtro.style.display = 'block';
                distritoOtro.required = true;
                const distritosArr = estructuraChile[zona] || [];
                if (!distritosArr.includes(currentDistrito)) {
                    distritoOtro.value = currentDistrito;
                }
            } else {
                distritoOtro.style.display = 'none';
                distritoOtro.required = false;
            }
        }

        // Initialize
        updateCities();
        if (document.getElementById('pais-selector').value === 'Otro') {
            document.getElementById('pais-otro').value = currentCountry;
        }
    </script>
            </form>
        </div>
    </main>
</body>
</html>
