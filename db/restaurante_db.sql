CREATE DATABASE  IF NOT EXISTS `restaurante_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `restaurante_db`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: restaurante_db
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asignacion_mesa`
--

DROP TABLE IF EXISTS `asignacion_mesa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asignacion_mesa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mesa_id` int NOT NULL,
  `personal_id` int NOT NULL,
  `hora_inicio` datetime NOT NULL,
  `hora_fin` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__asignacion_mesa_id` (`mesa_id`),
  KEY `fk__asignacion_mesa__personal_id` (`personal_id`),
  CONSTRAINT `fk__asignacion_mesa__personal_id` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk__asignacion_mesa_id` FOREIGN KEY (`mesa_id`) REFERENCES `mesa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asignacion_mesa`
--

LOCK TABLES `asignacion_mesa` WRITE;
/*!40000 ALTER TABLE `asignacion_mesa` DISABLE KEYS */;
/*!40000 ALTER TABLE `asignacion_mesa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_pedido`
--

DROP TABLE IF EXISTS `detalle_pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_pedido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pedido_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `instrucciones_preparacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__detalle_pedido__pedido_id` (`pedido_id`),
  KEY `fk__detalle_pedido__producto_id` (`producto_id`),
  CONSTRAINT `fk__detalle_pedido__pedido_id` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk__detalle_pedido__producto_id` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pedido`
--

LOCK TABLES `detalle_pedido` WRITE;
/*!40000 ALTER TABLE `detalle_pedido` DISABLE KEYS */;
/*!40000 ALTER TABLE `detalle_pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factura`
--

DROP TABLE IF EXISTS `factura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pedido_id` int NOT NULL,
  `fecha_emision` datetime DEFAULT CURRENT_TIMESTAMP,
  `subtotal` decimal(10,2) NOT NULL,
  `impuestos` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','tarjeta_debito','tarjeta_credito','transferencia','mercado_pago') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `estado` enum('pendiente','pagada','cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT 'pendiente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_factura_pedido_id` (`pedido_id`),
  CONSTRAINT `fk__factura__pedido_id` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura`
--

LOCK TABLES `factura` WRITE;
/*!40000 ALTER TABLE `factura` DISABLE KEYS */;
/*!40000 ALTER TABLE `factura` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesa`
--

DROP TABLE IF EXISTS `mesa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mesa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero_mesa` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `capacidad` int NOT NULL,
  `ubicacion` enum('salon','barra','exterior') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `estado_mesa` enum('libre','ocupada','reservada','inhabilitada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT 'libre',
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq__mesa__numero_mesa` (`numero_mesa`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesa`
--

LOCK TABLES `mesa` WRITE;
/*!40000 ALTER TABLE `mesa` DISABLE KEYS */;
INSERT INTO `mesa` VALUES (1,'S-01',4,'salon','libre',1),(2,'S-02',2,'salon','libre',1),(3,'S-03',6,'salon','libre',1),(4,'S-04',4,'salon','libre',1),(5,'S-05',2,'salon','libre',1),(6,'S-06',4,'salon','libre',1),(7,'S-07',2,'salon','ocupada',1),(8,'E-01',4,'exterior','libre',1),(9,'E-02',2,'exterior','libre',1);
/*!40000 ALTER TABLE `mesa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido`
--

DROP TABLE IF EXISTS `pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mesa_id` int NOT NULL,
  `personal_id` int NOT NULL,
  `fecha_hora` datetime DEFAULT CURRENT_TIMESTAMP,
  `tipo_pedido` enum('mesa','domicilio','llevar') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT 'mesa',
  `estado_pedido` enum('pendiente','preparacion','listo','entregado','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT 'pendiente',
  `total` decimal(10,2) DEFAULT '0.00',
  `observaciones` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `creacion_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk__pedido__mesa_id` (`mesa_id`),
  KEY `fk__pedido__personal_id` (`personal_id`),
  CONSTRAINT `fk__pedido__mesa_id` FOREIGN KEY (`mesa_id`) REFERENCES `mesa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk__pedido__personal_id` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido`
--

LOCK TABLES `pedido` WRITE;
/*!40000 ALTER TABLE `pedido` DISABLE KEYS */;
/*!40000 ALTER TABLE `pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal`
--

DROP TABLE IF EXISTS `personal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `dni` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` enum('m','f','x') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `puesto` enum('encargado','cocinero','mozo','cajero','bartender') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `fecha_contratacion` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq__personal__dni` (`dni`),
  UNIQUE KEY `uq__personal__email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal`
--

LOCK TABLES `personal` WRITE;
/*!40000 ALTER TABLE `personal` DISABLE KEYS */;
INSERT INTO `personal` VALUES (1,'40123456','Carlos','Sánchez','sanchez.carlos@gmail.com','1123236921','1990-01-01','m','encargado','2023-10-06'),(2,'42664123','Julio','Castro','castro.julio@gmail.com','1152448712','1996-03-09','m','mozo','2023-10-08'),(3,'3811238987','Jose','Conti','jose.conti@gmail.com','1123903112','1994-10-19','m','cocinero','2025-10-15'),(4,'3712332341','Carla','Sanchez','carla.sanchez@gmail.com','1141415341','1993-06-15','f','bartender','2025-10-15'),(5,'34123321','Santiago','Castro','casto.s@gmial.com','1143519098','1989-02-20','m','cocinero','2025-10-15'),(6,'41223456','Luca','Iglesias','inglesias.luca@gmail.com','1123335141','2001-09-06','m','cajero','2025-10-15'),(7,'29123008','Jorge','Casto','castro.jorge@gmail.com','1123412315','1979-10-25','m','cocinero','2025-10-15'),(8,'36123455','Julia','Santoro','santoro.julia@gmail.com','1154679890','1987-09-04','m','bartender','2025-10-15'),(9,'29556123','Jorge','Gonzalez','jorge.gonzalez@gmail.com','1123879131','1984-01-12','m','cajero','2025-11-02'),(10,'31123324','Pablo','Crespo','crespo.pablo@gmail.com','1132435565','1996-12-18','m','bartender','2025-11-03'),(11,'30669321','Javier','Lopez','lopez.javier@gmail.com','1132225689','1988-05-12','m','mozo','2025-11-03'),(12,'29123012','Jose','Hernandez','hernandez.j@gmail.com','1154657712','1985-05-29','m','mozo','2025-11-03'),(13,'41231456','Andrea','Fernandez','andrea.fernandez@gmail.com','1132667612','1999-02-04','f','bartender','2025-11-03'),(14,'29564123','Felipe','Aguirre','f.aguirre@outlook.com','1123567412','1987-02-15','m','mozo','2025-11-28');
/*!40000 ALTER TABLE `personal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto`
--

DROP TABLE IF EXISTS `producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `producto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad_stock` int NOT NULL,
  `categoria` enum('entrada','principal','guarnicion','bebida','postre') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq__producto__nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto`
--

LOCK TABLES `producto` WRITE;
/*!40000 ALTER TABLE `producto` DISABLE KEYS */;
/*!40000 ALTER TABLE `producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reserva`
--

DROP TABLE IF EXISTS `reserva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reserva` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mesa_id` int DEFAULT NULL,
  `nombre_cliente` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `telefono_cliente` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `fecha_reserva` datetime NOT NULL,
  `cantidad_personas` int NOT NULL,
  `estado_reserva` enum('confirmada','finalizada','cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'confirmada',
  PRIMARY KEY (`id`),
  KEY `fk__reserva__mesa__id` (`mesa_id`),
  CONSTRAINT `fk__reserva__mesa__id` FOREIGN KEY (`mesa_id`) REFERENCES `mesa` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserva`
--

LOCK TABLES `reserva` WRITE;
/*!40000 ALTER TABLE `reserva` DISABLE KEYS */;
/*!40000 ALTER TABLE `reserva` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id` int NOT NULL,
  `perfil_acceso` enum('admin','encargado','mozo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `pass_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  CONSTRAINT `fk__usuario__id__personal__id` FOREIGN KEY (`id`) REFERENCES `personal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'encargado','1234',1),(2,'mozo','1234',0),(3,'mozo','$2y$10$XJhwH93erj8RwgVdG.mfPO5CXtJ/IR/LUfnu9m1vFUNnP2yatuhpe',1),(4,'mozo','$2y$10$FFIndJVrSdpFyc6Injqktecnkhd1pAN0ywNtDDsSL5sdTsloVMqj2',1),(5,'mozo','$2y$10$wxJo6zwkS989DHqsyOiygeGu/JMvRkbCNTfrs64RQo98vCIN2ei2C',1),(6,'mozo','$2y$10$C8ADL2EX21Zk8wactPVlseVXIAuuhTV8xf1nfSusA98FWwWRUjKOe',1),(7,'mozo','$2y$10$/vFBdNaLzdPm45RSHOXFS.6hhlVCdYFAB4dzZB9v1hjj1ywrvZxWa',1),(8,'mozo','$2y$10$XHmzYvrE7Ztx6eva5EfvkOneb6/fAIUd9RXXiZK9u2CnTUqSdvRQK',0),(9,'mozo','$2y$10$8WWZRBTZ25vKDa8W0wpUEu24h2EtqzN/kQEuWUh4GMCchpKKgX83i',0),(10,'mozo','$2y$10$xbe5X/ZkOMfZYichX6p9MOFYnNlLQLSaXRDuFHsx9/CKOpu4vtfVG',0),(11,'mozo','$2y$10$Bw.B9vi0Pw6NYm2gKLfz2OqNIe87WB9SVGx3VCY8/IBbGnqShxWVW',1),(12,'mozo','$2y$10$rPuG1lPfXSpN3pH6xqNPmehXAyk0QHGDQ9QkuRMosRNa/BYBzB7qW',0),(13,'mozo','$2y$10$EkgYi27h9H10CZOIDEY1qOYuhyHYUg1cIBwV432Gjs.tPiRQ53KmC',0),(14,'mozo','$2y$10$4VxfVSBwx6AwRTamuQQXu..44IN/Dd6KzX0ANBN2QFzMb0mV0s/ee',1);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'restaurante_db'
--
/*!50003 DROP PROCEDURE IF EXISTS `sp_mesa_insert` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mesa_insert`(
    IN p_numero_mesa VARCHAR(20),
    IN p_capacidad INT,
    IN p_ubicacion ENUM('salon', 'barra', 'exterior')
)
BEGIN
    -- Inserta la nueva mesa. Por defecto, 'activo' es 1 y 'estadoMesa' es 'libre'
    INSERT INTO mesa (
        numero_mesa, 
        capacidad, 
        ubicacion, 
        estado_mesa, 
        activo
    ) 
    VALUES (
        p_numero_mesa, 
        p_capacidad, 
        p_ubicacion, 
        'libre',
        1
    );    
    -- Retorna el ID generado, necesario para recuperar el objeto completo en el Repository
    SELECT LAST_INSERT_ID();
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_mesa_select_by_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mesa_select_by_id`(
    IN mesa_id INT
)
BEGIN
    SELECT 
        m.id, 
        m.numero_mesa,
        m.capacidad, 
        m.ubicacion, 
        m.estado_mesa,
        m.activo
    FROM 
        mesa m
    WHERE 
        m.id = mesa_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_mesa_select_by_ubicacion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mesa_select_by_ubicacion`(
    IN p_ubicacion VARCHAR(10)
)
BEGIN
    SELECT
        id,
        numero_mesa,
        capacidad,
        ubicacion,
        estado_mesa,
        activo
    FROM
        mesa
    WHERE
        ubicacion = p_ubicacion
    ORDER BY
        numero_mesa ASC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_mesa_update` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mesa_update`(
    IN p_id INT,
    IN p_nroMesa VARCHAR(20),
    IN p_capacidad INT,
    IN p_ubicacion ENUM('salon', 'barra', 'exterior'),
    IN p_activo TINYINT(1) 
)
BEGIN
    UPDATE mesa
    SET 
        nroMesa = p_nroMesa,
        capacidad = p_capacidad,
        ubicacion = p_ubicacion,
        activo = p_activo
    WHERE 
        id = p_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_personal_existe_dni` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_personal_existe_dni`(
    IN personal_dni VARCHAR(20)
)
BEGIN
    SELECT 
        1 
    FROM 
        personal
    WHERE 
        dni = personal_dni
    LIMIT 1; -- Detiene la búsqueda después de la primera coincidencia
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_personal_existe_email` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_personal_existe_email`(
    IN personal_email VARCHAR(100)
)
BEGIN
    SELECT 
        1 
    FROM 
        personal
    WHERE 
        email = personal_email
    LIMIT 1;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_personal_insert` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_personal_insert`(
    -- Parámetros de Personal (los campos no automatizados)
    IN p_dni VARCHAR(20),
    IN p_nombre VARCHAR(100),
    IN p_apellido VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(20),
    IN p_fecha_nacimiento DATE,
    IN p_sexo CHAR(1),
    IN p_puesto VARCHAR(50),
    -- Parámetros de Usuario (los campos no automatizados)
    IN u_perfil_acceso VARCHAR(50),
    IN u_pass_hash VARCHAR(255)    
)
BEGIN
    START TRANSACTION;
    -- 1. Insertar en Personal
    INSERT INTO personal (dni, nombre, apellido, email, telefono, fecha_nacimiento, sexo, puesto, fecha_contratacion)
    VALUES (p_dni, p_nombre, p_apellido, p_email, p_telefono, p_fecha_nacimiento, p_sexo, p_puesto, CURDATE());

    SET @personal_id = LAST_INSERT_ID();

    -- 2. Insertar en Usuario
    INSERT INTO usuario (id, perfil_acceso, pass_hash, activo) 
    VALUES (@personal_id, u_perfil_acceso, u_pass_hash, TRUE);

COMMIT;
	-- 3. Select para devolver el ID nuevo id generado.
    SELECT @personal_id 'nuevoId';
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_personal_select_activo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_personal_select_activo`()
BEGIN
    SELECT 
        p.id, 
        p.dni, 
        p.nombre, 
        p.apellido, 
        p.fecha_nacimiento, 
        p.email, 
        p.telefono,
        p.sexo, 
        p.puesto, 
        p.fecha_contratacion,
        
        u.id 'idUsuario',
        u.pass_hash,
        u.perfil_acceso,
        u.activo 
    FROM 
        personal p
	INNER JOIN 
    usuario u
	ON 
    p.id = u.id
    WHERE
		u.activo = 1;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_personal_select_all` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_personal_select_all`()
BEGIN
    SELECT 
        p.id,
        p.dni,
        p.nombre,
        p.apellido,
        p.fecha_nacimiento,
        p.email,
        p.telefono,
        p.sexo,
        p.puesto,
        p.fecha_contratacion,
        
        u.id 'idUsuario',
        u.pass_hash,
        u.perfil_acceso,
        u.activo
    FROM
        personal p
	INNER JOIN
		usuario u
	ON
		p.id = u.id
	ORDER BY apellido;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_personal_select_by_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_personal_select_by_id`(
    IN personal_id INT
)
BEGIN
    SELECT 
        p.id, 
        p.dni, 
        p.nombre, 
        p.apellido, 
        p.fecha_nacimiento, 
        p.email, 
        p.telefono, 
        p.sexo, 
        p.puesto, 
        p.fecha_contratacion,
        u.id 'idUsuario',
        u.pass_hash,
        u.perfil_acceso,
        u.activo
    FROM 
        personal p
	INNER JOIN
		usuario u
	ON
		p.id = u.id    
    WHERE 
        p.id = personal_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_personal_update` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_personal_update`(
    IN p_id INT,
    IN p_dni VARCHAR(20), 
    IN p_nombre VARCHAR(100),
    IN p_apellido VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(20),
    IN p_fecha_nacimiento DATE,
    IN p_sexo CHAR(1),
    IN p_puesto VARCHAR(50),
    
    IN u_id INT,
    IN u_perfil_acceso VARCHAR(50),
    IN u_activo TINYINT(1),
    IN u_pass_hash VARCHAR(255)
)
BEGIN
    -- Definine un manejador para cualquier error SQL.
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Deshacer si algo falló.
        ROLLBACK;
        -- Re-lanzar la excepción para que el PDO de PHP la capture.
        RESIGNAL;
    END;
    START TRANSACTION;
    -- Actualiza la tabla Personal.
    UPDATE personal
    SET
        nombre = p_nombre,
        apellido = p_apellido,
        email = p_email,
        telefono = p_telefono,
        fecha_nacimiento = p_fecha_nacimiento,
        sexo = p_sexo,
        puesto = p_puesto
    WHERE 
        id = p_id;        
    -- Actualizar la tabla Usuario.
    UPDATE usuario
    SET
        perfil_acceso = u_perfil_acceso,
        activo = u_activo
    WHERE 
        id = u_id;
    -- Si todo es correcto, confirmar la transacción.
    COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-28  8:24:18
