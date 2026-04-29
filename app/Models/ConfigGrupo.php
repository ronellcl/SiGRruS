<?php
namespace App\Models;

class ConfigGrupo extends Model {
    protected $table = 'config_grupo';

    public function getConfig() {
        $stmt = $this->db->query("SELECT * FROM {$this->table} LIMIT 1");
        return $stmt->fetch() ?: [
            'nombre_grupo' => 'Grupo Scout SiGRruS',
            'logo_path' => '',
            'asociacion_logo_path' => '',
            'institucion_patrocinante' => '',
            'representante_patrocinante_nombre' => '',
            'pais' => 'Chile',
            'ciudad' => '',
            'zona' => '',
            'distrito' => ''
        ];
    }

    public function updateConfig($data) {
        // Obtenemos el debug_mode actual si no viene en el data para no perderlo
        $current = $this->getConfig();
        $debugMode = $data['debug_mode'] ?? ($current['debug_mode'] ?? 0);

        $stmt = $this->db->prepare("
            REPLACE INTO {$this->table} (
                id, nombre_grupo, logo_path, asociacion_logo_path, institucion_patrocinante, 
                representante_patrocinante_nombre, pais, ciudad, zona, distrito, 
                debug_mode, smtp_host, smtp_user, smtp_pass, smtp_port, smtp_encryption
            )
            VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['nombre_grupo'],
            $data['logo_path'],
            $data['asociacion_logo_path'],
            $data['institucion_patrocinante'],
            $data['representante_patrocinante_nombre'],
            $data['pais'],
            $data['ciudad'],
            $data['zona'],
            $data['distrito'],
            $debugMode,
            $data['smtp_host'] ?? '',
            $data['smtp_user'] ?? '',
            $data['smtp_pass'] ?? '',
            $data['smtp_port'] ?? 587,
            $data['smtp_encryption'] ?? 'tls'
        ]);
    }
}
