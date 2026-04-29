<?php
namespace App\Models;

use PDO;

class Apoderado extends Model {
    protected $table = 'apoderados';

    public function findByRut($rut) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE rut = ?");
        $stmt->execute([$rut]);
        return $stmt->fetch();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (nombre_completo, rut, tipo_documento, nacionalidad, email, telefono, direccion) VALUES (:nombre, :rut, :tipo, :nac, :email, :telefono, :direccion)");
        $stmt->execute([
            ':nombre' => $data['nombre_completo'],
            ':rut' => $data['rut'],
            ':tipo' => $data['tipo_documento'] ?? 'RUT',
            ':nac' => $data['nacionalidad'] ?? 'Chilena',
            ':email' => $data['email'] ?? '',
            ':telefono' => $data['telefono'] ?? '',
            ':direccion' => $data['direccion'] ?? ''
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET nombre_completo = :nombre, rut = :rut, tipo_documento = :tipo, nacionalidad = :nac, email = :email, telefono = :telefono, direccion = :direccion WHERE id = :id");
        return $stmt->execute([
            ':nombre' => $data['nombre_completo'],
            ':rut' => $data['rut'],
            ':tipo' => $data['tipo_documento'] ?? 'RUT',
            ':nac' => $data['nacionalidad'] ?? 'Chilena',
            ':email' => $data['email'] ?? '',
            ':telefono' => $data['telefono'] ?? '',
            ':direccion' => $data['direccion'] ?? '',
            ':id' => $id
        ]);
    }
    public function delete($id) {
        try {
            $this->db->beginTransaction();
            
            // 0. Obtener el RUT antes de borrar
            $stmtGet = $this->db->prepare("SELECT rut FROM {$this->table} WHERE id = ?");
            $stmtGet->execute([$id]);
            $apo = $stmtGet->fetch();
            $rut = $apo ? $apo['rut'] : null;

            // 1. Desvincular beneficiarios
            $stmtBenef = $this->db->prepare("UPDATE beneficiarios SET apoderado_id = NULL WHERE apoderado_id = ?");
            $stmtBenef->execute([$id]);
            
            // 2. Eliminar apoderado
            $stmtApo = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            $stmtApo->execute([$id]);

            // 3. Limpieza de Tabla Usuarios (Consistencia para "Navegar como...")
            if ($rut) {
                // Verificar si tiene roles de dirigente
                $stmtCheck = $this->db->prepare("
                    SELECT COUNT(*) FROM usuarios u
                    LEFT JOIN dirigente_inscripcion di ON u.id = di.usuario_id
                    WHERE u.rut = ? AND di.id IS NOT NULL
                ");
                $stmtCheck->execute([$rut]);
                $hasOtherRoles = $stmtCheck->fetchColumn() > 0;

                if (!$hasOtherRoles) {
                    // Si no es dirigente, borrarlo de la tabla usuarios también
                    $stmtDelUser = $this->db->prepare("DELETE FROM usuarios WHERE rut = ?");
                    $stmtDelUser->execute([$rut]);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }
}
