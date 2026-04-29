<?php
/**
 * Lista de parches y mejoras incrementales de base de datos.
 * Cada clave debe ser única (se recomienda usar la fecha).
 */
return [
    '2026_04_29_suplentes_y_trazabilidad' => [
        "ALTER TABLE beneficiarios ADD COLUMN apoderado_suplente_1_id INT NULL AFTER apoderado_id",
        "ALTER TABLE beneficiarios ADD COLUMN apoderado_suplente_2_id INT NULL AFTER apoderado_suplente_1_id",
        "ALTER TABLE fichas_medicas ADD COLUMN creado_por_usuario_id INT NULL AFTER observaciones_medicas",
        // Intentar añadir FKs
        "ALTER TABLE beneficiarios ADD CONSTRAINT fk_benef_suplente_1 FOREIGN KEY (apoderado_suplente_1_id) REFERENCES apoderados(id) ON DELETE SET NULL",
        "ALTER TABLE beneficiarios ADD CONSTRAINT fk_benef_suplente_2 FOREIGN KEY (apoderado_suplente_2_id) REFERENCES apoderados(id) ON DELETE SET NULL",
        "ALTER TABLE fichas_medicas ADD CONSTRAINT fk_fichas_creador FOREIGN KEY (creado_por_usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL"
    ],
    '2026_04_29_gestion_cuentas_y_sincronizacion' => [
        "SELECT 1; -- Parche de código: Soporte para configuración de cuentas y sincronización de perfiles"
    ],
    // Aquí iremos añadiendo más parches en el futuro...
];
