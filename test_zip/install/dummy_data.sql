-- ==========================================
-- DUMMY DATA PARA ENTORNO DE ENTRENAMIENTO
-- ==========================================

-- 1. USUARIOS (Dirigentes y Staff)
-- Contraseñas hasheadas para '123456'
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rut`, `tipo_documento`, `nacionalidad`, `must_change_password`) VALUES
(2, 'Akela (Dirigente Manada)', 'akela@test.cl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '11111111-1', 'RUT', 'Chilena', 0),
(3, 'Bagheera (Dirigente Manada)', 'bagheera@test.cl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '22222222-2', 'RUT', 'Chilena', 0),
(4, 'Golondrina Mayor (Dir. Bandada)', 'golondrina@test.cl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '33333333-3', 'RUT', 'Chilena', 0),
(5, 'Jefe de Tropa (Dir. Tropa)', 'tropa@test.cl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '44444444-4', 'RUT', 'Chilena', 0),
(6, 'Jefa de Compañía (Dir. Guías)', 'compania@test.cl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '55555555-5', 'RUT', 'Chilena', 0),
(7, 'Jefe de Avanzada (Dir. Pioneros)', 'avanzada@test.cl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '66666666-6', 'RUT', 'Chilena', 0),
(8, 'Jefe de Clan (Dir. Caminantes)', 'clan@test.cl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '77777777-7', 'RUT', 'Chilena', 0),
(9, 'Tesorero Grupo', 'tesorero@test.cl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '88888888-8', 'RUT', 'Chilena', 0);

-- 2. DIRIGENTES_INSCRIPCION (Asignación a unidades)
INSERT INTO `dirigente_inscripcion` (`usuario_id`, `unidad_id`, `rol`, `fecha_inicio`, `fecha_fin`, `anio`) VALUES
(2, 1, 'Responsable', '2023-01-01', NULL, 2026),
(3, 1, 'Asistente', '2023-01-01', NULL, 2026),
(4, 2, 'Responsable', '2023-01-01', NULL, 2026),
(5, 3, 'Responsable', '2023-01-01', NULL, 2026),
(6, 4, 'Responsable', '2023-01-01', NULL, 2026),
(7, 5, 'Responsable', '2023-01-01', NULL, 2026),
(8, 6, 'Responsable', '2023-01-01', NULL, 2026);

-- 3. APODERADOS
INSERT INTO `apoderados` (`id`, `nombre_completo`, `rut`, `email`, `telefono`, `direccion`) VALUES
(1, 'Juan Pérez Gómez', '10000000-1', 'juan.perez@test.cl', '+56912345678', 'Av. Siempre Viva 123, Ciudad'),
(2, 'María González Tapia', '10000000-2', 'maria.gonzalez@test.cl', '+56923456789', 'Los Alerces 456, Ciudad'),
(3, 'Pedro Rodríguez Soto', '10000000-3', 'pedro.rodriguez@test.cl', '+56934567890', 'Calle Las Rosas 789, Ciudad'),
(4, 'Ana Martínez López', '10000000-4', 'ana.martinez@test.cl', '+56945678901', 'Pje. Los Pinos 101, Ciudad'),
(5, 'Carlos Silva Morales', '10000000-5', 'carlos.silva@test.cl', '+56956789012', 'Av. Libertad 202, Ciudad'),
(6, 'Laura Torres Vera', '10000000-6', 'laura.torres@test.cl', '+56967890123', 'Calle El Sol 303, Ciudad'),
(7, 'Andrés Herrera Castro', '10000000-7', 'andres.herrera@test.cl', '+56978901234', 'Av. Central 404, Ciudad'),
(8, 'Carmen Díaz Rojas', '10000000-8', 'carmen.diaz@test.cl', '+56989012345', 'Pje. Las Lomas 505, Ciudad');

-- 4. BENEFICIARIOS
INSERT INTO `beneficiarios` (`id`, `nombre_completo`, `fecha_nacimiento`, `rut`, `apoderado_id`) VALUES
-- Manada (1) - Lobatos (7-11)
(1, 'Lucas Pérez Díaz', '2015-05-10', '25000001-1', 1),
(2, 'Mateo Rodríguez Castro', '2016-02-15', '25000002-2', 3),
(3, 'Agustín Silva Vera', '2015-11-20', '25000003-3', 5),
-- Bandada (2) - Golondrinas (7-11)
(4, 'Sofía Pérez Díaz', '2016-08-22', '25000004-4', 1),
(5, 'Isabella González Soto', '2015-01-30', '25000005-5', 2),
(6, 'Martina Torres López', '2016-12-12', '25000006-6', 6),
-- Tropa (3) - Scouts (11-15)
(7, 'Tomás González Soto', '2012-03-05', '24000001-1', 2),
(8, 'Diego Herrera Díaz', '2011-07-18', '24000002-2', 7),
(9, 'Vicente Silva Vera', '2012-09-25', '24000003-3', 5),
-- Compañía (4) - Guías (11-15)
(10, 'Valentina Martínez Rojas', '2011-04-12', '24000004-4', 4),
(11, 'Antonia Rodríguez Castro', '2012-10-08', '24000005-5', 3),
(12, 'Emilia Díaz Rojas', '2011-06-30', '24000006-6', 8),
-- Avanzada (5) - Pioneros (15-17)
(13, 'Benjamín Martínez Rojas', '2008-05-14', '23000001-1', 4),
(14, 'Joaquín Torres López', '2009-02-28', '23000002-2', 6),
(15, 'Florencia Herrera Díaz', '2008-11-15', '23000003-3', 7),
-- Clan (6) - Caminantes (17-21)
(16, 'Sebastián Pérez Díaz', '2006-08-20', '22000001-1', 1),
(17, 'Camila González Soto', '2005-03-10', '22000002-2', 2),
(18, 'Maximiliano Silva Vera', '2006-12-05', '22000003-3', 5);

-- 5. BENEFICIARIO_INSCRIPCION
INSERT INTO `beneficiario_inscripcion` (`beneficiario_id`, `unidad_id`, `subgrupo`, `fecha_ingreso`) VALUES
(1, 1, 'Seisena Blanca', '2023-03-01'), (2, 1, 'Seisena Gris', '2023-03-01'), (3, 1, 'Seisena Negra', '2024-03-01'),
(4, 2, 'Seisena Azul', '2023-03-01'), (5, 2, 'Seisena Roja', '2023-03-01'), (6, 2, 'Seisena Amarilla', '2024-03-01'),
(7, 3, 'Patrulla Pumas', '2021-03-01'), (8, 3, 'Patrulla Halcones', '2020-03-01'), (9, 3, 'Patrulla Lobos', '2022-03-01'),
(10, 4, 'Patrulla Golondrinas', '2020-03-01'), (11, 4, 'Patrulla Araucarias', '2021-03-01'), (12, 4, 'Patrulla Copihues', '2022-03-01'),
(13, 5, 'Comunidad 1', '2019-03-01'), (14, 5, 'Comunidad 2', '2020-03-01'), (15, 5, 'Comunidad 1', '2019-03-01'),
(16, 6, 'Equipo A', '2018-03-01'), (17, 6, 'Equipo B', '2017-03-01'), (18, 6, 'Equipo A', '2018-03-01');

-- 6. FICHAS MEDICAS
INSERT INTO `fichas_medicas` (`beneficiario_id`, `tipo_sangre`, `alergias`, `enfermedades_cronicas`, `medicamentos`, `prevision_salud`, `restricciones_alimenticias`, `vacunas_al_dia`) VALUES
(1, 'O+', 'Ninguna', 'Asma leve', 'Salbutamol SOS', 'Fonasa', 'Ninguna', 1),
(2, 'A+', 'Penicilina', 'Ninguna', 'Ninguno', 'Isapre Banmédica', 'Intolerante a la lactosa', 1),
(3, 'O-', 'Picadura de abeja', 'Ninguna', 'Antihistamínicos en campamento', 'Fonasa', 'Ninguna', 1),
(4, 'B+', 'Ninguna', 'Ninguna', 'Ninguno', 'Fonasa', 'Vegetariana', 1),
(7, 'O+', 'Polvo', 'Ninguna', 'Ninguno', 'Isapre CruzBlanca', 'Ninguna', 1),
(10, 'A-', 'Ninguna', 'Ninguna', 'Ninguno', 'Fonasa', 'Celíaca', 1),
(13, 'O+', 'Ninguna', 'Diabetes Tipo 1', 'Insulina', 'Fonasa', 'Dieta diabética', 1),
(16, 'AB+', 'Ninguna', 'Ninguna', 'Ninguno', 'Isapre Colmena', 'Ninguna', 1);

-- 7. CICLO DE PROGRAMA (Actividades)
INSERT INTO `ciclo_programa` (`id`, `unidad_id`, `nombre_actividad`, `fecha`, `lugar`, `es_extra`, `es_campamento`) VALUES
(1, 1, 'Reunión Normal Manada', '2026-03-14', 'Patio del Colegio', 0, 0),
(2, 1, 'Salida al Parque Ecológico', '2026-04-10', 'Parque Metropolitano', 1, 0),
(3, 3, 'Reunión Normal Tropa', '2026-03-14', 'Patio del Colegio', 0, 0),
(4, 3, 'Taller de Pionerismo', '2026-03-21', 'Bosque Cercano', 0, 0),
(5, 5, 'Servicio a la Comunidad', '2026-04-18', 'Hogar de Ancianos', 1, 0);

-- 8. ASISTENCIAS
INSERT INTO `asistencias` (`actividad_id`, `beneficiario_id`, `estado`) VALUES
(1, 1, 'presente'), (1, 2, 'presente'), (1, 3, 'ausente'),
(3, 7, 'presente'), (3, 8, 'ausente'), (3, 9, 'presente'),
(4, 7, 'presente'), (4, 8, 'presente'), (4, 9, 'presente');

-- 9. CAMPAMENTOS
INSERT INTO `campamentos` (`id`, `nombre`, `tipo`, `unidad_id`, `anio`, `fecha_inicio`, `fecha_fin`, `lugar`, `costo_cuota`, `estado`) VALUES
(1, 'Campamento de Invierno Tropa', 'Invierno', 3, 2026, '2026-07-15', '2026-07-20', 'Cajón del Maipo', 15000, 'Aprobado'),
(2, 'Acantonamiento Manada', 'Acantonamiento', 1, 2026, '2026-05-20', '2026-05-22', 'Refugio Local', 8000, 'Planificación'),
(3, 'Campamento de Verano Grupo', 'Verano', NULL, 2026, '2026-01-15', '2026-01-25', 'Sur de Chile', 45000, 'Finalizado');

-- 10. CAMPAMENTO PARTICIPANTES
INSERT INTO `campamento_participantes` (`campamento_id`, `beneficiario_id`, `autorizado`, `fecha_autorizacion`, `observaciones_apoderado`) VALUES
(1, 7, 1, '2026-06-01 10:00:00', 'Lleva su inhalador'),
(1, 8, 1, '2026-06-02 11:30:00', 'Todo bien'),
(1, 9, 0, NULL, NULL),
(2, 1, 1, '2026-05-01 09:00:00', 'Dejarle la chaqueta azul en la noche'),
(2, 2, 1, '2026-05-03 14:15:00', 'Ojo con su alergia alimentaria');

-- 11. FINANZAS (Movimientos Globales)
INSERT INTO `finanzas` (`tipo`, `monto`, `concepto`, `fecha`, `registrado_por`) VALUES
('ingreso', 50000, 'Aporte de institución patrocinante', '2026-03-01', 9),
('ingreso', 120000, 'Rifa de Grupo (Marzo)', '2026-03-15', 9),
('egreso', 25000, 'Compra de botiquín grupal', '2026-03-20', 9),
('egreso', 15000, 'Materiales para oficina', '2026-04-05', 9);

-- 12. FINANZAS_MOVIMIENTOS (Caja de Unidad - Tropa)
INSERT INTO `finanzas_movimientos` (`unidad_id`, `anio`, `fecha`, `tipo`, `monto`, `descripcion`, `beneficiario_id`, `estado`) VALUES
(3, 2026, '2026-03-14', 'ingreso', 1000, 'Cuota reunión', 7, 'aprobado'),
(3, 2026, '2026-03-14', 'ingreso', 1000, 'Cuota reunión', 8, 'aprobado'),
(3, 2026, '2026-03-14', 'ingreso', 1000, 'Cuota reunión', 9, 'aprobado'),
(3, 2026, '2026-03-21', 'egreso', 2500, 'Cuerdas para taller de pionerismo', NULL, 'aprobado');

-- 13. INVENTARIO
INSERT INTO `inventario` (`articulo`, `estado`, `cantidad`, `unidad_id`, `categoria`, `nombre_item`) VALUES
('Carpa Doite 4P', 'Bueno', 2, 3, 'Campamento', 'Carpa Patrulla Pumas'),
('Carpa Doite 4P', 'Regular', 1, 3, 'Campamento', 'Carpa Patrulla Halcones'),
('Hacha de mano', 'Bueno', 2, 3, 'Herramientas', 'Hachas de Tropa'),
('Bidón de Agua 20L', 'Bueno', 4, NULL, 'Logística', 'Bidones Grupo'),
('Pelotas de tenis', 'Malo', 10, 1, 'Juegos', 'Pelotas Manada');

-- 14. REUNIONES
INSERT INTO `reuniones` (`tipo_organo`, `fecha`, `tema`, `acta`, `creado_por`) VALUES
('Consejo de Grupo', '2026-03-05 20:00:00', 'Planificación Anual', 'Se definieron las fechas de campamentos y cuotas anuales. Aprobado por unanimidad.', 1),
('Asamblea de Grupo', '2026-03-28 16:00:00', 'Elección de Directiva', 'Se votó por la nueva directiva del grupo para el periodo 2026-2028.', 1);

-- 15. CUOTAS MENSUALES
INSERT INTO `cuotas_mensuales` (`beneficiario_id`, `anio`, `mes`, `monto`, `pagado`, `fecha_pago`) VALUES
(1, 2026, 3, 3000, 1, '2026-03-10'),
(2, 2026, 3, 3000, 1, '2026-03-14'),
(3, 2026, 3, 3000, 0, NULL),
(7, 2026, 3, 3000, 1, '2026-03-05'),
(8, 2026, 3, 3000, 1, '2026-03-07');
