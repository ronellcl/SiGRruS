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
            <h1 style="margin-top:0.5rem;">Panel de Control Técnico</h1>
            <p>Herramientas avanzadas de desarrollo y pruebas.</p>
        </header>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="glass-card" style="background:rgba(40,167,69,0.2); border-color:#28a745; margin-bottom:2rem; padding:1rem; text-align:center;">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="glass-card" style="background:rgba(220,53,69,0.2); border-color:#dc3545; margin-bottom:2rem; padding:1rem; text-align:center; color:#ffb3b3;">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- CONTROL DE DEBUG -->
        <div class="glass-card" style="margin-bottom: 2rem; border-left: 4px solid <?= $debug_mode ? '#ffc107' : '#6c757d' ?>;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h3 style="margin:0;">🛠️ Modo Debug del Sistema</h3>
                    <p style="font-size:0.85rem; opacity:0.7; margin:0.2rem 0 0 0;">Activa o desactiva la barra de información técnica en el Dashboard.</p>
                </div>
                <div style="text-align:right;">
                    <span class="badge" style="background: <?= $debug_mode ? '#ffc107' : '#6c757d' ?>; color:#000; margin-bottom:0.5rem; display:inline-block;">
                        <?= $debug_mode ? 'ACTIVADO' : 'DESACTIVADO' ?>
                    </span><br>
                    <a href="/tecnico/toggleDebug" class="btn btn-primary btn-sm" style="background: var(--color-primary);">
                        <?= $debug_mode ? 'Desactivar Debug' : 'Activar Debug' ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- ACTUALIZACIONES DE SOFTWARE (ESTILO WORDPRESS) -->
        <div class="glass-card" style="margin-bottom: 2rem; border-top: 4px solid #6366f1;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h3 style="margin:0;">🚀 Actualización del Sistema</h3>
                    <p style="font-size:0.85rem; opacity:0.7; margin:0.2rem 0 0 0;">
                        Versión Instalada: <strong>v<?= $current_version ?></strong>
                        <a href="<?= APP_URL ?>/tecnico/buscarActualizaciones" style="margin-left:10px; color:#6366f1; text-decoration:none; font-size:0.75rem; border:1px solid #6366f1; padding:2px 8px; border-radius:4px;" title="Buscar ahora">
                            🔄 Buscar Actualizaciones
                        </a>
                    </p>
                </div>
                <div style="text-align:right;">
                    <?php if ($updateInfo && $updateInfo['has_update']): ?>
                        <span class="badge" style="background:#6366f1; color:white; margin-bottom:0.5rem; display:inline-block;">
                            NUEVA VERSIÓN: v<?= $updateInfo['latest'] ?>
                        </span><br>
                        <a href="/tecnico/procesarActualizacion" class="btn btn-primary" style="background: #6366f1;" onclick="return confirm('¡IMPORTANTE! Se realizará un respaldo completo (SQL y Archivos) antes de actualizar. El sistema se sobrescribirá con la nueva versión. ¿Deseas continuar?')">
                            Actualizar Ahora
                        </a>
                    <?php else: ?>
                        <span class="badge" style="background:#28a745; color:white; margin-bottom:0.5rem; display:inline-block;">
                            SISTEMA AL DÍA
                        </span><br>
                        <small style="opacity:0.6;">Última comprobación: hoy</small>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($updateInfo && $updateInfo['has_update'] && !empty($updateInfo['body'])): ?>
                <div style="margin-top:1rem; padding:1rem; background:rgba(255,255,255,0.05); border-radius:8px; font-size:0.85rem;">
                    <strong style="display:block; margin-bottom:0.5rem;">Notas de la versión:</strong>
                    <div style="opacity:0.8; white-space: pre-wrap;"><?= htmlspecialchars($updateInfo['body']) ?></div>
                </div>
            <?php endif; ?>

            <?php if ($pendingPatches > 0): ?>
                <div style="margin-top:1.5rem; padding:1rem; background:rgba(99, 102, 241, 0.1); border:1px solid #6366f1; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <h4 style="margin:0; color:#6366f1;">🛠️ Mejoras y Parches Pendientes</h4>
                        <p style="margin:0.2rem 0 0 0; font-size:0.85rem; opacity:0.8;">Se han detectado <?= $pendingPatches ?> paquete(s) de mejoras de base de datos o sistema pendientes.</p>
                    </div>
                    <a href="/tecnico/aplicarParches" class="btn btn-primary" style="background:#6366f1;">Aplicar Mejoras</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- GESTIÓN DE RESPALDOS PREVENTIVOS -->
        <div class="glass-card" style="margin-bottom: 2rem;">
            <h3>📂 Almacén de Respaldos (SQL & Archivos)</h3>
            <p style="font-size:0.9rem; opacity:0.7; margin-bottom:1rem;">Respaldos generados automáticamente antes de actualizaciones o de forma manual.</p>
            
            <div style="max-height: 250px; overflow-y: auto;">
                <table style="width:100%; border-collapse: collapse; font-size:0.85rem;">
                    <thead>
                        <tr style="text-align:left; border-bottom:1px solid rgba(255,255,255,0.1);">
                            <th style="padding:0.5rem;">Archivo</th>
                            <th style="padding:0.5rem;">Fecha</th>
                            <th style="padding:0.5rem;">Tamaño</th>
                            <th style="padding:0.5rem; text-align:right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($backups)): ?>
                            <tr><td colspan="4" style="padding:1rem; text-align:center; opacity:0.5;">No hay respaldos almacenados.</td></tr>
                        <?php else: ?>
                            <?php foreach($backups as $b): ?>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                    <td style="padding:0.5rem;"><?= $b['name'] ?></td>
                                    <td style="padding:0.5rem; opacity:0.7;"><?= $b['date'] ?></td>
                                    <td style="padding:0.5rem; opacity:0.7;"><?= $b['size'] ?></td>
                                    <td style="padding:0.5rem; text-align:right;">
                                        <a href="/tecnico/descargarBackup/<?= $b['name'] ?>" title="Descargar" style="text-decoration:none; margin-right:0.5rem;">📥</a>
                                        <a href="/tecnico/eliminarBackup/<?= $b['name'] ?>" title="Eliminar" style="text-decoration:none;" onclick="return confirm('¿Eliminar este respaldo permanentemente?')">🗑️</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr;">
            <!-- NAVEGAR COMO -->
            <div class="glass-card" style="grid-column: 1 / -1; margin-bottom: 2rem;">
                <h3>🕵️ Navegar como...</h3>
                <p style="font-size:0.9rem; opacity:0.7; margin-bottom:1rem;">Permite ver el sistema exactamente como lo vería otro usuario sin cerrar tu sesión actual.</p>
                <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem;">
                    <?php foreach($usuarios as $u): 
                        if($u['id'] == $user['id']) continue; // No suplantarse a sí mismo
                    ?>
                        <div style="background:rgba(255,255,255,0.05); padding:1rem; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <strong><?= htmlspecialchars($u['nombre'] ?? '') ?></strong><br>
                                <small style="opacity:0.6;"><?= htmlspecialchars($u['rol'] ?? 'Sin Rol') ?></small>
                            </div>
                            <a href="/tecnico/suplantar/<?= $u['id'] ?>" class="btn btn-primary btn-sm">Navegar</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- SNAPSHOTS / BACKUPS -->
            <div class="glass-card" style="grid-column: 1 / -1; margin-bottom: 2rem; border-top: 4px solid #28a745;">
                <h3>💾 Snapshots de Base de Datos</h3>
                <p style="font-size:0.9rem; opacity:0.7; margin-bottom:1.5rem;">Crea un respaldo de la base de datos (Estructura + Datos) para recuperación inmediata.</p>
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:2rem;">
                    <div style="text-align:center; padding:1rem; background:rgba(255,255,255,0.05); border-radius:8px;">
                        <h4>Exportar</h4>
                        <p style="font-size:0.8rem; opacity:0.7; margin-bottom:1rem;">Descarga un archivo .sql con todo el estado actual del sitio.</p>
                        <a href="/tecnico/exportarDB" class="btn btn-primary" style="background:#28a745; width:100%;">Descargar Snapshot</a>
                    </div>
                    
                    <div style="padding:1rem; background:rgba(255,255,255,0.05); border-radius:8px;">
                        <h4 style="text-align:center;">Importar / Restaurar</h4>
                        <p style="font-size:0.8rem; opacity:0.7; margin-bottom:1rem; text-align:center;">Sube un archivo .sql generado previamente para volver a ese estado.</p>
                        <form action="/tecnico/restaurarDB" method="POST" enctype="multipart/form-data" onsubmit="return confirm('¡ADVERTENCIA! Restaurar borrará los datos actuales y los reemplazará con los del archivo. ¿Deseas continuar?')">
                            <input type="file" name="backup_file" accept=".sql" required style="margin-bottom:0.5rem; font-size:0.8rem;">
                            <button type="submit" class="btn btn-primary" style="width:100%; background:var(--color-secondary);">Restaurar de Backup</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- GESTION DE AÑOS SCOUT -->
            <div class="glass-card" style="grid-column: 1 / -1; margin-bottom: 2rem; border-top: 4px solid #6366f1;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div style="flex:1;">
                        <h3>🗓️ Gestión de Años Scout</h3>
                        <p style="font-size:0.9rem; opacity:0.7; margin-bottom:1.5rem;">Crea nuevos periodos y gestiona su ciclo de vida (Borrador &rarr; Activo &rarr; Cerrado).</p>
                        
                        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap:1rem;">
                            <?php foreach($anios as $a): ?>
                                <div style="background:rgba(255,255,255,0.05); padding:1rem; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                                    <div>
                                        <strong>Año <?= $a['anio'] ?></strong><br>
                                        <?php 
                                            $color = '#6c757d'; // Cerrado
                                            if ($a['estado'] === 'abierto') $color = '#28a745';
                                            if ($a['estado'] === 'borrador') $color = '#ffc107';
                                        ?>
                                        <span class="badge" style="background:<?= $color ?>; color:<?= ($a['estado'] === 'borrador' ? '#000' : '#fff') ?>; font-size:0.6rem;">
                                            <?= strtoupper($a['estado']) ?>
                                        </span>
                                    </div>
                                    <?php if ($a['estado'] === 'borrador'): ?>
                                        <div style="display:flex; gap:0.5rem;">
                                            <a href="/tecnico/activarAnio/<?= $a['anio'] ?>" class="btn btn-primary btn-sm" style="background:#28a745;" onclick="return confirm('¿Deseas activar oficialmente el año <?= $a['anio'] ?>? Esto permitirá que todos los usuarios lo seleccionen.')">Activar</a>
                                            <a href="/tecnico/eliminarAnioBorrador/<?= $a['anio'] ?>" class="btn btn-sm" style="background:#dc3545; color:white; border:none; border-radius:4px; padding:0.25rem 0.5rem; text-decoration:none;" onclick="return confirm('¿ESTÁS SEGURO? Se eliminará el año <?= $a['anio'] ?> y todas las inscripciones migradas a este.')">Eliminar</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div style="width:300px; margin-left:2rem; padding:1.5rem; background:rgba(99, 102, 241, 0.1); border-radius:12px; border:1px solid rgba(99, 102, 241, 0.3);">
                        <h4 style="margin-top:0;">Nuevo Año Scout</h4>
                        <form action="/tecnico/crearAnio" method="POST">
                            <div style="margin-bottom:1rem;">
                                <label style="font-size:0.8rem;">Año (Ej: <?= date('Y')+1 ?>)</label>
                                <input type="number" name="anio" value="<?= date('Y')+1 ?>" required style="width:100%; padding:0.5rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                            </div>
                            <div style="margin-bottom:1rem;">
                                <label style="font-size:0.8rem; display:block; margin-bottom:0.3rem;">¿Migrar datos del año actual?</label>
                                <select name="migrar" style="width:100%; padding:0.5rem; background:var(--color-bg); color:white; border:1px solid var(--glass-border); border-radius:4px;">
                                    <option value="si">Sí (Apoderados y Beneficiarios)</option>
                                    <option value="no">No (Empezar de cero)</option>
                                </select>
                                <small style="font-size:0.7rem; opacity:0.6; display:block; margin-top:0.3rem;">Se crearán las inscripciones para el nuevo año automáticamente.</small>
                            </div>
                            <button type="submit" class="btn btn-primary" style="width:100%; background:var(--color-primary);">Crear como Borrador</button>
                        </form>
                    </div>
                </div>
            </div>

            <?php if(isset($_SESSION['sigrrus_env']) && $_SESSION['sigrrus_env'] === 'testing'): ?>
            <!-- CARGA MASIVA -->
            <div class="glass-card" style="text-align:center; padding:2rem;">
                <div style="font-size:3rem; margin-bottom:1rem;">🧪</div>
                <h3>Carga de Pruebas</h3>
                <p style="font-size:0.9rem; opacity:0.7; margin-bottom:1.5rem;">Genera automáticamente apoderados, beneficiarios y registros base para estresar el sistema.</p>
                <form action="/tecnico/cargaMasiva" method="POST" onsubmit="return confirm('¿Estás seguro de generar datos de prueba masivos?')">
                    <button type="submit" class="btn btn-primary" style="width:100%; background:var(--color-primary);">Generar Datos Dummy</button>
                </form>
            </div>

            <!-- LIMPIEZA -->
            <div class="glass-card" style="text-align:center; padding:2rem; border-color:#dc3545;">
                <div style="font-size:3rem; margin-bottom:1rem;">🧨</div>
                <h3 style="color:#dc3545;">Reset de Datos</h3>
                <p style="font-size:0.9rem; opacity:0.7; margin-bottom:1.5rem;">Borra todos los registros (asistencias, finanzas, beneficiarios, etc.) manteniendo solo la configuración base.</p>
                <form action="/tecnico/limpiarSistema" method="POST" onsubmit="return confirm('¡ADVERTENCIA! Esta acción borrará TODO el historial del grupo. ¿Deseas continuar?')">
                    <button type="submit" class="btn btn-primary" style="width:100%; background:#dc3545;">Borrar Todo el Historial</button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <div class="glass-card" style="margin-top:2rem; opacity:0.8;">
            <h4>Nota de Seguridad</h4>
            <p style="font-size:0.85rem;">Estas herramientas son exclusivas del perfil <strong>Superusuario</strong>. No están visibles para el Responsable de Grupo ni otros dirigentes. Utilízalas con precaución en entornos de producción.</p>
        </div>
    </main>
</body>
</html>
