<?php
namespace App\Core;

abstract class Controller {
    /**
     * Cargar una vista y pasarle datos
     */
    protected function view($viewName, $data = []) {
        // Inyectar años disponibles globalmente para el Navbar
        $yearManager = new \App\Models\YearManager();
        $data['available_years'] = $yearManager->getAvailableYears();
        
        $configModel = new \App\Models\ConfigGrupo();
        $data['global_config'] = $configModel->getConfig();
        
        // Extraer variables para que estén disponibles en la vista
        extract($data);
        
        $viewFile = APP_PATH . '/Views/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            ob_start();
            require $viewFile;
            $output = ob_get_clean();
            
            $base = defined('APP_URL') ? APP_URL : '';
            if ($base) {
                // Rewrite all root-relative href, src, and action attributes to include APP_URL
                // pero evitando duplicar si ya empieza con la base (ej: /sigrrus/sigrrus)
                $quotedBase = preg_quote(ltrim($base, '/'), '/');
                $output = preg_replace('/(href|src|action)=["\']\/(?!' . $quotedBase . ')(?![\/])/i', '$1="' . $base . '/', $output);
            }
            echo $output;
        } else {
            die("La vista '$viewName' no existe.");
        }
    }

    /**
     * Redirigir a otra URL dentro de la app
     */
    protected function redirect($url) {
        if (strpos($url, 'http') === 0) {
            header("Location: " . $url);
        } else {
            $base = defined('APP_URL') ? APP_URL : '';
            header("Location: " . $base . '/' . ltrim($url, '/'));
        }
        exit;
    }
    
    protected function checkYearStatus($anio, $unidad_id = null) {
        $yearManager = new \App\Models\YearManager();
        if ($yearManager->isClosed($anio)) {
            $user = Auth::user();
            if ($user['rol'] !== 'Superusuario') {
                http_response_code(403);
                die("Error: El año scout $anio está CERRADO. No se permiten modificaciones.");
            } else {
                // Es superusuario, permitir pero notificar
                if ($unidad_id) {
                    $notifModel = new \App\Models\Notification();
                    // Buscar al responsable de esa unidad
                    $db = \App\DAL\Database::getInstance()->getConnection();
                    $stmt = $db->prepare("SELECT usuario_id FROM dirigente_inscripcion WHERE unidad_id = ? AND rol = 'Responsable de Unidad' AND (fecha_inicio <= ? AND (fecha_fin IS NULL OR fecha_fin >= ?))");
                    $today = date('Y-m-d');
                    $stmt->execute([$unidad_id, $today, $today]);
                    $responsable = $stmt->fetchColumn();
                    
                    if ($responsable) {
                        $msg = "El Superusuario {$user['nombre']} ha realizado una modificación en el año CERRADO $anio para tu unidad.";
                        $notifModel->send($responsable, $msg);
                    }
                }
            }
        }
    }

    /**
     * Responder en formato JSON (útil para AJAX/API)
     */
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Responder en formato JSON (útil para AJAX/API)
     */
    protected function show403($message = null) {
        http_response_code(403);
        $message = $message ?? "No tienes permisos para acceder a esta sección con tu perfil actual.";
        require_once dirname(__DIR__) . '/Views/errors/403.php';
        exit;
    }
}
