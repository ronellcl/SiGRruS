<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\ConfigGrupo;
use App\Models\Reunion;
use App\Models\Usuario;
use App\Models\Apoderado;

class GrupoController extends Controller {
    
    public function config() {
        Auth::requireRole(['Superusuario']);
        $configModel = new ConfigGrupo();
        $config = $configModel->getConfig();

        $this->view('grupo/config', [
            'title' => 'Configuración de Grupo',
            'config' => $config
        ]);
    }

    public function guardarConfig() {
        Auth::requireRole(['Superusuario']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $configModel = new ConfigGrupo();
            $oldConfig = $configModel->getConfig();
            
            $pais = $_POST['pais'] === 'Otro' ? $_POST['pais_otro'] : $_POST['pais'];
            $ciudad = $_POST['ciudad'] === 'Otra' ? $_POST['ciudad_otro'] : $_POST['ciudad'];

            $uploadDir = 'public/uploads/logos/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $logo_path = $oldConfig['logo_path'];
            if (!empty($_FILES['logo_file']['name'])) {
                $filename = 'logo_grupo_' . time() . '_' . basename($_FILES['logo_file']['name']);
                if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $uploadDir . $filename)) {
                    $logo_path = '/uploads/logos/' . $filename;
                }
            }

            $asoc_logo_path = $oldConfig['asociacion_logo_path'];
            if (!empty($_FILES['asociacion_logo_file']['name'])) {
                $filename = 'logo_asociacion_' . time() . '_' . basename($_FILES['asociacion_logo_file']['name']);
                if (move_uploaded_file($_FILES['asociacion_logo_file']['tmp_name'], $uploadDir . $filename)) {
                    $asoc_logo_path = '/uploads/logos/' . $filename;
                }
            }

            $data = [
                'nombre_grupo' => $_POST['nombre_grupo'],
                'logo_path' => $logo_path,
                'asociacion_logo_path' => $asoc_logo_path,
                'institucion_patrocinante' => $_POST['institucion_patrocinante'],
                'representante_patrocinante_nombre' => $_POST['representante_patrocinante_nombre'],
                'pais' => $pais,
                'ciudad' => $ciudad,
                'zona' => $_POST['zona'],
                'distrito' => $_POST['distrito'],
                'smtp_host' => $_POST['smtp_host'] ?? '',
                'smtp_user' => $_POST['smtp_user'] ?? '',
                'smtp_pass' => $_POST['smtp_pass'] ?? '',
                'smtp_port' => $_POST['smtp_port'] ?? 587,
                'smtp_encryption' => $_POST['smtp_encryption'] ?? 'tls'
            ];
            $configModel->updateConfig($data);
            $this->redirect('/dashboard');
        }
    }

    public function consejo() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo', 'Responsable de Unidad', 'Asistente de Unidad', 'Apoderado']);
        $user = Auth::user();
        
        $organoModel = new \App\Models\Organo();
        $esMiembro = $organoModel->esMiembro('Consejo', $user['id']);
        
        // El Superusuario siempre ve todo, otros deben ser miembros
        if ($user['rol'] !== 'Superusuario' && !$esMiembro) {
            $this->redirect('/dashboard?error=no_consejo');
        }

        $reunionModel = new Reunion();
        $reuniones = $reunionModel->getByOrgano('Consejo');
        $miembros = $organoModel->getMiembros('Consejo');

        $this->view('grupo/consejo', [
            'title' => 'Gestión del Consejo de Grupo',
            'reuniones' => $reuniones,
            'miembros' => $miembros,
            'user' => $user
        ]);
    }

    public function agregarMiembroConsejo() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $organoModel = new \App\Models\Organo();
            $organoModel->agregarPorRut('Consejo', $_POST['rut'], $_POST['rol_especifico']);
            $this->redirect('/grupo/consejo');
        }
    }

    public function eliminarMiembroConsejo($id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        $organoModel = new \App\Models\Organo();
        $organoModel->eliminarMiembro($id);
        $this->redirect('/grupo/consejo');
    }

    public function crearActa() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reunionModel = new Reunion();
            $user = Auth::user();
            $data = [
                'tipo_organo' => $_POST['tipo_organo'],
                'fecha' => $_POST['fecha'],
                'tema' => $_POST['tema'],
                'acta' => $_POST['acta'],
                'creado_por' => $user['id']
            ];
            $reunionId = $reunionModel->create($data);
            
            // Registrar asistencia del consejo
            if ($_POST['tipo_organo'] === 'Consejo') {
                $asistentesPost = isset($_POST['asistentes']) && is_array($_POST['asistentes']) ? $_POST['asistentes'] : [];
                $organoModel = new \App\Models\Organo();
                $miembros = $organoModel->getMiembros('Consejo');
                
                $participantesFinal = [];
                foreach ($miembros as $m) {
                    $tipo = $m['usuario_id'] ? 'Usuario' : 'Apoderado';
                    $id_entidad = $m['usuario_id'] ? $m['usuario_id'] : $m['apoderado_id'];
                    $val = $tipo . '-' . $id_entidad;
                    
                    $participantesFinal[] = [
                        'tipo_entidad' => $tipo,
                        'entidad_id' => $id_entidad,
                        'asiste' => in_array($val, $asistentesPost) ? 1 : 0
                    ];
                }
                $reunionModel->registrarAsistencia($reunionId, $participantesFinal);
            }

            $this->redirect('/grupo/' . strtolower($_POST['tipo_organo']));
        }
    }

    public function verActa($id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo', 'Responsable de Unidad', 'Asistente de Unidad', 'Apoderado']);
        $reunionModel = new Reunion();
        $reunion = $reunionModel->findById($id);
        
        $asistencia = $reunionModel->getAsistencia($id);
        $usuarioModel = new Usuario();
        $apoModel = new Apoderado();
        $asistentes_nombres = [];
        foreach($asistencia as $a) {
            if ($a['tipo_entidad'] === 'Usuario') {
                $u = $usuarioModel->findById($a['entidad_id']);
                if ($u) {
                    $asistentes_nombres[] = [
                        'nombre' => $u['nombre'],
                        'rol' => $u['rol'],
                        'asiste' => $a['asiste']
                    ];
                }
            } else if ($a['tipo_entidad'] === 'Apoderado') {
                $apo = $apoModel->findById($a['entidad_id']);
                if ($apo) {
                    $asistentes_nombres[] = [
                        'nombre' => $apo['nombre_completo'],
                        'rol' => 'Apoderado',
                        'asiste' => $a['asiste']
                    ];
                }
            }
        }
        
        $modificacion = $reunionModel->getModificacionPendiente($id);
        
        $user = Auth::user();
        $isResponsable = in_array('Responsable de Grupo', $user['roles'] ?? []);
        
        // Verificar si es secretario del consejo
        $organoModel = new \App\Models\Organo();
        $miembros = $organoModel->getMiembros('Consejo');
        $isSecretario = false;
        foreach ($miembros as $m) {
            $is_match = ($m['usuario_id'] == $user['id']);
            if ($is_match && stripos($m['rol_especifico'], 'Secretari') !== false) {
                $isSecretario = true;
                break;
            }
        }
        
        $this->view('grupo/ver_acta', [
            'title' => 'Acta de Reunión - ' . date('d/m/Y', strtotime($reunion['fecha'])),
            'reunion' => $reunion,
            'asistencia' => $asistentes_nombres,
            'modificacion' => $modificacion,
            'isResponsable' => $isResponsable,
            'isSecretario' => $isSecretario,
            'user' => $user
        ]);
    }

    public function proponerModificacion() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo', 'Responsable de Unidad', 'Asistente de Unidad', 'Apoderado']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reunionModel = new Reunion();
            $user = Auth::user();
            
            $data = [
                'reunion_id' => $_POST['reunion_id'],
                'solicitado_por_entidad_id' => $user['id'],
                'solicitado_por_tipo' => 'Usuario',
                'nuevo_tema' => $_POST['nuevo_tema'],
                'nueva_acta' => $_POST['nueva_acta']
            ];
            
            $reunionModel->proponerModificacion($data);
            $_SESSION['success'] = "Tu propuesta de modificación ha sido enviada y está a la espera de revisión.";
            $this->redirect('/grupo/verActa/' . $_POST['reunion_id']);
        }
    }

    public function revisarModificacion($mod_id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo', 'Responsable de Unidad', 'Asistente de Unidad', 'Apoderado']);
        $reunionModel = new Reunion();
        $modificacion = $reunionModel->getModificacionById($mod_id);
        
        if (!$modificacion) {
            $this->redirect('/grupo/consejo');
            return;
        }
        
        $reunion = $reunionModel->findById($modificacion['reunion_id']);
        $user = Auth::user();
        
        $isResponsable = in_array('Responsable de Grupo', $user['roles'] ?? []);
        $organoModel = new \App\Models\Organo();
        $miembros = $organoModel->getMiembros('Consejo');
        $isSecretario = false;
        foreach ($miembros as $m) {
            if ($m['usuario_id'] == $user['id'] && stripos($m['rol_especifico'], 'Secretari') !== false) {
                $isSecretario = true;
                break;
            }
        }
        
        if (!$isResponsable && !$isSecretario && $user['rol'] !== 'Superusuario') {
            $_SESSION['error'] = "No tienes permisos para revisar modificaciones.";
            $this->redirect('/grupo/verActa/' . $reunion['id']);
            return;
        }
        
        $this->view('grupo/revisar_modificacion', [
            'title' => 'Revisar Modificación',
            'modificacion' => $modificacion,
            'reunion' => $reunion,
            'isResponsable' => $isResponsable || $user['rol'] === 'Superusuario',
            'isSecretario' => $isSecretario || $user['rol'] === 'Superusuario'
        ]);
    }

    public function procesarModificacion($mod_id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo', 'Responsable de Unidad', 'Asistente de Unidad', 'Apoderado']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reunionModel = new Reunion();
            $modificacion = $reunionModel->getModificacionById($mod_id);
            if (!$modificacion) return $this->redirect('/grupo/consejo');

            $user = Auth::user();
            $isResponsable = in_array('Responsable de Grupo', $user['roles'] ?? []) || $user['rol'] === 'Superusuario';
            $organoModel = new \App\Models\Organo();
            $miembros = $organoModel->getMiembros('Consejo');
            $isSecretario = $user['rol'] === 'Superusuario';
            foreach ($miembros as $m) {
                if ($m['usuario_id'] == $user['id'] && stripos($m['rol_especifico'], 'Secretari') !== false) {
                    $isSecretario = true; break;
                }
            }

            if (isset($_POST['accion']) && $_POST['accion'] === 'rechazar') {
                $reunionModel->rechazarModificacion($mod_id, $_POST['motivo']);
                $_SESSION['error'] = "La modificación fue rechazada.";
                $this->redirect('/grupo/verActa/' . $modificacion['reunion_id']);
                return;
            }

            // Es aprobar
            $completado = false;
            if ($isSecretario && isset($_POST['aprobar_como']) && $_POST['aprobar_como'] === 'Secretario') {
                $completado = $reunionModel->registrarAprobacion($mod_id, 'Secretario');
            } elseif ($isResponsable && isset($_POST['aprobar_como']) && $_POST['aprobar_como'] === 'Responsable') {
                $completado = $reunionModel->registrarAprobacion($mod_id, 'Responsable');
            } else {
                $_SESSION['error'] = "Hubo un problema al validar tu rol.";
                $this->redirect('/grupo/revisarModificacion/' . $mod_id);
                return;
            }

            if ($completado) {
                $_SESSION['success'] = "¡El acta ha sido actualizada permanentemente con la nueva versión!";
            } else {
                $_SESSION['success'] = "Has aprobado tu parte. Falta la otra firma para que el cambio sea permanente.";
            }
            $this->redirect('/grupo/verActa/' . $modificacion['reunion_id']);
        }
    }

    public function imprimirActa($id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo', 'Responsable de Unidad', 'Asistente de Unidad', 'Apoderado']);
        $reunionModel = new Reunion();
        $reunion = $reunionModel->findById($id);
        
        $configModel = new ConfigGrupo();
        $config = $configModel->getConfig();

        $asistencia = $reunionModel->getAsistencia($id);
        $usuarioModel = new Usuario();
        $apoModel = new Apoderado();
        $asistentes_nombres = [];
        foreach($asistencia as $a) {
            if ($a['tipo_entidad'] === 'Usuario') {
                $u = $usuarioModel->findById($a['entidad_id']);
                if ($u) {
                    $asistentes_nombres[] = [
                        'nombre' => $u['nombre'],
                        'rol' => $u['rol'],
                        'asiste' => $a['asiste']
                    ];
                }
            } else if ($a['tipo_entidad'] === 'Apoderado') {
                $apo = $apoModel->findById($a['entidad_id']);
                if ($apo) {
                    $asistentes_nombres[] = [
                        'nombre' => $apo['nombre_completo'],
                        'rol' => 'Apoderado',
                        'asiste' => $a['asiste']
                    ];
                }
            }
        }

        $this->view('grupo/imprimir_acta', [
            'title' => 'Acta de Consejo - ' . date('d/m/Y', strtotime($reunion['fecha'])),
            'reunion' => $reunion,
            'config' => $config,
            'asistencia' => $asistentes_nombres
        ]);
    }

    public function finanzas() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        $user = Auth::user();
        $finModel = new \App\Models\Finanzas();
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        
        $balance = $finModel->getBalanceGrupo($anio);
        $balances_unidades = $finModel->getBalancesUnidades($anio);
        $pendientes = $finModel->getMovimientosPendientesGrupo($anio);

        $this->view('grupo/finanzas', [
            'title' => 'Tesorería de Grupo',
            'user' => $user,
            'anio' => $anio,
            'balance' => $balance,
            'balances_unidades' => $balances_unidades,
            'pendientes' => $pendientes
        ]);
    }

    public function aprobarTraspaso($id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        $finModel = new \App\Models\Finanzas();
        $finModel->aprobarMovimiento($id);
        $this->redirect('/grupo/finanzas');
    }

    public function rechazarTraspaso($id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $finModel = new \App\Models\Finanzas();
            $finModel->rechazarMovimiento($id, $_POST['justificacion']);
            $this->redirect('/grupo/finanzas');
        }
    }

    public function inventario() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        $user = Auth::user();
        $invModel = new \App\Models\Inventario();
        
        $groupItems = $invModel->getGroupAssets();
        $globalItems = $invModel->getGlobal();

        $this->view('grupo/inventario', [
            'title' => 'Inventario General', 
            'groupItems' => $groupItems,
            'globalItems' => $globalItems,
            'user' => $user
        ]);
    }

    public function campamentos() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        $user = Auth::user();
        $campModel = new \App\Models\Campamento();
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $campamentos = $campModel->getGlobal($anio);
        $this->view('grupo/campamentos', [
            'title' => 'Campamentos de Grupo', 
            'campamentos' => $campamentos,
            'user' => $user
        ]);
    }

    public function fichas() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        $user = Auth::user();
        $benefModel = new \App\Models\Beneficiario();
        
        $filters = [
            'unidad_id' => $_GET['unidad_id'] ?? '',
            'nombre' => $_GET['nombre'] ?? '',
            'rut' => $_GET['rut'] ?? '',
            'apoderado' => $_GET['apoderado'] ?? ''
        ];

        $beneficiarios = $benefModel->searchGlobal($filters);
        
        $fichaModel = new \App\Models\FichaMedica();
        $fichas = [];
        foreach($beneficiarios as $b) {
            $fichas[$b['id']] = $fichaModel->getByBeneficiario($b['id']);
        }

        $unidadModel = new \App\Models\Unidad();
        $unidades = $unidadModel->findAll();

        $this->view('grupo/fichas', [
            'title' => 'Fichas Médicas Globales', 
            'beneficiarios' => $beneficiarios, 
            'fichas' => $fichas,
            'unidades' => $unidades,
            'filters' => $filters,
            'user' => $user
        ]);
    }

    public function dirigentes() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        $user = Auth::user();
        $userModel = new Usuario();
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $dirigentes = $userModel->findAllUsersWithRoles($anio, true);
        
        $unidadModel = new \App\Models\Unidad();
        $unidades = $unidadModel->findAll();

        $this->view('grupo/dirigentes', [
            'title' => 'Gestión de Dirigentes', 
            'dirigentes' => $dirigentes,
            'unidades' => $unidades,
            'user' => $user
        ]);
    }

    public function apoderados() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        $user = Auth::user();
        $apoModel = new Apoderado();
        $apoderados = $apoModel->findAll();
        $this->view('grupo/apoderados', [
            'title' => 'Gestión de Apoderados', 
            'apoderados' => $apoderados,
            'user' => $user
        ]);
    }
    public function editarDirigente($id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        $userModel = new Usuario();
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $dirigente = $userModel->findById($id, $anio);
        
        $unidadModel = new \App\Models\Unidad();
        $unidades = $unidadModel->findAll();
        
        $certificados = $userModel->getCertificados($id);

        $this->view('grupo/editar_dirigente', [
            'title' => 'Editar Dirigente - ' . $dirigente['nombre'],
            'dirigente' => $dirigente,
            'unidades' => $unidades,
            'certificados' => $certificados,
            'user' => Auth::user()
        ]);
    }

    public function actualizarDirigente() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new Usuario();
            $id = $_POST['id'];
            
            $inscripciones = [];
            // Procesar roles de grupo
            if (!empty($_POST['roles_grupo'])) {
                foreach ($_POST['roles_grupo'] as $rol) {
                    $inscripciones[] = ['rol' => $rol, 'unidad_id' => null];
                }
            }
            // Procesar roles de unidad
            if (!empty($_POST['roles_unidad'])) {
                foreach ($_POST['roles_unidad'] as $unidad_id => $roles) {
                    foreach ($roles as $rol) {
                        $inscripciones[] = ['rol' => $rol, 'unidad_id' => $unidad_id];
                    }
                }
            }

            // Si es apoderado, asegurar registro en tabla apoderados
            if (isset($_POST['es_apoderado'])) {
                $inscripciones[] = ['rol' => 'Apoderado', 'unidad_id' => null];
                $apoModel = new Apoderado();
                if (!$apoModel->findByRut($_POST['rut'])) {
                    $apoModel->create([
                        'nombre_completo' => $_POST['nombre'],
                        'rut' => $_POST['rut'],
                        'email' => $_POST['email'],
                        'telefono' => 'No informado',
                        'direccion' => 'No informada'
                    ]);
                }
            }

            $anio = $_SESSION['anio_scout'] ?? date('Y');
            $oldUser = $userModel->findById($id, $anio);
            $oldRut = $oldUser['rut'];

            $tipo_doc = ($_POST['tipo_documento'] ?? 'RUT') === 'Otro' ? ($_POST['tipo_documento_otro'] ?? 'Otro') : ($_POST['tipo_documento'] ?? 'RUT');
            
            $data = [
                'nombre' => $_POST['nombre'],
                'rut' => $_POST['rut'],
                'tipo_documento' => $tipo_doc,
                'nacionalidad' => $_POST['nacionalidad'] ?? 'Chilena',
                'email' => $_POST['email'],
                'password' => $_POST['password'] ?? null,
                'inscripciones' => $inscripciones,
                'anio' => $_SESSION['anio_scout'] ?? date('Y')
            ];
            
            $userModel->update($id, $data);

            // Sincronizar con tabla apoderados si es apoderado
            $apoModel = new Apoderado();
            // Buscar por RUT actual o RUT anterior
            $esApo = $apoModel->findByRut($_POST['rut']) ?: $apoModel->findByRut($oldRut);
            
            if ($esApo || isset($_POST['es_apoderado'])) {
                $apoData = [
                    'nombre_completo' => $_POST['nombre'],
                    'rut' => $_POST['rut'],
                    'tipo_documento' => $tipo_doc,
                    'nacionalidad' => $_POST['nacionalidad'] ?? 'Chilena',
                    'email' => $_POST['email'],
                    'telefono' => $esApo['telefono'] ?? 'No informado',
                    'direccion' => $esApo['direccion'] ?? 'No informada'
                ];
                if ($esApo) {
                    $apoModel->update($esApo['id'], $apoData);
                } else {
                    $apoModel->create($apoData);
                }
            }
            $this->redirect('/grupo/dirigentes');
        }
    }

    public function subirCertificado() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
            $userId = $_POST['usuario_id'];
            $tipo = $_POST['tipo'];
            $anio = $_SESSION['anio_scout'] ?? date('Y');
            
            $uploadDir = 'public/uploads/certificados/' . $userId . '/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $filename = $tipo . "_" . date('Ymd') . "_" . basename($_FILES['archivo']['name']);
            $targetPath = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['archivo']['tmp_name'], $targetPath)) {
                $userModel = new Usuario();
                $userModel->addCertificado([
                    'usuario_id' => $userId,
                    'anio' => $anio,
                    'tipo' => $tipo,
                    'fecha_emision' => $_POST['fecha_emision'],
                    'archivo_path' => '/uploads/certificados/' . $userId . '/' . $filename
                ]);
            }
            $this->redirect('/grupo/editarDirigente/' . $userId);
        }
    }
}
