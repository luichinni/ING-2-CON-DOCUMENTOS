-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-05-2024 a las 22:44:00
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
-- Base de datos: `trueca_big_data`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `centros`
--

CREATE TABLE `centros` (
  `id` int(11) NOT NULL,
  `Nombre` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `hora_abre` time DEFAULT NULL,
  `hora_cierra` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `centro_volun`
--

CREATE TABLE `centro_volun` (
  `centro` int(11) NOT NULL,
  `voluntario` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

CREATE TABLE `comentario` (
  `id` int(11) NOT NULL,
  `publicacion` int(11) DEFAULT NULL,
  `user` varchar(50) DEFAULT NULL,
  `respondeA` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagen`
--

CREATE TABLE `imagen` (
  `archivo` varchar(255) NOT NULL,
  `publicacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intercambio`
--

CREATE TABLE `intercambio` (
  `id` int(11) NOT NULL,
  `voluntario` varchar(50) DEFAULT NULL,
  `publicacion1` int(11) DEFAULT NULL,
  `publicacion2` int(11) DEFAULT NULL,
  `horario` time DEFAULT NULL,
  `estado` enum('pendiente','cancelado','rechazado','aceptado','concretado') DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `donacion` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion`
--

CREATE TABLE `publicacion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `user` varchar(50) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publi_centro`
--

CREATE TABLE `publi_centro` (
  `publicacion` int(11) NOT NULL,
  `centro` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publi_inter`
--

CREATE TABLE `publi_inter` (
  `publicacion` int(11) NOT NULL,
  `intercambio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesionactiva`
--

CREATE TABLE `sesionactiva` (
  `user` varchar(50) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `ultimaAccion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fechaInicio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `username` varchar(50) NOT NULL,
  `clave` varchar(50) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `apellido` varchar(255) DEFAULT NULL,
  `dni` int(8) DEFAULT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `telefono` int(11) DEFAULT NULL,
  `rol` enum('user','volunt','admin') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `centros`
--
ALTER TABLE `centros`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `centro_volun`
--
ALTER TABLE `centro_volun`
  ADD PRIMARY KEY (`centro`,`voluntario`),
  ADD KEY `voluntario` (`voluntario`);

--
-- Indices de la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `respondeA` (`respondeA`);

--
-- Indices de la tabla `imagen`
--
ALTER TABLE `imagen`
  ADD PRIMARY KEY (`archivo`,`publicacion`),
  ADD KEY `publicacion` (`publicacion`);

--
-- Indices de la tabla `intercambio`
--
ALTER TABLE `intercambio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `voluntario` (`voluntario`),
  ADD KEY `publicacion1` (`publicacion1`),
  ADD KEY `publicacion2` (`publicacion2`);

--
-- Indices de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `publi_centro`
--
ALTER TABLE `publi_centro`
  ADD PRIMARY KEY (`publicacion`,`centro`),
  ADD KEY `centro` (`centro`);

--
-- Indices de la tabla `publi_inter`
--
ALTER TABLE `publi_inter`
  ADD PRIMARY KEY (`publicacion`,`intercambio`),
  ADD KEY `intercambio` (`intercambio`);

--
-- Indices de la tabla `sesionactiva`
--
ALTER TABLE `sesionactiva`
  ADD PRIMARY KEY (`user`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `centros`
--
ALTER TABLE `centros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comentario`
--
ALTER TABLE `comentario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `intercambio`
--
ALTER TABLE `intercambio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `centro_volun`
--
ALTER TABLE `centro_volun`
  ADD CONSTRAINT `centro_volun_ibfk_1` FOREIGN KEY (`centro`) REFERENCES `centros` (`id`),
  ADD CONSTRAINT `centro_volun_ibfk_2` FOREIGN KEY (`voluntario`) REFERENCES `usuarios` (`username`);

--
-- Filtros para la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD CONSTRAINT `comentario_ibfk_1` FOREIGN KEY (`user`) REFERENCES `usuarios` (`username`),
  ADD CONSTRAINT `comentario_ibfk_2` FOREIGN KEY (`respondeA`) REFERENCES `comentario` (`id`);

--
-- Filtros para la tabla `imagen`
--
ALTER TABLE `imagen`
  ADD CONSTRAINT `imagen_ibfk_1` FOREIGN KEY (`publicacion`) REFERENCES `publicacion` (`id`);

--
-- Filtros para la tabla `intercambio`
--
ALTER TABLE `intercambio`
  ADD CONSTRAINT `intercambio_ibfk_1` FOREIGN KEY (`voluntario`) REFERENCES `usuarios` (`username`),
  ADD CONSTRAINT `intercambio_ibfk_2` FOREIGN KEY (`publicacion1`) REFERENCES `publicacion` (`id`),
  ADD CONSTRAINT `intercambio_ibfk_3` FOREIGN KEY (`publicacion2`) REFERENCES `publicacion` (`id`);

--
-- Filtros para la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD CONSTRAINT `publicacion_ibfk_1` FOREIGN KEY (`user`) REFERENCES `usuarios` (`username`),
  ADD CONSTRAINT `publicacion_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`);

--
-- Filtros para la tabla `publi_centro`
--
ALTER TABLE `publi_centro`
  ADD CONSTRAINT `publi_centro_ibfk_1` FOREIGN KEY (`publicacion`) REFERENCES `publicacion` (`id`),
  ADD CONSTRAINT `publi_centro_ibfk_2` FOREIGN KEY (`centro`) REFERENCES `centros` (`id`);

--
-- Filtros para la tabla `publi_inter`
--
ALTER TABLE `publi_inter`
  ADD CONSTRAINT `publi_inter_ibfk_1` FOREIGN KEY (`publicacion`) REFERENCES `publicacion` (`id`),
  ADD CONSTRAINT `publi_inter_ibfk_2` FOREIGN KEY (`intercambio`) REFERENCES `intercambio` (`id`);

--
-- Filtros para la tabla `sesionactiva`
--
ALTER TABLE `sesionactiva`
  ADD CONSTRAINT `sesionactiva_ibfk_1` FOREIGN KEY (`user`) REFERENCES `usuarios` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
