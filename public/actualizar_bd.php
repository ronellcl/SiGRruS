<?php
// public/actualizar_bd.php
require_once dirname(__DIR__) . '/app/config.php';
require_once dirname(__DIR__) . '/app/DAL/Database.php';

try {
    $db = \App\DAL\Database::getInstance()->getConnection();
    $driver = defined('DB_DRIVER') ? DB_DRIVER : 'mysql';

    // 1. Crear tabla de Apoderados si no existe
    $db->exec("CREATE TABLE IF NOT EXISTS apoderados (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        nombre_completo VARCHAR(150),
        rut VARCHAR(20) UNIQUE,
        email VARCHAR(100),
        telefono VARCHAR(20),
        direccion VARCHAR(200)
    )");

    // 2. Intentar añadir apoderado_id a beneficiarios
    try {
        $db->exec("ALTER TABLE beneficiarios ADD COLUMN apoderado_id INTEGER");
    } catch (Exception $e) {
        // Probablemente ya existe la columna
    }

    // 3. Asegurar que existen las tablas de notificaciones y anios_scout (por si acaso)
    $db->exec("CREATE TABLE IF NOT EXISTS anios_scout (
        anio INTEGER PRIMARY KEY,
        estado VARCHAR(20) DEFAULT 'abierto',
        fecha_cierre DATETIME NULL,
        valor_inscripcion INTEGER DEFAULT 0
    )");
    try { $db->exec("ALTER TABLE anios_scout ADD COLUMN valor_inscripcion INTEGER DEFAULT 0"); } catch(Exception $e){}

    $db->exec("CREATE TABLE IF NOT EXISTS notificaciones (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        usuario_id INTEGER,
        mensaje TEXT,
        leida INTEGER DEFAULT 0,
        fecha DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS asistencias (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        actividad_id INTEGER,
        beneficiario_id INTEGER,
        estado VARCHAR(20),
        FOREIGN KEY (actividad_id) REFERENCES ciclo_programa(id),
        FOREIGN KEY (beneficiario_id) REFERENCES beneficiarios(id)
    )");

    // Ajustes para hojas_ruta
    try {
        $db->exec("ALTER TABLE hojas_ruta ADD COLUMN unidad_id INTEGER");
        $db->exec("ALTER TABLE hojas_ruta ADD COLUMN anio INTEGER");
        $db->exec("ALTER TABLE hojas_ruta ADD COLUMN nombre_actividad_manual VARCHAR(150)");
    } catch (Exception $e) {
        // Columnas ya existen
    }

    try {
        $db->exec("ALTER TABLE ciclo_programa ADD COLUMN es_extra INTEGER DEFAULT 0");
    } catch (Exception $e) {}

    $db->exec("CREATE TABLE IF NOT EXISTS finanzas_movimientos (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        unidad_id INTEGER,
        anio INTEGER,
        fecha DATE,
        tipo VARCHAR(20),
        monto INTEGER,
        descripcion TEXT,
        beneficiario_id INTEGER NULL,
        comprobante_archivo VARCHAR(255) NULL,
        justificacion TEXT NULL
    )");
    try { $db->exec("ALTER TABLE finanzas_movimientos ADD COLUMN comprobante_archivo VARCHAR(255) NULL"); } catch(Exception $e){}
    try { $db->exec("ALTER TABLE finanzas_movimientos ADD COLUMN justificacion TEXT NULL"); } catch(Exception $e){}
    try { $db->exec("ALTER TABLE finanzas_movimientos ADD COLUMN estado VARCHAR(20) DEFAULT 'aprobado'"); } catch(Exception $e){}

    $db->exec("CREATE TABLE IF NOT EXISTS cuotas_mensuales (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        beneficiario_id INTEGER,
        anio INTEGER,
        mes INTEGER,
        monto INTEGER,
        pagado INTEGER DEFAULT 0,
        fecha_pago DATE NULL
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS inventario (
        id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        unidad_id INTEGER NULL,
        nombre_item VARCHAR(150),
        categoria VARCHAR(50),
        cantidad INTEGER,
        estado VARCHAR(20),
        observaciones TEXT
    )");
    try { $db->exec("ALTER TABLE inventario ADD COLUMN unidad_id INTEGER NULL"); } catch(Exception $e){}
    try { $db->exec("ALTER TABLE inventario ADD COLUMN nombre_item VARCHAR(150)"); } catch(Exception $e){}
    try { $db->exec("ALTER TABLE inventario ADD COLUMN categoria VARCHAR(50)"); } catch(Exception $e){}
    try { $db->exec("ALTER TABLE inventario ADD COLUMN cantidad INTEGER"); } catch(Exception $e){}
    try { $db->exec("ALTER TABLE inventario ADD COLUMN estado VARCHAR(20)"); } catch(Exception $e){}
    try { $db->exec("ALTER TABLE inventario ADD COLUMN observaciones TEXT"); } catch(Exception $e){}

    try { $db->exec("ALTER TABLE ciclo_programa ADD COLUMN es_campamento INTEGER DEFAULT 0"); } catch(Exception $e){}

    $db->exec("CREATE TABLE IF NOT EXISTS fichas_medicas (
        beneficiario_id INTEGER PRIMARY KEY,
        tipo_sangre VARCHAR(10),
        alergias TEXT,
        enfermedades_cronicas TEXT,
        medicamentos TEXT,
        prevision_salud VARCHAR(100),
        restricciones_alimenticias TEXT,
        vacunas_al_dia INTEGER DEFAULT 1,
        observaciones_medicas TEXT,
        ultima_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (beneficiario_id) REFERENCES beneficiarios(id)
    )");

    try { $db->exec("ALTER TABLE usuarios ADD COLUMN rut VARCHAR(20) UNIQUE"); } catch(Exception $e){}
    $db->exec("CREATE TABLE IF NOT EXISTS dirigentes (
        id INTEGER PRIMARY KEY " . ($driver==='sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        nombre VARCHAR(150),
        tipo VARCHAR(50),
        unidad_id INTEGER NULL,
        anio INTEGER,
        fecha_inicio DATE,
        fecha_fin DATE,
        lugar VARCHAR(150),
        costo_cuota INTEGER DEFAULT 0,
        objetivos TEXT,
        programa_resumen TEXT,
        estado VARCHAR(20) DEFAULT 'Planificación',
        ciclo_actividad_id INTEGER NULL
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS campamentos (
        id INTEGER PRIMARY KEY " . ($driver==='sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        nombre VARCHAR(150),
        tipo VARCHAR(50),
        unidad_id INTEGER NULL,
        anio INTEGER,
        fecha_inicio DATE,
        fecha_fin DATE,
        lugar VARCHAR(150),
        costo_cuota INTEGER DEFAULT 0,
        objetivos TEXT,
        programa_resumen TEXT,
        estado VARCHAR(20) DEFAULT 'Planificación',
        ciclo_actividad_id INTEGER NULL
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS campamento_participantes (
        id INTEGER PRIMARY KEY " . ($driver==='sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        campamento_id INTEGER,
        beneficiario_id INTEGER,
        autorizado INTEGER DEFAULT 0,
        fecha_autorizacion DATETIME NULL,
        observaciones_apoderado TEXT,
        FOREIGN KEY (campamento_id) REFERENCES campamentos(id),
        FOREIGN KEY (beneficiario_id) REFERENCES beneficiarios(id)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS config_grupo (
        id INTEGER PRIMARY KEY,
        nombre_grupo VARCHAR(150),
        logo_path VARCHAR(255),
        institucion_patrocinante VARCHAR(150),
        representante_patrocinante_nombre VARCHAR(150)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS organos_roles (
        id INTEGER PRIMARY KEY " . ($driver==='sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        organo VARCHAR(50),
        rol_especifico VARCHAR(100),
        usuario_id INTEGER NULL,
        apoderado_id INTEGER NULL,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
        FOREIGN KEY (apoderado_id) REFERENCES apoderados(id)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS reuniones (
        id INTEGER PRIMARY KEY " . ($driver==='sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        tipo_organo VARCHAR(50),
        fecha DATETIME,
        tema VARCHAR(255),
        acta TEXT,
        archivo_acta_path VARCHAR(255),
        creado_por INTEGER
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS reuniones_asistencia (
        reunion_id INTEGER,
        entidad_id INTEGER,
        tipo_entidad VARCHAR(20),
        asiste INTEGER DEFAULT 0,
        PRIMARY KEY (reunion_id, entidad_id, tipo_entidad)
    )");

    // 4. Tabla de Certificados de Dirigentes
    $db->exec("CREATE TABLE IF NOT EXISTS certificados_dirigentes (
        id INTEGER PRIMARY KEY " . ($driver==='sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
        usuario_id INTEGER,
        anio INTEGER,
        tipo VARCHAR(100),
        archivo_path VARCHAR(255),
        fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Asegurar columnas una por una (MySQL)
    $columns = [
        'usuario_id' => "INTEGER",
        'anio' => "INTEGER",
        'tipo' => "VARCHAR(100)",
        'fecha_emision' => "DATE NULL",
        'archivo_path' => "VARCHAR(255)"
    ];

    foreach ($columns as $col => $definition) {
        try {
            $db->exec("ALTER TABLE certificados_dirigentes ADD COLUMN $col $definition");
            echo "<p>✅ Columna '$col' añadida.</p>";
        } catch (\PDOException $e) {
            // Ignorar si ya existe
        }
    }

    // Asegurar anio en dirigente_inscripcion
    try {
        @$db->exec("ALTER TABLE dirigente_inscripcion ADD COLUMN anio INTEGER NULL");
        echo "<p>✅ Columna 'anio' añadida a dirigente_inscripcion.</p>";
    } catch (\PDOException $e) {
        echo "<p>ℹ️ Nota sobre 'anio' en dirigente_inscripcion: " . $e->getMessage() . "</p>";
    }

    echo "<h1>✅ Base de datos actualizada con éxito</h1>";
    echo "<p>Se han añadido los Apoderados y el vínculo con Beneficiarios.</p>";
    echo "<p>Ya puedes volver al <a href='/dashboard'>Dashboard</a>.</p>";

} catch (Exception $e) {
    echo "<h1>❌ Error al actualizar</h1>";
    echo $e->getMessage();
}
