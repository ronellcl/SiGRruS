<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\FichaMedica;
use App\Models\Beneficiario;

class FichasController extends Controller {
    
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

        $unidadModel = new \App\Models\Unidad();
        $unidad = $unidadModel->findById($unidad_id);

        $beneficiarioModel = new Beneficiario();
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $beneficiarios = $beneficiarioModel->findByUnidad($unidad_id, $anio);

        // Traer fichas para ver quién ha actualizado
        $fichaModel = new FichaMedica();
        $fichas = [];
        foreach ($beneficiarios as $b) {
            $fichas[$b['id']] = $fichaModel->getByBeneficiario($b['id']);
        }

        $this->view('ficha-medica/index', [
            'title' => 'Fichas Médicas - ' . $unidad['nombre'],
            'unidad' => $unidad,
            'beneficiarios' => $beneficiarios,
            'fichas' => $fichas,
            'user' => $user
        ]);
    }
    public function editar($beneficiario_id) {
        // Verificar que sea el apoderado del niño o un dirigente con permiso
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad', 'Apoderado']);
        $user = Auth::user();

        $beneficiarioModel = new Beneficiario();
        $beneficiario = $beneficiarioModel->findById($beneficiario_id);

        // Si es apoderado, verificar vínculo
        if ($user['rol'] === 'Apoderado' && $beneficiario['apoderado_id'] != $user['apoderado_id']) {
            $this->redirect('/dashboard');
        }

        $fichaModel = new FichaMedica();
        $ficha = $fichaModel->getByBeneficiario($beneficiario_id);

        $this->view('ficha-medica/editar', [
            'title' => 'Ficha Médica - ' . $beneficiario['nombre_completo'],
            'ficha' => $ficha,
            'beneficiario' => $beneficiario,
            'user' => $user
        ]);
    }

    public function guardar() {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad', 'Apoderado']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = Auth::user();
            
            // Validación estricta para el Apoderado antes de guardar
            if ($user['rol'] === 'Apoderado') {
                $benefModel = new Beneficiario();
                $benef = $benefModel->findById($_POST['beneficiario_id']);
                if (!$benef || $benef['apoderado_id'] != $user['apoderado_id']) {
                    $this->show403("No puedes guardar información médica de un beneficiario que no es tu hijo/pupilo.");
                }
            }

            // Validación para Dirigentes de Unidad
            if (in_array($user['rol'], ['Responsable de Unidad', 'Asistente de Unidad'])) {
                $benefModel = new Beneficiario();
                $benef = $benefModel->getDetails($_POST['beneficiario_id']);
                if (!$benef || $benef['unidad_id'] != $user['unidad_id']) {
                    $this->show403("No tienes permisos para gestionar fichas médicas de beneficiarios fuera de tu unidad.");
                }
            }

            $fichaModel = new FichaMedica();
            $data = [
                'beneficiario_id' => $_POST['beneficiario_id'],
                'tipo_sangre' => $_POST['tipo_sangre'],
                'alergias' => $_POST['alergias'],
                'enfermedades_cronicas' => $_POST['enfermedades_cronicas'],
                'medicamentos' => $_POST['medicamentos'],
                'prevision_salud' => $_POST['prevision_salud'],
                'restricciones_alimenticias' => $_POST['restricciones_alimenticias'],
                'vacunas_al_dia' => isset($_POST['vacunas_al_dia']) ? 1 : 0,
                'observaciones_medicas' => $_POST['observaciones_medicas'],
                'creado_por_usuario_id' => ($user['rol'] !== 'Apoderado') ? $user['id'] : null
            ];

            $fichaModel->save($data);

            // Redirigir según el rol
            if ($user['rol'] === 'Apoderado') {
                $this->redirect('/dashboard');
            } else {
                $this->redirect('/beneficiarios/ver/' . $_POST['beneficiario_id']);
            }
        }
    }

    public function ver($beneficiario_id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo', 'Responsable de Unidad', 'Asistente de Unidad']);
        $fichaModel = new FichaMedica();
        $ficha = $fichaModel->getByBeneficiario($beneficiario_id);
        
        $beneficiarioModel = new Beneficiario();
        $beneficiario = $beneficiarioModel->findById($beneficiario_id);

        $this->view('ficha-medica/ver', [
            'title' => 'Ficha Médica - ' . $beneficiario['nombre_completo'],
            'ficha' => $ficha,
            'beneficiario' => $beneficiario
        ]);
    }

    public function grupo() {
        Auth::requireRole(['Superusuario']);
        $user = Auth::user();

        $beneficiarioModel = new Beneficiario();
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $beneficiarios = $beneficiarioModel->findAll(); // O filtrar por año si es necesario

        $fichaModel = new FichaMedica();
        $fichas = [];
        foreach ($beneficiarios as $b) {
            $fichas[$b['id']] = $fichaModel->getByBeneficiario($b['id']);
        }

        $this->view('grupo/fichas', [
            'title' => 'Fichas Médicas Globales',
            'beneficiarios' => $beneficiarios,
            'fichas' => $fichas,
            'user' => $user
        ]);
    }
}
