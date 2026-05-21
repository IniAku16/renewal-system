-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2026 at 10:12 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `renewal_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  `expired_date` date NOT NULL,
  `harga_renewal` bigint(20) NOT NULL,
  `payment_status` enum('done') NOT NULL,
  `payment_date` date NOT NULL,
  `request_count` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `serial_number`, `expired_date`, `harga_renewal`, `payment_status`, `payment_date`, `request_count`, `user_id`) VALUES
(144, 'sdwan', 'asasa', '2028-07-12', 500000000, 'done', '0000-00-00', 0, 16),
(145, 'Tape Drive', 'ssasqwqwqw', '2028-05-15', 555500000, 'done', '0000-00-00', 0, 16),
(146, 'hci', 'ssss', '2027-05-01', 2000000, 'done', '0000-00-00', 0, 16);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `departemen` varchar(100) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `last_activity` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `email`, `password`, `departemen`, `role`, `last_activity`) VALUES
(16, 'dika', 'dika@gmail.com', '$2y$10$Qi.dKzjaYg4MjXF98rhnY.ow4n7dWs7nb9G5YXSYJFCq18cLOLMj2', 'IT', 'user', NULL),
(17, 'biya', 'biya@gmail.com', '$2y$10$9BKWFjYKNpYkIAZb90yNuuAwTgxOB3svs1nE.gcyzIrbIjN.cP62u', 'accounting', 'user', NULL),
(18, 'ADMIN', 'admin@hexindo.co.id', '$2y$10$eDS1ruxPeaUQeCAxUF28oe0wLMd3uMY0kflQEjVD3FwQO8LEk2SxK', '-', 'admin', '2026-05-19 14:27:16'),
(19, 'egi', 'eginur@gmail.com', '$2y$10$J1wEGCXkaphYFfJxoPbr1OQmNzOfAkmmt0Lbo.yQcJJzo2OdZK6lq', 'Procurement', 'user', '2026-05-12 08:41:39'),
(22, 'ara', 'ara@gmail.com', '$2y$10$2oL3SbEfJmU2MXuN9u.bneTLE6sw/ktGM199NZ6fGLPD53hn7HMjK', 'Human Capital', 'user', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial_number` (`serial_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
