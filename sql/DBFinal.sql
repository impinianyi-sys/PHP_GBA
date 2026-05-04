-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 04, 2026 at 04:57 PM
-- Server version: 8.0.45-0ubuntu0.22.04.1
-- PHP Version: 8.1.2-1ubuntu2.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `PHP_GBA`
--

-- --------------------------------------------------------

--
-- Table structure for table `casos`
--

CREATE TABLE `casos` (
  `id` int NOT NULL,
  `num_expedient` varchar(50) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `area_dret` varchar(100) DEFAULT NULL,
  `descripcion` text,
  `estado` enum('Prospecte','En negociació','Judicialitzat') DEFAULT 'Prospecte',
  `abogado_id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `client_dni_tipus` enum('DNI','NIE','Passaport') DEFAULT NULL,
  `client_dni_num` varchar(20) DEFAULT NULL,
  `client_rol` varchar(100) DEFAULT NULL,
  `adversari_nom` varchar(255) DEFAULT NULL,
  `adversari_advocat` varchar(255) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `casos`
--

INSERT INTO `casos` (`id`, `num_expedient`, `titulo`, `area_dret`, `descripcion`, `estado`, `abogado_id`, `cliente_id`, `client_dni_tipus`, `client_dni_num`, `client_rol`, `adversari_nom`, `adversari_advocat`, `fecha_creacion`) VALUES
(1, 'GBA-2026-5270', 'Divorcio forzado', 'Família', NULL, 'Prospecte', 8, 6, 'NIE', 'Z3674389G', 'Demandat', 'Julian', '', '2026-04-30 00:00:00'),
(2, 'GBA-2026-7213', 'Papeles Extranjeria', 'Penal', NULL, 'Prospecte', 8, 1, 'DNI', 'G3r2374340', 'Demandant', 'Hola', '', '2026-04-30 00:00:00'),
(3, 'GBA-2026-4150', 'Divorcio', 'Família', NULL, 'Prospecte', 11, 1, 'DNI', 'D1423587g', 'Demandant', 'Cristina', '', '2026-04-30 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` enum('admin','abogado','cliente') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'cliente',
  `especialidad` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `contraseña`, `rol`, `especialidad`) VALUES
(1, 'hola', 'hola@gmail.com', '$2y$10$Ctc.wx/lf..sIYUTnIJNpum.8chSvZ9hYM45f7GVp4UORZNiJzOWi', 'cliente', NULL),
(2, 'Abogado', 'abogado@gmail.com', '$2y$10$RA71Fq3OxvIWfLb.4jbbVurH0mdA6ot5JBSL1BIcuFLdgwCpOvgA6', 'abogado', NULL),
(3, 'test', 'test@gmail.com', '$2y$10$jomnQNH3nMWeU3vAOfdsYOLXQQqy0MDB5ageEuyNMkiqarwIOsID.', 'cliente', NULL),
(4, 'abotest', 'abotest@gmail.com', '$2y$10$lXOzCMrARpBpYnynHFCNkuYX.SDK1G.qj7yhc5MWfh1/bznJWAab.', 'abogado', NULL),
(5, 'admin_GBA', 'impinianyi@gmail.com', '$2y$10$qbCXW9bVs/p7p0xVzdwdverle4Q3wyedRJ7S4dFnLXNKssoKyvpaC', 'admin', NULL),
(6, 'test1', 'test1@gmail.com', '$2y$10$Q8j.8tzdphU3MMeGa6glSuAntpV3xS54gzMf.MoZjMv/yXU9iHGjK', 'cliente', NULL),
(7, 'holaa', 'holaa@gmail.com', '$2y$10$/nROOGObIHwf5SfORCxUBuqL/xleGZ1D0VZiINkAAHYefcmJvWz4S', 'cliente', NULL),
(8, 'Xavi garcia', 'xgarcia@gmail.com', '$2y$10$xc.Y4/Y/ZpzrQbkCJ8BDneLigpS1CBkLy0ji2lMYzIHQOQHJrTqa2', 'abogado', NULL),
(9, 'agastin ruiz', 'agus@gmail.com', '$2y$10$WPsNsuryRNm8P65iT0maEOURHHUsPQkV3QD6RSAR1wLiFD1BlM4H6', 'cliente', NULL),
(10, 'Agustin Lopez', 'agustin@gmail.com', '$2y$10$rhGsANtdEMA8Piq3hxNFF.dt3IRQp1TpveVkE7/uGCy.x2anSbHxy', 'cliente', NULL),
(11, 'Gus Ruiz', 'gus@gmail.com', '$2y$10$iAhYsl3DfwmP6FcttfZvW.hfblt6rCOziSku7znkBWEGwii8/NMgq', 'abogado', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `casos`
--
ALTER TABLE `casos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `num_expedient` (`num_expedient`),
  ADD KEY `fk_abogado_id` (`abogado_id`),
  ADD KEY `fk_cliente_id` (`cliente_id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `casos`
--
ALTER TABLE `casos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `casos`
--
ALTER TABLE `casos`
  ADD CONSTRAINT `fk_abogado_id` FOREIGN KEY (`abogado_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cliente_id` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
