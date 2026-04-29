<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Usuario;
use App\Models\Unidad;

class DirigentesController extends Controller {
    
    public function index() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo']);
        
        $user = Auth::user();
        $anioScout = $_SESSION['anio_scout'] ?? date('Y');
        
        $usuarioModel = new Usuario();
        $dirigentes = $usuarioModel->getAllWithUnidad($anioScout);

        // Obtener el estado de los certificados para el año actual para cada dirigente
        $certificadoModel = new \App\Models\Certificado();
        
        foreach ($dirigentes as &$d) {
            $certs = $certificadoModel->getByUsuarioYear($d['id'], $anioScout);
            $d['cert_habilidad'] = null;
            $d['cert_inhabilidades'] = null;
            
            foreach ($certs as $c) {
                if ($c['tipo'] === 'Habilidad') $d['cert_habilidad'] = $c['archivo_path'];
                if ($c['tipo'] === 'Inhabilidades') $d['cert_inhabilidades'] = $c['archivo_path'];
            }
        }

        $unidadModel = new Unidad();
        $unidades = $unidadModel->getAll();

        $this->view('dirigentes/index', [
            'title' => 'Equipo de Dirigentes',
            'dirigentes' => $dirigentes,
            'unidades' => $unidades,
            'anio_actual' => $anioScout,
            'user' => $user
        ]);
    }

    public function crear() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo']);
        $user = Auth::user();
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $this->checkYearStatus($anio);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rolSeleccionado = $_POST['rol'] ?? '';
            
            // RBAC: Validación estricta según el rol de quien crea
            if ($user['rol'] !== 'Superusuario') {
                $rolesPermitidos = ['Responsable de Unidad', 'Asistente de Unidad'];
                if (!in_array($rolSeleccionado, $rolesPermitidos)) {
                    $this->show403("No tienes permisos para crear un usuario con el rol de $rolSeleccionado.");
                }
            }

            $tipo_doc = $_POST['tipo_documento'] === 'Otro' ? $_POST['tipo_documento_otro'] : $_POST['tipo_documento'];

            $plainPassword = $_POST['password'] ?? '';
            $mustChange = 0;

            if (empty($plainPassword)) {
                $plainPassword = bin2hex(random_bytes(4)); // 8 caracteres
                $mustChange = 1;
            }

            $data = [
                'nombre' => $_POST['nombre'] ?? '',
                'rut' => $_POST['rut'] ?? '',
                'tipo_documento' => $tipo_doc ?? 'RUT',
                'nacionalidad' => $_POST['nacionalidad'] ?? 'Chilena',
                'email' => $_POST['email'] ?? '',
                'password' => $plainPassword,
                'must_change_password' => $mustChange,
                'rol' => $rolSeleccionado,
                'unidad_id' => ($_POST['unidad_id'] !== '' && $_POST['unidad_id'] !== null) ? $_POST['unidad_id'] : null,
                'anio' => $_SESSION['anio_scout'] ?? date('Y')
            ];

            $usuarioModel = new Usuario();
            if ($usuarioModel->create($data)) {
                // Si se generó contraseña, enviar correo
                if ($mustChange && !empty($data['email'])) {
                    \App\Core\Email::sendWelcome($data, $plainPassword);
                }
            }
            
            $this->redirect('/grupo/dirigentes');
        }
    }

    public function subirCertificado() {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo']);
        $anio = $_POST['anio'] ?? date('Y');
        $this->checkYearStatus($anio);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['certificado']) && isset($_POST['usuario_id']) && isset($_POST['anio']) && isset($_POST['tipo'])) {
            $usuario_id = $_POST['usuario_id'];
            $anio = (int)$_POST['anio'];
            $tipo = $_POST['tipo'];
            
            $storage = new \App\DAL\Storage();
            // Guardar en carpeta organizada por año y tipo
            $path = $storage->save($_FILES['certificado'], 'certificados/' . $anio . '/' . strtolower($tipo));
            
            if ($path) {
                $certificadoModel = new \App\Models\Certificado();
                $certificadoModel->saveCertificate($usuario_id, $anio, $tipo, $path);
            }
        }
        $this->redirect('/grupo/dirigentes');
    }
    public function eliminar($id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo']);
        $this->checkYearStatus($_SESSION['anio_scout'] ?? date('Y'));
        
        $usuarioModel = new Usuario();
        $usuarioModel->delete($id);
        
        $this->redirect('/grupo/dirigentes');
    }
}
