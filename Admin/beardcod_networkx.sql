-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 03, 2020 at 02:29 AM
-- Server version: 10.3.24-MariaDB
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `beardcod_networkx`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `state` varchar(65) NOT NULL,
  `pin` int(6) NOT NULL,
  `house` varchar(255) NOT NULL,
  `area` varchar(255) NOT NULL,
  `landmark` varchar(255) NOT NULL,
  `town` varchar(65) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `state`, `pin`, `house`, `area`, `landmark`, `town`) VALUES
(1, 1, 'Chhattisgarh', 495684, '658', 'sector 3', 'balco club', 'balco, korba'),
(2, 1, 'Odisha', 751021, '364', 'lane 1, sector 5', 'niladri vihar', 'Bhubaneswar ');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(65) NOT NULL,
  `password` varchar(65) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`) VALUES
(1, 'Ritam', 'ritambalco@gmail.com', '$2y$10$vgVUl9GwGLsTIXnUxNX2Z.I2rS6dWGk.A9QJbimSm4fmMYNJQusGy');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(65) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `image`) VALUES
(1, 'Vestige', 'uploads/9118-vestige.jpg'),
(2, 'ModiCare', 'uploads/3123-modicare.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` varchar(10) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `delivered` int(11) NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `address_id`, `user_id`, `order_id`, `product_id`, `quantity`, `delivered`, `date`) VALUES
(10, 2, 1, 'NX-3D7D5', 3, 4, 0, '2020-10-01'),
(11, 2, 1, 'NX-3D7D5', 6, 2, 0, '2020-10-01'),
(12, 2, 1, 'NX-7479F', 1, 2, 0, '2020-10-01'),
(13, 2, 1, 'NX-7FF85', 8, 1, 1, '2020-10-02'),
(14, 2, 1, 'NX-DD9D0', 5, 1, 0, '2020-10-02'),
(15, 2, 1, 'NX-1EB8F', 2, 2, 0, '2020-10-02'),
(16, 2, 1, 'NX-1EB8F', 1, 1, 0, '2020-10-02'),
(17, 1, 1, 'NX-BF36C', 4, 1, 1, '2020-10-02');

-- --------------------------------------------------------

--
-- Table structure for table `product-category`
--

CREATE TABLE `product-category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product-category`
--

INSERT INTO `product-category` (`id`, `name`, `image`) VALUES
(1, 'Health Care', 'uploads/1014-health.jpg'),
(2, 'Education', 'uploads/5577-education.png'),
(3, 'Skin Care', 'uploads/5986-skincare.jpg'),
(4, 'Fitness', 'uploads/6852-fitness.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `company` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(65) NOT NULL,
  `description` text NOT NULL,
  `price` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `available` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `company`, `name`, `category`, `description`, `price`, `image`, `available`) VALUES
(1, 'Vestige', 'Protein Powder', 'Health Care', 'Chocolate or Vanilla flavoured protein powder.', 1000, 'uploads/9906-product1.jpg', 1),
(2, 'Vestige', 'Horlicks Combo', 'Health Care', 'A Horlicks combo pack for the entire family.', 500, 'uploads/6703-product2.jpeg', 1),
(3, 'Vestige', 'Python Course', 'Education', 'A Python crash course for beginners.', 100, 'uploads/2423-product3.jpg', 0),
(4, 'Vestige', 'Digital Marketing course', 'Education', 'Digital Marketing Course with certification.', 500, 'uploads/1901-product4.png', 1),
(5, 'Vestige', 'Colossal Kajal', 'Skin Care', 'Colossal Eye Kajal, 24 hours smudge free guaranteed .', 100, 'uploads/5721-product5.jpg', 1),
(6, 'Vestige', 'Dove Combo Pack', 'Skin Care', 'Dove skin care combo pack.\r\nIncludes shampoo, conditioner , deodorant and more..', 750, 'uploads/2510-product6.jpg', 1),
(7, 'Vestige', '30 kg Hex Dumbells', 'Fitness', 'A set of 30 kg Hex Dumbells.', 1500, 'uploads/7742-product7.jpg', 1),
(8, 'Vestige', 'Gloucon D Nimbu', 'Fitness', 'Gloucon D Nimbu flavour combo pack', 400, 'uploads/1719-product8.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reset`
--

CREATE TABLE `reset` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(10) NOT NULL,
  `time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shop-slider`
--

CREATE TABLE `shop-slider` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `shop-slider`
--

INSERT INTO `shop-slider` (`id`, `image`) VALUES
(2, 'uploads/1783-chain.png'),
(3, 'uploads/4703-chain2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `email` varchar(65) NOT NULL,
  `password` varchar(65) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'uploads/default.png',
  `aadhaar` varchar(12) NOT NULL,
  `my_ref` varchar(10) NOT NULL,
  `super_ref` varchar(10) NOT NULL,
  `company` varchar(65) NOT NULL,
  `mobile` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `dob`, `email`, `password`, `image`, `aadhaar`, `my_ref`, `super_ref`, `company`, `mobile`) VALUES
(1, 'Ritam Banerjee', '1999-10-18', 'ritambalco@gmail.com', '$2y$10$.227DZnyf/GK6NohToJL6eEZROI/0p5UWqaoVqTCY5kpKS8hXq/ga', 'uploads/1188-IMG-20200127-WA0002.jpg', '324425000123', 'RIT-59228', 'null', 'Vestige', '7898789574'),
(8, 'Ankit Roy', '2020-09-01', 'beardcoder.roy@gmail.com', '$2y$10$77ZK7it1VXKy0z93kJavK.kFGsuB4Rsj6VLkxhgWzI2BQV2HRpy5u', 'uploads/6610-LRM_EXPORT_651737128415495_20190911_020730244.jpeg', '123456789012', 'ANK-E43BA', 'RIT-59228', 'Vestige', '8888888888'),
(9, 'Deba', '2020-09-02', 'd@d.com', '$2y$10$ldKUaLf/W1AGvLNsZgxhdOeqzTrVbDO/65pmpgZ87TOHPMdZIZUm6', 'uploads/default.png', '343434343434', 'DEB-67EA2', 'RIT-59228', 'Vestige', '9999999999'),
(10, 'Prerna', '2020-09-02', 'p@p.com', '$2y$10$J7wscg5wgUoI7pVoz.5Vweue0AtBIX.SrfgLo0YuUP8gjjeMcXxAe', 'uploads/default.png', '676767766776', 'PRE-02316', 'ANK-E43BA', 'Vestige', '7777777777'),
(11, 'Shree', '2020-09-12', 's@s.com', '$2y$10$qDgyr/dpvu1kfTTYIQTjGuoCNEER.Oxhef7wF3tSmK5p3nTbKa8ku', 'uploads/default.png', '434343434343', 'SHR-571FE', 'RIT-59228', 'Vestige', '6666666666'),
(12, 'Prasant kumar', '2000-01-06', 'kprasant635@gmail.com', '$2y$10$gsN2KQKvPKTTgGld9Wiv/uLYGBFIB6vSJQEqbDaW5p4Q3gBrp6xQi', 'uploads/default.png', '801833934242', 'PRA-EE866', 'ANK-E43BA', 'Vestige', '7440138835');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product-category`
--
ALTER TABLE `product-category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reset`
--
ALTER TABLE `reset`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop-slider`
--
ALTER TABLE `shop-slider`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `product-category`
--
ALTER TABLE `product-category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reset`
--
ALTER TABLE `reset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shop-slider`
--
ALTER TABLE `shop-slider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
