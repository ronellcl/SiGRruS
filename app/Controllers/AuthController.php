<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

class AuthController extends Controller {
    public function login() {
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }

        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            $userModel = new Usuario();
            $anioScout = $_SESSION['anio_scout'] ?? date('Y');
            $user = $userModel->findByEmail($email, $anioScout);

            if ($user && $userModel->verifyPassword($password, $user['password'])) {
                // Iniciar sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nombre'] = $user['nombre'];
                $_SESSION['user_rol'] = $user['rol'];
                $_SESSION['user_roles'] = $user['roles'] ?? [$user['rol']];
                $_SESSION['user_unidad'] = $user['unidad_id'];
                $_SESSION['anio_scout'] = $anioScout;
                
                // Detectar si también es apoderado
                $apoModel = new \App\Models\Apoderado();
                $apoderado = $apoModel->findByRut($user['rut']);
                if ($apoderado) {
                    $_SESSION['can_switch'] = true;
                    $_SESSION['apoderado_id'] = $apoderado['id'];
                    if (!in_array('Apoderado', $_SESSION['user_roles'])) {
                        $_SESSION['user_roles'][] = 'Apoderado';
                    }
                } else {
                    $_SESSION['can_switch'] = count($_SESSION['user_roles']) > 1;
                }
                
                $_SESSION['active_rol'] = $_SESSION['user_rol'];

                // REGLA: Forzar cambio de contraseña si es nueva
                if ($user['must_change_password']) {
                    $_SESSION['must_change'] = true;
                    $this->redirect('/auth/changePassword');
                }

                $this->redirect('/dashboard');
            } else {
                $error = "Credenciales incorrectas.";
            }
        }

        $this->view('auth/login', ['title' => 'Iniciar Sesión', 'error' => $error]);
    }

    public function forgotPassword() {
        $this->view('auth/forgot_password', ['title' => 'Recuperar Contraseña']);
    }

    public function sendResetLink() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $userModel = new Usuario();
            $db = \App\DAL\Database::getInstance()->getConnection();
            
            // Generar Token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token, $expires]);
            
            \App\Core\Email::sendResetLink($email, $token);
            
            $this->view('auth/forgot_password', [
                'title' => 'Recuperar Contraseña',
                'success' => 'Si el correo existe en nuestro sistema, recibirás un enlace de recuperación pronto.'
            ]);
        }
    }

    public function resetPassword($token) {
        $db = \App\DAL\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1");
        $stmt->execute([$token]);
        $reset = $stmt->fetch();

        if (!$reset) {
            die("Token inválido o expirado.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pass = $_POST['password'] ?? '';
            $userModel = new Usuario();
            
            // Actualizar contraseña del usuario
            $stmtUpdate = $db->prepare("UPDATE usuarios SET password = ?, must_change_password = 0 WHERE email = ?");
            $stmtUpdate->execute([password_hash($pass, PASSWORD_DEFAULT), $reset['email']]);
            
            // Borrar token
            $db->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$reset['email']]);
            
            $this->redirect('/auth/login?msg=password_updated');
        }

        $this->view('auth/reset_password', ['title' => 'Nueva Contraseña', 'token' => $token]);
    }

    public function changePassword() {
        if (!isset($_SESSION['user_id'])) $this->redirect('/auth/login');
        
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pass = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if (strlen($pass) < 6) {
                $error = "La contraseña debe tener al menos 6 caracteres.";
            } elseif ($pass !== $confirm) {
                $error = "Las contraseñas no coinciden.";
            } else {
                $db = \App\DAL\Database::getInstance()->getConnection();
                $stmt = $db->prepare("UPDATE usuarios SET password = ?, must_change_password = 0 WHERE id = ?");
                $stmt->execute([password_hash($pass, PASSWORD_DEFAULT), $_SESSION['user_id']]);
                
                unset($_SESSION['must_change']);
                $this->redirect('/dashboard');
            }
        }

        $this->view('auth/change_password', ['title' => 'Cambiar Contraseña', 'error' => $error]);
    }


    public function switchRole($newRole = null) {
        if (isset($_GET['debug_stop'])) {
            die("SwitchRole reached! Role parameter: '$newRole'");
        }
        $logFile = __DIR__ . '/../../switch_debug.log';
        $log = date('Y-m-d H:i:s') . " - Attempting switch to: '$newRole'\n";
        $log .= "Current User ID: " . ($_SESSION['user_id'] ?? 'NONE') . "\n";
        $log .= "Available Roles: " . implode(', ', $_SESSION['user_roles'] ?? []) . "\n";

        if (!isset($_SESSION['user_id'])) {
            $log .= "FAILED: No user_id in session\n";
            file_put_contents($logFile, $log, FILE_APPEND);
            $this->redirect('/dashboard');
        }

        if ($newRole) {
            $newRole = urldecode($newRole);
            $log .= "Decoded Role: '$newRole'\n";
            
            if (in_array($newRole, $_SESSION['user_roles'])) {
                $_SESSION['active_rol'] = $newRole;
                $log .= "SUCCESS: active_rol updated to $newRole\n";
                
                if ($newRole !== 'Apoderado' && $newRole !== 'Superusuario') {
                    $db = \App\DAL\Database::getInstance()->getConnection();
                    $stmt = $db->prepare("
                        SELECT unidad_id 
                        FROM dirigente_inscripcion 
                        WHERE usuario_id = ? AND rol = ? 
                          AND (fecha_inicio <= ? AND (fecha_fin IS NULL OR fecha_fin >= ?))
                        LIMIT 1
                    ");
                    $anio = $_SESSION['anio_scout'] ?? date('Y');
                    $stmt->execute([
                        $_SESSION['user_id'], 
                        $newRole, 
                        $anio . '-12-31', 
                        $anio . '-01-01'
                    ]);
                    $res = $stmt->fetch();
                    if ($res) {
                        $_SESSION['user_unidad'] = $res['unidad_id'];
                        $log .= "SUCCESS: user_unidad updated to " . $res['unidad_id'] . "\n";
                    } else {
                        $_SESSION['user_unidad'] = null;
                        $log .= "NOTICE: No unit found for this role/year\n";
                    }
                } else {
                    $log .= "NOTICE: Role is Apoderado or Superusuario, no unit_id update needed\n";
                }
            } else {
                $log .= "FAILED: Role '$newRole' not found in user_roles\n";
            }
        } else {
            // Comportamiento antiguo de toggle si no se envía rol
            $currentActive = $_SESSION['active_rol'] ?? $_SESSION['user_rol'];
            if ($currentActive === 'Apoderado') {
                $_SESSION['active_rol'] = $_SESSION['user_rol'];
            } else {
                $_SESSION['active_rol'] = 'Apoderado';
            }
        }

        $this->redirect('/dashboard');
    }

    public function logout() {
        session_destroy();
        $this->redirect('/');
    }

    public function setYear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['anio_scout'])) {
            $_SESSION['anio_scout'] = (int) $_POST['anio_scout'];
            // Actualizar el rol en base al nuevo año
            if (isset($_SESSION['user_id'])) {
                $userModel = new Usuario();
                $user = $userModel->findById($_SESSION['user_id'], $_SESSION['anio_scout']);
                if ($user) {
                    $_SESSION['user_rol'] = $user['rol'];
                    $_SESSION['user_unidad'] = $user['unidad_id'];
                }
            }
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? '/dashboard';
        $this->redirect($referer);
    }
}
