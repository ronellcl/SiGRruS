<?php
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/DAL/Database.php';

use App\DAL\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h1>Iniciando actualización de base de datos en Producción...</h1>";

    // 1. Actualizar tabla beneficiarios
    $db->exec("ALTER TABLE beneficiarios 
               ADD COLUMN IF NOT EXISTS apoderado_suplente_1_id INT NULL AFTER apoderado_id,
               ADD COLUMN IF NOT EXISTS apoderado_suplente_2_id INT NULL AFTER apoderado_suplente_1_id");
    
    // Intentar añadir las FKs (usamos un try/catch interno por si ya existen)
    try {
        $db->exec("ALTER TABLE beneficiarios 
                   ADD CONSTRAINT fk_benef_suplente_1 FOREIGN KEY (apoderado_suplente_1_id) REFERENCES apoderados(id) ON DELETE SET NULL,
                   ADD CONSTRAINT fk_benef_suplente_2 FOREIGN KEY (apoderado_suplente_2_id) REFERENCES apoderados(id) ON DELETE SET NULL");
        echo "<li>Tablas de beneficiarios vinculadas con éxito.</li>";
    } catch(Exception $e) { echo "<li>Nota: Las llaves foráneas de beneficiarios ya existían o no se pudieron crear (omitido).</li>"; }
               
    echo "<li>Tabla beneficiarios actualizada.</li>";

    // 2. Actualizar tabla fichas_medicas
    $db->exec("ALTER TABLE fichas_medicas 
               ADD COLUMN IF NOT EXISTS creado_por_usuario_id INT NULL AFTER observaciones_medicas");
               
    try {
        $db->exec("ALTER TABLE fichas_medicas 
                   ADD CONSTRAINT fk_fichas_creador FOREIGN KEY (creado_por_usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL");
        echo "<li>Tabla de fichas médicas vinculada con éxito.</li>";
    } catch(Exception $e) { echo "<li>Nota: La llave foránea de fichas médicas ya existía o no se pudo crear (omitido).</li>"; }

    echo "<li>Tabla fichas_medicas actualizada.</li>";

    echo "<h2>✅ Base de datos actualizada con éxito.</h2>";
    echo "<p>Ya puedes volver al sistema.</p>";

} catch (Exception $e) {
    echo "<h2 style='color:red;'>❌ Error: " . $e->getMessage() . "</h2>";
}
