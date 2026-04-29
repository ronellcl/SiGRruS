<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Asistencia;
use App\Models\Beneficiario;
use App\Models\CicloPrograma;
use App\Models\Unidad;

class AsistenciasController extends Controller {
    
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
        $cicloModel = new CicloPrograma();
        $actividades = $cicloModel->getByUnidad($unidad_id, $anio);
        
        $unidadModel = new Unidad();
        $unidad = $unidadModel->findById($unidad_id);

        $this->view('asistencia/index', [
            'title' => 'Asistencias - ' . $unidad['nombre'],
            'unidad' => $unidad,
            'actividades' => $actividades,
            'user' => $user,
            'anio_actual' => $anio
        ]);
    }

    public function registrar($unidad_id, $actividad_id) {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad']);
        $user = Auth::user();
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $this->checkYearStatus($anio, $unidad_id);

        $asistenciaModel = new Asistencia();
        $beneficiarioModel = new Beneficiario();
        $unidadModel = new Unidad();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $asistencias = $_POST['asistencia'] ?? [];
            $asistenciaModel->save($actividad_id, $asistencias);
            $this->redirect("/unidades/$unidad_id/asistencias");
        }

        $db = \App\DAL\Database::getInstance()->getConnection();
        $actividad = $db->query("SELECT * FROM ciclo_programa WHERE id = $actividad_id")->fetch();
        $beneficiarios = $beneficiarioModel->findByUnidad($unidad_id, $anio);
        $asistenciaActual = $asistenciaModel->getByActividad($actividad_id);
        $unidad = $unidadModel->findById($unidad_id);

        $this->view('asistencia/registrar', [
            'title' => 'Tomar Asistencia - ' . $actividad['nombre_actividad'],
            'unidad' => $unidad,
            'actividad' => $actividad,
            'beneficiarios' => $beneficiarios,
            'asistenciaActual' => $asistenciaActual,
            'user' => $user
        ]);
    }

    public function crearExtra($unidad_id) {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad']);
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $this->checkYearStatus($anio, $unidad_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cicloModel = new CicloPrograma();
            $cicloModel->create([
                'unidad_id' => $unidad_id,
                'nombre_actividad' => $_POST['nombre_actividad'],
                'fecha' => $_POST['fecha'],
                'lugar' => $_POST['lugar'],
                'es_extra' => 1
            ]);
            $this->redirect("/unidades/$unidad_id/asistencias");
        }
    }
}
