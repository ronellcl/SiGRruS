<?php
namespace App\Models;

use PDO;

class Unidad extends Model {
    protected $table = 'unidades';

    public function getAll() {
        return $this->findAll();
    }

    public function getApoderadosByUnidad($unidad_id) {
        $stmt = $this->db->prepare("
            SELECT DISTINCT a.* 
            FROM apoderados a
            JOIN beneficiarios b ON a.id = b.apoderado_id
            WHERE b.unidad_id = ?
        ");
        $stmt->execute([$unidad_id]);
        return $stmt->fetchAll();
    }
}
