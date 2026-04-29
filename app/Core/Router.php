<?php
namespace App\Core;

class Router {
    public function dispatch($url) {
        $url = rtrim($url, '/');
        // $url = filter_var($url, FILTER_SANITIZE_URL); // Eliminado porque rompe nombres con espacios en la URL
        $urlParts = explode('/', $url);

        // Rutas personalizadas
        if ($urlParts[0] === 'unidades' && isset($urlParts[1]) && isset($urlParts[2])) {
            // Ejemplo: /unidades/1/beneficiarios -> BeneficiariosController@index(1)
            $rawController = str_replace('-', '', $urlParts[2]);
            $controllerName = ucfirst($rawController) . 'Controller';
            $controllerClass = '\\App\\Controllers\\' . $controllerName;
            $methodName = 'index';
            $params = [$urlParts[1]];
            
            // Si hay una acción adicional: /unidades/1/beneficiarios/crear -> BeneficiariosController@crear(1)
            if (isset($urlParts[3])) {
                $rawMethod = str_replace('-', '', $urlParts[3]);
                // Si el 4to parámetro es un ID numérico, probablemente la acción sea index o ver
                if (is_numeric($urlParts[3])) {
                    $methodName = 'ver'; // O el que prefieras por defecto para IDs
                    $params[] = $urlParts[3];
                } else {
                    $methodName = $rawMethod;
                    // Pasar el resto como parámetros
                    $params = array_merge($params, array_slice($urlParts, 4));
                }
            }
        } elseif ($urlParts[0] === 'grupo' && isset($urlParts[1])) {
            // Intentar cargar GrupoController@acción
            $controllerClass = '\\App\\Controllers\\GrupoController';
            $methodName = $urlParts[1];
            
            // Si no existe el método en GrupoController, intentar controlador específico
            if (!class_exists($controllerClass) || !method_exists($controllerClass, $methodName)) {
                $controllerName = ucfirst($urlParts[1]) . 'Controller';
                $controllerClass = '\\App\\Controllers\\' . $controllerName;
                $methodName = isset($urlParts[2]) ? $urlParts[2] : 'index';
                $params = array_slice($urlParts, 3);
            } else {
                $params = array_slice($urlParts, 2);
            }
        } else {
            // Controlador por defecto
            $controllerName = !empty($urlParts[0]) ? ucfirst($urlParts[0]) . 'Controller' : 'HomeController';
            $controllerClass = '\\App\\Controllers\\' . $controllerName;

            // Método por defecto
            $methodName = !empty($urlParts[1]) ? $urlParts[1] : 'index';

            // Parámetros
            $params = array_slice($urlParts, 2);
        }

        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            if (method_exists($controller, $methodName)) {
                call_user_func_array([$controller, $methodName], $params);
            } else {
                $this->notFound($controllerClass, $methodName, $url);
            }
        } else {
            $this->notFound($controllerClass, $methodName, $url);
        }
    }

    private function notFound($controller = '', $method = '', $url = '') {
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        echo "<p>La página solicitada no existe.</p>";
        echo "<p style='color:gray; font-size:12px;'>Debug Info - Controller: " . htmlspecialchars($controller) . " | Method: " . htmlspecialchars($method) . " | URL: " . htmlspecialchars($url) . " | URI: " . htmlspecialchars($_SERVER['REQUEST_URI'] ?? '') . "</p>";
    }
}
