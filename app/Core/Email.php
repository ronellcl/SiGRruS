<?php
namespace App\Core;

class Email {
    
    /**
     * Envía un correo electrónico utilizando la configuración del grupo.
     */
    public static function send($to, $subject, $body, $altBody = '') {
        $configModel = new \App\Models\ConfigGrupo();
        $config = $configModel->getConfig();

        $fromName = $config['nombre_grupo'] ?? 'SiGRruS';
        $fromEmail = 'no-reply@' . ($_SERVER['SERVER_NAME'] ?? 'sigrrus.cl');

        // Si hay configuración SMTP, aquí es donde usaríamos una librería como PHPMailer.
        // Por ahora, usamos mail() de PHP pero preparamos los headers.
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: $fromName <$fromEmail>" . "\r\n";

        // LOG para depuración (opcional)
        // file_put_contents(dirname(__DIR__).'/../mail.log', "To: $to\nSubject: $subject\nBody: $body\n\n", FILE_APPEND);

        return mail($to, $subject, $body, $headers);
    }

    public static function sendWelcome($user, $plainPassword) {
        $subject = "Bienvenido a SiGRruS - " . $user['nombre'];
        $body = "
            <h2>¡Hola, {$user['nombre']}!</h2>
            <p>Se ha creado tu cuenta en el sistema de gestión SiGRruS.</p>
            <p>Tus credenciales de acceso son:</p>
            <ul>
                <li><strong>Usuario:</strong> {$user['email']} (o tu RUT)</li>
                <li><strong>Contraseña Temporal:</strong> {$plainPassword}</li>
            </ul>
            <p>Por seguridad, el sistema te pedirá cambiar esta contraseña al ingresar por primera vez.</p>
            <p><a href='" . self::getBaseUrl() . "/auth/login'>Haz clic aquí para ingresar</a></p>
            <hr>
            <small>Este es un correo automático, por favor no respondas.</small>
        ";
        return self::send($user['email'], $subject, $body);
    }

    public static function sendResetLink($email, $token) {
        $subject = "Recuperación de Contraseña - SiGRruS";
        $link = self::getBaseUrl() . "/auth/resetPassword/" . $token;
        $body = "
            <h2>Recuperación de Contraseña</h2>
            <p>Has solicitado restablecer tu contraseña en SiGRruS.</p>
            <p>Haz clic en el siguiente enlace para continuar (válido por 1 hora):</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
            <hr>
            <small>SiGRruS Identity Management</small>
        ";
        return self::send($email, $subject, $body);
    }

    private static function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = defined('APP_URL') ? APP_URL : '';
        return $protocol . "://" . $host . $base;
    }
}
