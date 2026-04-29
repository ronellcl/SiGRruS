<?php
namespace App\Models;

use PDO;

class Notification extends Model {
    protected $table = 'notificaciones';

    public function getByUser($usuario_id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE usuario_id = :usuario_id ORDER BY fecha DESC LIMIT 20");
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchAll();
    }

    public function getUnreadCount($usuario_id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE usuario_id = :usuario_id AND leida = 0");
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchColumn();
    }

    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET leida = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function send($usuario_id, $mensaje) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (usuario_id, mensaje) VALUES (?, ?)");
        return $stmt->execute([$usuario_id, $mensaje]);
    }

    public function create($data) {
        return $this->send($data['usuario_id'], $data['mensaje']);
    }
}
