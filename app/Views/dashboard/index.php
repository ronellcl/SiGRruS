<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .section-title {
            margin: 3rem 0 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--glass-border);
            color: var(--color-primary);
        }
        @media (prefers-color-scheme: dark) {
            .section-title { color: #fff; }
        }
        .unit-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .unit-card .btn-group {
            margin-top: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .unit-card .btn {
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            flex-grow: 1;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php
    $renderStaff = function($unitId) use ($unit_staff) {
        $staff = $unit_staff[$unitId] ?? [];
        $responsable = null;
        $asistentes = [];
        foreach($staff as $s) {
            if ($s['rol'] === 'Responsable de Unidad') $responsable = $s['nombre'];
            elseif ($s['rol'] === 'Asistente de Unidad') $asistentes[] = $s['nombre'];
        }
        
        $html = '<div style="margin-top:0.5rem; font-size:0.8rem; opacity:0.8; line-height:1.2;">';
        $html .= '👤 <strong>Responsable:</strong> ' . ($responsable ?: 'No asignado') . '<br>';
        $html .= '👥 <strong>Asistente(s):</strong> ' . (!empty($asistentes) ? implode(', ', $asistentes) : 'Ninguno');
        $html .= '</div>';
        return $html;
    };
    ?>
    <div style="position: sticky; top: 0; z-index: 2000; width: 100%;">
        <?php if (isset($_SESSION['impersonating'])): ?>
            <div style="background: linear-gradient(90deg, #6366f1, #a855f7); color:white; padding:0.4rem; text-align:center; font-size:0.85rem; font-weight:600;">
                🕵️ MODO SUPLANTACIÓN: Navegando como <strong><?= htmlspecialchars($user['nombre']) ?></strong>
                <a href="/tecnico/detenerSuplantacion" style="color:white; margin-left:1.5rem; text-decoration:underline;">[ Volver a mi cuenta ]</a>
                <?php if (!empty($global_config['debug_mode'])): ?>
                    <span style="margin-left:2rem; opacity:0.8; font-size:0.7rem; font-weight:normal;">DEBUG: RolActivo: [<?= $user['rol'] ?>] | UnidadID: [<?= $user['unidad_id'] ?>]</span>
                <?php endif; ?>
            </div>
        <?php elseif (!empty($global_config['debug_mode'])): ?>
            <div style="background:#333; color:#ccc; padding:0.2rem; text-align:center; font-size:0.7rem;">
                DEBUG: RolActivo: [<?= $user['rol'] ?>] | UnidadID: [<?= $user['unidad_id'] ?>]
            </div>
        <?php endif; ?>
        <?php include APP_PATH . '/Views/partials/navbar.php'; ?>
    </div>

    <main class="container">
        <header style="margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:1rem;">
                <h1 style="margin:0;">Panel de Control - Año <?= $anio_scout ?></h1>
                <?php if ($year_closed): ?>
                    <span class="badge" style="background:#dc3545; color:white; font-size:0.9rem; padding:0.4rem 0.8rem; border-radius:20px;">CERRADO</span>
                <?php else: ?>
                    <span class="badge" style="background:#28a745; color:white; font-size:0.9rem; padding:0.4rem 0.8rem; border-radius:20px;">ABIERTO</span>
                <?php endif; ?>
            </div>

            <div style="display:flex; gap:1rem;">
                <!-- Botón de Notificaciones -->
                <div style="position:relative;">
                    <button onclick="toggleNotif()" class="btn btn-primary" style="background:var(--color-secondary); border-radius:20px;">
                        🔔 <?= $notif_unread > 0 ? "<span style='background:red; border-radius:50%; padding:2px 6px; font-size:0.7rem;'>$notif_unread</span>" : "" ?> Notificaciones
                    </button>
                    <div id="notif-panel" class="glass-card" style="display:none; position:absolute; right:0; top:3.5rem; width:350px; z-index:100; max-height:400px; overflow-y:auto; border:1px solid var(--color-primary);">
                        <h4 style="margin-bottom:1rem;">Notificaciones</h4>
                        <?php if (empty($notificaciones)): ?>
                            <p style="font-size:0.9rem; opacity:0.7;">No tienes notificaciones.</p>
                        <?php else: ?>
                            <?php foreach($notificaciones as $n): ?>
                                <div style="padding:0.8rem; border-bottom:1px solid rgba(255,255,255,0.1); font-size:0.85rem; <?= $n['leida'] ? 'opacity:0.6' : 'border-left:3px solid var(--color-secondary)' ?>">
                                    <p><?= htmlspecialchars($n['mensaje']) ?></p>
                                    <small style="opacity:0.7;"><?= $n['fecha'] ?></small>
                                    <?php if (!$n['leida']): ?>
                                        <br><a href="/dashboard/leerNotificacion/<?= $n['id'] ?>" style="color:var(--color-secondary); text-decoration:none; font-weight:bold;">Marcar como leída</a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <script>
            function toggleNotif() {
                const panel = document.getElementById('notif-panel');
                panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
            }
        </script>

        <?php if ($user['rol'] === 'Apoderado'): ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1rem;">
            <h2 class="section-title" style="margin:0; border:none;">Mis Beneficiarios</h2>
            <a href="/apoderados/editar/<?= $user['apoderado_id'] ?>" class="btn btn-primary" style="background:var(--color-secondary);"><i style="font-style:normal;">📝</i> Mis Datos de Contacto</a>
        </div>
        <div class="dashboard-grid">
            <?php if (empty($hijos)): ?>
                <div class="glass-card" style="text-align: center; padding: 3rem; grid-column: 1 / -1;">
                    <h3 style="margin-bottom: 1rem; color: var(--color-primary); font-size: 1.5rem;">No tienes beneficiarios asociados</h3>
                    <p style="margin-bottom: 2rem; opacity: 0.8;">Para poder ver hojas de ruta, finanzas o fichas médicas, necesitas asociar tu cuenta a tus beneficiarios (hijos/as).</p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="/apoderados/vincular/<?= $user['apoderado_id'] ?>" class="btn btn-primary" style="background: var(--color-primary);">Asociar Beneficiario Existente</a>
                        <a href="/apoderados/vincular/<?= $user['apoderado_id'] ?>?tab=nuevo" class="btn btn-primary" style="background: var(--color-secondary);">Crear Nuevo Beneficiario</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($hijos as $h): ?>
                <div class="glass-card" style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <h3 style="color:var(--color-primary);"><?= htmlspecialchars($h['nombre_completo']) ?></h3>
                        <p style="font-size:0.9rem; opacity:0.8;">Unidad: <?= htmlspecialchars($h['unidad_nombre']) ?></p>
                    </div>
                    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                        <a href="/dashboard/finanzas/<?= $h['id'] ?>" class="btn btn-primary" style="background:#dc3545; font-size:0.8rem;">Finanzas</a>
                        <a href="/fichas/editar/<?= $h['id'] ?>" class="btn btn-primary" style="background:var(--color-secondary); font-size:0.8rem;">Ficha Médica</a>
                        <a href="/dashboard/hojasRuta/<?= $h['id'] ?>" class="btn btn-primary" style="background:var(--color-lobatos); color:#000; font-size:0.8rem;">🌀 Ciclo / Hojas Ruta</a>
                        <a href="/dashboard/asistencias/<?= $h['id'] ?>" class="btn btn-primary" style="background:var(--color-primary); font-size:0.8rem;">Asistencias</a>
                        <a href="/dashboard/campamentos/<?= $h['id'] ?>" class="btn btn-primary" style="background:#28a745; font-size:0.8rem;">Campamentos</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($user['rol'] === 'Superusuario'): ?>
        <!-- SECCIÓN: ADMINISTRACIÓN CENTRAL -->
        <h2 class="section-title">Panel de Administración Central</h2>
        <div class="dashboard-grid" style="margin-bottom: 2rem;">
            
            <!-- CONFIGURACIÓN INSTITUCIONAL -->
            <div class="glass-card" style="border-top: 4px solid var(--color-primary); display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <h3>⚙️ Configuración del Grupo</h3>
                    <p style="margin: 1rem 0; font-size:0.9rem; opacity:0.8;">Identidad, logos, institución patrocinante y valores de inscripción.</p>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:0.5rem;">
                    <a href="/grupo/config" class="btn btn-primary" style="font-size:0.8rem; background:var(--color-secondary)">Identidad</a>
                    <button onclick="document.getElementById('modal-inscripcion').style.display='block'" class="btn btn-primary" style="font-size:0.8rem;">Inscripción</button>
                </div>
            </div>

            <!-- GESTIÓN DEL AÑO -->
            <div class="glass-card" style="border-top: 4px solid #dc3545; display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <h3>📅 Ciclo Anual</h3>
                    <p style="margin: 1rem 0; font-size:0.9rem; opacity:0.8;">Apertura y cierre de años scout.</p>
                </div>
                <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    <?php if (!$year_closed): ?>
                        <form action="/dashboard/cerrarAnio" method="POST" onsubmit="return confirm('¿Cerrar año <?= $anio_scout ?>?')">
                            <input type="hidden" name="anio" value="<?= $anio_scout ?>">
                            <button type="submit" class="btn btn-primary" style="background:#dc3545; font-size:0.75rem;">Cerrar <?= $anio_scout ?></button>
                        </form>
                    <?php else: ?>
                        <form action="/dashboard/abrirAnio" method="POST">
                            <input type="hidden" name="anio" value="<?= $anio_scout ?>">
                            <button type="submit" class="btn btn-primary" style="background:#28a745; font-size:0.75rem;">Abrir <?= $anio_scout ?></button>
                        </form>
                    <?php endif; ?>
                    <button onclick="document.getElementById('modal-new-year').style.display='block'" class="btn btn-primary" style="background:var(--color-primary); font-size:0.75rem;">Nuevo Año</button>
                </div>
            </div>

            <!-- HERRAMIENTAS TÉCNICAS -->
            <div class="glass-card" style="border-top: 4px solid #6f42c1; display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <h3 style="color:#6f42c1;">🛠️ Herramientas Técnicas</h3>
                    <p style="margin: 1rem 0; font-size:0.9rem; opacity:0.8;">Pruebas, carga de datos y suplantación.</p>
                </div>
                <a href="/tecnico" class="btn btn-primary" style="background:#6f42c1; font-size:0.8rem;">Ir al Panel Técnico</a>
            </div>
        </div>

        <!-- Modales Administrativos -->
        <div id="modal-inscripcion" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
            <div class="glass-card" style="max-width:400px; margin: 10rem auto; background:var(--color-bg);">
                <h3>Valor Inscripción Anual</h3>
                <form action="/dashboard/setValorInscripcion" method="POST">
                    <input type="hidden" name="anio" value="<?= $anio_scout ?>">
                    <div style="margin: 1.5rem 0;">
                        <label>Monto global para el año <?= $anio_scout ?></label>
                        <input type="number" name="monto" value="<?= $valor_inscripcion ?>" required style="width:100%; padding:0.75rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border); border-radius:8px;">
                    </div>
                    <div style="display:flex; gap:0.5rem;">
                        <button type="submit" class="btn btn-primary" style="flex:1;">Actualizar</button>
                        <button type="button" onclick="document.getElementById('modal-inscripcion').style.display='none'" class="btn btn-primary" style="flex:1; background:var(--color-secondary);">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Modal Nuevo Año -->
        <div id="modal-new-year" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
            <div class="glass-card" style="max-width:400px; margin: 10rem auto; position:relative; background:var(--color-bg);">
                <h3>Crear Nuevo Año Scout</h3>
                <form action="/dashboard/crearAnio" method="POST" style="margin-top:1rem;">
                    <input type="number" name="anio_nuevo" value="<?= date('Y') + 1 ?>" required style="width:100%; padding:0.75rem; margin-bottom:1rem; background:rgba(255,255,255,0.1); color:white; border:1px solid var(--glass-border);">
                    <button type="submit" class="btn btn-primary" style="width:100%;">Inicializar Año</button>
                    <button type="button" onclick="document.getElementById('modal-new-year').style.display='none'" class="btn btn-primary" style="width:100%; margin-top:0.5rem; background:var(--color-secondary);">Cancelar</button>
                </form>
            </div>
        </div>

        <?php if ($user['rol'] === 'Superusuario' || $user['rol'] === 'Responsable de Grupo'): ?>
        <!-- SECCIÓN 1: GESTIÓN DE GRUPO -->
        <h2 class="section-title">🏛️ Gestión de Grupo</h2>
        <p style="opacity: 0.8; margin-bottom: 2rem;">Administración de recursos, finanzas y actividades a nivel de Grupo Scout.</p>
        
        <div class="dashboard-grid">
            <div class="glass-card" style="border-top: 4px solid var(--color-primary);">
                <h3>⚖️ Consejo de Grupo</h3>
                <p style="margin: 1rem 0;">Reuniones, Actas de Acuerdos y Asistencia de Dirigentes.</p>
                <a href="/grupo/consejo" class="btn btn-primary">Gestionar Consejo</a>
            </div>
            <div class="glass-card" style="border-top: 4px solid var(--color-primary);">
                <h3>💰 Finanzas Generales</h3>
                <p style="margin: 1rem 0;">Control de tesorería y validación de traspasos de unidades.</p>
                <a href="/grupo/finanzas" class="btn btn-primary">Entrar a Tesorería</a>
            </div>
            <div class="glass-card" style="border-top: 4px solid var(--color-primary);">
                <h3>📦 Inventario de Grupo</h3>
                <p style="margin: 1rem 0;">Gestión de activos globales y visión de inventarios de unidades.</p>
                <a href="/grupo/inventario" class="btn btn-primary">Ver Inventario</a>
            </div>
            <div class="glass-card" style="border-top: 4px solid var(--color-primary);">
                <h3>⛺ Campamentos y Eventos</h3>
                <p style="margin: 1rem 0;">Planificación de campamentos grupales y distritales.</p>
                <a href="/grupo/campamentos" class="btn btn-primary">Ver Campamentos</a>
            </div>
            <div class="glass-card" style="border-top: 4px solid var(--color-primary);">
                <h3>🏥 Fichas de Salud Globales</h3>
                <p style="margin: 1rem 0;">Acceso a información médica de todos los miembros.</p>
                <a href="/grupo/fichas" class="btn btn-primary">Ver Fichas</a>
            </div>
            <div class="glass-card" style="border-top: 4px solid var(--color-primary);">
                <h3>👥 Dirigentes y Apoderados</h3>
                <p style="margin: 1rem 0;">Administración de usuarios y roles del grupo.</p>
                <div style="display:flex; gap:0.5rem;">
                    <a href="/grupo/dirigentes" class="btn btn-primary btn-sm">Dirigentes</a>
                    <a href="/grupo/apoderados" class="btn btn-primary btn-sm" style="background:var(--color-secondary)">Apoderados</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- SECCIÓN 2: UNIDADES -->
        <h2 class="section-title">Unidades</h2>
        <p style="opacity: 0.8; margin-bottom: 2rem;">Ingreso a los módulos y submódulos independientes por rama.</p>
        
        <?php
        $canViewUnit = function($unitId) use ($user) {
            if ($user['rol'] === 'Apoderado') return false;
            $rolesGlobales = ['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo'];
            if (in_array($user['rol'], $rolesGlobales)) return true;
            return $user['unidad_id'] == $unitId;
        };
        ?>

        <div class="dashboard-grid">
            <!-- LOBATOS -->
            <?php if ($canViewUnit(1)): ?>
            <div class="glass-card unit-card theme-lobatos">
                <div>
                    <h2>Manada de Lobatos</h2>
                    <?= $renderStaff(1) ?>
                </div>
                <div class="btn-group">
                    <a href="/unidades/1/beneficiarios" class="btn btn-primary" style="background:var(--color-lobatos); color:#000">👦 Beneficiarios</a>
                    <a href="/unidades/1/apoderados" class="btn btn-primary" style="background:var(--color-lobatos); color:#000">👪 Apoderados</a>
                    <a href="/unidades/1/ciclo" class="btn btn-primary" style="background:var(--color-lobatos); color:#000">🌀 Ciclos</a>
                    <a href="/unidades/1/hoja-ruta" class="btn btn-primary" style="background:var(--color-lobatos); color:#000">📄 Hojas de Ruta</a>
                    <a href="/unidades/1/finanzas" class="btn btn-primary" style="background:var(--color-lobatos); color:#000">💵 Finanzas</a>
                    <a href="/unidades/1/inventario" class="btn btn-primary" style="background:var(--color-lobatos); color:#000">🎒 Inventario</a>
                    <a href="/unidades/1/asistencias" class="btn btn-primary" style="background:var(--color-lobatos); color:#000">🗓️ Asistencias</a>
                    <a href="/unidades/1/fichas" class="btn btn-primary" style="background:var(--color-lobatos); color:#000">🩺 Salud</a>
                    <a href="/unidades/1/campamentos" class="btn btn-primary" style="background:var(--color-lobatos); color:#000">🏕️ Campamento</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- GOLONDRINAS -->
            <?php if ($canViewUnit(2)): ?>
            <div class="glass-card unit-card theme-golondrinas">
                <div>
                    <h2>Bandada de Golondrinas</h2>
                    <?= $renderStaff(2) ?>
                </div>
                <div class="btn-group">
                    <a href="/unidades/2/beneficiarios" class="btn btn-primary" style="background:var(--color-golondrinas)">👦 Beneficiarios</a>
                    <a href="/unidades/2/apoderados" class="btn btn-primary" style="background:var(--color-golondrinas)">👪 Apoderados</a>
                    <a href="/unidades/2/ciclo" class="btn btn-primary" style="background:var(--color-golondrinas)">🌀 Ciclos</a>
                    <a href="/unidades/2/hoja-ruta" class="btn btn-primary" style="background:var(--color-golondrinas)">📄 Hojas de Ruta</a>
                    <a href="/unidades/2/finanzas" class="btn btn-primary" style="background:var(--color-golondrinas)">💵 Finanzas</a>
                    <a href="/unidades/2/inventario" class="btn btn-primary" style="background:var(--color-golondrinas)">🎒 Inventario</a>
                    <a href="/unidades/2/asistencias" class="btn btn-primary" style="background:var(--color-golondrinas)">🗓️ Asistencias</a>
                    <a href="/unidades/2/fichas" class="btn btn-primary" style="background:var(--color-golondrinas)">🩺 Salud</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- SCOUTS -->
            <?php if ($canViewUnit(3)): ?>
            <div class="glass-card unit-card theme-tropa">
                <div>
                    <h2>Tropa Scout</h2>
                    <?= $renderStaff(3) ?>
                </div>
                <div class="btn-group">
                    <a href="/unidades/3/beneficiarios" class="btn btn-primary" style="background:var(--color-tropa)">👦 Beneficiarios</a>
                    <a href="/unidades/3/apoderados" class="btn btn-primary" style="background:var(--color-tropa)">👪 Apoderados</a>
                    <a href="/unidades/3/ciclo" class="btn btn-primary" style="background:var(--color-tropa)">🌀 Ciclos</a>
                    <a href="/unidades/3/hoja-ruta" class="btn btn-primary" style="background:var(--color-tropa)">📄 Hojas de Ruta</a>
                    <a href="/unidades/3/finanzas" class="btn btn-primary" style="background:var(--color-tropa)">💵 Finanzas</a>
                    <a href="/unidades/3/inventario" class="btn btn-primary" style="background:var(--color-tropa)">🎒 Inventario</a>
                    <a href="/unidades/3/asistencias" class="btn btn-primary" style="background:var(--color-tropa)">🗓️ Asistencias</a>
                    <a href="/unidades/3/fichas" class="btn btn-primary" style="background:var(--color-tropa)">🩺 Salud</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- GUIAS -->
            <?php if ($canViewUnit(4)): ?>
            <div class="glass-card unit-card theme-guias">
                <div>
                    <h2>Compañía de Guías</h2>
                    <?= $renderStaff(4) ?>
                </div>
                <div class="btn-group">
                    <a href="/unidades/4/beneficiarios" class="btn btn-primary" style="background:var(--color-guias)">👦 Beneficiarios</a>
                    <a href="/unidades/4/apoderados" class="btn btn-primary" style="background:var(--color-guias)">👪 Apoderados</a>
                    <a href="/unidades/4/ciclo" class="btn btn-primary" style="background:var(--color-guias)">🌀 Ciclos</a>
                    <a href="/unidades/4/hoja-ruta" class="btn btn-primary" style="background:var(--color-guias)">📄 Hojas de Ruta</a>
                    <a href="/unidades/4/finanzas" class="btn btn-primary" style="background:var(--color-guias)">💵 Finanzas</a>
                    <a href="/unidades/4/inventario" class="btn btn-primary" style="background:var(--color-guias)">🎒 Inventario</a>
                    <a href="/unidades/4/asistencias" class="btn btn-primary" style="background:var(--color-guias)">🗓️ Asistencias</a>
                    <a href="/unidades/4/fichas" class="btn btn-primary" style="background:var(--color-guias)">🩺 Salud</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- PIONEROS -->
            <?php if ($canViewUnit(5)): ?>
            <div class="glass-card unit-card theme-pioneros">
                <div>
                    <h2>Avanzada de Pioneros</h2>
                    <?= $renderStaff(5) ?>
                </div>
                <div class="btn-group">
                    <a href="/unidades/5/beneficiarios" class="btn btn-primary" style="background:var(--color-pioneros)">👦 Beneficiarios</a>
                    <a href="/unidades/5/apoderados" class="btn btn-primary" style="background:var(--color-pioneros)">👪 Apoderados</a>
                    <a href="/unidades/5/ciclo" class="btn btn-primary" style="background:var(--color-pioneros)">🌀 Ciclos</a>
                    <a href="/unidades/5/hoja-ruta" class="btn btn-primary" style="background:var(--color-pioneros)">📄 Hojas de Ruta</a>
                    <a href="/unidades/5/finanzas" class="btn btn-primary" style="background:var(--color-pioneros)">💵 Finanzas</a>
                    <a href="/unidades/5/inventario" class="btn btn-primary" style="background:var(--color-pioneros)">🎒 Inventario</a>
                    <a href="/unidades/5/asistencias" class="btn btn-primary" style="background:var(--color-pioneros)">🗓️ Asistencias</a>
                    <a href="/unidades/5/fichas" class="btn btn-primary" style="background:var(--color-pioneros)">🩺 Salud</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- CAMINANTES -->
            <?php if ($canViewUnit(6)): ?>
            <div class="glass-card unit-card theme-caminantes">
                <div>
                    <h2>Clan de Caminantes</h2>
                    <?= $renderStaff(6) ?>
                </div>
                <div class="btn-group">
                    <a href="/unidades/6/beneficiarios" class="btn btn-primary" style="background:var(--color-caminantes)">👦 Beneficiarios</a>
                    <a href="/unidades/6/apoderados" class="btn btn-primary" style="background:var(--color-caminantes)">👪 Apoderados</a>
                    <a href="/unidades/6/ciclo" class="btn btn-primary" style="background:var(--color-caminantes)">🌀 Ciclos</a>
                    <a href="/unidades/6/hoja-ruta" class="btn btn-primary" style="background:var(--color-caminantes)">📄 Hojas de Ruta</a>
                    <a href="/unidades/6/finanzas" class="btn btn-primary" style="background:var(--color-caminantes)">💵 Finanzas</a>
                    <a href="/unidades/6/inventario" class="btn btn-primary" style="background:var(--color-caminantes)">🎒 Inventario</a>
                    <a href="/unidades/6/asistencias" class="btn btn-primary" style="background:var(--color-caminantes)">🗓️ Asistencias</a>
                    <a href="/unidades/6/fichas" class="btn btn-primary" style="background:var(--color-caminantes)">🩺 Salud</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
