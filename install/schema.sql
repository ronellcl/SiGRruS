-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sigrrus
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `actas`
--

DROP TABLE IF EXISTS `actas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `contenido` text DEFAULT NULL,
  `acuerdos` text DEFAULT NULL,
  `registrado_por` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `anios_scout`
--

DROP TABLE IF EXISTS `anios_scout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `anios_scout` (
  `anio` int(11) NOT NULL,
  `estado` varchar(20) DEFAULT 'borrador',
  `fecha_cierre` datetime DEFAULT NULL,
  `valor_inscripcion` int(11) DEFAULT 0,
  PRIMARY KEY (`anio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `apoderados`
--

DROP TABLE IF EXISTS `apoderados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apoderados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(150) DEFAULT NULL,
  `rut` varchar(20) DEFAULT NULL,
  `tipo_documento` varchar(50) DEFAULT 'RUT',
  `nacionalidad` varchar(100) DEFAULT 'Chilena',
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rut` (`rut`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asistencias`
--

DROP TABLE IF EXISTS `asistencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asistencias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actividad_id` int(11) DEFAULT NULL,
  `beneficiario_id` int(11) DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `actividad_id` (`actividad_id`),
  KEY `beneficiario_id` (`beneficiario_id`),
  CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`actividad_id`) REFERENCES `ciclo_programa` (`id`),
  CONSTRAINT `asistencias_ibfk_2` FOREIGN KEY (`beneficiario_id`) REFERENCES `beneficiarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `beneficiario_inscripcion`
--

DROP TABLE IF EXISTS `beneficiario_inscripcion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `beneficiario_inscripcion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `beneficiario_id` int(11) DEFAULT NULL,
  `unidad_id` int(11) DEFAULT NULL,
  `subgrupo` varchar(50) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_salida` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `beneficiarios`
--

DROP TABLE IF EXISTS `beneficiarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `beneficiarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(150) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `rut` varchar(20) DEFAULT NULL,
  `tipo_documento` varchar(50) DEFAULT 'RUT',
  `nacionalidad` varchar(100) DEFAULT 'Chilena',
  `apoderado_id` int(11) DEFAULT NULL,
  `apoderado_suplente_1_id` int(11) DEFAULT NULL,
  `apoderado_suplente_2_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rut` (`rut`),
  KEY `apoderado_id` (`apoderado_id`),
  KEY `apoderado_suplente_1_id` (`apoderado_suplente_1_id`),
  KEY `apoderado_suplente_2_id` (`apoderado_suplente_2_id`),
  CONSTRAINT `beneficiarios_ibfk_1` FOREIGN KEY (`apoderado_id`) REFERENCES `apoderados` (`id`) ON DELETE SET NULL,
  CONSTRAINT `beneficiarios_ibfk_2` FOREIGN KEY (`apoderado_suplente_1_id`) REFERENCES `apoderados` (`id`) ON DELETE SET NULL,
  CONSTRAINT `beneficiarios_ibfk_3` FOREIGN KEY (`apoderado_suplente_2_id`) REFERENCES `apoderados` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campamento_participantes`
--

DROP TABLE IF EXISTS `campamento_participantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campamento_participantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campamento_id` int(11) DEFAULT NULL,
  `beneficiario_id` int(11) DEFAULT NULL,
  `autorizado` int(11) DEFAULT 0,
  `fecha_autorizacion` datetime DEFAULT NULL,
  `observaciones_apoderado` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campamento_id` (`campamento_id`),
  KEY `beneficiario_id` (`beneficiario_id`),
  CONSTRAINT `campamento_participantes_ibfk_1` FOREIGN KEY (`campamento_id`) REFERENCES `campamentos` (`id`),
  CONSTRAINT `campamento_participantes_ibfk_2` FOREIGN KEY (`beneficiario_id`) REFERENCES `beneficiarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campamentos`
--

DROP TABLE IF EXISTS `campamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `unidad_id` int(11) DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `lugar` varchar(150) DEFAULT NULL,
  `costo_cuota` int(11) DEFAULT 0,
  `objetivos` text DEFAULT NULL,
  `programa_resumen` text DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'Planificación',
  `ciclo_actividad_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `certificados_dirigentes`
--

DROP TABLE IF EXISTS `certificados_dirigentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certificados_dirigentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `archivo_path` varchar(255) DEFAULT NULL,
  `fecha_subida` datetime DEFAULT current_timestamp(),
  `tipo` varchar(100) DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ciclo_programa`
--

DROP TABLE IF EXISTS `ciclo_programa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ciclo_programa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unidad_id` int(11) DEFAULT NULL,
  `nombre_actividad` varchar(150) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `lugar` varchar(150) DEFAULT NULL,
  `es_extra` int(11) DEFAULT 0,
  `es_campamento` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `config_grupo`
--

DROP TABLE IF EXISTS `config_grupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_grupo` (
  `id` int(11) NOT NULL,
  `nombre_grupo` varchar(150) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `institucion_patrocinante` varchar(150) DEFAULT NULL,
  `representante_patrocinante_nombre` varchar(150) DEFAULT NULL,
  `pais` varchar(100) DEFAULT 'Chile',
  `ciudad` varchar(100) DEFAULT NULL,
  `zona` varchar(100) DEFAULT NULL,
  `distrito` varchar(100) DEFAULT NULL,
  `asociacion_logo_path` varchar(255) DEFAULT NULL,
  `debug_mode` tinyint(1) DEFAULT 0,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_user` varchar(255) DEFAULT NULL,
  `smtp_pass` varchar(255) DEFAULT NULL,
  `smtp_port` int(11) DEFAULT NULL,
  `smtp_encryption` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `configuracion_grupo`
--

DROP TABLE IF EXISTS `configuracion_grupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuracion_grupo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_grupo` varchar(200) NOT NULL,
  `pais` varchar(100) DEFAULT 'Chile',
  `ciudad` varchar(100) DEFAULT NULL,
  `zona` varchar(100) DEFAULT NULL,
  `distrito` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cuotas_mensuales`
--

DROP TABLE IF EXISTS `cuotas_mensuales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cuotas_mensuales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `beneficiario_id` int(11) DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `monto` int(11) DEFAULT NULL,
  `pagado` int(11) DEFAULT 0,
  `fecha_pago` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dirigente_inscripcion`
--

DROP TABLE IF EXISTS `dirigente_inscripcion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dirigente_inscripcion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `unidad_id` int(11) DEFAULT NULL,
  `rol` varchar(50) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dirigentes`
--

DROP TABLE IF EXISTS `dirigentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dirigentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `unidad_id` int(11) DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `lugar` varchar(150) DEFAULT NULL,
  `costo_cuota` int(11) DEFAULT 0,
  `objetivos` text DEFAULT NULL,
  `programa_resumen` text DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'Planificación',
  `ciclo_actividad_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fichas_medicas`
--

DROP TABLE IF EXISTS `fichas_medicas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fichas_medicas` (
  `beneficiario_id` int(11) NOT NULL,
  `tipo_sangre` varchar(10) DEFAULT NULL,
  `alergias` text DEFAULT NULL,
  `enfermedades_cronicas` text DEFAULT NULL,
  `medicamentos` text DEFAULT NULL,
  `prevision_salud` varchar(100) DEFAULT NULL,
  `restricciones_alimenticias` text DEFAULT NULL,
  `vacunas_al_dia` int(11) DEFAULT 1,
  `observaciones_medicas` text DEFAULT NULL,
  `creado_por_usuario_id` int(11) DEFAULT NULL,
  `ultima_actualizacion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`beneficiario_id`),
  CONSTRAINT `fichas_medicas_ibfk_1` FOREIGN KEY (`beneficiario_id`) REFERENCES `beneficiarios` (`id`),
  CONSTRAINT `fichas_medicas_ibfk_2` FOREIGN KEY (`creado_por_usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `finanzas`
--

DROP TABLE IF EXISTS `finanzas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finanzas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(20) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `concepto` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `registrado_por` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `finanzas_movimientos`
--

DROP TABLE IF EXISTS `finanzas_movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finanzas_movimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unidad_id` int(11) DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `monto` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `beneficiario_id` int(11) DEFAULT NULL,
  `comprobante_archivo` varchar(255) DEFAULT NULL,
  `justificacion` text DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'aprobado',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hojas_ruta`
--

DROP TABLE IF EXISTS `hojas_ruta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hojas_ruta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actividad_id` int(11) DEFAULT NULL,
  `motivacion` text DEFAULT NULL,
  `lugar_detalles` text DEFAULT NULL,
  `fases` text DEFAULT NULL,
  `variantes` text DEFAULT NULL,
  `participacion` text DEFAULT NULL,
  `recursos_humanos` text DEFAULT NULL,
  `materiales` text DEFAULT NULL,
  `costos` text DEFAULT NULL,
  `seguridad` text DEFAULT NULL,
  `unidad_id` int(11) DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `nombre_actividad_manual` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventario`
--

DROP TABLE IF EXISTS `inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `articulo` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `unidad_id` int(11) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `nombre_item` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `leida` int(11) DEFAULT 0,
  `fecha` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `organos_roles`
--

DROP TABLE IF EXISTS `organos_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organos_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organo` varchar(50) DEFAULT NULL,
  `rol_especifico` varchar(100) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `apoderado_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `apoderado_id` (`apoderado_id`),
  CONSTRAINT `organos_roles_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `organos_roles_ibfk_2` FOREIGN KEY (`apoderado_id`) REFERENCES `apoderados` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reuniones`
--

DROP TABLE IF EXISTS `reuniones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reuniones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_organo` varchar(50) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `tema` varchar(255) DEFAULT NULL,
  `acta` text DEFAULT NULL,
  `archivo_acta_path` varchar(255) DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reuniones_asistencia`
--

DROP TABLE IF EXISTS `reuniones_asistencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reuniones_asistencia` (
  `reunion_id` int(11) NOT NULL,
  `entidad_id` int(11) NOT NULL,
  `tipo_entidad` varchar(20) NOT NULL,
  `asiste` int(11) DEFAULT 0,
  PRIMARY KEY (`reunion_id`,`entidad_id`,`tipo_entidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unidades`
--

DROP TABLE IF EXISTS `unidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `rama` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rut` varchar(20) DEFAULT NULL,
  `tipo_documento` varchar(50) DEFAULT 'RUT',
  `nacionalidad` varchar(100) DEFAULT 'Chilena',
  `must_change_password` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `rut` (`rut`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-24 22:03:14

INSERT INTO `unidades` (`id`, `nombre`, `rama`) VALUES 
(1, 'Manada de Lobatos', 'Lobatos'), 
(2, 'Bandada de Golondrinas', 'Golondrinas'), 
(3, 'Tropa Scout', 'Scouts'), 
(4, 'Compañía de Guías', 'Guías'), 
(5, 'Avanzada de Pioneros', 'Pioneros'), 
(6, 'Clan de Caminantes', 'Caminantes');
