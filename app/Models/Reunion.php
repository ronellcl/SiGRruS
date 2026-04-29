<?php
namespace App\Models;

class Reunion extends Model {
    protected $table = 'reuniones';

    public function __construct() {
        parent::__construct();
    }

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

    // MODIFICACIONES DE ACTAS
    
    public function getModificacionPendiente($reunion_id) {
        $stmt = $this->db->prepare("SELECT * FROM reuniones_modificaciones WHERE reunion_id = ? AND estado = 'Pendiente' ORDER BY fecha_solicitud DESC LIMIT 1");
        $stmt->execute([$reunion_id]);
        return $stmt->fetch();
    }
    
    public function getModificacionById($id) {
        $stmt = $this->db->prepare("SELECT * FROM reuniones_modificaciones WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function proponerModificacion($data) {
        // Cancelar cualquier pendiente anterior
        $stmtCancel = $this->db->prepare("UPDATE reuniones_modificaciones SET estado = 'Cancelada' WHERE reunion_id = ? AND estado = 'Pendiente'");
        $stmtCancel->execute([$data['reunion_id']]);

        $stmt = $this->db->prepare("INSERT INTO reuniones_modificaciones (reunion_id, solicitado_por_entidad_id, solicitado_por_tipo, nuevo_tema, nueva_acta) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['reunion_id'],
            $data['solicitado_por_entidad_id'],
            $data['solicitado_por_tipo'],
            $data['nuevo_tema'],
            $data['nueva_acta']
        ]);
        return $this->db->lastInsertId();
    }

    public function registrarAprobacion($mod_id, $rol_aprobador) {
        if ($rol_aprobador === 'Secretario') {
            $stmt = $this->db->prepare("UPDATE reuniones_modificaciones SET aprobado_secretario = 1 WHERE id = ?");
        } else {
            $stmt = $this->db->prepare("UPDATE reuniones_modificaciones SET aprobado_responsable = 1 WHERE id = ?");
        }
        $stmt->execute([$mod_id]);
        
        // Comprobar si ambos aprobaron
        $mod = $this->getModificacionById($mod_id);
        if ($mod['aprobado_secretario'] && $mod['aprobado_responsable']) {
            // Aplicar cambios
            $stmtApply = $this->db->prepare("UPDATE reuniones SET tema = ?, acta = ? WHERE id = ?");
            $stmtApply->execute([$mod['nuevo_tema'], $mod['nueva_acta'], $mod['reunion_id']]);
            
            // Marcar como aprobada
            $stmtEnd = $this->db->prepare("UPDATE reuniones_modificaciones SET estado = 'Aprobada', fecha_resolucion = CURRENT_TIMESTAMP WHERE id = ?");
            $stmtEnd->execute([$mod_id]);
            return true; // Completado
        }
        return false; // Falta una firma
    }

    public function rechazarModificacion($mod_id, $motivo) {
        $stmt = $this->db->prepare("UPDATE reuniones_modificaciones SET estado = 'Rechazada', motivo_rechazo = ?, fecha_resolucion = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$motivo, $mod_id]);
    }
}
