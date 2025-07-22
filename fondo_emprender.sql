-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 22-07-2025 a las 19:45:38
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.4.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `fondo_emprender`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formulario_lean_canvas`
--

DROP TABLE IF EXISTS `formulario_lean_canvas`;
CREATE TABLE IF NOT EXISTS `formulario_lean_canvas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_emprendador` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento_emprendedor` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_proyecto` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `problema` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `solucion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternativas` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_unico` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ventaja` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuarios` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `clientes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `canales` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ingresos` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `costos` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `metricas` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `early_adopters` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs_to_be_done`
--

DROP TABLE IF EXISTS `jobs_to_be_done`;
CREATE TABLE IF NOT EXISTS `jobs_to_be_done` (
  `id` int NOT NULL AUTO_INCREMENT,
  `actor` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `job_1` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `job_2` varchar(255) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `job_3` varchar(255) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `jobs_to_be_done`
--

INSERT INTO `jobs_to_be_done` (`id`, `actor`, `job_1`, `job_2`, `job_3`, `created_at`) VALUES
(2, 'DSLKA', 'LDSAJL', 'DWLKJ', 'DLK', '2025-07-22 16:39:47'),
(3, 'JKLJSAD', 'KDJALKS', 'DSALJK', 'DLSKJ', '2025-07-22 16:39:47'),
(4, 'JSDALK', 'LKDJSALK', 'LKDLKJS', 'KDLKJ', '2025-07-22 16:39:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `necesidades`
--

DROP TABLE IF EXISTS `necesidades`;
CREATE TABLE IF NOT EXISTS `necesidades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `situacion_problematica` text COLLATE utf8mb3_spanish_ci,
  `descripcion_nino` text COLLATE utf8mb3_spanish_ci,
  `descripcion_persona_mayor` text COLLATE utf8mb3_spanish_ci,
  `validadores_entendieron` enum('Sí','No') COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

--
-- Volcado de datos para la tabla `necesidades`
--

INSERT INTO `necesidades` (`id`, `situacion_problematica`, `descripcion_nino`, `descripcion_persona_mayor`, `validadores_entendieron`, `fecha`) VALUES
(12, 'pasta peros', 'sqw', 'sqwa', 'No', '2025-07-21 14:43:10'),
(13, 'querer comper y no poder', 'ni idea', 'waaradag', 'Sí', '2025-07-21 14:43:41'),
(14, 'per peraa', 'asasasasasa', 'sqwa', 'No', '2025-07-21 14:45:47'),
(15, 'pasta perosaq', 'asasasasasa', 'queee', 'No', '2025-07-21 14:54:38'),
(16, 'efef', 'fwefewf', 'ewfewf', 'Sí', '2025-07-22 13:43:22'),
(17, 'dwjdklj', 'DLDKA', 'J', '', '2025-07-22 13:43:39'),
(18, 'rwerwrwe', 'rerew', 'rwerwer', 'Sí', '2025-07-22 13:45:46'),
(19, 'dererewrr', 'rerew', 'rwerwer', 'No', '2025-07-22 13:54:33'),
(20, 'dererewrr', 'rerew', 'rwerwer', 'No', '2025-07-22 13:54:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ruta_emprendedora`
--

DROP TABLE IF EXISTS `ruta_emprendedora`;
CREATE TABLE IF NOT EXISTS `ruta_emprendedora` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departamento` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `municipio` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pais` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `fecha_orientacion` date NOT NULL,
  `genero` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nacionalidad` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pais_origen` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clasificacion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discapacidad` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_emprendedor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel_formacion` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `celular` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `programa` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `situacion_negocio` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ficha` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `programa_formacion` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `centro_orientacion` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orientador` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarjeta_persona`
--

DROP TABLE IF EXISTS `tarjeta_persona`;
CREATE TABLE IF NOT EXISTS `tarjeta_persona` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb3_spanish_ci NOT NULL,
  `descriptor` varchar(120) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `citas` text COLLATE utf8mb3_spanish_ci,
  `quien` text COLLATE utf8mb3_spanish_ci,
  `metas` text COLLATE utf8mb3_spanish_ci,
  `actitud` text COLLATE utf8mb3_spanish_ci,
  `comportamiento` text COLLATE utf8mb3_spanish_ci,
  `modas` text COLLATE utf8mb3_spanish_ci,
  `beneficios` text COLLATE utf8mb3_spanish_ci,
  `decisiones_tiempo` text COLLATE utf8mb3_spanish_ci,
  `decisiones_base` text COLLATE utf8mb3_spanish_ci,
  `job_funcional` text COLLATE utf8mb3_spanish_ci,
  `job_emocional` text COLLATE utf8mb3_spanish_ci,
  `job_social` text COLLATE utf8mb3_spanish_ci,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

--
-- Volcado de datos para la tabla `tarjeta_persona`
--

INSERT INTO `tarjeta_persona` (`id`, `nombre`, `descriptor`, `citas`, `quien`, `metas`, `actitud`, `comportamiento`, `modas`, `beneficios`, `decisiones_tiempo`, `decisiones_base`, `job_funcional`, `job_emocional`, `job_social`, `fecha_registro`) VALUES
(2, 'oe mka', 'as', 'as', 'as', 'as', 'as', 'ass', 'as', 'as', 'as', 'as', 'sas', 'sa', 'sasasa', '2025-07-21 16:13:04'),
(3, 'felpie', 'hjbjbhjbj', 'jbhh', 'bj', 'hjbhj', 'hjbhjb', 'jbhjb', 'jhbhjb', 'bjhb', 'bhjbhj', 'hjbhj', 'hjbhjb', 'jb', 'oe', '2025-07-22 08:20:16');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
