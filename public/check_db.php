<?php
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/DAL/Database.php';

try {
    $db = \App\DAL\Database::getInstance()->getConnection();
    $stmt = $db->query("DESCRIBE dirigente_inscripcion");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Columns in dirigente_inscripcion:\n";
    print_r($cols);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
