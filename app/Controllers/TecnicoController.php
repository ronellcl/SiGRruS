<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Apoderado;
use App\Models\Beneficiario;
use App\Models\Usuario;
use App\Models\Unidad;
use App\Models\Backup;
use App\Models\UpdateManager;

class TecnicoController extends Controller {
    
    public function index() {
        Auth::requireRole(['Superusuario']);
        $userModel = new Usuario();
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $usuarios = $userModel->findAllUsersWithRoles($anio);

        $db = \App\DAL\Database::getInstance()->getConnection();
        $debugMode = $db->query("SELECT debug_mode FROM config_grupo LIMIT 1")->fetchColumn();

        $yearManager = new \App\Models\YearManager();
        $anios = $yearManager->getAllStates();

        $updateModel = new UpdateManager();
        $updateInfo = $updateModel->checkUpdates();

        $migrationManager = new \App\Models\MigrationManager();
        $pendingPatches = $migrationManager->getPendingPatches();

        // Obtener lista de backups
        $backupDir = dirname(__DIR__, 2) . '/public/backups';
        $backups = [];
        if (is_dir($backupDir)) {
            $files = array_diff(scandir($backupDir), array('.', '..', 'tmp'));
            foreach ($files as $f) {
                if (!is_dir($backupDir . '/' . $f)) {
                    $backups[] = [
                        'name' => $f,
                        'size' => round(filesize($backupDir . '/' . $f) / 1024 / 1024, 2) . ' MB',
                        'date' => date('d/m/Y H:i', filemtime($backupDir . '/' . $f))
                    ];
                }
            }
            usort($backups, function($a, $b) { return strcmp($b['name'], $a['name']); });
        }

        $this->view('tecnico/index', [
            'title' => 'Gestión Técnica del Sistema',
            'user' => Auth::user(),
            'usuarios' => $usuarios,
            'debug_mode' => $debugMode,
            'anios' => $anios,
            'updateInfo' => $updateInfo,
            'backups' => $backups,
            'current_version' => \SIGRRUS_VERSION,
            'pendingPatches' => count($pendingPatches)
        ]);
    }

    public function aplicarParches() {
        Auth::requireRole(['Superusuario']);
        $migrationManager = new \App\Models\MigrationManager();
        $results = $migrationManager->applyPatches();
        
        if (!empty($results['errors'])) {
            $_SESSION['error'] = implode("<br>", $results['errors']);
        } else {
            $_SESSION['success'] = "Se han aplicado {$results['success']} paquetes de mejoras correctamente.";
        }
        
        $this->redirect('/tecnico');
    }

    public function crearAnio() {
        Auth::requireRole(['Superusuario']);
        $nuevoAnio = (int)$_POST['anio'];
        $migrar = $_POST['migrar'] === 'si';
        
        $yearManager = new \App\Models\YearManager();
        if ($yearManager->getByAnio($nuevoAnio)) {
            $_SESSION['error'] = "El año $nuevoAnio ya existe.";
            $this->redirect('/tecnico');
        }

        $yearManager->create($nuevoAnio, 'borrador');
        
        $msg = "El año $nuevoAnio ha sido creado en estado BORRADOR.";
        
        if ($migrar) {
            $anioActual = $_SESSION['anio_scout'] ?? date('Y');
            $this->migrateData($anioActual, $nuevoAnio);
            $msg .= " Se han migrado los datos de beneficiarios desde el año $anioActual.";
        }

        $_SESSION['success'] = $msg;
        $this->redirect('/tecnico');
    }

    public function activarAnio($anio) {
        Auth::requireRole(['Superusuario']);
        $yearManager = new \App\Models\YearManager();
        $yearManager->activate($anio);
        $_SESSION['success'] = "El año $anio ahora está ACTIVO oficialmente.";
        $this->redirect('/tecnico');
    }

    public function eliminarAnioBorrador($anio) {
        Auth::requireRole(['Superusuario']);
        $yearManager = new \App\Models\YearManager();
        
        if ($yearManager->isDraft($anio)) {
            try {
                $yearManager->deleteDraft($anio);
                $_SESSION['success'] = "El año borrador $anio y sus datos asociados han sido eliminados correctamente.";
            } catch (\Exception $e) {
                $_SESSION['error'] = "Ocurrió un error al intentar eliminar el año: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "No se puede eliminar el año $anio porque ya no está en estado Borrador.";
        }
        
        $this->redirect('/tecnico');
    }

    private function migrateData($source, $target) {
        $db = \App\DAL\Database::getInstance()->getConnection();
        
        // Migrar inscripciones de beneficiarios
        $stmt = $db->prepare("
            INSERT INTO beneficiario_inscripcion (beneficiario_id, unidad_id, subgrupo, fecha_ingreso)
            SELECT beneficiario_id, unidad_id, subgrupo, ?
            FROM beneficiario_inscripcion
            WHERE fecha_ingreso <= ? AND (fecha_salida IS NULL OR fecha_salida >= ?)
        ");
        
        $targetDate = $target . "-01-01";
        $sourceEnd = $source . "-12-31";
        $sourceStart = $source . "-01-01";
        
        $stmt->execute([$targetDate, $sourceEnd, $sourceStart]);

        // También migramos Dirigentes (esencial para que el año funcione)
        $stmtDir = $db->prepare("
            INSERT INTO dirigente_inscripcion (usuario_id, unidad_id, rol, fecha_inicio)
            SELECT usuario_id, unidad_id, rol, ?
            FROM dirigente_inscripcion
            WHERE fecha_inicio <= ? AND (fecha_fin IS NULL OR fecha_fin >= ?)
        ");
        $stmtDir->execute([$targetDate, $sourceEnd, $sourceStart]);
    }

    public function toggleDebug() {
        Auth::requireRole(['Superusuario']);
        $db = \App\DAL\Database::getInstance()->getConnection();
        $current = $db->query("SELECT debug_mode FROM config_grupo LIMIT 1")->fetchColumn();
        $new = $current ? 0 : 1;
        
        $db->prepare("UPDATE config_grupo SET debug_mode = ?")->execute([$new]);
        $_SESSION['success'] = "Debug " . ($new ? "ACTIVADO" : "DESACTIVADO");
        $this->redirect('/tecnico');
    }

    public function cargaMasiva() {
        Auth::requireRole(['Superusuario']);
        
        if (!isset($_SESSION['sigrrus_env']) || $_SESSION['sigrrus_env'] !== 'testing') {
            $_SESSION['error'] = 'Esta acción solo está permitida en el entorno de Entrenamiento.';
            $this->redirect('/tecnico');
            return;
        }

        $db = \App\DAL\Database::getInstance()->getConnection();
        
        try {
            $db->beginTransaction();

            // 1. Asegurar Unidades
            $unidadModel = new Unidad();
            $unidades = $unidadModel->findAll();
            if (empty($unidades)) {
                $unidades_data = [
                    ['Manada de Lobatos', 'Lobatos'], ['Bandada de Golondrinas', 'Golondrinas'],
                    ['Tropa Scout', 'Scouts'], ['Compañía de Guías', 'Guías'],
                    ['Avanzada de Pioneros', 'Pioneros'], ['Clan de Caminantes', 'Caminantes']
                ];
                $stmt = $db->prepare("INSERT INTO unidades (nombre, rama) VALUES (?, ?)");
                foreach($unidades_data as $u) $stmt->execute($u);
                $unidades = $unidadModel->findAll();
            }

            // 2. Crear Apoderados, Hijos y Fichas Médicas
            $apellidos = ['Perez', 'Garcia', 'Soto', 'Morales', 'Ruiz', 'Paz', 'Tapia', 'Munoz'];
            $benefModel = new Beneficiario();
            $apoModel = new Apoderado();
            $fichaModel = new \App\Models\FichaMedica();
            $anio = $_SESSION['anio_scout'] ?? date('Y');

            $tiposSangre = ['A+', 'B+', 'O+', 'AB-', 'O-', 'A-'];

            foreach ($apellidos as $i => $apellido) {
                $rut = (10000000 + $i) . "-" . ($i % 9);
                $apoderado_id = $apoModel->create([
                    'nombre_completo' => "Apoderado " . $apellido,
                    'rut' => $rut,
                    'email' => "apo" . $i . "@example.com",
                    'telefono' => "+5691234567" . $i,
                    'direccion' => "Pasaje " . $apellido . " " . (100 + $i)
                ]);

                // 1 o 2 hijos por apoderado
                $cantHijos = ($i % 2) + 1;
                for ($h = 1; $h <= $cantHijos; $h++) {
                    $u_index = array_rand($unidades);
                    $benef_id = $benefModel->create([
                        'nombre_completo' => "Beneficiario " . $apellido . " " . ($h == 1 ? 'A' : 'B'),
                        'rut' => (20000000 + $i * 10 + $h) . "-K",
                        'fecha_nacimiento' => (2010 + $i) . "-05-15",
                        'apoderado_id' => $apoderado_id,
                        'unidad_id' => $unidades[$u_index]['id'],
                        'subgrupo' => 'Patrulla ' . ($h == 1 ? 'Halcones' : 'Zorros'),
                        'anio' => $anio
                    ]);

                    // Crear Ficha Médica Dummy (método save() del modelo)
                    $fichaModel->save([
                        'beneficiario_id' => $benef_id,
                        'tipo_sangre' => $tiposSangre[array_rand($tiposSangre)],
                        'alergias' => ($i % 3 == 0) ? 'Polen, Penicilina' : 'Ninguna',
                        'enfermedades_cronicas' => ($i % 5 == 0) ? 'Asma leve' : 'Ninguna',
                        'medicamentos' => ($i % 5 == 0) ? 'Salbutamol inhalador' : 'Ninguno',
                        'prevision_salud' => ($i % 2 == 0) ? 'Fonasa B' : 'Isapre Colmena',
                        'restricciones_alimenticias' => ($i % 4 == 0) ? 'Intolerancia a la lactosa' : 'Ninguna',
                        'vacunas_al_dia' => 1,
                        'observaciones_medicas' => ''
                    ]);
                }
            }

            // 3. Crear Ciclos de Programa, Hojas de Ruta y Campamentos
            $finanzasModel = new \App\Models\Finanzas();

            foreach ($unidades as $u) {
                // Actividades de ciclo de programa
                $actividadesNombres = [
                    "Reunión de Patrulla", "Juego Cooperativo", "Taller de Nudos"
                ];
                foreach ($actividadesNombres as $actNom) {
                    $stmtCiclo = $db->prepare("INSERT INTO ciclo_programa (unidad_id, nombre_actividad, fecha, lugar) VALUES (?, ?, ?, ?)");
                    $stmtCiclo->execute([$u['id'], $actNom . " - " . $u['nombre'], $anio . "-06-" . (10 + array_search($actNom, $actividadesNombres)), "Local de Grupo"]);
                }
                $ciclo_id = $db->lastInsertId();

                // Hoja de ruta vinculada (usando columnas reales de la tabla)
                $stmtHR = $db->prepare("INSERT INTO hojas_ruta (actividad_id, unidad_id, anio, motivacion, fases, materiales) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtHR->execute([
                    $ciclo_id,
                    $u['id'],
                    $anio,
                    "Fomentar el trabajo en equipo y la técnica scout.",
                    "Fase 1: Bienvenida y formación.\nFase 2: Actividad central.\nFase 3: Evaluación y cierre.",
                    "Cuerdas, estacas, vendas, silbato."
                ]);

                // Un campamento por unidad
                $stmtCamp = $db->prepare("INSERT INTO campamentos (nombre, tipo, unidad_id, anio, fecha_inicio, fecha_fin, lugar, costo_cuota, objetivos, programa_resumen, estado) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                $stmtCamp->execute([
                    "Campamento de Invierno " . $u['nombre'],
                    'Unidad',
                    $u['id'],
                    $anio,
                    $anio . "-07-15",
                    $anio . "-07-18",
                    "Cajón del Maipo",
                    25000,
                    "Convivencia y progresión personal.",
                    "Día 1: Instalación. Día 2: Excursión. Día 3: Fogata. Día 4: Desarme.",
                    'Planificación'
                ]);

                // Movimientos financieros
                $finanzasModel->registrarMovimiento([
                    'unidad_id' => $u['id'],
                    'anio' => $anio,
                    'fecha' => $anio . '-03-15',
                    'tipo' => 'Ingreso',
                    'monto' => 50000,
                    'descripcion' => 'Aporte inicial para materiales'
                ]);
                $finanzasModel->registrarMovimiento([
                    'unidad_id' => $u['id'],
                    'anio' => $anio,
                    'fecha' => $anio . '-04-10',
                    'tipo' => 'Egreso',
                    'monto' => 15000,
                    'descripcion' => 'Compra de cuerdas y estacas'
                ]);
                $finanzasModel->registrarMovimiento([
                    'unidad_id' => $u['id'],
                    'anio' => $anio,
                    'fecha' => $anio . '-05-01',
                    'tipo' => 'Ingreso',
                    'monto' => 30000,
                    'descripcion' => 'Cuotas recaudadas mes de mayo'
                ]);

                // Actas de Reunión de Unidad
                $reunionModel = new \App\Models\Reunion();
                $admin = $db->query("SELECT u.id FROM usuarios u JOIN dirigente_inscripcion di ON u.id = di.usuario_id WHERE di.rol = 'Superusuario' LIMIT 1")->fetch();
                $adminId = $admin ? $admin['id'] : 1;
                $organoModel = new \App\Models\Organo();
                
                $reunionId = $reunionModel->create([
                    'tipo_organo' => $u['nombre'],
                    'fecha' => $anio . '-04-05 18:00:00',
                    'tema' => 'Evaluación de Ciclo ' . $u['nombre'],
                    'acta' => "Evaluamos el progreso de los beneficiarios y la participación.\nTodo en orden, se planifica el próximo campamento.",
                    'creado_por' => $adminId
                ]);
                $miembrosUnidad = $organoModel->getMiembros($u['nombre']);
                if (!empty($miembrosUnidad)) {
                    $asistenciaData = [];
                    foreach ($miembrosUnidad as $m) {
                        $tipo = $m['usuario_id'] ? 'Usuario' : 'Apoderado';
                        $id_entidad = $m['usuario_id'] ? $m['usuario_id'] : $m['apoderado_id'];
                        $asistenciaData[] = [
                            'tipo_entidad' => $tipo,
                            'entidad_id' => $id_entidad,
                            'asiste' => rand(0, 10) > 2 ? 1 : 0
                        ];
                    }
                    $reunionModel->registrarAsistencia($reunionId, $asistenciaData);
                }
            }

            // Acta de Consejo de Grupo
            if (!isset($reunionModel)) {
                $reunionModel = new \App\Models\Reunion();
                $admin = $db->query("SELECT u.id FROM usuarios u JOIN dirigente_inscripcion di ON u.id = di.usuario_id WHERE di.rol = 'Superusuario' LIMIT 1")->fetch();
                $adminId = $admin ? $admin['id'] : 1;
                $organoModel = new \App\Models\Organo();
            }
            $reunionIdConsejo = $reunionModel->create([
                'tipo_organo' => 'Consejo',
                'fecha' => $anio . '-03-10 19:30:00',
                'tema' => 'Planificación Anual y Presupuesto',
                'acta' => "Se discutieron los lineamientos del año.\n1. Aprobar calendario de campamentos.\n2. Fijar cuota anual en $30.000.\nAcuerdos aprobados por unanimidad.",
                'creado_por' => $adminId
            ]);
            $miembrosConsejo = $organoModel->getMiembros('Consejo');
            if (!empty($miembrosConsejo)) {
                $asistenciaDataConsejo = [];
                foreach ($miembrosConsejo as $m) {
                    $tipo = $m['usuario_id'] ? 'Usuario' : 'Apoderado';
                    $id_entidad = $m['usuario_id'] ? $m['usuario_id'] : $m['apoderado_id'];
                    $asistenciaDataConsejo[] = [
                        'tipo_entidad' => $tipo,
                        'entidad_id' => $id_entidad,
                        'asiste' => rand(0, 10) > 2 ? 1 : 0
                    ];
                }
                $reunionModel->registrarAsistencia($reunionIdConsejo, $asistenciaDataConsejo);
            }

            $db->commit();
            $_SESSION['success'] = "✅ Entorno de pruebas generado: " . count($apellidos) . " apoderados, beneficiarios con fichas médicas, actividades, actas y movimientos financieros para " . count($unidades) . " unidades.";
            $this->redirect('/tecnico');
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            die("Error en carga masiva: " . $e->getMessage());
        }
    }

    public function limpiarSistema() {
        Auth::requireRole(['Superusuario']);

        if (!isset($_SESSION['sigrrus_env']) || $_SESSION['sigrrus_env'] !== 'testing') {
            $_SESSION['error'] = 'Esta acción solo está permitida en el entorno de Entrenamiento.';
            $this->redirect('/tecnico');
            return;
        }
        
        $db = \App\DAL\Database::getInstance()->getConnection();
        
        try {
            $db->exec("SET FOREIGN_KEY_CHECKS = 0");
            
            $tables = [
                'asistencias', 'finanzas_movimientos', 'cuotas_mensuales', 
                'beneficiario_inscripcion', 'beneficiarios', 'apoderados', 
                'campamento_participantes', 'campamentos', 'hojas_ruta', 
                'ciclo_programa', 'reuniones_asistencia', 'reuniones',
                'organos_roles', 'fichas_medicas', 'inventario', 'notificaciones'
            ];

            foreach ($tables as $t) {
                $db->exec("DELETE FROM $t");
                // Reset auto-increment si es posible (depende del driver)
                try { $db->exec("ALTER TABLE $t AUTO_INCREMENT = 1"); } catch(\Exception $e){}
            }

            $db->exec("SET FOREIGN_KEY_CHECKS = 1");
            
            $_SESSION['success'] = "Sistema limpiado. Se mantuvieron usuarios y configuración base.";
            $this->redirect('/tecnico');
        } catch (\Exception $e) {
            die("Error al limpiar: " . $e->getMessage());
        }
    }

    public function suplantar($usuario_id) {
        Auth::requireRole(['Superusuario']);
        $userModel = new Usuario();
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $target = $userModel->findById($usuario_id, $anio);

        if ($target) {
            // Guardar sesión original si no se está suplantando ya
            if (!isset($_SESSION['original_user'])) {
                $_SESSION['original_user'] = [
                    'id' => $_SESSION['user_id'],
                    'nombre' => $_SESSION['user_nombre'],
                    'rol' => $_SESSION['user_rol'],
                    'unidad' => $_SESSION['user_unidad'],
                    'active_rol' => $_SESSION['active_rol'] ?? null,
                    'user_roles' => $_SESSION['user_roles'] ?? [],
                    'can_switch' => $_SESSION['can_switch'] ?? false,
                    'apoderado_id' => $_SESSION['apoderado_id'] ?? null
                ];
            }

            // Simular proceso de login para el objetivo
            $_SESSION['user_id'] = $target['id'];
            $_SESSION['user_nombre'] = $target['nombre'];
            $_SESSION['user_rol'] = $target['rol'];
            $_SESSION['user_unidad'] = $target['unidad_id'];
            $_SESSION['user_roles'] = $target['roles'];
            $_SESSION['apoderado_id'] = null;

            $apoModel = new Apoderado();
            $apoderado = $apoModel->findByRut($target['rut']);
            if ($apoderado) {
                $_SESSION['can_switch'] = true;
                $_SESSION['apoderado_id'] = $apoderado['id'];
                if (!in_array('Apoderado', $_SESSION['user_roles'])) {
                    $_SESSION['user_roles'][] = 'Apoderado';
                }
                // Si no tiene rol de dirigente, su rol base es Apoderado
                if ($_SESSION['user_rol'] === 'Sin Rol') {
                    $_SESSION['user_rol'] = 'Apoderado';
                }
            } else {
                $_SESSION['can_switch'] = count($_SESSION['user_roles']) > 1;
            }

            $_SESSION['active_rol'] = $_SESSION['user_rol'];
            $_SESSION['impersonating'] = true;
        }

        $this->redirect('/dashboard');
    }

    public function detenerSuplantacion() {
        if (isset($_SESSION['original_user'])) {
            $orig = $_SESSION['original_user'];
            $_SESSION['user_id'] = $orig['id'];
            $_SESSION['user_nombre'] = $orig['nombre'];
            $_SESSION['user_rol'] = $orig['rol'];
            $_SESSION['user_unidad'] = $orig['unidad'];
            $_SESSION['active_rol'] = $orig['active_rol'];
            $_SESSION['user_roles'] = $orig['user_roles'];
            $_SESSION['can_switch'] = $orig['can_switch'];
            $_SESSION['apoderado_id'] = $orig['apoderado_id'];
            
            unset($_SESSION['original_user']);
            unset($_SESSION['impersonating']);
        }
        $this->redirect('/tecnico');
    }

    public function exportarDB() {
        Auth::requireRole(['Superusuario']);
        $backupModel = new Backup();
        $sql = $backupModel->generateBackup();
        
        $filename = "snapshot_" . date('Ymd_His') . ".sql";
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $sql;
        exit;
    }

    public function restaurarDB() {
        Auth::requireRole(['Superusuario']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['backup_file'])) {
            $sql = \file_get_contents($_FILES['backup_file']['tmp_name']);
            $backupModel = new Backup();
            try {
                $backupModel->restore($sql);
                $_SESSION['success'] = "Base de datos restaurada con éxito.";
            } catch (\Exception $e) {
                die("Error al restaurar: " . $e->getMessage());
            }
        }
        $this->redirect('/tecnico');
    }

    public function buscarActualizaciones() {
        Auth::requireRole(['Superusuario']);
        unset($_SESSION['last_update_check']);
        unset($_SESSION['update_notified_version']);
        $this->redirect('/tecnico');
    }

    public function procesarActualizacion() {
        Auth::requireRole(['Superusuario']);
        
        try {
            $backupModel = new Backup();
            $updateModel = new UpdateManager();
            
            // 1. Verificar actualización de nuevo por seguridad
            $info = $updateModel->checkUpdates();
            if (!$info || !$info['has_update']) {
                $_SESSION['error'] = "No hay actualizaciones disponibles.";
                return $this->redirect('/tecnico');
            }

            // 2. Realizar Backups
            $sqlFile = $backupModel->saveSqlBackup();
            $zipFile = $backupModel->createFilesBackup();

            // 3. Descargar e Instalar
            $zipPath = $updateModel->downloadUpdate($info['zip_url']);
            if (!$zipPath) throw new \Exception("Error al descargar el paquete de actualización.");
            
            if ($updateModel->installUpdate($zipPath)) {
                $_SESSION['success'] = "¡Sistema actualizado con éxito a la versión {$info['latest']}! Se han generado respaldos preventivos: $sqlFile y $zipFile.";
            } else {
                $_SESSION['error'] = "Error durante la instalación de los archivos.";
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = "Error en la actualización: " . $e->getMessage();
        }
        
        $this->redirect('/tecnico');
    }

    public function descargarBackup($filename) {
        Auth::requireRole(['Superusuario']);
        $path = dirname(__DIR__, 2) . '/public/backups/' . $filename;
        
        if (file_exists($path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($path).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
        }
        $this->redirect('/tecnico');
    }

    public function eliminarBackup($filename) {
        Auth::requireRole(['Superusuario']);
        $path = dirname(__DIR__, 2) . '/public/backups/' . $filename;
        if (file_exists($path)) {
            unlink($path);
            $_SESSION['success'] = "Respaldo eliminado correctamente.";
        }
        $this->redirect('/tecnico');
    }
}
