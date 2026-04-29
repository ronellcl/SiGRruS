<?php
namespace App\Models;

use PDO;

class Backup extends Model {
    
    public function generateBackup() {
        $tables = [
            'config_grupo', 'unidades', 'usuarios', 'anios_scout', 'apoderados', 
            'beneficiarios', 'beneficiario_inscripcion', 'ciclo_programa', 
            'hojas_ruta', 'asistencias', 'fichas_medicas', 'campamentos', 
            'campamento_participantes', 'finanzas_movimientos', 'cuotas_mensuales', 
            'inventario', 'reuniones', 'reuniones_asistencia', 'organos_roles', 
            'notificaciones'
        ];

        $sql = "-- SiGRruS Snapshot\n";
        $sql .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $table) {
            // Get create table
            $stmt = $this->db->query("SHOW CREATE TABLE `$table` ");
            $createTable = $stmt->fetch(PDO::FETCH_ASSOC);
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            $sql .= $createTable['Create Table'] . ";\n\n";

            // Get data
            $stmt = $this->db->query("SELECT * FROM `$table` ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($rows)) {
                $sql .= "INSERT INTO `$table` VALUES \n";
                $inserts = [];
                foreach ($rows as $row) {
                    $values = array_map(function($v) {
                        if (is_null($v)) return "NULL";
                        return $this->db->quote($v);
                    }, $row);
                    $inserts[] = "(" . implode(", ", $values) . ")";
                }
                $sql .= implode(",\n", $inserts) . ";\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        return $sql;
    }

    public function restore($sql) {
        try {
            $this->db->beginTransaction();
            $this->db->exec($sql);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Guarda el respaldo SQL en un archivo físico.
     */
    public function saveSqlBackup() {
        $sql = $this->generateBackup();
        $dir = dirname(__DIR__, 2) . '/public/backups';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        
        $filename = 'db_backup_' . date('Ymd_His') . '.sql';
        $path = $dir . '/' . $filename;
        file_put_contents($path, $sql);
        return $filename;
    }

    /**
     * Crea un respaldo ZIP de los archivos del sistema.
     */
    public function createFilesBackup() {
        $dir = dirname(__DIR__, 2) . '/public/backups';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        
        $filename = 'files_backup_' . date('Ymd_His') . '.zip';
        $path = $dir . '/' . $filename;

        $zip = new \ZipArchive();
        if ($zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception("No se pudo crear el archivo ZIP.");
        }

        $rootPath = dirname(__DIR__, 2);
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Excluir carpetas pesadas o temporales
                if (strpos($relativePath, 'public/backups') === 0 || 
                    strpos($relativePath, 'public/uploads') === 0 || 
                    strpos($relativePath, '.git') === 0) {
                    continue;
                }

                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        return $filename;
    }
}
