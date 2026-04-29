<?php
namespace App\Models;

use App\DAL\Database;
use PDO;

abstract class Model {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
