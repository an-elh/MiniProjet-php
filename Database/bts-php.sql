-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2025 at 11:19 AM
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
-- Database: `bts`
--

-- --------------------------------------------------------

--
-- Table structure for table `amie`
--

CREATE TABLE `amie` (
  `id_utilisateur` int(11) NOT NULL,
  `id_amie` int(11) NOT NULL,
  `etat` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `amie`
--

INSERT INTO `amie` (`id_utilisateur`, `id_amie`, `etat`) VALUES
(30, 25, 'accepted'),
(32, 25, 'accepted'),
(32, 28, 'accepted'),
(32, 29, 'pending'),
(32, 30, 'accepted'),
(33, 25, 'accepted'),
(33, 29, 'pending'),
(33, 30, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `created_at`) VALUES
(1, 1, 25, 'test', '2025-04-08 20:07:25'),
(2, 1, 25, 'test', '2025-04-08 20:24:09'),
(3, 1, 25, 'test', '2025-04-08 20:52:29'),
(4, 1, 25, 'comment', '2025-04-08 20:57:28'),
(5, 1, 25, 'comment1', '2025-04-08 21:00:59'),
(6, 1, 32, 'comment', '2025-04-08 23:46:18'),
(7, 2, 25, 'test1', '2025-04-09 00:17:56');

-- --------------------------------------------------------

--
-- Table structure for table `invitation`
--

CREATE TABLE `invitation` (
  `emetteur` int(11) NOT NULL,
  `recipient` int(11) NOT NULL,
  `date` date NOT NULL,
  `etat` varchar(50) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES
(12, 1, 25, '2025-04-08 22:56:36'),
(13, 1, 32, '2025-04-08 23:46:09'),
(14, 2, 32, '2025-04-08 23:59:14'),
(15, 2, 25, '2025-04-09 00:18:01');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `expediteur_id` int(11) NOT NULL,
  `destinataire_id` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date_envoi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `expediteur_id`, `destinataire_id`, `contenu`, `date_envoi`) VALUES
(1, 25, 32, 'test', '2025-04-05 14:14:22'),
(2, 25, 32, 'test', '2025-04-05 14:21:16'),
(3, 32, 25, 'tss', '2025-04-05 14:22:59'),
(4, 32, 25, 'test', '2025-04-05 14:26:38'),
(5, 25, 32, 'cc', '2025-04-05 14:26:53'),
(6, 32, 25, 'cv', '2025-04-05 14:26:58'),
(7, 28, 32, 'cc', '2025-04-05 14:28:28'),
(8, 32, 28, 'cc', '2025-04-05 14:28:38'),
(9, 25, 32, 'trq', '2025-04-05 14:30:14'),
(10, 32, 28, 'ss', '2025-04-07 22:15:50'),
(11, 32, 25, 'test', '2025-04-07 22:15:57'),
(12, 32, 25, 'test', '2025-04-07 23:31:37'),
(13, 25, 32, 'wch', '2025-04-07 23:31:45'),
(14, 25, 32, 'cc', '2025-04-07 23:33:35'),
(15, 32, 25, 'cc', '2025-04-07 23:33:45'),
(16, 30, 32, 'ss', '2025-04-08 00:45:31'),
(17, 32, 30, 'ss', '2025-04-08 00:46:28'),
(18, 25, 30, 'test', '2025-04-09 01:18:41'),
(19, 25, 32, 'test', '2025-04-09 01:18:49'),
(20, 25, 33, 'test', '2025-04-09 01:27:29'),
(21, 33, 25, 'test', '2025-04-09 01:29:24');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `content`, `image`, `created_at`, `updated_at`) VALUES
(1, 30, 'Morning', 'post_image.jpg', '2025-04-07 22:56:24', '2025-04-07 22:56:25'),
(2, 32, 'test', NULL, '2025-04-08 23:59:10', '2025-04-08 23:59:10'),
(3, 33, 'evening\r\n', NULL, '2025-04-09 00:23:03', '2025-04-09 00:23:03'),
(4, 33, 'evening\r\n', NULL, '2025-04-09 00:25:45', '2025-04-09 00:25:45'),
(5, 33, 'evening\r\n', 'post_image.png', '2025-04-09 00:26:13', '2025-04-09 00:26:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `titre` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `adresse` text NOT NULL,
  `fonction` varchar(30) NOT NULL,
  `dateNaissance` date NOT NULL,
  `branche` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `interets` text NOT NULL,
  `photo` varchar(100) NOT NULL,
  `etat` varchar(20) NOT NULL DEFAULT 'hors ligne',
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `titre`, `email`, `adresse`, `fonction`, `dateNaissance`, `branche`, `password`, `interets`, `photo`, `etat`, `last_activity`) VALUES
(25, 'test3', 'test3', 'M', 'test@gmail.com', 'kenitra', 'Etudiant', '2024-10-29', 'SE', '1234', 'LEC, ART', 'Benz.jpg', 'hors ligne', '2025-04-09 00:27:39'),
(28, 'Elhout', 'Abdennour', 'M', 'abdennourelhout@gmail.com', 'Kenitra', 'Etudiant', '2024-11-05', 'DSI', '1234', 'LEC, VOY', 'noImage.jpg', 'hors ligne', '2025-04-07 16:15:45'),
(29, 'Elhout', 'Abdennour', 'M', 'abdennourelhout@gmail.com', 'Kenitra', 'Etudiant', '2024-11-05', 'DSI', '81dc9bdb52d04dc20036dbd8313ed055', 'LEC, VOY, ART', 'noImage.jpg', 'hors ligne', '2025-04-07 16:15:45'),
(30, 'test123', 'test123', 'M', '123@gmail.com', 'Kenitra', 'Professeur', '2025-02-24', 'DSI', '1234', 'VOY, SPORT', 'noImage.jpg', 'hors ligne', '2025-04-07 23:57:50'),
(32, 'testeur', 'testeur', '', 'testeur@gmail.com', 'kenitra', 'etd', '2025-03-03', 'MI', '1234', '', 'noImage.jpg', 'hors ligne', '2025-04-08 23:53:52'),
(33, 'Chamakh', 'Mohammed', 'M', 'Chamakh@gmail.com', 'Kenitra', 'Etudiant', '2003-11-05', 'DSI', '1234', 'VOY, SPORT', 'noImage.jpg', 'hors ligne', '2025-04-09 09:26:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amie`
--
ALTER TABLE `amie`
  ADD PRIMARY KEY (`id_utilisateur`,`id_amie`),
  ADD KEY `id_amie` (`id_amie`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `invitation`
--
ALTER TABLE `invitation`
  ADD PRIMARY KEY (`emetteur`,`recipient`),
  ADD KEY `recipient` (`recipient`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expediteur_id` (`expediteur_id`),
  ADD KEY `destinataire_id` (`destinataire_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `amie`
--
ALTER TABLE `amie`
  ADD CONSTRAINT `amie_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `amie_ibfk_2` FOREIGN KEY (`id_amie`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invitation`
--
ALTER TABLE `invitation`
  ADD CONSTRAINT `invitation_ibfk_1` FOREIGN KEY (`emetteur`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invitation_ibfk_2` FOREIGN KEY (`recipient`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`expediteur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`destinataire_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
