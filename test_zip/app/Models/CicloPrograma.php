<?php
namespace App\Models;

use PDO;

class CicloPrograma extends Model {
    protected $table = 'ciclo_programa';

    public function getByUnidad($unidad_id, $anio = null) {
        $anio = $anio ?: date('Y');
        $stmt = $this->db->prepare("
            SELECT cp.*, hr.id as hoja_ruta_id 
            FROM {$this->table} cp
            LEFT JOIN hojas_ruta hr ON cp.id = hr.actividad_id
            WHERE cp.unidad_id = :unidad_id 
              AND cp.fecha BETWEEN :inicio AND :fin
            ORDER BY cp.fecha ASC
        ");
        $stmt->execute([
            ':unidad_id' => $unidad_id,
            ':inicio' => $anio . '-01-01',
            ':fin' => $anio . '-12-31'
        ]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (unidad_id, nombre_actividad, fecha, lugar, es_extra, es_campamento) VALUES (:unidad_id, :nombre, :fecha, :lugar, :extra, :camp)");
        return $stmt->execute([
            ':unidad_id' => $data['unidad_id'],
            ':nombre' => $data['nombre_actividad'],
            ':fecha' => $data['fecha'],
            ':lugar' => $data['lugar'],
            ':extra' => $data['es_extra'] ?? 0,
            ':camp' => $data['es_campamento'] ?? 0
        ]);
    }
}
