<?php
namespace App\Models;

use PDO;

class Finanzas extends Model {
    
    public function getMovimientos($unidad_id, $anio) {
        $stmt = $this->db->prepare("SELECT fm.*, b.nombre_completo as beneficiario FROM finanzas_movimientos fm LEFT JOIN beneficiarios b ON fm.beneficiario_id = b.id WHERE fm.unidad_id = ? AND fm.anio = ? ORDER BY fm.fecha DESC");
        $stmt->execute([$unidad_id, $anio]);
        return $stmt->fetchAll();
    }

    public function registrarMovimiento($data) {
        $stmt = $this->db->prepare("INSERT INTO finanzas_movimientos (unidad_id, anio, fecha, tipo, monto, descripcion, beneficiario_id, comprobante_archivo, justificacion, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['unidad_id'],
            $data['anio'],
            $data['fecha'],
            $data['tipo'],
            $data['monto'],
            $data['descripcion'],
            $data['beneficiario_id'] ?? null,
            $data['comprobante_archivo'] ?? null,
            $data['justificacion'] ?? null,
            $data['estado'] ?? 'aprobado'
        ]);
    }

    public function getMovimientosPendientesGrupo($anio) {
        $stmt = $this->db->prepare("
            SELECT fm.*, u.nombre as unidad_nombre 
            FROM finanzas_movimientos fm 
            JOIN unidades u ON fm.unidad_id = u.id 
            WHERE fm.estado = 'pendiente' AND fm.anio = ?
        ");
        $stmt->execute([$anio]);
        return $stmt->fetchAll();
    }

    public function aprobarMovimiento($id) {
        $stmt = $this->db->prepare("UPDATE finanzas_movimientos SET estado = 'aprobado' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function rechazarMovimiento($id, $justificacion) {
        $stmt = $this->db->prepare("UPDATE finanzas_movimientos SET estado = 'rechazado', justificacion = ? WHERE id = ?");
        return $stmt->execute([$justificacion, $id]);
    }

    public function getBalanceGrupo($anio) {
        $stmt = $this->db->prepare("SELECT SUM(monto) as total FROM finanzas_movimientos WHERE estado = 'aprobado' AND anio = ? AND tipo = 'Egreso' AND (descripcion LIKE '%Depósito%' OR descripcion LIKE '%Traspaso%')");
        $stmt->execute([$anio]);
        $ingresosTraspaso = $stmt->fetchColumn() ?: 0;
        
        // También podrían haber egresos propios del grupo (ej: compra de material común)
        $stmt = $this->db->prepare("SELECT SUM(monto) as total FROM finanzas_movimientos WHERE unidad_id IS NULL AND anio = ? AND tipo = 'Egreso' AND estado = 'aprobado'");
        $stmt->execute([$anio]);
        $egresosGrupo = $stmt->fetchColumn() ?: 0;

        return $ingresosTraspaso - $egresosGrupo;
    }

    public function getBalancesUnidades($anio) {
        $stmt = $this->db->prepare("
            SELECT u.id, u.nombre,
                   SUM(CASE WHEN fm.tipo = 'Ingreso' AND fm.estado = 'aprobado' THEN fm.monto ELSE 0 END) as ingresos,
                   SUM(CASE WHEN fm.tipo = 'Egreso' AND fm.estado = 'aprobado' THEN fm.monto ELSE 0 END) as egresos
            FROM unidades u
            LEFT JOIN finanzas_movimientos fm ON u.id = fm.unidad_id AND fm.anio = ?
            GROUP BY u.id, u.nombre
        ");
        $stmt->execute([$anio]);
        return $stmt->fetchAll();
    }

    public function getCuotasUnidad($unidad_id, $anio) {
        $stmt = $this->db->prepare("
            SELECT b.id, b.nombre_completo, cm.mes, cm.pagado, cm.monto
            FROM beneficiarios b
            JOIN beneficiario_inscripcion bi ON b.id = bi.beneficiario_id
            LEFT JOIN cuotas_mensuales cm ON b.id = cm.beneficiario_id AND cm.anio = ?
            WHERE bi.unidad_id = ? AND (bi.fecha_salida IS NULL OR bi.fecha_salida >= ?)
        ");
        $stmt->execute([$anio, $unidad_id, $anio . '-01-01']);
        $results = $stmt->fetchAll();

        // Estructurar datos: [beneficiario_id][mes] = pagado
        $cuotas = [];
        foreach ($results as $r) {
            if (!isset($cuotas[$r['id']])) {
                $cuotas[$r['id']] = [
                    'nombre' => $r['nombre_completo'],
                    'meses' => array_fill(1, 12, ['pagado' => 0, 'monto' => 0]),
                    'inscripcion' => ['pagado' => 0, 'monto' => 0]
                ];
            }
            if ($r['mes'] == 0) {
                $cuotas[$r['id']]['inscripcion'] = ['pagado' => $r['pagado'], 'monto' => $r['monto']];
            } elseif ($r['mes'] > 0) {
                $cuotas[$r['id']]['meses'][$r['mes']] = ['pagado' => $r['pagado'], 'monto' => $r['monto']];
            }
        }
        return $cuotas;
    }

    public function registrarPagoCuota($data) {
        // Eliminar registro previo si existe
        $stmt = $this->db->prepare("DELETE FROM cuotas_mensuales WHERE beneficiario_id = ? AND anio = ? AND mes = ?");
        $stmt->execute([$data['beneficiario_id'], $data['anio'], $data['mes']]);

        // Insertar nuevo registro de pago
        $stmt = $this->db->prepare("INSERT INTO cuotas_mensuales (beneficiario_id, anio, mes, monto, pagado, fecha_pago) VALUES (?, ?, ?, ?, 1, ?)");
        $stmt->execute([
            $data['beneficiario_id'],
            $data['anio'],
            $data['mes'],
            $data['monto'],
            date('Y-m-d')
        ]);

        // Opcional: Registrar como movimiento de ingreso en la unidad
        $desc = ($data['mes'] == 0) ? "Pago Inscripción Anual" : "Pago cuota mes " . $data['mes'];
        return $this->registrarMovimiento([
            'unidad_id' => $data['unidad_id'],
            'anio' => $data['anio'],
            'fecha' => date('Y-m-d'),
            'tipo' => 'Ingreso',
            'monto' => $data['monto'],
            'descripcion' => $desc,
            'beneficiario_id' => $data['beneficiario_id']
        ]);
    }

    public function getPagosByBeneficiario($beneficiario_id, $anio) {
        $stmt = $this->db->prepare("
            SELECT mes, pagado, monto, fecha_pago 
            FROM cuotas_mensuales 
            WHERE beneficiario_id = ? AND anio = ?
            ORDER BY mes ASC
        ");
        $stmt->execute([$beneficiario_id, $anio]);
        return $stmt->fetchAll();
    }
}
