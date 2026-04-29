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
            // MySQL/PDO doesn't support multiple queries in one exec easily with some drivers
            // but for SQL snapshots we can try to split by ; if they are simple
            // or just execute as one if the driver supports it
            $this->db->exec($sql);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }
}
