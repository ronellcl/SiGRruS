<?php
namespace App\DAL;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        // En una app real, estas constantes vienen de config.php
        $dbDriver = defined('DB_DRIVER') ? DB_DRIVER : 'sqlite'; // sqlite, mysql, pgsql
        $dbHost = defined('DB_HOST') ? DB_HOST : 'localhost';
        $dbName = defined('DB_NAME') ? DB_NAME : APP_PATH . '/DAL/sigrrus.sqlite';
        $dbUser = defined('DB_USER') ? DB_USER : 'root';
        $dbPass = defined('DB_PASS') ? DB_PASS : '';

        // Soporte para ambiente de pruebas
        if (isset($_SESSION['sigrrus_env']) && $_SESSION['sigrrus_env'] === 'testing') {
            $dbName = defined('DB_TEST_NAME') ? DB_TEST_NAME : $dbName . '_test';
        }

        try {
            if ($dbDriver === 'sqlite') {
                $dsn = "sqlite:" . $dbName;
                $this->connection = new PDO($dsn);
            } else {
                $dsn = "$dbDriver:host=$dbHost;dbname=$dbName;charset=utf8mb4";
                $this->connection = new PDO($dsn, $dbUser, $dbPass);
            }
            
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
