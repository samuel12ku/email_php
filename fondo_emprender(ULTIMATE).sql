-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-07-2025 a las 18:24:44
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
(22, 'salsa', 'asasasasasa', 'queee', 'No', '2025-07-25 14:24:54'),
(23, 'querer comper y no poder', 'sdad', 'digamsoq ue si', 'Sí', '2025-07-25 14:26:49');

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
  `fecha_expedicion` date DEFAULT NULL,
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
  `centro_orientacion` varchar(10) NOT NULL,
  `orientador` varchar(100) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ruta_emprendedora`
--

INSERT INTO `ruta_emprendedora` (`id`, `nombres`, `apellidos`, `departamento`, `municipio`, `pais`, `tipo_id`, `numero_id`, `fecha_nacimiento`, `fecha_expedicion`, `fecha_orientacion`, `genero`, `nacionalidad`, `pais_origen`, `correo`, `clasificacion`, `discapacidad`, `tipo_emprendedor`, `nivel_formacion`, `carrera`, `celular`, `programa`, `situacion_negocio`, `ficha`, `centro_orientacion`, `orientador`, `fecha_registro`) VALUES
(25, 'carla', 'gaviota', 'Huila', 'La constumbre', '', 'CE', '1234567890', '2025-07-10', '2025-06-20', '2025-07-17', 'Mujer', 'Beliceño/a', 'Belice', 'panseco@dot.com', 'Raizales', 'Psicosocial', 'Egresado de otras instituciones', 'TECNÓLOGO', 'Gestión empresarial', '1234543212', 'Jóvenes en paz', 'Unidad productiva', '2825817', 'CLEM', 'Eiider cardona', '2025-07-28 15:07:59'),
(27, 'Yoana', 'Montana', 'Guainía', 'Tolima', '', 'CE', '5555010101', '2025-07-08', '2025-06-13', '0000-00-00', 'Mujer', 'Bareiní/a', 'Baréin', 'sofia@plus.com', 'Víctima de minas antipersona', 'Sordoceguera', 'Egresado de otras instituciones', 'TECNÓLOGO', 'Regencia de farmacia', '1234543212', 'Ninguno', 'Unidad productiva', 'No aplica', 'CLEM', 'Eiider cardona', '2025-07-30 10:46:54');

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuarios` int(11) NOT NULL,
  `nombres` varchar(50) DEFAULT NULL,
  `apellidos` varchar(20) DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `numero_id` varchar(50) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` set('emprendedor','orientador') DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado_avance` varchar(255) DEFAULT 'Sin iniciar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuarios`, `nombres`, `apellidos`, `correo`, `numero_id`, `celular`, `contrasena`, `rol`, `fecha_registro`, `estado_avance`) VALUES
(11, 'carla', 'gaviota', 'panseco@dot.com', '1234567890', '1234543212', '$2y$10$H.KOFxwP85n63LFqRkfGQOpmTCZWbZAL/Na5DTN9xp6Wykk2ccv7q', 'emprendedor', '2025-07-28 20:07:59', 'Sin iniciar'),
(12, 'pepe', 'pastel', 'pepe@doritos.com', '1029384756', '5555010101', '12345678', 'orientador', '2025-07-30 14:55:56', 'Sin iniciar'),
(13, 'Yoana', 'Montana', 'sofia@plus.com', '5555010101', '1234543212', '$2y$10$06Bu5g8XuvyGkGBmdjuJlOvbrgiXbSGyE2.NnJAEy9XnJJdkXcXNe', 'emprendedor', '2025-07-30 15:46:54', 'Sin iniciar');

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
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuarios`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `num_doc` (`numero_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `ruta_emprendedora`
--
ALTER TABLE `ruta_emprendedora`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `tarjeta_persona`
--
ALTER TABLE `tarjeta_persona`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuarios` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
