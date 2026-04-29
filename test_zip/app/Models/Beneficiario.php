<?php
namespace App\Models;

use PDO;

class Beneficiario extends Model {
    protected $table = 'beneficiarios';

    public function findByUnidad($unidad_id, $anio = null) {
        $anio = $anio ?: date('Y');
        $finAnio = $anio . "-12-31";
        $inicioAnio = $anio . "-01-01";

        $stmt = $this->db->prepare("
            SELECT b.id, b.rut, b.nombre_completo, b.fecha_nacimiento, bi.subgrupo, a.nombre_completo as apoderado_nombre 
            FROM {$this->table} b 
            JOIN beneficiario_inscripcion bi ON b.id = bi.beneficiario_id 
            LEFT JOIN apoderados a ON b.apoderado_id = a.id
            WHERE bi.unidad_id = :unidad_id 
              AND bi.fecha_ingreso <= :fin_anio 
              AND (bi.fecha_salida IS NULL OR bi.fecha_salida >= :inicio_anio)
            ORDER BY b.nombre_completo ASC
        ");
        $stmt->execute([':unidad_id' => $unidad_id, ':fin_anio' => $finAnio, ':inicio_anio' => $inicioAnio]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $inTransaction = $this->db->inTransaction();
        try {
            if (!$inTransaction) {
                $this->db->beginTransaction();
            }
            
            // 1. Insertar el perfil permanente del beneficiario con su apoderado
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (nombre_completo, fecha_nacimiento, rut, tipo_documento, nacionalidad, apoderado_id) VALUES (:nombre, :fecha, :rut, :tipo, :nac, :apoderado_id)");
            $stmt->execute([
                ':nombre' => $data['nombre_completo'],
                ':fecha' => $data['fecha_nacimiento'],
                ':rut' => $data['rut'],
                ':tipo' => $data['tipo_documento'] ?? 'RUT',
                ':nac' => $data['nacionalidad'] ?? 'Chilena',
                ':apoderado_id' => $data['apoderado_id']
            ]);
            $beneficiario_id = $this->db->lastInsertId();

            // 2. Insertar la inscripción anual
            $anio = $data['anio'] ?? date('Y');
            $fecha_ingreso = $anio . "-01-01";
            
            $stmtInsc = $this->db->prepare("INSERT INTO beneficiario_inscripcion (beneficiario_id, unidad_id, subgrupo, fecha_ingreso) VALUES (:beneficiario_id, :unidad_id, :subgrupo, :fecha_ingreso)");
            $stmtInsc->execute([
                ':beneficiario_id' => $beneficiario_id,
                ':unidad_id' => $data['unidad_id'],
                ':subgrupo' => $data['subgrupo'] ?? null,
                ':fecha_ingreso' => $fecha_ingreso
            ]);

            if (!$inTransaction) {
                $this->db->commit();
            }
            return $beneficiario_id;
        } catch (\Exception $e) {
            if (!$inTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
    public function searchGlobal($filters = []) {
        $sql = "SELECT b.*, u.nombre as unidad_nombre, a.nombre_completo as apoderado_nombre 
                FROM {$this->table} b
                JOIN beneficiario_inscripcion bi ON b.id = bi.beneficiario_id
                LEFT JOIN unidades u ON bi.unidad_id = u.id
                LEFT JOIN apoderados a ON b.apoderado_id = a.id
                WHERE (bi.fecha_salida IS NULL OR bi.fecha_salida >= ?) ";
        
        $params = [date('Y-01-01')];

        if (!empty($filters['unidad_id'])) {
            $sql .= " AND bi.unidad_id = ? ";
            $params[] = $filters['unidad_id'];
        }
        if (!empty($filters['nombre'])) {
            $sql .= " AND b.nombre_completo LIKE ? ";
            $params[] = '%' . $filters['nombre'] . '%';
        }
        if (!empty($filters['rut'])) {
            $sql .= " AND b.rut LIKE ? ";
            $params[] = '%' . $filters['rut'] . '%';
        }
        if (!empty($filters['apoderado'])) {
            $sql .= " AND a.nombre_completo LIKE ? ";
            $params[] = '%' . $filters['apoderado'] . '%';
        }

        $sql .= " GROUP BY b.id ORDER BY u.nombre, b.nombre_completo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function getDetails($id) {
        $stmt = $this->db->prepare("
            SELECT b.*, bi.unidad_id, bi.subgrupo, u.nombre as unidad_nombre, a.nombre_completo as apoderado_nombre
            FROM {$this->table} b
            JOIN beneficiario_inscripcion bi ON b.id = bi.beneficiario_id
            LEFT JOIN unidades u ON bi.unidad_id = u.id
            LEFT JOIN apoderados a ON b.apoderado_id = a.id
            WHERE b.id = ?
            ORDER BY bi.id DESC LIMIT 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByApoderado($apoderado_id) {
        $stmt = $this->db->prepare("
            SELECT b.*, u.nombre as unidad_nombre 
            FROM {$this->table} b
            LEFT JOIN beneficiario_inscripcion bi ON b.id = bi.beneficiario_id
            LEFT JOIN unidades u ON bi.unidad_id = u.id
            WHERE b.apoderado_id = ?
            GROUP BY b.id
            ORDER BY b.nombre_completo ASC
        ");
        $stmt->execute([$apoderado_id]);
        return $stmt->fetchAll();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET nombre_completo = :nombre, fecha_nacimiento = :fecha, rut = :rut, tipo_documento = :tipo, nacionalidad = :nac WHERE id = :id");
        return $stmt->execute([
            ':nombre' => $data['nombre_completo'],
            ':fecha' => $data['fecha_nacimiento'],
            ':rut' => $data['rut'],
            ':tipo' => $data['tipo_documento'] ?? 'RUT',
            ':nac' => $data['nacionalidad'] ?? 'Chilena',
            ':id' => $id
        ]);
    }
    public function delete($id) {
        try {
            $this->db->beginTransaction();
            // 1. Eliminar ficha médica
            $stmtFicha = $this->db->prepare("DELETE FROM fichas_medicas WHERE beneficiario_id = ?");
            $stmtFicha->execute([$id]);
            // 2. Eliminar inscripciones
            $stmtInsc = $this->db->prepare("DELETE FROM beneficiario_inscripcion WHERE beneficiario_id = ?");
            $stmtInsc->execute([$id]);
            // 3. Eliminar beneficiario
            $stmtBenef = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            $stmtBenef->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }
}
