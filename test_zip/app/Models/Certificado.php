<?php
namespace App\Models;

use PDO;

class Certificado extends Model {
    protected $table = 'certificados_dirigentes';

    public function getByUsuario($usuario_id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE usuario_id = :usuario_id ORDER BY anio DESC, tipo ASC");
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByUsuarioYear($usuario_id, $anio) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE usuario_id = :usuario_id AND anio = :anio");
        $stmt->execute([':usuario_id' => $usuario_id, ':anio' => $anio]);
        return $stmt->fetchAll();
    }

    public function getSpecific($usuario_id, $anio, $tipo) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE usuario_id = :usuario_id AND anio = :anio AND tipo = :tipo");
        $stmt->execute([':usuario_id' => $usuario_id, ':anio' => $anio, ':tipo' => $tipo]);
        return $stmt->fetch();
    }

    public function saveCertificate($usuario_id, $anio, $tipo, $path) {
        $existente = $this->getSpecific($usuario_id, $anio, $tipo);
        if ($existente) {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET archivo_path = :path, fecha_subida = CURRENT_TIMESTAMP WHERE id = :id");
            return $stmt->execute([':path' => $path, ':id' => $existente['id']]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (usuario_id, anio, tipo, archivo_path) VALUES (:usuario_id, :anio, :tipo, :path)");
            return $stmt->execute([':usuario_id' => $usuario_id, ':anio' => $anio, ':tipo' => $tipo, ':path' => $path]);
        }
    }
}
