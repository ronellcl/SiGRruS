<?php
namespace App\Models;

class Organo extends Model {
    protected $table = 'organos_roles';

    public function getMiembros($organo) {
        // Unir con usuarios y apoderados para obtener nombres
        $stmt = $this->db->prepare("
            SELECT ogr.*, 
                   COALESCE(u.nombre, a.nombre_completo) as nombre,
                   COALESCE(u.rut, a.rut) as rut,
                   COALESCE(u.email, a.email) as email
            FROM {$this->table} ogr
            LEFT JOIN usuarios u ON ogr.usuario_id = u.id
            LEFT JOIN apoderados a ON ogr.apoderado_id = a.id
            WHERE ogr.organo = ?
        ");
        $stmt->execute([$organo]);
        return $stmt->fetchAll();
    }

    public function agregarPorRut($organo, $rut, $rol_especifico) {
        // Buscar en usuarios
        $stmtU = $this->db->prepare("SELECT id FROM usuarios WHERE rut = ?");
        $stmtU->execute([$rut]);
        $u = $stmtU->fetch();
        if ($u) {
            $stmt = $this->db->prepare("REPLACE INTO {$this->table} (organo, rol_especifico, usuario_id) VALUES (?, ?, ?)");
            return $stmt->execute([$organo, $rol_especifico, $u['id']]);
        }

        // Buscar en apoderados
        $stmtA = $this->db->prepare("SELECT id FROM apoderados WHERE rut = ?");
        $stmtA->execute([$rut]);
        $a = $stmtA->fetch();
        if ($a) {
            $stmt = $this->db->prepare("REPLACE INTO {$this->table} (organo, rol_especifico, apoderado_id) VALUES (?, ?, ?)");
            return $stmt->execute([$organo, $rol_especifico, $a['id']]);
        }

        return false;
    }

    public function eliminarMiembro($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function esMiembro($organo, $usuario_id, $apoderado_id = null) {
        if ($usuario_id) {
            $stmt = $this->db->prepare("SELECT 1 FROM {$this->table} WHERE organo = ? AND usuario_id = ?");
            $stmt->execute([$organo, $usuario_id]);
            if ($stmt->fetch()) return true;
        }
        if ($apoderado_id) {
            $stmt = $this->db->prepare("SELECT 1 FROM {$this->table} WHERE organo = ? AND apoderado_id = ?");
            $stmt->execute([$organo, $apoderado_id]);
            if ($stmt->fetch()) return true;
        }
        return false;
    }
}
