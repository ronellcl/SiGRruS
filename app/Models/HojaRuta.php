<?php
namespace App\Models;

use PDO;

class HojaRuta extends Model {
    protected $table = 'hojas_ruta';

    public function getByUnidad($unidad_id, $anio) {
        $stmt = $this->db->prepare("
            SELECT hr.*, cp.nombre_actividad as nombre_ciclo 
            FROM {$this->table} hr
            LEFT JOIN ciclo_programa cp ON hr.actividad_id = cp.id
            WHERE hr.unidad_id = :unidad_id AND hr.anio = :anio
        ");
        $stmt->execute([':unidad_id' => $unidad_id, ':anio' => $anio]);
        return $stmt->fetchAll();
    }

    public function getByActividad($actividad_id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE actividad_id = :actividad_id");
        $stmt->execute([':actividad_id' => $actividad_id]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT hr.*, cp.nombre_actividad as nombre_ciclo FROM {$this->table} hr LEFT JOIN ciclo_programa cp ON hr.actividad_id = cp.id WHERE hr.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function save($data) {
        // Si tiene actividad_id, intentamos buscar por ese ID primero
        $existente = null;
        if (!empty($data['actividad_id'])) {
            $existente = $this->getByActividad($data['actividad_id']);
        } elseif (!empty($data['id'])) {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
            $stmt->execute([$data['id']]);
            $existente = $stmt->fetch();
        }
        
        if ($existente) {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET 
                motivacion = :motivacion, 
                lugar_detalles = :lugar_detalles, 
                fases = :fases, 
                variantes = :variantes, 
                participacion = :participacion, 
                recursos_humanos = :recursos_humanos, 
                materiales = :materiales, 
                costos = :costos, 
                seguridad = :seguridad,
                nombre_actividad_manual = :nombre_manual
                WHERE id = :id");
            $data['id'] = $existente['id'];
        } else {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} 
                (actividad_id, unidad_id, anio, nombre_actividad_manual, motivacion, lugar_detalles, fases, variantes, participacion, recursos_humanos, materiales, costos, seguridad) 
                VALUES 
                (:actividad_id, :unidad_id, :anio, :nombre_manual, :motivacion, :lugar_detalles, :fases, :variantes, :participacion, :recursos_humanos, :materiales, :costos, :seguridad)");
        }

        return $stmt->execute([
            ':id' => $data['id'] ?? null,
            ':actividad_id' => !empty($data['actividad_id']) ? $data['actividad_id'] : null,
            ':unidad_id' => $data['unidad_id'],
            ':anio' => $data['anio'],
            ':nombre_manual' => $data['nombre_actividad_manual'] ?? null,
            ':motivacion' => $data['motivacion'] ?? '',
            ':lugar_detalles' => $data['lugar_detalles'] ?? '',
            ':fases' => $data['fases'] ?? '',
            ':variantes' => $data['variantes'] ?? '',
            ':participacion' => $data['participacion'] ?? '',
            ':recursos_humanos' => $data['recursos_humanos'] ?? '',
            ':materiales' => $data['materiales'] ?? '',
            ':costos' => $data['costos'] ?? '',
            ':seguridad' => $data['seguridad'] ?? ''
        ]);
    }
}
