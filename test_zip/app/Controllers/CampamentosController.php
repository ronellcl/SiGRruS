<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Campamento;
use App\Models\Unidad;
use App\Models\CicloPrograma;

class CampamentosController extends Controller {
    
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
        $campModel = new Campamento();
        $campamentos = $campModel->getByUnidad($unidad_id, $anio);
        
        $unidadModel = new Unidad();
        $unidad = $unidadModel->findById($unidad_id);

        $cicloModel = new CicloPrograma();
        $actividades = $cicloModel->getByUnidad($unidad_id, $anio);

        $this->view('campamentos/index', [
            'title' => 'Campamentos - ' . $unidad['nombre'],
            'unidad' => $unidad,
            'campamentos' => $campamentos,
            'actividades' => $actividades,
            'user' => $user
        ]);
    }

    public function grupo() {
        Auth::requireRole(['Superusuario']);
        $user = Auth::user();
        $anio = $_SESSION['anio_scout'] ?? date('Y');

        $campModel = new Campamento();
        $campamentos = $campModel->getGlobal($anio);

        $this->view('grupo/campamentos', [
            'title' => 'Campamentos de Grupo',
            'campamentos' => $campamentos,
            'user' => $user
        ]);
    }

    public function guardar($unidad_id = null) {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $campModel = new Campamento();
            $data = [
                'nombre' => $_POST['nombre'],
                'tipo' => $_POST['tipo'],
                'unidad_id' => !empty($_POST['unidad_id']) ? $_POST['unidad_id'] : $unidad_id,
                'anio' => $_SESSION['anio_scout'] ?? date('Y'),
                'fecha_inicio' => $_POST['fecha_inicio'],
                'fecha_fin' => $_POST['fecha_fin'],
                'lugar' => $_POST['lugar'],
                'costo_cuota' => $_POST['costo_cuota'],
                'objetivos' => $_POST['objetivos'],
                'programa_resumen' => $_POST['programa_resumen'],
                'ciclo_actividad_id' => $_POST['ciclo_actividad_id'] ?: null
            ];

            $campModel->create($data);

            if ($unidad_id) {
                $this->redirect("/unidades/$unidad_id/campamentos");
            } else {
                $this->redirect("/grupo/campamentos");
            }
        }
    }

    public function ver($id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo', 'Responsable de Unidad', 'Asistente de Unidad', 'Apoderado']);
        $user = Auth::user();

        $campModel = new Campamento();
        $camp = $campModel->findById($id);
        $participantes = $campModel->getParticipantes($id);

        $this->view('campamentos/ver', [
            'title' => 'Detalle de Campamento: ' . $camp['nombre'],
            'camp' => $camp,
            'participantes' => $participantes,
            'user' => $user
        ]);
    }

    public function autorizar($id) {
        Auth::requireRole(['Apoderado']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $campModel = new Campamento();
            $benef_id = $_POST['beneficiario_id'];
            $obs = $_POST['observaciones_apoderado'] ?? '';
            
            $campModel->registrarAutorizacion($id, $benef_id, $obs);
            $this->redirect('/dashboard');
        }
    }
}
