<?php
namespace App\Models;

use PDO;

class FichaMedica extends Model {
    protected $table = 'fichas_medicas';

    public function getByBeneficiario($beneficiario_id) {
        $stmt = $this->db->prepare("
            SELECT fm.*, b.nombre_completo, b.fecha_nacimiento, b.rut 
            FROM {$this->table} fm 
            JOIN beneficiarios b ON fm.beneficiario_id = b.id 
            WHERE fm.beneficiario_id = ?
        ");
        $stmt->execute([$beneficiario_id]);
        $ficha = $stmt->fetch();

        if (!$ficha) {
            // Si no existe, traer datos básicos del beneficiario
            $stmt = $this->db->prepare("SELECT id as beneficiario_id, nombre_completo, fecha_nacimiento, rut FROM beneficiarios WHERE id = ?");
            $stmt->execute([$beneficiario_id]);
            return $stmt->fetch();
        }
        return $ficha;
    }

    public function save($data) {
        $stmt = $this->db->prepare("
            REPLACE INTO {$this->table} (
                beneficiario_id, tipo_sangre, alergias, enfermedades_cronicas, 
                medicamentos, prevision_salud, restricciones_alimenticias, 
                vacunas_al_dia, observaciones_medicas, ultima_actualizacion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ");
        return $stmt->execute([
            $data['beneficiario_id'],
            $data['tipo_sangre'],
            $data['alergias'],
            $data['enfermedades_cronicas'],
            $data['medicamentos'],
            $data['prevision_salud'],
            $data['restricciones_alimenticias'],
            $data['vacunas_al_dia'],
            $data['observaciones_medicas']
        ]);
    }
}
