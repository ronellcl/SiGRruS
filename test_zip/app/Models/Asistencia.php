<?php
namespace App\Models;

use PDO;

class Asistencia extends Model {
    protected $table = 'asistencias';

    public function getByActividad($actividad_id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE actividad_id = ?");
        $stmt->execute([$actividad_id]);
        $results = $stmt->fetchAll();
        
        $attendance = [];
        foreach ($results as $row) {
            $attendance[$row['beneficiario_id']] = $row['estado'];
        }
        return $attendance;
    }

    public function save($actividad_id, $asistencias) {
        // Limpiar asistencias previas para esta actividad
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE actividad_id = ?");
        $stmt->execute([$actividad_id]);

        $stmt = $this->db->prepare("INSERT INTO {$this->table} (actividad_id, beneficiario_id, estado) VALUES (?, ?, ?)");
        foreach ($asistencias as $beneficiario_id => $estado) {
            $stmt->execute([$actividad_id, $beneficiario_id, $estado]);
        }
        return true;
    }

    public function getByBeneficiario($beneficiario_id) {
        $stmt = $this->db->prepare("
            SELECT a.*, cp.nombre_actividad, cp.fecha, cp.lugar 
            FROM {$this->table} a
            JOIN ciclo_programa cp ON a.actividad_id = cp.id
            WHERE a.beneficiario_id = ?
            ORDER BY cp.fecha DESC
        ");
        $stmt->execute([$beneficiario_id]);
        return $stmt->fetchAll();
    }
}
