    <nav class="navbar" style="display:flex; justify-content:space-between; align-items:center; padding: 0.5rem 2rem; position: sticky; top: 0; z-index: 1000; background: var(--color-bg); backdrop-filter: blur(10px); border-bottom: 1px solid var(--glass-border);">
        <div style="display:flex; align-items:center; gap:1rem;">
            <?php if (!empty($global_config['logo_path'])): ?>
                <img src="<?= $global_config['logo_path'] ?>" alt="Logo Grupo" style="height:40px; width:auto;">
            <?php endif; ?>
            <div style="display:flex; flex-direction:column;">
                <a href="/dashboard" class="navbar-brand" style="margin:0;"><?= htmlspecialchars($global_config['nombre_grupo']) ?></a>
                <?php 
                    $isTest = isset($_SESSION['sigrrus_env']) && $_SESSION['sigrrus_env'] === 'testing';
                    $envLabel = $isTest ? 'ENTRENAMIENTO' : 'PRODUCCIÓN';
                    $envColor = $isTest ? '#10b981' : '#6366f1';
                ?>
                <span style="font-size:0.6rem; font-weight:bold; color:<?= $envColor ?>; letter-spacing:0.1em;"><?= $envLabel ?></span>
            </div>
        </div>
        
        <div class="navbar-links" style="display:flex; gap: 1.5rem; align-items:center;">
            <form action="/auth/setYear" method="POST" style="margin:0; display:inline-block;">
                <select name="anio_scout" onchange="this.form.submit()" style="padding:0.3rem; border-radius:4px; background:var(--glass-bg); color:var(--color-text); border:1px solid var(--glass-border);">
                    <?php 
                        $anioAct = $_SESSION['anio_scout'] ?? date('Y');
                        foreach($available_years as $i) {
                            $sel = $i == $anioAct ? 'selected' : '';
                            echo "<option value=\"$i\" $sel style=\"background:var(--color-bg); color:var(--color-text);\">Año Scout $i</option>";
                        }
                    ?>
                </select>
            </form>
            <span style="font-size:0.9rem;">Hola, <strong><?= htmlspecialchars($user['nombre'] ?? '') ?></strong></span>
            
            <?php if (isset($user['roles_disponibles']) && count($user['roles_disponibles']) > 1): ?>
                <div style="display:flex; gap:0.5rem; background:var(--glass-bg); padding:0.2rem; border-radius:8px; border:1px solid var(--glass-border);">
                    <?php foreach($user['roles_disponibles'] as $r): ?>
                        <a href="/auth/switchRole/<?= urlencode($r) ?>" 
                           class="btn btn-sm" 
                           style="padding:0.2rem 0.5rem; font-size:0.7rem; background: <?= $user['rol'] === $r ? 'var(--color-primary)' : 'transparent' ?>; color: <?= $user['rol'] === $r ? 'white' : 'var(--color-text)' ?>; border:none; text-decoration:none;">
                           <?= htmlspecialchars($r) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <a href="/auth/logout" class="btn btn-primary" style="background:var(--color-secondary); padding: 0.4rem 0.8rem; font-size:0.85rem;">Salir</a>
            
            <?php if (!empty($global_config['asociacion_logo_path'])): ?>
                <img src="<?= $global_config['asociacion_logo_path'] ?>" alt="Logo Asociación" style="height:40px; width:auto; margin-left:1rem;">
            <?php endif; ?>
        </div>
    </nav>
