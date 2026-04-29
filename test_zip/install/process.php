<?php
session_start();

if (file_exists('../app/config.php')) {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_dir = str_replace('/install/process.php', '', $script_name);
    if ($base_dir === $script_name) {
        $base_dir = dirname($script_name);
    }
    $base_dir = rtrim($base_dir, '/\\');
    
    header("Location: " . $base_dir . "/public/");
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'setup_db') {
    $db_host = $_POST['db_host'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $db_name = $_POST['db_name'];
    $db_test_name = $_POST['db_test_name'];

    try {
        // Conectar a MySQL sin base de datos específica primero
        $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Crear base de datos de producción y entrenamiento si no existen
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_test_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // Leer el schema original
        $schema = file_get_contents('schema.sql');
        if (!$schema) {
            throw new Exception("No se pudo leer el archivo schema.sql.");
        }

        // Poblar base de producción
        $pdo->exec("USE `$db_name`");
        $pdo->exec($schema);

        // Poblar base de pruebas
        $pdo->exec("USE `$db_test_name`");
        $pdo->exec($schema);
        
        // Cargar datos dummy solo en la base de pruebas
        if (file_exists('dummy_data.sql')) {
            $dummy_data = file_get_contents('dummy_data.sql');
            if ($dummy_data) {
                $pdo->exec($dummy_data);
            }
        }

        // Guardar credenciales en sesión para el siguiente paso
        $_SESSION['db_config'] = [
            'host' => $db_host,
            'user' => $db_user,
            'pass' => $db_pass,
            'name' => $db_name,
            'test_name' => $db_test_name
        ];

        header('Location: index.php?step=3');
        exit;

    } catch (Exception $e) {
        $_SESSION['install_error'] = "Error de Base de Datos: " . $e->getMessage();
        header('Location: index.php?step=2');
        exit;
    }
}

if ($action === 'setup_group') {
    $config = $_SESSION['db_config'] ?? null;
    if (!$config) {
        $_SESSION['install_error'] = "Sesión perdida. Vuelve a configurar la base de datos.";
        header('Location: index.php?step=2');
        exit;
    }

    try {
        // Conectar a la base de producción
        $pdoProd = new PDO("mysql:host={$config['host']};dbname={$config['name']};charset=utf8mb4", $config['user'], $config['pass']);
        $pdoProd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Conectar a la base de pruebas
        $pdoTest = new PDO("mysql:host={$config['host']};dbname={$config['test_name']};charset=utf8mb4", $config['user'], $config['pass']);
        $pdoTest->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $grupo_nombre = $_POST['grupo_nombre'];
        $hash = password_hash($_POST['admin_pass'], PASSWORD_DEFAULT);

        foreach ([$pdoProd, $pdoTest] as $pdo) {
            // 1. Guardar ConfigGrupo
            $stmt = $pdo->prepare("INSERT INTO config_grupo (id, nombre_grupo) VALUES (1, ?) ON DUPLICATE KEY UPDATE nombre_grupo = ?");
            $stmt->execute([$grupo_nombre, $grupo_nombre]);

            // 2. Crear Superusuario
            $stmtAdmin = $pdo->prepare("INSERT INTO usuarios (rut, nombre, email, password, must_change_password) VALUES (?, ?, ?, ?, 0)");
            $stmtAdmin->execute([
                $_POST['admin_rut'],
                $_POST['admin_nombre'],
                $_POST['admin_email'],
                $hash
            ]);
            $admin_id = $pdo->lastInsertId();

            $stmtRol = $pdo->prepare("INSERT INTO dirigente_inscripcion (usuario_id, rol, fecha_inicio, anio) VALUES (?, 'Superusuario', CURRENT_DATE(), YEAR(CURRENT_DATE()))");
            $stmtRol->execute([$admin_id]);
        }

        // 3. Generar archivo config.php
        $template = file_get_contents('../app/config.example.php');
        
        // Calcular URL Base aproximada
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $app_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . str_replace('/install/process.php', '/public', $_SERVER['REQUEST_URI']);
        
        $configContent = str_replace(
            ['{{APP_NAME}}', '{{DB_HOST}}', '{{DB_NAME}}', '{{DB_TEST_NAME}}', '{{DB_USER}}', '{{DB_PASS}}', '{{APP_URL}}'],
            [$grupo_nombre, $config['host'], $config['name'], $config['test_name'], $config['user'], $config['pass'], $app_url],
            $template
        );

        if (file_put_contents('../app/config.php', $configContent) === false) {
            throw new Exception("No se pudo escribir app/config.php. Verifica los permisos.");
        }

        // Limpiar sesión
        unset($_SESSION['db_config']);

        header('Location: index.php?step=done');
        exit;

    } catch (Exception $e) {
        $_SESSION['install_error'] = "Error configurando el grupo: " . $e->getMessage();
        header('Location: index.php?step=3');
        exit;
    }
}

header('Location: index.php');
