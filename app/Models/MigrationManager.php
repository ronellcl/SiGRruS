<?php
namespace App\Models;

use App\DAL\Database;
use PDO;

class MigrationManager extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->initControlTable();
    }

    private function initControlTable() {
        $this->db->exec("CREATE TABLE IF NOT EXISTS sys_patches (
            patch_id VARCHAR(100) PRIMARY KEY,
            applied_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function getPendingPatches() {
        $allPatches = require dirname(__DIR__) . '/Config/patches.php';
        
        $stmt = $this->db->query("SELECT patch_id FROM sys_patches");
        $applied = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $pending = [];
        foreach ($allPatches as $id => $queries) {
            if (!in_array($id, $applied)) {
                $pending[$id] = $queries;
            }
        }
        return $pending;
    }

    public function applyPatches() {
        $pending = $this->getPendingPatches();
        $results = [
            'success' => 0,
            'errors' => []
        ];

        foreach ($pending as $id => $queries) {
            try {
                $this->db->beginTransaction();
                foreach ($queries as $sql) {
                    try {
                        $this->db->exec($sql);
                    } catch (\Exception $e) {
                        // Algunos errores como "FK ya existe" los podemos ignorar si el SQL es ad-hoc
                        if (strpos($e->getMessage(), 'Duplicate key') === false && 
                            strpos($e->getMessage(), 'already exists') === false) {
                            throw $e;
                        }
                    }
                }
                
                $stmt = $this->db->prepare("INSERT INTO sys_patches (patch_id) VALUES (?)");
                $stmt->execute([$id]);
                
                $this->db->commit();
                $results['success']++;
            } catch (\Exception $e) {
                if ($this->db->inTransaction()) $this->db->rollBack();
                $results['errors'][] = "Error en parche {$id}: " . $e->getMessage();
            }
        }

        return $results;
    }
}
