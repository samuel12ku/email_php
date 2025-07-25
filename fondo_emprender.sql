-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-07-2025 a las 15:25:25
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

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

CREATE TABLE `formulario_lean_canvas` (
  `id` int(11) NOT NULL,
  `nombre_emprendador` varchar(100) NOT NULL,
  `documento_emprendedor` varchar(20) NOT NULL,
  `nombre_proyecto` varchar(100) NOT NULL,
  `problema` text NOT NULL,
  `solucion` text NOT NULL,
  `alternativas` text NOT NULL,
  `valor_unico` text NOT NULL,
  `ventaja` text NOT NULL,
  `usuarios` text NOT NULL,
  `clientes` text NOT NULL,
  `canales` text NOT NULL,
  `ingresos` text NOT NULL,
  `costos` text NOT NULL,
  `metricas` text NOT NULL,
  `early_adopters` text NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `formulario_lean_canvas`
--

INSERT INTO `formulario_lean_canvas` (`id`, `nombre_emprendador`, `documento_emprendedor`, `nombre_proyecto`, `problema`, `solucion`, `alternativas`, `valor_unico`, `ventaja`, `usuarios`, `clientes`, `canales`, `ingresos`, `costos`, `metricas`, `early_adopters`, `fecha_registro`) VALUES
(16, 'f', '1234567890', 'Verdevivo', 'EDQ', 'WQEQQWE', 'QWEQWEQ', 'QEWQEWQEW', 'WEQWE', 'WEWEWQ', 'QWE', 'QEWWQ', 'EQWQWE', 'QWEQEW', 'WEQQWE', 'QWEQWE', '2025-07-22 16:08:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs_to_be_done`
--

CREATE TABLE `jobs_to_be_done` (
  `id` int(11) NOT NULL,
  `actor` varchar(255) NOT NULL,
  `job_1` varchar(255) NOT NULL,
  `job_2` varchar(255) DEFAULT NULL,
  `job_3` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `jobs_to_be_done`
--

INSERT INTO `jobs_to_be_done` (`id`, `actor`, `job_1`, `job_2`, `job_3`, `created_at`) VALUES
(2, 'DSLKA', 'LDSAJL', 'DWLKJ', 'DLK', '2025-07-22 16:39:47'),
(3, 'JKLJSAD', 'KDJALKS', 'DSALJK', 'DLSKJ', '2025-07-22 16:39:47'),
(4, 'JSDALK', 'LKDJSALK', 'LKDLKJS', 'KDLKJ', '2025-07-22 16:39:47'),
(5, 'klwñdk', 'kñlsdk', 'ñdsñ', 'ÑDÑKSA', '2025-07-22 21:07:19'),
(6, 'DAÑL', 'DÑKLASK', 'DÑAKÑ', 'ÑDSKL', '2025-07-22 21:07:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `necesidades`
--

CREATE TABLE `necesidades` (
  `id` int(11) NOT NULL,
  `situacion_problematica` text DEFAULT NULL,
  `descripcion_nino` text DEFAULT NULL,
  `descripcion_persona_mayor` text DEFAULT NULL,
  `validadores_entendieron` enum('Sí','No') DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
(20, 'dererewrr', 'rerew', 'rwerwer', 'No', '2025-07-22 13:54:59'),
(21, 'QEWQEW', 'WWQE', 'QWEQWE', 'Sí', '2025-07-22 21:08:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ruta_emprendedora`
--

CREATE TABLE `ruta_emprendedora` (
  `id` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `departamento` varchar(50) NOT NULL,
  `municipio` varchar(50) NOT NULL,
  `pais` varchar(50) NOT NULL,
  `tipo_id` varchar(5) NOT NULL,
  `numero_id` varchar(20) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `fecha_orientacion` date NOT NULL,
  `genero` varchar(15) NOT NULL,
  `nacionalidad` varchar(20) NOT NULL,
  `pais_origen` varchar(50) DEFAULT NULL,
  `correo` varchar(120) NOT NULL,
  `clasificacion` varchar(100) DEFAULT NULL,
  `discapacidad` varchar(30) DEFAULT NULL,
  `tipo_emprendedor` varchar(50) NOT NULL,
  `nivel_formacion` varchar(30) NOT NULL,
  `carrera` varchar(200) DEFAULT NULL,
  `celular` varchar(15) NOT NULL,
  `programa` varchar(30) NOT NULL,
  `situacion_negocio` varchar(30) NOT NULL,
  `ficha` varchar(30) NOT NULL,
  `programa_formacion` varchar(150) NOT NULL,
  `centro_orientacion` varchar(10) NOT NULL,
  `orientador` varchar(100) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ruta_emprendedora`
--

INSERT INTO `ruta_emprendedora` (`id`, `nombres`, `apellidos`, `departamento`, `municipio`, `pais`, `tipo_id`, `numero_id`, `fecha_nacimiento`, `fecha_orientacion`, `genero`, `nacionalidad`, `pais_origen`, `correo`, `clasificacion`, `discapacidad`, `tipo_emprendedor`, `nivel_formacion`, `carrera`, `celular`, `programa`, `situacion_negocio`, `ficha`, `programa_formacion`, `centro_orientacion`, `orientador`, `fecha_registro`) VALUES
(10, 'kevin', 'chenli_2mil', 'san fransico', 'chainis', 'Baréin', 'CC', '123456', '2323-12-13', '0023-03-22', 'Mujer', 'colombiano', NULL, 'ninetales@root.com', 'Víctima de minas antipersona', 'Auditiva', 'Aprendiz', 'Tecnólogo', 'Gestión de empresas agropecuarias', '5555010101', 'Jóvenes en paz', 'Unidad productiva', '12331232', '', 'CAB', 'Celiced Castaño Barco', '2025-07-25 08:24:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarjeta_persona`
--

CREATE TABLE `tarjeta_persona` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descriptor` varchar(120) DEFAULT NULL,
  `citas` text DEFAULT NULL,
  `quien` text DEFAULT NULL,
  `metas` text DEFAULT NULL,
  `actitud` text DEFAULT NULL,
  `comportamiento` text DEFAULT NULL,
  `modas` text DEFAULT NULL,
  `beneficios` text DEFAULT NULL,
  `decisiones_tiempo` text DEFAULT NULL,
  `decisiones_base` text DEFAULT NULL,
  `job_funcional` text DEFAULT NULL,
  `job_emocional` text DEFAULT NULL,
  `job_social` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tarjeta_persona`
--

INSERT INTO `tarjeta_persona` (`id`, `nombre`, `descriptor`, `citas`, `quien`, `metas`, `actitud`, `comportamiento`, `modas`, `beneficios`, `decisiones_tiempo`, `decisiones_base`, `job_funcional`, `job_emocional`, `job_social`, `fecha_registro`) VALUES
(2, 'oe mka', 'as', 'as', 'as', 'as', 'as', 'ass', 'as', 'as', 'as', 'as', 'sas', 'sa', 'sasasa', '2025-07-21 16:13:04'),
(3, 'felpie', 'hjbjbhjbj', 'jbhh', 'bj', 'hjbhj', 'hjbhjb', 'jbhjb', 'jhbhjb', 'bjhb', 'bhjbhj', 'hjbhj', 'hjbhjb', 'jb', 'oe', '2025-07-22 08:20:16');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `formulario_lean_canvas`
--
ALTER TABLE `formulario_lean_canvas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `jobs_to_be_done`
--
ALTER TABLE `jobs_to_be_done`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `necesidades`
--
ALTER TABLE `necesidades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ruta_emprendedora`
--
ALTER TABLE `ruta_emprendedora`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tarjeta_persona`
--
ALTER TABLE `tarjeta_persona`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `formulario_lean_canvas`
--
ALTER TABLE `formulario_lean_canvas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `jobs_to_be_done`
--
ALTER TABLE `jobs_to_be_done`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `necesidades`
--
ALTER TABLE `necesidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `ruta_emprendedora`
--
ALTER TABLE `ruta_emprendedora`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tarjeta_persona`
--
ALTER TABLE `tarjeta_persona`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
