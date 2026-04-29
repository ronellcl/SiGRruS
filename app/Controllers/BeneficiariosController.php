<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Beneficiario;
use App\Models\Unidad;

class BeneficiariosController extends Controller {
    
    public function index($unidad_id = null) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo', 'Responsable de Unidad', 'Asistente de Unidad']);
        
        $user = Auth::user();
        
        // RBAC: Si no es un rol de gestión global, forzar la unidad a la que pertenece
        $globalRoles = ['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo'];
        if (!in_array($user['rol'], $globalRoles)) {
            $unidad_id = $user['unidad_id'];
        } else {
            // Si es global y no envió ID, redirigir al dashboard
            if (!$unidad_id) {
                $this->redirect('/dashboard');
            }
        }

        $anioScout = $_SESSION['anio_scout'] ?? date('Y');

        $beneficiarioModel = new Beneficiario();
        $beneficiarios = $beneficiarioModel->findByUnidad($unidad_id, $anioScout);
        
        $unidadModel = new Unidad();
        $unidad = $unidadModel->findById($unidad_id);

        if (!$unidad) {
            http_response_code(404);
            echo "Error 404: La unidad solicitada no existe o no está configurada en este entorno.";
            exit;
        }

        $apoderadoModel = new \App\Models\Apoderado();
        $todosApoderados = $apoderadoModel->findAll();

        $this->view('beneficiarios/index', [
            'title' => 'Beneficiarios - ' . $unidad['nombre'],
            'unidad' => $unidad,
            'beneficiarios' => $beneficiarios,
            'apoderados' => $todosApoderados,
            'anio_actual' => $anioScout,
            'user' => $user
        ]);
    }

    public function crear($unidad_id) {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad']);
        $user = Auth::user();
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $this->checkYearStatus($anio, $unidad_id);
        
        if ($user['rol'] !== 'Superusuario' && $user['unidad_id'] != $unidad_id) {
            http_response_code(403);
            $message = "No tienes permisos para realizar cambios en esta unidad.";
            require_once dirname(dirname(__DIR__)) . '/app/Views/errors/403.php';
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Gestionar el Apoderado
            $apoderadoModel = new \App\Models\Apoderado();
            $rutApoderado = $_POST['apoderado_rut'] ?? '';
            
            $apoderado = $apoderadoModel->findByRut($rutApoderado);
            
            $tipo_doc_apo = ($_POST['apoderado_tipo_doc'] ?? 'RUT') === 'Otro' ? ($_POST['apoderado_tipo_doc_otro'] ?? 'Otro') : ($_POST['apoderado_tipo_doc'] ?? 'RUT');

            $apoData = [
                'nombre_completo' => $_POST['apoderado_nombre'] ?? '',
                'rut' => $rutApoderado,
                'tipo_documento' => $tipo_doc_apo,
                'nacionalidad' => $_POST['apoderado_nacionalidad'] ?? 'Chilena',
                'email' => $_POST['apoderado_email'] ?? '',
                'telefono' => $_POST['apoderado_telefono'] ?? '',
                'direccion' => $_POST['apoderado_direccion'] ?? ''
            ];

            if (!$apoderado) {
                $apoderado_id = $apoderadoModel->create($apoData);
            } else {
                $apoderado_id = $apoderado['id'];
                $apoderadoModel->update($apoderado_id, $apoData);
            }

            // 1.1 Gestionar Suplente 1 (Opcional)
            $suplente1_id = null;
            if (!empty($_POST['s1_rut']) && !empty($_POST['s1_nombre'])) {
                $s1 = $apoderadoModel->findByRut($_POST['s1_rut']);
                $s1Data = [
                    'nombre_completo' => $_POST['s1_nombre'],
                    'rut' => $_POST['s1_rut'],
                    'tipo_documento' => $_POST['s1_tipo_doc'] ?? 'RUT',
                    'nacionalidad' => $_POST['s1_nacionalidad'] ?? 'Chilena',
                    'email' => $_POST['s1_email'] ?? '',
                    'telefono' => $_POST['s1_telefono'] ?? '',
                    'direccion' => $_POST['s1_direccion'] ?? $_POST['apoderado_direccion'] // Por defecto misma dirección
                ];
                $suplente1_id = $s1 ? $s1['id'] : $apoderadoModel->create($s1Data);
                if ($s1) $apoderadoModel->update($suplente1_id, $s1Data);
            }

            // 1.2 Gestionar Suplente 2 (Opcional)
            $suplente2_id = null;
            if (!empty($_POST['s2_rut']) && !empty($_POST['s2_nombre'])) {
                $s2 = $apoderadoModel->findByRut($_POST['s2_rut']);
                $s2Data = [
                    'nombre_completo' => $_POST['s2_nombre'],
                    'rut' => $_POST['s2_rut'],
                    'tipo_documento' => $_POST['s2_tipo_doc'] ?? 'RUT',
                    'nacionalidad' => $_POST['s2_nacionalidad'] ?? 'Chilena',
                    'email' => $_POST['s2_email'] ?? '',
                    'telefono' => $_POST['s2_telefono'] ?? '',
                    'direccion' => $_POST['s2_direccion'] ?? $_POST['apoderado_direccion']
                ];
                $suplente2_id = $s2 ? $s2['id'] : $apoderadoModel->create($s2Data);
                if ($s2) $apoderadoModel->update($suplente2_id, $s2Data);
            }

            // 2. Crear Beneficiario
            $tipo_doc_bene = ($_POST['tipo_documento'] ?? 'RUT') === 'Otro' ? ($_POST['tipo_documento_otro'] ?? 'Otro') : ($_POST['tipo_documento'] ?? 'RUT');

            $data = [
                'unidad_id' => $unidad_id,
                'nombre_completo' => $_POST['nombre_completo'] ?? '',
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
                'rut' => $_POST['rut'] ?? '',
                'tipo_documento' => $tipo_doc_bene,
                'nacionalidad' => $_POST['nacionalidad'] ?? 'Chilena',
                'subgrupo' => $_POST['subgrupo'] ?? '',
                'anio' => $anio,
                'apoderado_id' => $apoderado_id,
                'apoderado_suplente_1_id' => $suplente1_id,
                'apoderado_suplente_2_id' => $suplente2_id
            ];

            $beneficiarioModel = new Beneficiario();
            $beneficiarioModel->create($data);
            
            $this->redirect('/unidades/' . $unidad_id . '/beneficiarios');
        }
    }

    public function editar($id) {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad']);
        $beneficiarioModel = new Beneficiario();
        $beneficiario = $beneficiarioModel->getDetails($id);
        
        if (!$beneficiario) $this->redirect('/dashboard');

        $apoderadoModel = new \App\Models\Apoderado();
        $apoderados = $apoderadoModel->findAll();

        $this->view('beneficiarios/editar', [
            'title' => 'Editar Beneficiario',
            'beneficiario' => $beneficiario,
            'apoderados' => $apoderados,
            'user' => Auth::user()
        ]);
    }

    public function actualizar($id) {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $beneficiarioModel = new Beneficiario();
            $apoderadoModel = new \App\Models\Apoderado();

            // Procesar Suplente 1
            $suplente1_id = null;
            if (!empty($_POST['s1_rut']) && !empty($_POST['s1_nombre'])) {
                $s1 = $apoderadoModel->findByRut($_POST['s1_rut']);
                $s1Data = [
                    'nombre_completo' => $_POST['s1_nombre'],
                    'rut' => $_POST['s1_rut'],
                    'tipo_documento' => $_POST['s1_tipo_doc'] ?? 'RUT',
                    'nacionalidad' => $_POST['s1_nacionalidad'] ?? 'Chilena',
                    'email' => $_POST['s1_email'] ?? '',
                    'telefono' => $_POST['s1_telefono'] ?? '',
                    'direccion' => $_POST['s1_direccion'] ?? ''
                ];
                $suplente1_id = $s1 ? $s1['id'] : $apoderadoModel->create($s1Data);
                if ($s1) $apoderadoModel->update($suplente1_id, $s1Data);
            }

            // Procesar Suplente 2
            $suplente2_id = null;
            if (!empty($_POST['s2_rut']) && !empty($_POST['s2_nombre'])) {
                $s2 = $apoderadoModel->findByRut($_POST['s2_rut']);
                $s2Data = [
                    'nombre_completo' => $_POST['s2_nombre'],
                    'rut' => $_POST['s2_rut'],
                    'tipo_documento' => $_POST['s2_tipo_doc'] ?? 'RUT',
                    'nacionalidad' => $_POST['s2_nacionalidad'] ?? 'Chilena',
                    'email' => $_POST['s2_email'] ?? '',
                    'telefono' => $_POST['s2_telefono'] ?? '',
                    'direccion' => $_POST['s2_direccion'] ?? ''
                ];
                $suplente2_id = $s2 ? $s2['id'] : $apoderadoModel->create($s2Data);
                if ($s2) $apoderadoModel->update($suplente2_id, $s2Data);
            }

            $data = [
                'nombre_completo' => $_POST['nombre_completo'],
                'rut' => $_POST['rut'],
                'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                'tipo_documento' => $_POST['tipo_documento'],
                'nacionalidad' => $_POST['nacionalidad'],
                'apoderado_id' => $_POST['apoderado_id'],
                'apoderado_suplente_1_id' => $suplente1_id,
                'apoderado_suplente_2_id' => $suplente2_id
            ];
            $beneficiarioModel->update($id, $data);
            $this->redirect('/beneficiarios/ver/' . $id);
        }
    }

    public function ver($id) {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad', 'Apoderado']);
        $user = Auth::user();
        
        $beneficiarioModel = new Beneficiario();
        $beneficiario = $beneficiarioModel->getDetails($id);

        if (!$beneficiario) {
            $this->redirect('/dashboard');
        }

        // Si es apoderado, verificar vínculo
        if ($user['rol'] === 'Apoderado' && $beneficiario['apoderado_id'] != $user['id']) {
            $this->redirect('/dashboard');
        }

        $unidadModel = new Unidad();
        $unidad = $unidadModel->findById($beneficiario['unidad_id']);

        $this->view('beneficiarios/ver', [
            'title' => 'Perfil de Beneficiario - ' . $beneficiario['nombre_completo'],
            'beneficiario' => $beneficiario,
            'unidad' => $unidad,
            'user' => $user
        ]);
    }
    public function eliminar($id) {
        $beneficiarioModel = new Beneficiario();
        $beneficiario = $beneficiarioModel->getDetails($id);
        if (!$beneficiario) $this->redirect('/dashboard');

        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Responsable de Unidad']);
        $this->checkYearStatus($_SESSION['anio_scout'] ?? date('Y'), $beneficiario['unidad_id']);
        
        $beneficiarioModel->delete($id);
        
        $this->redirect('/unidades/' . $beneficiario['unidad_id'] . '/beneficiarios');
    }
}
