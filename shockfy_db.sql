-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-09-2025 a las 07:56:41
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
-- Base de datos: `brixventas_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `user_id`, `category_id`, `name`) VALUES
(1, 4, NULL, 'Polos Hombre'),
(2, 4, NULL, 'Pantalon Hombre'),
(3, 4, NULL, 'Polos Mujer'),
(4, 4, NULL, 'Pantalones Mujer'),
(11, 0, NULL, 'asda'),
(12, 1, NULL, 'categoria admin'),
(14, 5, NULL, 'categoria cliente1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `size` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `sale_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `total_sold` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `user_id`, `code`, `name`, `category`, `size`, `color`, `cost_price`, `sale_price`, `stock`, `created_at`, `category_id`, `image`, `total_sold`) VALUES
(3, 4, NULL, 'Polo Basic Hombre', '', 'L', 'Blanco', 6.83, 18.00, 5, '2025-09-16 04:27:07', 1, 'uploads/prod_68cf22898a1c74.85239569.jpg', 0),
(4, 4, NULL, 'Polo Basico Hombre', '', 'XL', 'NEGRO', 6.83, 18.00, 5, '2025-09-16 05:02:03', 1, 'uploads/prod_68cf2295212da7.99982652.jpeg', 0),
(5, 4, NULL, 'Polo Basico Hombre', '', 'L', 'NEGRO', 6.83, 18.00, 10, '2025-09-16 05:02:27', 1, 'uploads/prod_68cf22ab395097.98269225.jpeg', 0),
(6, 4, NULL, 'Polo Basico Hombre', '', 'M', 'BLANCO', 6.83, 18.00, 6, '2025-09-16 05:04:17', 1, 'uploads/prod_68cf22b58976c4.84092499.jpg', 0),
(8, 4, NULL, 'POLO BASICO HOMBRE', '', 'XL', 'BLANCO', 6.83, 18.00, 3, '2025-09-16 05:37:15', 1, 'uploads/prod_68cf22dc8e3aa0.11664551.jpg', 0),
(9, 4, NULL, 'Polo Basico Hombre', '', 'M', 'CAMELLO', 6.83, 18.00, 4, '2025-09-16 05:40:25', 1, 'uploads/prod_68cf22f3d46058.88589590.jpg', 0),
(10, 4, NULL, 'Polo Basico Hombre', '', 'L', 'CAMELLO', 6.83, 18.00, 4, '2025-09-16 05:40:54', 1, 'uploads/prod_68cf23039f4a82.68967147.jpg', 0),
(11, 4, NULL, 'Polo Basico Hombre', '', 'XL', 'CAMELLO', 6.83, 18.00, 2, '2025-09-16 05:41:20', 1, 'uploads/prod_68cf2313b4f678.99124509.jpg', 0),
(12, 4, NULL, 'Polo Basico Hombre', '', 'M', 'VERDE BOTELLA', 6.83, 18.00, 2, '2025-09-16 05:43:42', 1, 'uploads/prod_68cf232aba5909.70327956.jpg', 0),
(15, 4, NULL, 'Polo Basico Hombre', '', 'L', 'VERDE BOTELLA', 6.83, 18.00, 2, '2025-09-16 05:51:09', 1, 'uploads/prod_68cf233350efd5.12668416.jpg', 0),
(16, 4, NULL, 'Polo Basico Hombre', '', 'XL', 'VERDE BOTELLA', 6.83, 18.00, 1, '2025-09-16 05:51:36', 1, 'uploads/prod_68cf2348729608.84287907.jpg', 0),
(17, 4, NULL, 'Polo Basico Hombre', '', 'L', 'VINO', 6.83, 18.00, 5, '2025-09-16 05:52:26', 1, 'uploads/prod_68cf23661176a3.13262697.jpg', 0),
(18, 4, 'MELAGNE OSCURO', 'Polo Basico Hombre', '', 'M', 'PLOMO - GRIS', 6.83, 18.00, 2, '2025-09-16 05:53:46', 1, 'uploads/prod_68cf237a925ae2.41334368.jpeg', 0),
(20, 4, 'MELAGNE OSCURO2', 'Polo Basico Hombre', '', 'L', 'PLOMO - GRIS', 6.83, 18.00, 2, '2025-09-16 05:57:15', 1, 'uploads/prod_68cf23871ab262.08751218.jpeg', 0),
(21, 4, 'MELAGNE OSCURO3', 'Polo Basico Hombre', '', 'XL', 'PLOMO - GRIS', 6.83, 18.00, 1, '2025-09-16 05:57:39', 1, 'uploads/prod_68cf23904d8503.60242184.jpeg', 0),
(22, 4, NULL, 'Polo Basico Hombre', '', 'XL', 'AZUL', 6.83, 18.00, 1, '2025-09-16 06:03:53', 1, 'uploads/prod_68cf23ace762a5.89534758.jpg', 1),
(23, 4, NULL, 'Polo Basico Hombre', '', 'L', 'AZUL', 6.83, 18.00, 2, '2025-09-16 06:04:27', 1, 'uploads/prod_68cf23bd21dd41.41489018.jpg', 0),
(24, 4, NULL, 'Polo Basico Hombre', '', 'M', 'AZUL', 6.83, 18.00, 2, '2025-09-16 06:04:39', 1, 'uploads/prod_68cf23c5c72810.22151176.jpg', 0),
(25, 4, NULL, 'Polo Mangalarga Caido Suplex', '', 'Standard', 'Blanco', 13.40, 25.00, 0, '2025-09-18 00:23:26', 3, 'uploads/prod_68cf251e5e4ee6.83827429.png', 1),
(26, 4, NULL, 'Polo Mangalarga Caido Suplex', '', 'Standard', 'Beige', 13.40, 25.00, 1, '2025-09-18 00:23:58', NULL, NULL, 0),
(27, 4, NULL, 'Polo Mangalarga Caido Suplex', '', 'Standard', 'Negro', 13.40, 25.00, 1, '2025-09-18 00:24:19', NULL, NULL, 0),
(28, 4, NULL, 'Polo Mangalarga Caido Suplex', '', 'Standard', 'Verde Botella', 13.40, 25.00, 1, '2025-09-18 00:24:47', NULL, NULL, 0),
(29, 4, NULL, 'Polo Mangalarga Acampanado', '', 'Standard', 'Vino', 13.40, 25.00, 1, '2025-09-18 01:04:04', 3, 'uploads/prod_68cf2407a0e787.06179307.jpeg', 0),
(30, 4, NULL, 'Polo Mangalarga Acampanado', '', 'Standard', 'Negro', 13.40, 25.00, 1, '2025-09-18 01:04:28', 3, 'uploads/prod_68cf241e34b674.89069113.jpeg', 0),
(31, 4, NULL, 'Polo Strapless', '', 'Standard', 'Esmeralda - Verde', 13.40, 25.00, 1, '2025-09-18 01:07:01', 3, 'uploads/prod_68cf26646d10f5.18149683.jpeg', 0),
(32, 4, NULL, 'Polo Strapless', '', 'Standard', 'Marron', 13.40, 25.00, 1, '2025-09-18 01:07:26', 3, 'uploads/prod_68cf265a0a1d32.48959257.jpeg', 0),
(33, 4, NULL, 'Polo Strapless', '', 'Standard', 'Blanco', 13.40, 25.00, 1, '2025-09-18 01:07:51', 3, 'uploads/prod_68cf2645438f29.49673316.jpeg', 0),
(34, 4, NULL, 'Polo Strapless', '', 'Standard', 'Beige', 13.40, 25.00, 1, '2025-09-18 01:08:14', 3, 'uploads/prod_68cf263978ce06.82327615.jpeg', 0),
(35, 4, NULL, 'Polo Rip Manga Corta Caido', '', 'Standard', 'Azul Noche', 13.40, 25.00, 1, '2025-09-18 01:18:18', NULL, NULL, 0),
(36, 4, NULL, 'Polo Rip Mangacorta Caido', '', 'Standard', 'Camello', 13.40, 25.00, 1, '2025-09-18 01:20:32', NULL, NULL, 0),
(37, 4, NULL, 'Polo Rip Mangacorta Caido', '', 'Standard', 'Rojo', 13.40, 25.00, 1, '2025-09-18 01:20:50', 3, 'uploads/prod_68cf26864960c1.01975451.jpeg', 0),
(38, 4, NULL, 'Polo Rip Mangacorta Caido', '', 'Standard', 'Blanco', 13.40, 25.00, 1, '2025-09-18 01:22:56', 3, 'uploads/prod_68cf267c3b9e59.55648110.jpeg', 0),
(39, 4, NULL, 'Manga Larga Rip Rayitas', '', 'Standard', 'Verde Olivo', 13.40, 25.00, 1, '2025-09-18 01:33:57', 3, 'uploads/68cf204f55e2f.jpeg', 0),
(40, 4, NULL, 'Manga Larga Rip Rayitas', '', 'Standard', 'Camello Claro', 13.40, 25.00, 1, '2025-09-18 01:34:25', 3, 'uploads/prod_68cf21c82c37b4.29116463.jpeg', 0),
(41, 4, NULL, 'Manga Larga Rip Rayitas', '', 'Standard', 'Negro', 13.40, 25.00, 1, '2025-09-18 01:34:58', 3, 'uploads/prod_68cf2213462069.88303755.jpeg', 0),
(42, 4, NULL, 'Manga Larga Rip Rayitas', '', 'Standard', 'Beige', 13.40, 25.00, 1, '2025-09-18 01:35:19', 3, 'uploads/prod_68cf2254700bc3.20129000.jpeg', 0),
(43, 4, NULL, 'Manga Larga Rip Rayitas', '', 'Standard', 'Blanco', 13.40, 25.00, 1, '2025-09-18 01:35:44', 3, 'uploads/prod_68cf226b1b5715.63910061.jpeg', 0),
(44, 4, NULL, 'Polo Manga Larga Rip Rayitas Cuello Cuadrado', '', 'Standard', 'Negro', 13.40, 25.00, 1, '2025-09-18 01:39:15', 3, 'uploads/prod_68cf23e3bde9e5.09111457.jpeg', 0),
(49, 5, NULL, 'prueba cliente1', '', 'Standard', 'Negro', 13.00, 29.00, 7, '2025-09-21 21:36:36', 14, 'uploads/prod_68d06fe4e7ad60.40260841.jpg', 0),
(50, 1, NULL, 'prueba', '', 'Standard', 'Blanco', 5.00, 6.00, 2, '2025-09-22 04:13:37', 12, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products_backup`
--

CREATE TABLE `products_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `size` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `color` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `sale_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `products_backup`
--

INSERT INTO `products_backup` (`id`, `user_id`, `code`, `name`, `category`, `size`, `color`, `cost_price`, `sale_price`, `stock`, `created_at`, `category_id`, `image`) VALUES
(3, 4, NULL, 'Polo Basic Hombre', '', 'L', 'Blanco', 6.83, 18.00, 5, '2025-09-16 04:27:07', 1, 'uploads/prod_68cf22898a1c74.85239569.jpg'),
(4, 4, NULL, 'Polo Basico Hombre', '', 'XL', 'NEGRO', 6.83, 18.00, 5, '2025-09-16 05:02:03', 1, 'uploads/prod_68cf2295212da7.99982652.jpeg'),
(5, 4, NULL, 'Polo Basico Hombre', '', 'L', 'NEGRO', 6.83, 18.00, 10, '2025-09-16 05:02:27', 1, 'uploads/prod_68cf22ab395097.98269225.jpeg'),
(6, 4, NULL, 'Polo Basico Hombre', '', 'M', 'BLANCO', 6.83, 18.00, 6, '2025-09-16 05:04:17', 1, 'uploads/prod_68cf22b58976c4.84092499.jpg'),
(8, 4, NULL, 'POLO BASICO HOMBRE', '', 'XL', 'BLANCO', 6.83, 18.00, 3, '2025-09-16 05:37:15', 1, 'uploads/prod_68cf22dc8e3aa0.11664551.jpg'),
(9, 4, NULL, 'Polo Basico Hombre', '', 'M', 'CAMELLO', 6.83, 18.00, 4, '2025-09-16 05:40:25', 1, 'uploads/prod_68cf22f3d46058.88589590.jpg'),
(10, 4, NULL, 'Polo Basico Hombre', '', 'L', 'CAMELLO', 6.83, 18.00, 4, '2025-09-16 05:40:54', 1, 'uploads/prod_68cf23039f4a82.68967147.jpg'),
(11, 4, NULL, 'Polo Basico Hombre', '', 'XL', 'CAMELLO', 6.83, 18.00, 2, '2025-09-16 05:41:20', 1, 'uploads/prod_68cf2313b4f678.99124509.jpg'),
(12, 4, NULL, 'Polo Basico Hombre', '', 'M', 'VERDE BOTELLA', 6.83, 18.00, 2, '2025-09-16 05:43:42', 1, 'uploads/prod_68cf232aba5909.70327956.jpg'),
(15, 4, NULL, 'Polo Basico Hombre', '', 'L', 'VERDE BOTELLA', 6.83, 18.00, 2, '2025-09-16 05:51:09', 1, 'uploads/prod_68cf233350efd5.12668416.jpg'),
(16, 4, NULL, 'Polo Basico Hombre', '', 'XL', 'VERDE BOTELLA', 6.83, 18.00, 1, '2025-09-16 05:51:36', 1, 'uploads/prod_68cf2348729608.84287907.jpg'),
(17, 4, NULL, 'Polo Basico Hombre', '', 'L', 'VINO', 6.83, 18.00, 5, '2025-09-16 05:52:26', 1, 'uploads/prod_68cf23661176a3.13262697.jpg'),
(18, 4, 'MELAGNE OSCURO', 'Polo Basico Hombre', '', 'M', 'PLOMO - GRIS', 6.83, 18.00, 2, '2025-09-16 05:53:46', 1, 'uploads/prod_68cf237a925ae2.41334368.jpeg'),
(20, 4, 'MELAGNE OSCURO2', 'Polo Basico Hombre', '', 'L', 'PLOMO - GRIS', 6.83, 18.00, 2, '2025-09-16 05:57:15', 1, 'uploads/prod_68cf23871ab262.08751218.jpeg'),
(21, 4, 'MELAGNE OSCURO3', 'Polo Basico Hombre', '', 'XL', 'PLOMO - GRIS', 6.83, 18.00, 1, '2025-09-16 05:57:39', 1, 'uploads/prod_68cf23904d8503.60242184.jpeg'),
(22, 4, NULL, 'Polo Basico Hombre', '', 'XL', 'AZUL', 6.83, 18.00, 1, '2025-09-16 06:03:53', 1, 'uploads/prod_68cf23ace762a5.89534758.jpg'),
(23, 4, NULL, 'Polo Basico Hombre', '', 'L', 'AZUL', 6.83, 18.00, 2, '2025-09-16 06:04:27', 1, 'uploads/prod_68cf23bd21dd41.41489018.jpg'),
(24, 4, NULL, 'Polo Basico Hombre', '', 'M', 'AZUL', 6.83, 18.00, 2, '2025-09-16 06:04:39', 1, 'uploads/prod_68cf23c5c72810.22151176.jpg'),
(25, 4, NULL, 'Polo Mangalarga Caido Suplex', '', 'Standard', 'Blanco', 13.40, 25.00, 0, '2025-09-18 00:23:26', 3, 'uploads/prod_68cf251e5e4ee6.83827429.png'),
(26, 4, NULL, 'Polo Mangalarga Caido Suplex', '', 'Standard', 'Beige', 13.40, 25.00, 1, '2025-09-18 00:23:58', NULL, NULL),
(27, 4, NULL, 'Polo Mangalarga Caido Suplex', '', 'Standard', 'Negro', 13.40, 25.00, 1, '2025-09-18 00:24:19', NULL, NULL),
(28, 4, NULL, 'Polo Mangalarga Caido Suplex', '', 'Standard', 'Verde Botella', 13.40, 25.00, 1, '2025-09-18 00:24:47', NULL, NULL),
(29, 4, NULL, 'Polo Mangalarga Acampanado', '', 'Standard', 'Vino', 13.40, 25.00, 1, '2025-09-18 01:04:04', 3, 'uploads/prod_68cf2407a0e787.06179307.jpeg'),
(30, 4, NULL, 'Polo Mangalarga Acampanado', '', 'Standard', 'Negro', 13.40, 25.00, 1, '2025-09-18 01:04:28', 3, 'uploads/prod_68cf241e34b674.89069113.jpeg'),
(31, 4, NULL, 'Polo Strapless', '', 'Standard', 'Esmeralda - Verde', 13.40, 25.00, 1, '2025-09-18 01:07:01', 3, 'uploads/prod_68cf26646d10f5.18149683.jpeg'),
(32, 4, NULL, 'Polo Strapless', '', 'Standard', 'Marron', 13.40, 25.00, 1, '2025-09-18 01:07:26', 3, 'uploads/prod_68cf265a0a1d32.48959257.jpeg'),
(33, 4, NULL, 'Polo Strapless', '', 'Standard', 'Blanco', 13.40, 25.00, 1, '2025-09-18 01:07:51', 3, 'uploads/prod_68cf2645438f29.49673316.jpeg'),
(34, 4, NULL, 'Polo Strapless', '', 'Standard', 'Beige', 13.40, 25.00, 1, '2025-09-18 01:08:14', 3, 'uploads/prod_68cf263978ce06.82327615.jpeg'),
(35, 4, NULL, 'Polo Rip Manga Corta Caido', '', 'Standard', 'Azul Noche', 13.40, 25.00, 1, '2025-09-18 01:18:18', NULL, NULL),
(36, 4, NULL, 'Polo Rip Mangacorta Caido', '', 'Standard', 'Camello', 13.40, 25.00, 1, '2025-09-18 01:20:32', NULL, NULL),
(37, 4, NULL, 'Polo Rip Mangacorta Caido', '', 'Standard', 'Rojo', 13.40, 25.00, 1, '2025-09-18 01:20:50', 3, 'uploads/prod_68cf26864960c1.01975451.jpeg'),
(38, 4, NULL, 'Polo Rip Mangacorta Caido', '', 'Standard', 'Blanco', 13.40, 25.00, 1, '2025-09-18 01:22:56', 3, 'uploads/prod_68cf267c3b9e59.55648110.jpeg'),
(39, 4, NULL, 'Manga Larga Rip Rayitas', '', 'Standard', 'Verde Olivo', 13.40, 25.00, 1, '2025-09-18 01:33:57', 3, 'uploads/68cf204f55e2f.jpeg'),
(40, 4, NULL, 'Manga Larga Rip Rayitas', '', 'Standard', 'Camello Claro', 13.40, 25.00, 1, '2025-09-18 01:34:25', 3, 'uploads/prod_68cf21c82c37b4.29116463.jpeg'),
(41, 4, NULL, 'Manga Larga Rip Rayitas', '', 'Standard', 'Negro', 13.40, 25.00, 1, '2025-09-18 01:34:58', 3, 'uploads/prod_68cf2213462069.88303755.jpeg'),
(42, 4, NULL, 'Manga Larga Rip Rayitas', '', 'Standard', 'Beige', 13.40, 25.00, 1, '2025-09-18 01:35:19', 3, 'uploads/prod_68cf2254700bc3.20129000.jpeg'),
(43, 4, NULL, 'Manga Larga Rip Rayitas', '', 'Standard', 'Blanco', 13.40, 25.00, 1, '2025-09-18 01:35:44', 3, 'uploads/prod_68cf226b1b5715.63910061.jpeg'),
(44, 4, NULL, 'Polo Manga Larga Rip Rayitas Cuello Cuadrado', '', 'Standard', 'Negro', 13.40, 25.00, 1, '2025-09-18 01:39:15', 3, 'uploads/prod_68cf23e3bde9e5.09111457.jpeg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `sale_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sales`
--

INSERT INTO `sales` (`id`, `user_id`, `product_id`, `quantity`, `unit_price`, `total`, `sale_date`) VALUES
(22, 4, 22, 1, 18.00, 18.00, '2025-09-21 02:44:47'),
(23, 4, 25, 1, 25.00, 25.00, '2025-09-21 02:45:03'),
(24, 5, 49, 1, 29.00, 29.00, '2025-09-21 16:36:46'),
(26, 1, 50, 1, 6.00, 6.00, '2025-09-21 23:56:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'S/.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `currency_pref` varchar(10) DEFAULT 'S/.',
  `role` varchar(20) DEFAULT 'user',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `currency_pref`, `role`, `status`, `created_at`) VALUES
(1, 'admin', '$2y$10$2AiboY67TJ9460.1Fjq84.JEqjyZlXg4MVp0LxtzvAg1KZBCneus6', 'Freiber', '$', 'admin', 1, '2025-09-21 04:43:24'),
(4, 'wasted4k', '$2y$10$Rr4K1L9ywzltcSkACdsy1O2JM9kPheRhTiaYX7I51rbobsw1Zj77S', 'twenty one ventas', 'VES', 'user', 1, '2025-09-21 05:21:25'),
(5, 'cliente1', '$2y$10$xOrAjL88xu5L6RBiMaoyHOODsyeGahPtmGAqFrkQ2etzZCB8Nqr92', 'Cliente', 'S/.', 'user', 1, '2025-09-21 21:34:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_logs`
--

CREATE TABLE `user_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_logs`
--

INSERT INTO `user_logs` (`id`, `user_id`, `action`, `created_at`) VALUES
(1, 1, 'Cambió estado usuario ID 5 a 0', '2025-09-21 21:38:59'),
(2, 1, 'Cambió estado usuario ID 5 a 1', '2025-09-22 04:12:05'),
(3, 1, 'Cambió estado usuario ID 5 a 0', '2025-09-22 05:37:54'),
(4, 1, 'Cambió estado usuario ID 5 a 1', '2025-09-22 05:37:57');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `category_id` (`category_id`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `user_logs`
--
ALTER TABLE `user_logs`
  ADD CONSTRAINT `user_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
