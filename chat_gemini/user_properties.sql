-- phpMyAdmin SQL Dump
-- version 5.0.4deb2+deb11u2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 26-04-2026 a las 16:27:40
-- Versión del servidor: 10.5.29-MariaDB-0+deb11u1
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `devcarm_dev01`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_properties`
--

CREATE TABLE `user_properties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `agent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `country_id` bigint(20) UNSIGNED DEFAULT NULL,
  `state_id` bigint(20) UNSIGNED DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `floor_planning_image` varchar(255) DEFAULT NULL,
  `video_image` varchar(255) DEFAULT NULL,
  `price` decimal(15,2) DEFAULT NULL,
  `purpose` varchar(255) NOT NULL COMMENT 'Condicion',
  `type` varchar(255) NOT NULL,
  `km` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `area` varchar(100) NOT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `featured` int(11) NOT NULL DEFAULT 0,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `approve_status` int(11) NOT NULL DEFAULT 0 COMMENT '1=approved,0=pending,2=rejected	',
  `currency_symbol` varchar(255) NOT NULL DEFAULT '$',
  `currency_symbol_position` varchar(255) NOT NULL DEFAULT 'left',
  `brand_id` bigint(20) NOT NULL,
  `model_id` bigint(20) NOT NULL,
  `version_id` bigint(20) DEFAULT NULL,
  `transmision` varchar(100) NOT NULL COMMENT 'manual,automatica',
  `color` varchar(100) DEFAULT NULL,
  `traction` varchar(100) NOT NULL COMMENT '4x2,4x4,AWD,4wd',
  `engine` decimal(2,1) DEFAULT NULL,
  `cost` decimal(15,2) DEFAULT NULL,
  `shared` int(11) NOT NULL COMMENT '1=shared,0=not share',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `user_properties`
--
ALTER TABLE `user_properties`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `user_properties`
--
ALTER TABLE `user_properties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
