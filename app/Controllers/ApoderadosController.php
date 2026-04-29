<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Apoderado;
use App\Models\Unidad;
use App\Models\Beneficiario;

class ApoderadosController extends Controller
{

    public function index($unidad_id = null)
    {
        $user = Auth::user();
        $db = \App\DAL\Database::getInstance()->getConnection();

        if ($unidad_id) {
            // Contexto de UNIDAD
            Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad']);
            if ($user['rol'] !== 'Superusuario' && $user['unidad_id'] != $unidad_id) {
                $this->show403("No tienes permisos en esta unidad.");
            }

            $stmt = $db->prepare("
                SELECT DISTINCT a.* 
                FROM apoderados a
                JOIN beneficiarios b ON a.id = b.apoderado_id
                JOIN beneficiario_inscripcion bi ON b.id = bi.beneficiario_id
                WHERE bi.unidad_id = ? AND (bi.fecha_salida IS NULL OR bi.fecha_salida >= ?)
            ");
            $stmt->execute([$unidad_id, date('Y-01-01')]);
            $apoderados = $stmt->fetchAll();

            $unidadModel = new Unidad();
            $unidad = $unidadModel->findById($unidad_id);

            $this->view('apoderados/index', [
                'title' => 'Apoderados - ' . $unidad['nombre'],
                'unidad' => $unidad,
                'apoderados' => $apoderados,
                'user' => $user
            ]);
        } else {
            // Contexto GLOBAL de Grupo
            Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo']);

            $stmt = $db->prepare("
                SELECT a.*, GROUP_CONCAT(b.nombre_completo, ', ') as hijos
                FROM apoderados a
                JOIN beneficiarios b ON a.id = b.apoderado_id
                GROUP BY a.id
            ");
            $stmt->execute();
            $apoderados = $stmt->fetchAll();

            $this->view('apoderados/global', [
                'title' => 'Gestión Global de Apoderados',
                'apoderados' => $apoderados,
                'user' => $user
            ]);
        }
    }

    public function crear()
    {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        $unidadModel = new Unidad();
        $unidades = $unidadModel->findAll();

        $this->view('apoderados/crear', [
            'title' => 'Nuevo Apoderado y Beneficiarios',
            'unidades' => $unidades,
            'user' => Auth::user()
        ]);
    }

    public function guardar()
    {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = \App\DAL\Database::getInstance()->getConnection();
            try {
                $db->beginTransaction();

                // 1. Crear o Recuperar Apoderado (Smart Upsert por RUT)
                $apoModel = new Apoderado();
                $existing = $apoModel->findByRut($_POST['rut']);

                $tipo_doc_apo = ($_POST['tipo_documento'] ?? 'RUT') === 'Otro' ? ($_POST['tipo_documento_otro'] ?? 'Otro') : ($_POST['tipo_documento'] ?? 'RUT');

                $apoData = [
                    'nombre_completo' => $_POST['nombre_completo'],
                    'rut' => $_POST['rut'],
                    'tipo_documento' => $tipo_doc_apo,
                    'nacionalidad' => $_POST['nacionalidad'] ?? 'Chilena',
                    'email' => $_POST['email'],
                    'telefono' => $_POST['telefono'],
                    'direccion' => $_POST['direccion']
                ];

                if ($existing) {
                    $apoderado_id = $existing['id'];
                    $apoModel->update($apoderado_id, $apoData);
                } else {
                    $apoderado_id = $apoModel->create($apoData);
                }

                // 1.2 Procesar Suplentes (Opcionales)
                $suplente1_id = null;
                if (!empty($_POST['s1_rut']) && !empty($_POST['s1_nombre'])) {
                    $s1 = $apoModel->findByRut($_POST['s1_rut']);
                    $s1Data = [
                        'nombre_completo' => $_POST['s1_nombre'],
                        'rut' => $_POST['s1_rut'],
                        'tipo_documento' => 'RUT',
                        'nacionalidad' => 'Chilena',
                        'email' => $_POST['s1_email'] ?? '',
                        'telefono' => $_POST['s1_telefono'] ?? '',
                        'direccion' => $_POST['direccion']
                    ];
                    $suplente1_id = $s1 ? $s1['id'] : $apoModel->create($s1Data);
                    if ($s1) $apoModel->update($suplente1_id, $s1Data);
                }

                $suplente2_id = null;
                if (!empty($_POST['s2_rut']) && !empty($_POST['s2_nombre'])) {
                    $s2 = $apoModel->findByRut($_POST['s2_rut']);
                    $s2Data = [
                        'nombre_completo' => $_POST['s2_nombre'],
                        'rut' => $_POST['s2_rut'],
                        'tipo_documento' => 'RUT',
                        'nacionalidad' => 'Chilena',
                        'email' => $_POST['s2_email'] ?? '',
                        'telefono' => $_POST['s2_telefono'] ?? '',
                        'direccion' => $_POST['direccion']
                    ];
                    $suplente2_id = $s2 ? $s2['id'] : $apoModel->create($s2Data);
                    if ($s2) $apoModel->update($suplente2_id, $s2Data);
                }

                // 1.5 Sincronizar con tabla USUARIOS para permitir LOGIN
                $userModel = new \App\Models\Usuario();
                $userAccount = $userModel->findByRut($apoData['rut']);

                if (!$userAccount && !empty($apoData['email'])) {
                    $plainPassword = bin2hex(random_bytes(4));
                    $userData = [
                        'nombre' => $apoData['nombre_completo'],
                        'rut' => $apoData['rut'],
                        'tipo_documento' => $apoData['tipo_documento'],
                        'nacionalidad' => $apoData['nacionalidad'],
                        'email' => $apoData['email'],
                        'password' => $plainPassword,
                        'must_change_password' => 1,
                        'rol' => 'Apoderado',
                        'unidad_id' => null,
                        'anio' => $_SESSION['anio_scout'] ?? date('Y')
                    ];
                    if ($userModel->create($userData)) {
                        \App\Core\Email::sendWelcome($userData, $plainPassword);
                    }
                }

                // 2. Crear Beneficiarios (Hijos)
                if (!empty($_POST['hijos_nombre'])) {
                    $benefModel = new Beneficiario();
                    foreach ($_POST['hijos_nombre'] as $key => $nombre) {
                        if (empty($nombre))
                            continue;

                        $tipo_doc_hijo = $_POST['hijos_tipo_doc'][$key] === 'Otro' ? ($_POST['hijos_tipo_doc_otro'][$key] ?? 'Otro') : $_POST['hijos_tipo_doc'][$key];

                        $benefModel->create([
                            'nombre_completo' => $nombre,
                            'rut' => $_POST['hijos_rut'][$key],
                            'tipo_documento' => $tipo_doc_hijo ?? 'RUT',
                            'nacionalidad' => $_POST['hijos_nacionalidad'][$key] ?? 'Chilena',
                            'fecha_nacimiento' => $_POST['hijos_fecha'][$key],
                            'apoderado_id' => $apoderado_id,
                            'apoderado_suplente_1_id' => $suplente1_id,
                            'apoderado_suplente_2_id' => $suplente2_id,
                            'unidad_id' => $_POST['hijos_unidad'][$key],
                            'anio' => $_SESSION['anio_scout'] ?? date('Y')
                        ]);
                    }
                }

                $db->commit();
                $this->redirect('/grupo/apoderados');
            } catch (\Exception $e) {
                if ($db->inTransaction())
                    $db->rollBack();
                die("Error al guardar: " . $e->getMessage());
            }
        }
    }

    public function editar($id)
    {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad', 'Responsable de Grupo', 'Apoderado']);
        $user = Auth::user();

        if ($user['rol'] === 'Apoderado' && $user['apoderado_id'] != $id) {
            $this->show403("Acceso denegado. Solo puedes editar tus propios datos.");
        }

        $db = \App\DAL\Database::getInstance()->getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $tipo_doc = ($_POST['tipo_documento'] ?? 'RUT') === 'Otro' ? ($_POST['tipo_documento_otro'] ?? 'Otro') : ($_POST['tipo_documento'] ?? 'RUT');

                $apoModel = new Apoderado();
                $oldApo = $apoModel->findById($id);
                
                $apoData = [
                    'nombre_completo' => $_POST['nombre_completo'],
                    'rut' => $_POST['rut'],
                    'tipo_documento' => $tipo_doc,
                    'nacionalidad' => $_POST['nacionalidad'] ?? 'Chilena',
                    'email' => $_POST['email'],
                    'telefono' => $_POST['telefono'],
                    'direccion' => $_POST['direccion']
                ];

                $apoModel->update($id, $apoData);

                // Sincronizar con tabla USUARIOS
                $userModel = new \App\Models\Usuario();
                // Buscar por RUT actual o RUT anterior
                $userAccount = $userModel->findByRut($oldApo['rut']);
                if ($userAccount) {
                    $userData = [
                        'nombre' => $_POST['nombre_completo'],
                        'rut' => $_POST['rut'],
                        'tipo_documento' => $tipo_doc,
                        'nacionalidad' => $_POST['nacionalidad'] ?? 'Chilena',
                        'email' => $_POST['email'],
                        'password' => $_POST['password'] ?? null,
                        'anio' => $_SESSION['anio_scout'] ?? date('Y')
                    ];
                    // Mantener roles actuales
                    $currentRoles = $userModel->getRoleHistory($userAccount['id']);
                    $userData['inscripciones'] = array_map(function($r) {
                        return ['rol' => $r['rol'], 'unidad_id' => $r['unidad_id']];
                    }, $currentRoles);

                    $userModel->update($userAccount['id'], $userData);
                }

                $referer = $_POST['referer'] ?? '/dashboard';
                $this->redirect($referer);
            } catch (\PDOException $e) {
                // Si el error es por RUT duplicado (Código 23000 o mensaje 'Duplicate entry')
                if ($e->getCode() == 23000 || strpos($e->getMessage(), 'Duplicate entry') !== false) {

                    // Buscar quién es el dueño original de ese RUT
                    $stmtSearch = $db->prepare("SELECT id FROM apoderados WHERE rut = ? AND id != ?");
                    $stmtSearch->execute([$_POST['rut'], $id]);
                    $original = $stmtSearch->fetch();

                    if ($original) {
                        $originalId = $original['id'];
                        // UNIFICACIÓN AUTOMÁTICA: Mover hijos al registro original y eliminar este duplicado
                        $db->prepare("UPDATE beneficiarios SET apoderado_id = ? WHERE apoderado_id = ?")->execute([$originalId, $id]);
                        $db->prepare("DELETE FROM apoderados WHERE id = ?")->execute([$id]);

                        // Redirigir al registro original unificado
                        $this->redirect("/apoderados/editar/" . $originalId . "?msg=merged");
                    } else {
                        $this->show403("Error de integridad: El RUT ingresado ya está en uso por otro registro.");
                    }
                } else {
                    throw $e;
                }
            }
        }

        $stmt = $db->prepare("SELECT * FROM apoderados WHERE id = ?");
        $stmt->execute([$id]);
        $apoderado = $stmt->fetch();

        $this->view('apoderados/editar', [
            'title' => 'Editar Apoderado',
            'apoderado' => $apoderado,
            'user' => Auth::user()
        ]);
    }
    public function vincular($id = null)
    {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Apoderado']);
        $user = Auth::user();
        $db = \App\DAL\Database::getInstance()->getConnection();

        // Autocompletar ID o crear registro si el apoderado ingresa por primera vez
        if ($user['rol'] === 'Apoderado') {
            if (empty($user['apoderado_id'])) {
                $userModel = new \App\Models\Usuario();
                $userData = $userModel->findById($user['id']);
                $apoModel = new Apoderado();

                $nuevoApoId = $apoModel->create([
                    'nombre_completo' => $userData['nombre'],
                    'rut' => $userData['rut'],
                    'tipo_documento' => $userData['tipo_documento'] ?? 'RUT',
                    'nacionalidad' => $userData['nacionalidad'] ?? 'Chilena',
                    'email' => $userData['email'],
                    'telefono' => 'No informado',
                    'direccion' => 'No informada'
                ]);
                $_SESSION['apoderado_id'] = $nuevoApoId;
                $user['apoderado_id'] = $nuevoApoId;
            }

            if ($id !== null && $id != $user['apoderado_id']) {
                $this->show403("Acceso denegado: Solo puedes vincular beneficiarios a tu propia cuenta.");
            }
            $id = $user['apoderado_id'];
        }

        if (!$id) {
            $this->redirect('/dashboard');
        }

        // Cargar apoderado
        $stmt = $db->prepare("SELECT * FROM apoderados WHERE id = ?");
        $stmt->execute([$id]);
        $apoderado = $stmt->fetch();
        if (!$apoderado)
            $this->redirect('/dashboard');

        // Cargar TODOS los beneficiarios (para vincular existente)
        $stmtBenef = $db->prepare("SELECT b.*, u.nombre as unidad_nombre FROM beneficiarios b LEFT JOIN beneficiario_inscripcion bi ON b.id = bi.beneficiario_id LEFT JOIN unidades u ON bi.unidad_id = u.id GROUP BY b.id ORDER BY b.nombre_completo ASC");
        $stmtBenef->execute();
        $beneficiarios = $stmtBenef->fetchAll();

        // Cargar Unidades (para crear nuevo)
        $unidadModel = new Unidad();
        $unidades = $unidadModel->findAll();

        $this->view('apoderados/vincular', [
            'title' => 'Vincular Beneficiario a ' . $apoderado['nombre_completo'],
            'apoderado' => $apoderado,
            'beneficiarios' => $beneficiarios,
            'unidades' => $unidades,
            'user' => $user
        ]);
    }

    public function postVincularExistente()
    {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Apoderado']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = Auth::user();
            $apoderado_id = $_POST['apoderado_id'];

            if ($user['rol'] === 'Apoderado' && $user['apoderado_id'] != $apoderado_id) {
                $this->show403("Acceso denegado.");
            }

            $beneficiario_id = $_POST['beneficiario_id'];

            $db = \App\DAL\Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE beneficiarios SET apoderado_id = ? WHERE id = ?");
            $stmt->execute([$apoderado_id, $beneficiario_id]);

            $redirect_url = $user['rol'] === 'Apoderado' ? '/dashboard' : '/grupo/apoderados';
            $this->redirect($redirect_url);
        }
    }

    public function postVincularNuevo()
    {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Apoderado']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = Auth::user();
            $apoderado_id = $_POST['apoderado_id'];

            if ($user['rol'] === 'Apoderado' && $user['apoderado_id'] != $apoderado_id) {
                $this->show403("Acceso denegado.");
            }

            $benefModel = new Beneficiario();
            $apoModel = new Apoderado();

            // Procesar Suplentes (Opcionales)
            $suplente1_id = null;
            if (!empty($_POST['s1_rut']) && !empty($_POST['s1_nombre'])) {
                $s1 = $apoModel->findByRut($_POST['s1_rut']);
                $s1Data = [
                    'nombre_completo' => $_POST['s1_nombre'],
                    'rut' => $_POST['s1_rut'],
                    'tipo_documento' => 'RUT',
                    'nacionalidad' => 'Chilena',
                    'email' => $_POST['s1_email'] ?? '',
                    'telefono' => $_POST['s1_telefono'] ?? '',
                    'direccion' => ''
                ];
                $suplente1_id = $s1 ? $s1['id'] : $apoModel->create($s1Data);
                if ($s1) $apoModel->update($suplente1_id, $s1Data);
            }

            $suplente2_id = null;
            if (!empty($_POST['s2_rut']) && !empty($_POST['s2_nombre'])) {
                $s2 = $apoModel->findByRut($_POST['s2_rut']);
                $s2Data = [
                    'nombre_completo' => $_POST['s2_nombre'],
                    'rut' => $_POST['s2_rut'],
                    'tipo_documento' => 'RUT',
                    'nacionalidad' => 'Chilena',
                    'email' => $_POST['s2_email'] ?? '',
                    'telefono' => $_POST['s2_telefono'] ?? '',
                    'direccion' => ''
                ];
                $suplente2_id = $s2 ? $s2['id'] : $apoModel->create($s2Data);
                if ($s2) $apoModel->update($suplente2_id, $s2Data);
            }

            $tipo_doc = ($_POST['tipo_documento'] ?? 'RUT') === 'Otro' ? ($_POST['tipo_documento_otro'] ?? 'Otro') : ($_POST['tipo_documento'] ?? 'RUT');

            $benefModel->create([
                'nombre_completo' => $_POST['nombre_completo'],
                'rut' => $_POST['rut'],
                'tipo_documento' => $tipo_doc,
                'nacionalidad' => $_POST['nacionalidad'] ?? 'Chilena',
                'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                'apoderado_id' => $apoderado_id,
                'apoderado_suplente_1_id' => $suplente1_id,
                'apoderado_suplente_2_id' => $suplente2_id,
                'unidad_id' => $_POST['unidad_id'],
                'subgrupo' => $_POST['subgrupo'] ?? '',
                'anio' => $_SESSION['anio_scout'] ?? date('Y')
            ]);

            $redirect_url = $user['rol'] === 'Apoderado' ? '/dashboard' : '/grupo/apoderados';
            $this->redirect($redirect_url);
        }
    }
}
