<?php
/**
 * SiGRruS - Sistema de Gestión para Grupos Scouts y Guías
 * Front Controller
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Soporte para servidor integrado de PHP (php -S)
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if ($path && is_file($path)) {
        return false; // Servir archivo estático
    }
}

file_put_contents(dirname(__DIR__) . '/request_debug.log', date('Y-m-d H:i:s') . " - URI: " . $_SERVER['REQUEST_URI'] . " - URL_PARAM: " . ($_GET['url'] ?? 'NONE') . "\n", FILE_APPEND);

// Rutas base
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('INSTALL_PATH', BASE_PATH . '/install');

// Autocargador simple de clases PSR-4 (básico)
spl_autoload_register(function ($class) {
    // Convertir App\Core\Router a app/Core/Router.php
    $prefix = 'App\\';
    $base_dir = APP_PATH . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Comprobar si el sistema está instalado
if (!file_exists(APP_PATH . '/config.php')) {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_dir = str_replace('/public/index.php', '', $script_name);
    if ($base_dir === $script_name) {
        $base_dir = dirname($script_name);
    }
    $base_dir = rtrim($base_dir, '/\\');
    
    header("Location: " . $base_dir . "/install/index.php");
    exit;
}

// Cargar configuración y versión
require_once APP_PATH . '/config.php';
require_once APP_PATH . '/version.php';

// Iniciar sesión segura
session_start();

// Enrutador
$router = new \App\Core\Router();

// Detección robusta de la URL (soporte para subcarpetas en localhost)
$url = $_GET['url'] ?? '';

$scriptName = $_SERVER['SCRIPT_NAME']; // Ej: /sigrrus/public/index.php
$baseDir = str_replace('/public/index.php', '', $scriptName); // Ej: /sigrrus
$cleanBase = ltrim($baseDir, '/');

if (!empty($cleanBase)) {
    // Si la URL capturada por .htaccess empieza por el nombre de la subcarpeta, lo limpiamos
    if (stripos($url, $cleanBase . '/') === 0) {
        $url = substr($url, strlen($cleanBase) + 1);
    } elseif (strcasecmp($url, $cleanBase) === 0) {
        $url = '';
    }
}

// Si después de limpiar sigue vacío, intentamos detectar por REQUEST_URI
if (empty($url) && isset($_SERVER['REQUEST_URI'])) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Si estamos en una subcarpeta, la quitamos de la URI
    if ($baseDir !== '/' && stripos($uri, $baseDir) === 0) {
        $uri = substr($uri, strlen($baseDir));
    }
    
    $url = ltrim($uri, '/');
}

$router->dispatch($url);
