<?php
namespace App\Models;

class Reunion extends Model {
    protected $table = 'reuniones';

    public function getByOrgano($organo) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE tipo_organo = ? ORDER BY fecha DESC");
        $stmt->execute([$organo]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (tipo_organo, fecha, tema, acta, creado_por) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['tipo_organo'],
            $data['fecha'],
            $data['tema'],
            $data['acta'],
            $data['creado_por']
        ]);
        return $this->db->lastInsertId();
    }

    public function registrarAsistencia($reunion_id, $participantes) {
        // $participantes: array of ['entidad_id' => X, 'tipo_entidad' => 'Usuario|Apoderado', 'asiste' => 0|1]
        foreach ($participantes as $p) {
            $stmt = $this->db->prepare("
                REPLACE INTO reuniones_asistencia (reunion_id, entidad_id, tipo_entidad, asiste)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$reunion_id, $p['entidad_id'], $p['tipo_entidad'], $p['asiste']]);
        }
    }

    public function getAsistencia($reunion_id) {
        $stmt = $this->db->prepare("SELECT * FROM reuniones_asistencia WHERE reunion_id = ?");
        $stmt->execute([$reunion_id]);
        return $stmt->fetchAll();
    }
}
