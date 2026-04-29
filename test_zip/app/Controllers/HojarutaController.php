<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\HojaRuta;
use App\Models\CicloPrograma;
use App\Models\Unidad;

class HojarutaController extends Controller {
    
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

        $hojaModel = new HojaRuta();
        $hojas = $hojaModel->getByUnidad($unidad_id, $anio);

        $unidadModel = new Unidad();
        $unidad = $unidadModel->findById($unidad_id);

        $this->view('hoja-ruta/index', [
            'title' => 'Hojas de Ruta - ' . $unidad['nombre'],
            'unidad' => $unidad,
            'hojas' => $hojas,
            'user' => $user,
            'anio_actual' => $anio
        ]);
    }

    public function ver($unidad_id, $id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo', 'Responsable de Unidad', 'Asistente de Unidad']);
        
        $hojaModel = new HojaRuta();
        $hoja = $hojaModel->findById($id);
        
        if (!$hoja) {
            $this->redirect("/unidades/$unidad_id/hoja-ruta");
        }

        $unidadModel = new Unidad();
        $unidad = $unidadModel->findById($unidad_id);

        $this->view('hoja-ruta/ver', [
            'title' => 'Hoja de Ruta',
            'unidad' => $unidad,
            'hoja' => $hoja,
            'user' => Auth::user()
        ]);
    }

    public function crear($unidad_id) {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad']);
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $this->checkYearStatus($anio, $unidad_id);

        $cicloModel = new CicloPrograma();
        $actividadesCycle = $cicloModel->getByUnidad($unidad_id, $anio);

        $unidadModel = new Unidad();
        $unidad = $unidadModel->findById($unidad_id);

        $this->view('hoja-ruta/crear', [
            'title' => 'Nueva Hoja de Ruta',
            'unidad' => $unidad,
            'actividadesCycle' => $actividadesCycle,
            'user' => Auth::user()
        ]);
    }

    public function editar($unidad_id, $id = null) {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad']);
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $this->checkYearStatus($anio, $unidad_id);
        
        $hojaModel = new HojaRuta();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['unidad_id'] = $unidad_id;
            $data['anio'] = $anio;
            $data['id'] = $id;
            $hojaModel->save($data);
            
            // Si venía de una actividad del ciclo, el ID de la hoja se recupera
            $this->redirect("/unidades/$unidad_id/hoja-ruta");
        }

        $hoja = $id ? $hojaModel->findById($id) : null;
        
        // Si no hay ID pero viene actividad_id por GET (vínculo desde ciclo)
        if (!$hoja && isset($_GET['actividad_id'])) {
            $hoja = $hojaModel->getByActividad($_GET['actividad_id']);
            if (!$hoja) {
                $hoja = ['actividad_id' => $_GET['actividad_id']];
            }
        }

        $unidadModel = new Unidad();
        $unidad = $unidadModel->findById($unidad_id);

        $this->view('hoja-ruta/editar', [
            'title' => 'Editar Hoja de Ruta',
            'unidad' => $unidad,
            'hoja' => $hoja,
            'user' => Auth::user()
        ]);
    }
}
