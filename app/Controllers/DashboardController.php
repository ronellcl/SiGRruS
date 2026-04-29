<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\UpdateManager;
use App\Models\Notification;
use App\Core\Email;

class DashboardController extends Controller {
    public function index() {
        // Verificar que el usuario esté logueado
        if (!Auth::check()) {
            $this->redirect('/auth/login');
        }

        $user = Auth::user();
        $anioScout = $_SESSION['anio_scout'] ?? date('Y');

        $notifModel = new Notification();
        $notificaciones = $notifModel->getByUser($user['id']);
        $notifUnread = $notifModel->getUnreadCount($user['id']);

        // --- Verificación de Actualizaciones para Superusuarios ---
        if ($user['rol'] === 'Superusuario') {
            $lastCheck = $_SESSION['last_update_check'] ?? 0;
            if (time() - $lastCheck > 3600) { // Una vez por hora
                $updateModel = new UpdateManager();
                $updateInfo = $updateModel->checkUpdates();
                
                if ($updateInfo && $updateInfo['has_update']) {
                    // Evitar duplicar notificaciones si ya se avisó en esta sesión
                    if (($_SESSION['update_notified_version'] ?? '') !== $updateInfo['latest']) {
                        $msg = "🚀 ¡Nueva actualización disponible (v{$updateInfo['latest']})! Revisa el Panel Técnico para instalarla.";
                        $notifModel->send($user['id'], $msg);
                        
                        // Enviar correo también
                        Email::send($user['email'], "Actualización de SiGRruS disponible (v{$updateInfo['latest']})", "
                            <h2>¡Hola!</h2>
                            <p>Se ha detectado una nueva versión de SiGRruS en GitHub: <strong>v{$updateInfo['latest']}</strong>.</p>
                            <p>Puedes revisarla e instalarla directamente desde el Panel de Control Técnico de tu plataforma.</p>
                            <p>Recuerda que el sistema realizará un respaldo completo antes de proceder.</p>
                        ");
                        
                        $_SESSION['update_notified_version'] = $updateInfo['latest'];
                    }
                }
                $_SESSION['last_update_check'] = time();
            }
        }
        // ---------------------------------------------------------

        $yearManager = new \App\Models\YearManager();
        $yearClosed = $yearManager->isClosed($anioScout);

        $anioData = $yearManager->getByAnio($anioScout);
        $valorInscripcion = $anioData['valor_inscripcion'] ?? 0;

        $beneficiarios = [];
        if ($user['rol'] === 'Apoderado') {
            $benefModel = new \App\Models\Beneficiario();
            $beneficiarios = $benefModel->getByApoderado($user['apoderado_id']);
        }

        $userModel = new \App\Models\Usuario();
        $unitStaff = $userModel->getStaffByUnidad($anioScout);

        $this->view('dashboard/index', [
            'title' => 'Panel de Control - SiGRruS',
            'user' => $user,
            'anio_scout' => $anioScout,
            'notificaciones' => $notificaciones,
            'notif_unread' => $notifUnread,
            'year_closed' => $yearClosed,
            'valor_inscripcion' => $valorInscripcion,
            'hijos' => $beneficiarios,
            'unit_staff' => $unitStaff
        ]);
    }

    public function setValorInscripcion() {
        Auth::requireRole(['Superusuario']);
        $anio = $_POST['anio'];
        $monto = $_POST['monto'];
        $yearManager = new \App\Models\YearManager();
        $yearManager->setValorInscripcion($anio, $monto);
        $this->redirect('/dashboard');
    }

    public function cerrarAnio() {
        Auth::requireRole('Superusuario');
        $anio = $_POST['anio'] ?? date('Y');
        $yearManager = new \App\Models\YearManager();
        $yearManager->close($anio);
        $this->redirect('/dashboard');
    }

    public function abrirAnio() {
        Auth::requireRole('Superusuario');
        $anio = $_POST['anio'] ?? date('Y');
        $yearManager = new \App\Models\YearManager();
        $yearManager->reopen($anio);
        $this->redirect('/dashboard');
    }

    public function crearAnio() {
        Auth::requireRole('Superusuario');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $anio = $_POST['anio_nuevo'] ?? '';
            if (!empty($anio)) {
                $yearManager = new \App\Models\YearManager();
                $yearManager->create($anio);
            }
        }
        $this->redirect('/dashboard');
    }

    public function leerNotificacion($id) {
        $notifModel = new Notification();
        $notifModel->markAsRead($id);
        $this->redirect('/dashboard');
    }

    public function campamentos($beneficiario_id) {
        Auth::requireRole(['Apoderado']);
        $user = Auth::user();
        
        $benefModel = new \App\Models\Beneficiario();
        $benef = $benefModel->getDetails($beneficiario_id);
        
        // Seguridad
        if ($benef['apoderado_id'] != $user['apoderado_id']) $this->redirect('/dashboard');

        $campModel = new \App\Models\Campamento();
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $campamentos = $campModel->getByUnidad($benef['unidad_id'], $anio);

        $this->view('dashboard/hijo_campamentos', [
            'title' => 'Campamentos para ' . $benef['nombre_completo'],
            'beneficiario' => $benef,
            'campamentos' => $campamentos,
            'user' => $user
        ]);
    }

    public function hojasRuta($beneficiario_id) {
        Auth::requireRole(['Apoderado']);
        $user = Auth::user();
        $benefModel = new \App\Models\Beneficiario();
        $benef = $benefModel->getDetails($beneficiario_id);
        if ($benef['apoderado_id'] != $user['apoderado_id']) $this->redirect('/dashboard');

        $cicloModel = new \App\Models\CicloPrograma();
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        $actividades = $cicloModel->getByUnidad($benef['unidad_id'], $anio);

        $this->view('dashboard/hijo_hojas_ruta', [
            'title' => 'Programa de Actividades: ' . $benef['nombre_completo'],
            'beneficiario' => $benef,
            'actividades' => $actividades
        ]);
    }

    public function asistencias($beneficiario_id) {
        Auth::requireRole(['Apoderado']);
        $user = Auth::user();
        $benefModel = new \App\Models\Beneficiario();
        $benef = $benefModel->getDetails($beneficiario_id);
        if ($benef['apoderado_id'] != $user['apoderado_id']) $this->redirect('/dashboard');

        $asistModel = new \App\Models\Asistencia();
        $asistencias = $asistModel->getByBeneficiario($beneficiario_id);

        $this->view('dashboard/hijo_asistencias', [
            'title' => 'Registro de Asistencia: ' . $benef['nombre_completo'],
            'beneficiario' => $benef,
            'asistencias' => $asistencias
        ]);
    }

    public function finanzas($beneficiario_id) {
        Auth::requireRole(['Apoderado']);
        $user = Auth::user();
        $benefModel = new \App\Models\Beneficiario();
        $benef = $benefModel->getDetails($beneficiario_id);
        if ($benef['apoderado_id'] != $user['apoderado_id']) $this->redirect('/dashboard');

        $finanzasModel = new \App\Models\Finanzas();
        $anio = $_SESSION['anio_scout'] ?? date('Y');
        
        $cuotas = $finanzasModel->getPagosByBeneficiario($beneficiario_id, $anio);
        
        $this->view('dashboard/hijo_finanzas', [
            'title' => 'Finanzas: ' . $benef['nombre_completo'],
            'beneficiario' => $benef,
            'cuotas' => $cuotas
        ]);
    }
}
