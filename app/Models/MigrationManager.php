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
        $stmt->closeCursor();
        
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
            $hasError = false;
            foreach ($queries as $sql) {
                try {
                    $this->db->exec($sql);
                } catch (\Exception $e) {
                    // Ignorar errores si la columna o la llave ya existen
                    // 1060: Duplicate column name
                    // 1061: Duplicate key name
                    // 1022: Can't write; duplicate key in table
                    if (strpos($e->getMessage(), '1060') === false && 
                        strpos($e->getMessage(), '1061') === false &&
                        strpos($e->getMessage(), '1022') === false &&
                        strpos($e->getMessage(), 'already exists') === false) {
                        $results['errors'][] = "Error en SQL de {$id}: " . $e->getMessage();
                        $hasError = true;
                        break; 
                    }
                }
            }
            
            if (!$hasError) {
                $stmt = $this->db->prepare("INSERT INTO sys_patches (patch_id) VALUES (?)");
                $stmt->execute([$id]);
                $stmt->closeCursor();
                $results['success']++;
            }
        }

        return $results;
    }
}
