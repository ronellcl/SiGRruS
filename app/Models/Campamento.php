<?php
namespace App\Models;

use PDO;

class Campamento extends Model {
    protected $table = 'campamentos';

    public function getByUnidad($unidad_id, $anio) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE (unidad_id = ? OR tipo != 'Unidad') AND anio = ? ORDER BY fecha_inicio ASC");
        $stmt->execute([$unidad_id, $anio]);
        return $stmt->fetchAll();
    }

    public function getGlobal($anio) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE anio = ? ORDER BY fecha_inicio ASC");
        $stmt->execute([$anio]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (nombre, tipo, unidad_id, anio, fecha_inicio, fecha_fin, lugar, costo_cuota, objetivos, programa_resumen, ciclo_actividad_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['nombre'],
            $data['tipo'],
            $data['unidad_id'] ?? null,
            $data['anio'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $data['lugar'],
            $data['costo_cuota'],
            $data['objetivos'],
            $data['programa_resumen'],
            $data['ciclo_actividad_id'] ?? null
        ]);
    }

    public function getParticipantes($campamento_id) {
        $stmt = $this->db->prepare("
            SELECT cp.*, b.nombre_completo, b.rut, u.nombre as unidad_nombre
            FROM campamento_participantes cp
            JOIN beneficiarios b ON cp.beneficiario_id = b.id
            JOIN unidades u ON b.unidad_id = u.id
            WHERE cp.campamento_id = ?
        ");
        $stmt->execute([$campamento_id]);
        return $stmt->fetchAll();
    }

    public function registrarAutorizacion($camp_id, $benef_id, $obs = '') {
        $stmt = $this->db->prepare("
            REPLACE INTO campamento_participantes (campamento_id, beneficiario_id, autorizado, fecha_autorizacion, observaciones_apoderado)
            VALUES (?, ?, 1, CURRENT_TIMESTAMP, ?)
        ");
        return $stmt->execute([$camp_id, $benef_id, $obs]);
    }

    public function getAutorizacion($camp_id, $benef_id) {
        $stmt = $this->db->prepare("SELECT * FROM campamento_participantes WHERE campamento_id = ? AND beneficiario_id = ?");
        $stmt->execute([$camp_id, $benef_id]);
        return $stmt->fetch();
    }
}
