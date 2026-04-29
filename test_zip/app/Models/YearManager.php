<?php
namespace App\Models;

use PDO;

class YearManager extends Model {
    protected $table = 'anios_scout';

    public function isClosed($anio) {
        $stmt = $this->db->prepare("SELECT estado FROM {$this->table} WHERE anio = ?");
        $stmt->execute([$anio]);
        $estado = $stmt->fetchColumn();
        return $estado === 'cerrado';
    }

    public function isDraft($anio) {
        $stmt = $this->db->prepare("SELECT estado FROM {$this->table} WHERE anio = ?");
        $stmt->execute([$anio]);
        $estado = $stmt->fetchColumn();
        return $estado === 'borrador';
    }

    public function close($anio) {
        if ($this->isClosed($anio)) return true;
        $stmt = $this->db->prepare("REPLACE INTO {$this->table} (anio, estado, fecha_cierre) VALUES (?, 'cerrado', CURRENT_TIMESTAMP)");
        return $stmt->execute([$anio]);
    }

    public function create($anio, $estado = 'borrador') {
        $stmt = $this->db->prepare("REPLACE INTO {$this->table} (anio, estado) VALUES (?, ?)");
        return $stmt->execute([$anio, $estado]);
    }

    public function activate($anio) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET estado = 'abierto' WHERE anio = ?");
        return $stmt->execute([$anio]);
    }

    public function deleteDraft($anio) {
        if (!$this->isDraft($anio)) {
            return false;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Borrar inscripciones que comenzaron ese año (usado por la migración)
            $fecha_inicio = $anio . '-01-01';
            
            $stmtBenef = $this->db->prepare("DELETE FROM beneficiario_inscripcion WHERE fecha_ingreso = ?");
            $stmtBenef->execute([$fecha_inicio]);
            
            $stmtDir = $this->db->prepare("DELETE FROM dirigente_inscripcion WHERE fecha_inicio = ?");
            $stmtDir->execute([$fecha_inicio]);
            
            // Borrar el año
            $stmtAnio = $this->db->prepare("DELETE FROM {$this->table} WHERE anio = ? AND estado = 'borrador'");
            $stmtAnio->execute([$anio]);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function getAvailableYears() {
        $stmt = $this->db->prepare("SELECT anio FROM {$this->table} ORDER BY anio DESC");
        $stmt->execute();
        $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Si no hay ninguno, devolver al menos el actual para que el sistema funcione
        if (empty($years)) {
            return [date('Y')];
        }
        return $years;
    }

    public function reopen($anio) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET estado = 'abierto', fecha_cierre = NULL WHERE anio = ?");
        return $stmt->execute([$anio]);
    }

    public function getByAnio($anio) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE anio = ?");
        $stmt->execute([$anio]);
        return $stmt->fetch();
    }

    public function setValorInscripcion($anio, $monto) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET valor_inscripcion = ? WHERE anio = ?");
        return $stmt->execute([$monto, $anio]);
    }

    public function getAllStates() {
        return $this->findAll();
    }
}
