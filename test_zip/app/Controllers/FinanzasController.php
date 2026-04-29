<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Finanzas;
use App\Models\Unidad;
use App\Models\Beneficiario;

class FinanzasController extends Controller {
    
    public function index($unidad_id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo', 'Responsable de Unidad', 'Asistente de Unidad']);
        $user = Auth::user();
        
        // RBAC: Si no es un rol de gestión global, validar unidad
        $globalRoles = ['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo'];
        if (!in_array($user['rol'], $globalRoles)) {
            if ($user['unidad_id'] != $unidad_id) {
                $this->redirect('/dashboard');
            }
        }

        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $finanzasModel = new Finanzas();
        $movimientos = $finanzasModel->getMovimientos($unidad_id, $anio);
        $cuotas = $finanzasModel->getCuotasUnidad($unidad_id, $anio);
        
        $unidadModel = new Unidad();
        $unidad = $unidadModel->findById($unidad_id);

        $anioModel = new \App\Models\YearManager();
        $anioData = $anioModel->getByAnio($anio);
        $valor_inscripcion_grupo = $anioData['valor_inscripcion'] ?? 0;

        $this->view('finanzas/index', [
            'title' => 'Finanzas - ' . $unidad['nombre'],
            'unidad' => $unidad,
            'movimientos' => $movimientos,
            'cuotas' => $cuotas,
            'user' => $user,
            'anio_actual' => $anio,
            'valor_inscripcion_grupo' => $valor_inscripcion_grupo
        ]);
    }

    public function registrarMovimiento($unidad_id) {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad']);
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $this->checkYearStatus($anio, $unidad_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comprobante_path = null;
            if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
                $dir = dirname(dirname(__DIR__)) . '/public/uploads/comprobantes/';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                $ext = pathinfo($_FILES['comprobante']['name'], PATHINFO_EXTENSION);
                $filename = 'comp_' . time() . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['comprobante']['tmp_name'], $dir . $filename);
                $comprobante_path = '/uploads/comprobantes/' . $filename;
            }

            $estado = 'aprobado';
            if ($_POST['tipo'] === 'Egreso' && isset($_POST['es_traspaso_grupo'])) {
                $estado = 'pendiente';
            }

            $finanzasModel = new Finanzas();
            $finanzasModel->registrarMovimiento([
                'unidad_id' => $unidad_id,
                'anio' => $anio,
                'fecha' => $_POST['fecha'],
                'tipo' => $_POST['tipo'],
                'monto' => $_POST['monto'],
                'descripcion' => $_POST['descripcion'],
                'beneficiario_id' => !empty($_POST['beneficiario_id']) ? $_POST['beneficiario_id'] : null,
                'comprobante_archivo' => $comprobante_path,
                'justificacion' => $_POST['justificacion'] ?? null,
                'estado' => $estado
            ]);
            $this->redirect("/unidades/$unidad_id/finanzas");
        }
    }

    public function pagarCuota($unidad_id) {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad']);
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $this->checkYearStatus($anio, $unidad_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $finanzasModel = new Finanzas();
            $finanzasModel->registrarPagoCuota([
                'unidad_id' => $unidad_id,
                'anio' => $anio,
                'beneficiario_id' => $_POST['beneficiario_id'],
                'mes' => $_POST['mes'],
                'monto' => $_POST['monto']
            ]);
            $this->redirect("/unidades/$unidad_id/finanzas");
        }
    }
}
