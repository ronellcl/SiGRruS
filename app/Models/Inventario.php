<?php
namespace App\Models;

use PDO;

class Inventario extends Model {
    protected $table = 'inventario';

    public function getByUnidad($unidad_id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE unidad_id = ? ORDER BY categoria, nombre_item");
        $stmt->execute([$unidad_id]);
        return $stmt->fetchAll();
    }

    public function getGlobal() {
        $stmt = $this->db->prepare("
            SELECT i.*, u.nombre as unidad_nombre 
            FROM {$this->table} i 
            LEFT JOIN unidades u ON i.unidad_id = u.id 
            ORDER BY u.nombre, i.categoria, i.nombre_item
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getGroupAssets() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE unidad_id IS NULL ORDER BY categoria, nombre_item");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (unidad_id, nombre_item, categoria, cantidad, estado, observaciones) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['unidad_id'] ?? null,
            $data['nombre_item'],
            $data['categoria'],
            $data['cantidad'],
            $data['estado'],
            $data['observaciones']
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET nombre_item = ?, categoria = ?, cantidad = ?, estado = ?, observaciones = ? WHERE id = ?");
        return $stmt->execute([
            $data['nombre_item'],
            $data['categoria'],
            $data['cantidad'],
            $data['estado'],
            $data['observaciones'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
