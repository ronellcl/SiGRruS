<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\CicloPrograma;
use App\Models\Unidad;

class CicloController extends Controller {
    
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

        $anioScout = $_SESSION['anio_scout'] ?? date('Y');
        
        $cicloModel = new CicloPrograma();
        $actividades = $cicloModel->getByUnidad($unidad_id, $anioScout);
        
        $unidadModel = new Unidad();
        $unidad = $unidadModel->findById($unidad_id);

        $this->view('ciclo/index', [
            'title' => 'Ciclo de Programa - ' . $unidad['nombre'],
            'unidad' => $unidad,
            'actividades' => $actividades,
            'anio_actual' => $anioScout,
            'user' => $user
        ]);
    }

    public function crear($unidad_id) {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad']);
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $this->checkYearStatus($anio, $unidad_id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $actividades = $_POST['actividades'] ?? [];
            $cicloModel = new CicloPrograma();

            $anyCamp = false;
            foreach ($actividades as $act) {
                if (!empty($act['nombre_actividad'])) {
                    $actData = [
                        'unidad_id' => $unidad_id,
                        'nombre_actividad' => $act['nombre_actividad'],
                        'fecha' => $act['fecha'],
                        'lugar' => $act['lugar'],
                        'es_campamento' => isset($act['es_campamento']) ? 1 : 0
                    ];
                    $cicloModel->create($actData);
                    if ($actData['es_campamento']) $anyCamp = true;
                }
            }

            if ($anyCamp) {
                // Notificar a apoderados
                $unidadModel = new \App\Models\Unidad();
                $apoderados = $unidadModel->getApoderadosByUnidad($unidad_id);
                $notifModel = new \App\Models\Notification();
                
                foreach ($apoderados as $ap) {
                    $notifModel->create([
                        'usuario_id' => $ap['usuario_id'],
                        'mensaje' => "🏕️ Se ha programado un nuevo campamento. Por favor, actualice la Ficha Médica de su hijo/a para asegurar que contamos con la información de salud vigente."
                    ]);
                }
            }
            
            $this->redirect("/unidades/$unidad_id/ciclo");
        }
    }
}
