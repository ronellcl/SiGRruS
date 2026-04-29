<?php
namespace App\Core;

class Auth {
    public static function check() {
        return isset($_SESSION['user_id']);
    }

    public static function isReadOnly() {
        $user = self::user();
        if (!$user) return true;
        $readOnlyRoles = ['Responsable de Grupo', 'Asistente de Grupo'];
        return in_array($user['rol'], $readOnlyRoles);
    }

    public static function user() {
        if (self::check()) {
            return [
                'id' => $_SESSION['user_id'],
                'nombre' => $_SESSION['user_nombre'],
                'rol' => $_SESSION['active_rol'] ?? $_SESSION['user_rol'],
                'rol_base' => $_SESSION['user_rol'],
                'roles_disponibles' => $_SESSION['user_roles'] ?? [$_SESSION['user_rol']],
                'unidad_id' => $_SESSION['user_unidad'],
                'can_switch' => $_SESSION['can_switch'] ?? false,
                'apoderado_id' => $_SESSION['apoderado_id'] ?? null
            ];
        }
        return null;
    }

    public static function requireRole($roles) {
        if (!self::check()) {
            header("Location: /auth/login");
            exit;
        }

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $activeRol = $_SESSION['active_rol'] ?? $_SESSION['user_rol'];

        // El Superusuario siempre tiene acceso, a menos que esté en modo "Apoderado" estricto
        if ($activeRol === 'Superusuario') {
            return true;
        }

        if (!in_array($activeRol, $roles)) {
            http_response_code(403);
            $message = "No tienes permisos para acceder a esta sección con tu perfil actual (" . $activeRol . ").";
            require_once dirname(__DIR__) . '/Views/errors/403.php';
            exit;
        }
        return true;
    }
}
