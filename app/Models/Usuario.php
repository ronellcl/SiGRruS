<?php
namespace App\Models;

use PDO;

class Usuario extends Model {
    protected $table = 'usuarios';

    public function findByEmail($email, $anio = null) {
        $anio = $anio ?: date('Y');
        
        // 1. Buscar los datos básicos del usuario
        $stmtUser = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmtUser->execute([':email' => $email]);
        $user = $stmtUser->fetch();
        
        if (!$user) return null;

        // 2. Buscar sus roles para el año solicitado
        $stmtInsc = $this->db->prepare("
            SELECT rol, unidad_id 
            FROM dirigente_inscripcion 
            WHERE usuario_id = :usuario_id 
              AND (fecha_inicio <= :fin_anio AND (fecha_fin IS NULL OR fecha_fin >= :inicio_anio))
        ");
        $finAnio = $anio . "-12-31";
        $inicioAnio = $anio . "-01-01";
        $stmtInsc->execute([
            ':usuario_id' => $user['id'], 
            ':fin_anio' => $finAnio, 
            ':inicio_anio' => $inicioAnio
        ]);
        $inscripciones = $stmtInsc->fetchAll();

        $user['roles'] = [];
        $user['unidades'] = [];
        
        if ($inscripciones) {
            foreach ($inscripciones as $i) {
                $user['roles'][] = $i['rol'];
                $user['unidades'][] = $i['unidad_id'];
            }
            // Para compatibilidad hacia atrás:
            $user['rol'] = $user['roles'][0];
            $user['unidad_id'] = $user['unidades'][0];
        } else {
            $user['rol'] = 'Sin Rol';
            $user['unidad_id'] = null;
        }
        
        return $user;
    }

    public function findById($id, $anio = null) {
        $anio = $anio ?: date('Y');
        
        $stmtUser = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmtUser->execute([':id' => $id]);
        $user = $stmtUser->fetch();
        
        if (!$user) return null;

        $stmtInsc = $this->db->prepare("
            SELECT rol, unidad_id 
            FROM dirigente_inscripcion 
            WHERE usuario_id = :usuario_id 
              AND (fecha_inicio <= :fin_anio AND (fecha_fin IS NULL OR fecha_fin >= :inicio_anio))
        ");
        $finAnio = $anio . "-12-31";
        $inicioAnio = $anio . "-01-01";
        $stmtInsc->execute([
            ':usuario_id' => $user['id'], 
            ':fin_anio' => $finAnio, 
            ':inicio_anio' => $inicioAnio
        ]);
        $inscripciones = $stmtInsc->fetchAll();

        $user['roles'] = [];
        $user['unidades'] = [];

        if ($inscripciones) {
            foreach ($inscripciones as $i) {
                $user['roles'][] = $i['rol'];
                $user['unidades'][] = $i['unidad_id'];
            }
            $user['rol'] = $user['roles'][0];
            $user['unidad_id'] = $user['unidades'][0];
        } else {
            $user['rol'] = 'Sin Rol';
            $user['unidad_id'] = null;
        }
        
        return $user;
    }

    public function findByRut($rut, $anio = null) {
        $anio = $anio ?: date('Y');
        
        $stmtUser = $this->db->prepare("SELECT * FROM {$this->table} WHERE rut = :rut");
        $stmtUser->execute([':rut' => $rut]);
        $user = $stmtUser->fetch();
        
        if (!$user) return null;

        $stmtInsc = $this->db->prepare("
            SELECT rol, unidad_id 
            FROM dirigente_inscripcion 
            WHERE usuario_id = :usuario_id 
              AND (fecha_inicio <= :fin_anio AND (fecha_fin IS NULL OR fecha_fin >= :inicio_anio))
        ");
        $finAnio = $anio . "-12-31";
        $inicioAnio = $anio . "-01-01";
        $stmtInsc->execute([
            ':usuario_id' => $user['id'], 
            ':fin_anio' => $finAnio, 
            ':inicio_anio' => $inicioAnio
        ]);
        $inscripciones = $stmtInsc->fetchAll();

        $user['roles'] = [];
        $user['unidades'] = [];

        if ($inscripciones) {
            foreach ($inscripciones as $i) {
                $user['roles'][] = $i['rol'];
                $user['unidades'][] = $i['unidad_id'];
            }
            $user['rol'] = $user['roles'][0];
            $user['unidad_id'] = $user['unidades'][0];
        } else {
            $user['rol'] = 'Sin Rol';
            $user['unidad_id'] = null;
        }
        
        return $user;
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function getAllWithUnidad($anio = null) {
        $anio = $anio ?: date('Y');
        $finAnio = $anio . "-12-31";
        $inicioAnio = $anio . "-01-01";

        $stmt = $this->db->prepare("
            SELECT u.id, u.nombre, u.rut, u.tipo_documento, u.nacionalidad, u.email, di.rol, un.nombre as unidad_nombre 
            FROM {$this->table} u 
            JOIN dirigente_inscripcion di ON u.id = di.usuario_id 
            LEFT JOIN unidades un ON di.unidad_id = un.id 
            WHERE di.rol != 'Apoderado' 
              AND di.fecha_inicio <= :fin_anio AND (di.fecha_fin IS NULL OR di.fecha_fin >= :inicio_anio)
            ORDER BY di.rol, u.nombre ASC
        ");
        $stmt->execute([':fin_anio' => $finAnio, ':inicio_anio' => $inicioAnio]);
        return $stmt->fetchAll();
    }

    public function findAllUsersWithRoles($anio, $onlyLeaders = false) {
        $finAnio = $anio . "-12-31";
        $inicioAnio = $anio . "-01-01";

        $stmt = $this->db->prepare("
            SELECT u.id, u.nombre, u.email, u.rut,
                   di.rol as rol_dirigente, un.nombre as unidad_nombre
            FROM {$this->table} u
            LEFT JOIN dirigente_inscripcion di ON u.id = di.usuario_id AND (di.fecha_inicio <= :fin_anio AND (di.fecha_fin IS NULL OR di.fecha_fin >= :inicio_anio))
            LEFT JOIN unidades un ON di.unidad_id = un.id
        ");
        $stmt->execute([':fin_anio' => $finAnio, ':inicio_anio' => $inicioAnio]);
        $users = $stmt->fetchAll();

        // Obtener RUTs de apoderados
        $stmtApo = $this->db->query("SELECT rut FROM apoderados");
        $apoderadosRuts = $stmtApo->fetchAll(PDO::FETCH_COLUMN);

        $filtered = [];
        foreach ($users as $u) {
            if ($u['rol_dirigente']) {
                $u['rol'] = $u['rol_dirigente'];
            } elseif (in_array($u['rut'], $apoderadosRuts)) {
                $u['rol'] = 'Apoderado';
            } else {
                $u['rol'] = 'Sin Rol';
            }

            if ($onlyLeaders) {
                // Si solo queremos líderes, excluir Apoderados puros y Sin Rol
                if ($u['rol'] !== 'Apoderado' && $u['rol'] !== 'Sin Rol') {
                    $filtered[] = $u;
                }
            } else {
                $filtered[] = $u;
            }
        }
        return $filtered;
    }

    public function create($data) {
        // En SQLite el BEGIN TRANSACTION no siempre funciona nativamente con PDO de esta manera si la bd no soporta transacciones anidadas, pero PDO lo abstrae.
        try {
            $this->db->beginTransaction();
            
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $mustChange = isset($data['must_change_password']) ? (int)$data['must_change_password'] : 0;
            
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (nombre, rut, tipo_documento, nacionalidad, email, password, must_change_password) VALUES (:nombre, :rut, :tipo, :nac, :email, :password, :must_change)");
            $stmt->execute([
                ':nombre' => $data['nombre'],
                ':rut' => $data['rut'] ?? null,
                ':tipo' => $data['tipo_documento'] ?? 'RUT',
                ':nac' => $data['nacionalidad'] ?? 'Chilena',
                ':email' => $data['email'],
                ':password' => $hash,
                ':must_change' => $mustChange
            ]);
            $userId = $this->db->lastInsertId();

            $anio = $data['anio'] ?? date('Y');
            $fecha_inicio = $anio . "-01-01";

            $stmtInsc = $this->db->prepare("INSERT INTO dirigente_inscripcion (usuario_id, unidad_id, rol, fecha_inicio) VALUES (:usuario_id, :unidad_id, :rol, :fecha_inicio)");
            $stmtInsc->execute([
                ':usuario_id' => $userId,
                ':unidad_id' => $data['unidad_id'] ?: null,
                ':rol' => $data['rol'],
                ':fecha_inicio' => $fecha_inicio
            ]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }
    public function update($id, $data) {
        try {
            $this->db->beginTransaction();
            
            // 1. Actualizar datos básicos
            $passwordSql = "";
            $params = [
                ':nombre' => $data['nombre'],
                ':rut' => $data['rut'],
                ':tipo' => $data['tipo_documento'] ?? 'RUT',
                ':nac' => $data['nacionalidad'] ?? 'Chilena',
                ':email' => $data['email'],
                ':id' => $id
            ];

            if (!empty($data['password'])) {
                $passwordSql = ", password = :password, must_change_password = 0";
                $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $stmt = $this->db->prepare("UPDATE {$this->table} SET nombre = :nombre, rut = :rut, tipo_documento = :tipo, nacionalidad = :nac, email = :email {$passwordSql} WHERE id = :id");
            $stmt->execute($params);

            // 2. Actualizar roles e inscripciones (del año actual)
            $anio = $data['anio'] ?? date('Y');
            $finAnio = $anio . "-12-31";
            $inicioAnio = $anio . "-01-01";

            // Eliminar inscripciones actuales del año para reemplazarlas
            $stmtDel = $this->db->prepare("
                DELETE FROM dirigente_inscripcion 
                WHERE usuario_id = :usuario_id 
                  AND (fecha_inicio <= :fin_anio AND (fecha_fin IS NULL OR fecha_fin >= :inicio_anio))
            ");
            $stmtDel->execute([':usuario_id' => $id, ':fin_anio' => $finAnio, ':inicio_anio' => $inicioAnio]);

            // Insertar nuevas inscripciones
            if (!empty($data['inscripciones'])) {
                $stmtIns = $this->db->prepare("
                    INSERT INTO dirigente_inscripcion (usuario_id, unidad_id, rol, fecha_inicio, anio) 
                    VALUES (:usuario_id, :unidad_id, :rol, :fecha_inicio, :anio)
                ");
                foreach ($data['inscripciones'] as $ins) {
                    $stmtIns->execute([
                        ':usuario_id' => $id,
                        ':unidad_id' => $ins['unidad_id'] ?: null,
                        ':rol' => $ins['rol'],
                        ':fecha_inicio' => $inicioAnio,
                        ':anio' => $anio
                    ]);
                }
            }

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            if ($e->getCode() == 23000 || strpos($e->getMessage(), 'Duplicate entry') !== false) {
                throw new \Exception("Error: Ya existe otro usuario registrado con el RUT " . $data['rut'] . ". El RUT debe ser único.");
            }
            throw $e;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }

    public function getCertificados($userId) {
        $stmt = $this->db->prepare("SELECT * FROM certificados_dirigentes WHERE usuario_id = ? ORDER BY anio DESC, fecha_emision DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function addCertificado($data) {
        $stmt = $this->db->prepare("INSERT INTO certificados_dirigentes (usuario_id, anio, tipo, fecha_emision, archivo_path) VALUES (:usuario_id, :anio, :tipo, :fecha, :path)");
        return $stmt->execute([
            ':usuario_id' => $data['usuario_id'],
            ':anio' => $data['anio'],
            ':tipo' => $data['tipo'],
            ':fecha' => $data['fecha_emision'],
            ':path' => $data['archivo_path']
        ]);
    }
    public function getStaffByUnidad($anio) {
        $stmt = $this->db->prepare("
            SELECT u.nombre, di.rol, di.unidad_id
            FROM {$this->table} u
            JOIN dirigente_inscripcion di ON u.id = di.usuario_id
            WHERE di.rol != 'Apoderado' 
              AND di.fecha_inicio <= :fin AND (di.fecha_fin IS NULL OR di.fecha_fin >= :ini)
        ");
        $stmt->execute([':fin' => $anio . '-12-31', ':ini' => $anio . '-01-01']);
        $all = $stmt->fetchAll();
        
        $staff = [];
        foreach ($all as $s) {
            $key = $s['unidad_id'] ?? '';
            $staff[$key][] = $s;
        }
        return $staff;
    }

    public function getRoleHistory($userId) {
        $stmt = $this->db->prepare("
            SELECT di.*, un.nombre as unidad_nombre
            FROM dirigente_inscripcion di
            LEFT JOIN unidades un ON di.unidad_id = un.id
            WHERE di.usuario_id = ?
            ORDER BY di.fecha_inicio DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    public function delete($id) {
        try {
            $this->db->beginTransaction();
            // 1. Eliminar certificados
            $stmtCerts = $this->db->prepare("DELETE FROM certificados_dirigentes WHERE usuario_id = ?");
            $stmtCerts->execute([$id]);
            // 2. Eliminar inscripciones
            $stmtInsc = $this->db->prepare("DELETE FROM dirigente_inscripcion WHERE usuario_id = ?");
            $stmtInsc->execute([$id]);
            // 3. Eliminar usuario
            $stmtUser = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            $stmtUser->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }
}
