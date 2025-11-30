-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour finalprojectcda
CREATE DATABASE IF NOT EXISTS `finalprojectcda` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `finalprojectcda`;

-- Listage de la structure de table finalprojectcda. doctrine_migration_versions
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Listage des données de la table finalprojectcda.doctrine_migration_versions : ~4 rows (environ)
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20250224203148', '2025-02-24 20:32:13', 22),
	('DoctrineMigrations\\Version20250302160817', '2025-03-02 16:08:29', 29),
	('DoctrineMigrations\\Version20250923183821', '2025-09-23 18:38:40', 127),
	('DoctrineMigrations\\Version20250924193528', '2025-09-24 19:35:57', 43);

-- Listage de la structure de table finalprojectcda. itinerary
CREATE TABLE IF NOT EXISTS `itinerary` (
  `id` int NOT NULL AUTO_INCREMENT,
  `itinerary_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` int DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `is_public` tinyint(1) NOT NULL,
  `departure_date` datetime NOT NULL,
  `created_by_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FF2238F6B03A8386` (`created_by_id`),
  CONSTRAINT `FK_FF2238F6B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table finalprojectcda.itinerary : ~9 rows (environ)
INSERT INTO `itinerary` (`id`, `itinerary_name`, `duration`, `creation_date`, `is_public`, `departure_date`, `created_by_id`) VALUES
	(4, 'Mon itinéraire', 3, '2025-10-28 15:30:10', 1, '2025-10-30 00:00:00', 3),
	(5, 'Road trip en Alsace', 8, '2025-11-05 11:04:22', 1, '2025-11-14 00:00:00', 3),
	(6, 'Voyage Grand Est', 4, '2025-11-05 11:05:31', 1, '2025-11-22 00:00:00', 3),
	(7, 'Premier road trip', 2, '2025-11-05 11:06:52', 1, '2025-11-15 00:00:00', 6),
	(8, 'Voyage en famille', 7, '2025-11-05 11:07:35', 1, '2025-11-20 00:00:00', 6),
	(9, 'Week-end entre amis', 2, '2025-11-05 11:08:27', 1, '2025-11-15 00:00:00', 6),
	(10, 'Semaine en Alsace', 7, '2025-11-05 12:40:36', 1, '2025-11-21 00:00:00', 8),
	(11, 'Week-end à Strasbourg', 2, '2025-11-05 12:41:16', 1, '2025-11-15 00:00:00', 8),
	(12, '3 jours à Mulhouse', 3, '2025-11-05 12:44:20', 1, '2025-11-16 00:00:00', 8),
	(13, '2 jours à Colmar', 2, '2025-11-05 12:47:42', 1, '2025-11-15 00:00:00', 8),
	(14, '5 jours en Alsace', 5, '2025-11-05 12:50:29', 1, '2025-11-16 00:00:00', 8);

-- Listage de la structure de table finalprojectcda. itinerary_location
CREATE TABLE IF NOT EXISTS `itinerary_location` (
  `id` int NOT NULL AUTO_INCREMENT,
  `itinerary_id` int NOT NULL,
  `location_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_index` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7F7CBD0A15F737B2` (`itinerary_id`),
  CONSTRAINT `FK_7F7CBD0A15F737B2` FOREIGN KEY (`itinerary_id`) REFERENCES `itinerary` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table finalprojectcda.itinerary_location : ~29 rows (environ)
INSERT INTO `itinerary_location` (`id`, `itinerary_id`, `location_id`, `order_index`) VALUES
	(5, 4, '68e50afde0747f909a774634', 4),
	(6, 4, '68e50b69e0747f909a77463a', 3),
	(7, 4, '68e273b170b9e9800b87c12f', 2),
	(8, 4, '68e5077ce0747f909a77462d', 0),
	(9, 4, '68e2732070b9e9800b87c127', 1),
	(10, 4, '68e2609782a7475004887b6f', 5),
	(11, 4, '68e5021be0747f909a77461f', 6),
	(12, 5, '68e2732070b9e9800b87c127', 0),
	(13, 5, '68e50b69e0747f909a77463a', 1),
	(14, 5, '68e50c09e0747f909a774643', 2),
	(15, 6, '68e273b170b9e9800b87c12f', 0),
	(16, 6, '68e2609782a7475004887b6f', 1),
	(17, 7, '68e50b69e0747f909a77463a', 0),
	(18, 7, '68e50c09e0747f909a774643', 1),
	(19, 8, '68e50b69e0747f909a77463a', 0),
	(20, 8, '68e504c3e0747f909a774629', 1),
	(21, 8, '68e50bb5e0747f909a77463f', 2),
	(22, 9, '6908ccef1f97097569463d7e', 0),
	(23, 9, '68e50c09e0747f909a774643', 1),
	(24, 10, '68e50afde0747f909a774634', 0),
	(25, 10, '68e50b69e0747f909a77463a', 1),
	(26, 11, '68e504c3e0747f909a774629', 0),
	(27, 11, '68e5077ce0747f909a77462d', 1),
	(28, 12, '68e273b170b9e9800b87c12f', 0),
	(29, 12, '6908ccef1f97097569463d7e', 1),
	(30, 13, '68e2609782a7475004887b6f', 0),
	(31, 13, '68e504c3e0747f909a774629', 1),
	(32, 14, '68e5021be0747f909a77461f', 0),
	(33, 14, '68e50bb5e0747f909a77463f', 1);

-- Listage de la structure de table finalprojectcda. itinerary_user
CREATE TABLE IF NOT EXISTS `itinerary_user` (
  `itinerary_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`itinerary_id`,`user_id`),
  KEY `IDX_8AB0BC1D15F737B2` (`itinerary_id`),
  KEY `IDX_8AB0BC1DA76ED395` (`user_id`),
  CONSTRAINT `FK_8AB0BC1D15F737B2` FOREIGN KEY (`itinerary_id`) REFERENCES `itinerary` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_8AB0BC1DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table finalprojectcda.itinerary_user : ~9 rows (environ)
INSERT INTO `itinerary_user` (`itinerary_id`, `user_id`) VALUES
	(4, 3),
	(5, 3),
	(6, 3),
	(7, 6),
	(8, 6),
	(9, 6),
	(10, 8),
	(11, 8),
	(12, 8),
	(13, 8),
	(14, 8);

-- Listage de la structure de table finalprojectcda. rating
CREATE TABLE IF NOT EXISTS `rating` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `location_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int NOT NULL,
  `comment` longtext COLLATE utf8mb4_unicode_ci,
  `rating_date` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_D8892622A76ED395` (`user_id`),
  CONSTRAINT `FK_D8892622A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table finalprojectcda.rating : ~11 rows (environ)
INSERT INTO `rating` (`id`, `user_id`, `location_id`, `rating`, `comment`, `rating_date`) VALUES
	(1, 3, '68b964a8a6c9cc27b3defa12', 4, 'ceci est un commentaire test', '2025-09-23 19:52:37'),
	(2, 3, '68b964a8a6c9cc27b3defa12', 4, 'ceci est un commentaire test', '2025-09-23 19:55:26'),
	(3, 3, '68b964a8a6c9cc27b3defa12', 4, 'super', '2025-09-24 18:56:20'),
	(4, 3, '68b964a8a6c9cc27b3defa12', 4, 'encore un avis', '2025-09-24 19:04:38'),
	(5, 3, '68b964a8a6c9cc27b3defa12', 4, 'blah', '2025-09-24 19:06:42'),
	(6, 3, '68b964a8a6c9cc27b3defa12', 4, 'blah', '2025-09-24 19:07:30'),
	(7, 3, '68b964a8a6c9cc27b3defa12', 4, 'blahblah', '2025-09-24 19:14:17'),
	(8, 3, '68b964a8a6c9cc27b3defa12', 4, 'dernier', '2025-09-24 19:29:07'),
	(9, 3, '68e2609782a7475004887b6f', 4, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam', '2025-10-05 17:08:51'),
	(10, 3, '68e2732070b9e9800b87c127', 5, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam', '2025-10-05 17:09:49'),
	(11, 3, '68e273b170b9e9800b87c12f', 3, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam', '2025-10-05 17:10:06'),
	(12, 3, '68e50b69e0747f909a77463a', 5, 'super', '2025-10-27 12:53:27');

-- Listage de la structure de table finalprojectcda. reset_password_request
CREATE TABLE IF NOT EXISTS `reset_password_request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `selector` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashed_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_7CE748AA76ED395` (`user_id`),
  CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table finalprojectcda.reset_password_request : ~0 rows (environ)

-- Listage de la structure de table finalprojectcda. user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL,
  `google_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_date` datetime DEFAULT NULL,
  `pseudonymized_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `is_pending_deletion` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table finalprojectcda.user : ~7 rows (environ)
INSERT INTO `user` (`id`, `email`, `roles`, `password`, `profile_picture`, `is_verified`, `google_id`, `username`, `registration_date`, `pseudonymized_at`, `is_pending_deletion`) VALUES
	(1, 'test@test.fr', '["ROLE_USER"]', '$2y$13$4F2sY7K6q29c6ibFSnZvieI09gZqw61TLPdGujXCixMuniFwSMjk6', NULL, 0, NULL, NULL, '2025-01-04 15:44:17', NULL, NULL),
	(3, 'franckpauline1@gmail.com', '[]', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocIdXfGhcuc3FcGmzqtyu_cuLYv-JZ57PHraALFYZ0MZZApUVSU=s96-c', 0, '102230588395851154073', 'Pauline Franck', '2025-09-04 15:19:08', NULL, NULL),
	(4, 'honey@pot.fr', '[]', '$2y$13$/sahg83D0aZGCh1QUQTbQeoPduxVdJeb6SG5zNUBoZhSyjyp9JQOO', NULL, 0, NULL, NULL, '2025-02-08 15:44:23', NULL, NULL),
	(5, 'honey@pooot.fr', '[]', '$2y$13$BHvTliaqFgT1f8KSxnNHaueLdbuSuHo4Cu/yhyRDzFSrTmHudPlFq', NULL, 0, NULL, NULL, '2025-06-04 15:44:31', NULL, NULL),
	(6, 'pauline-franck@hotmail.fr', '[]', '$2y$13$Wfh2yqfufcCCjPN4c61z7OY9Z526vWLDJj.lO8bFly22LgAxiDMXW', NULL, 0, NULL, NULL, '2025-10-04 15:44:39', NULL, NULL),
	(7, 'pauline@admin.fr', '["ROLE_ADMIN"]', '$2y$13$dRVPlYlE7AGQ0xVYhmjc8e/1kyYQVeIaF8hHUK8KhZQx.X6opjSom', NULL, 0, NULL, 'Administrateur', '2025-03-12 15:44:45', NULL, NULL),
	(8, 'pauline@test.fr', '[]', '$2y$13$hMyvUKu8L.FXR8iCp056SucWFYVJ.WiL4siYkGJ9vaf3lUr7z9Zcq', 'liam-neeson-690b45260b55c.jpg', 0, NULL, 'Paupau', '2025-11-05 12:37:57', NULL, NULL);

-- Listage de la structure de table finalprojectcda. user_itinerary
CREATE TABLE IF NOT EXISTS `user_itinerary` (
  `user_id` int NOT NULL,
  `itinerary_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`itinerary_id`),
  KEY `IDX_FFC2B512A76ED395` (`user_id`),
  KEY `IDX_FFC2B51215F737B2` (`itinerary_id`),
  CONSTRAINT `FK_FFC2B51215F737B2` FOREIGN KEY (`itinerary_id`) REFERENCES `itinerary` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_FFC2B512A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table finalprojectcda.user_itinerary : ~3 rows (environ)
INSERT INTO `user_itinerary` (`user_id`, `itinerary_id`) VALUES
	(8, 5),
	(8, 6),
	(8, 8);

-- Listage de la structure de table finalprojectcda. user_visited_location
CREATE TABLE IF NOT EXISTS `user_visited_location` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `location_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `visited_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_7EE93B2FA76ED395` (`user_id`),
  CONSTRAINT `FK_7EE93B2FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table finalprojectcda.user_visited_location : ~1 rows (environ)
INSERT INTO `user_visited_location` (`id`, `user_id`, `location_id`, `visited_at`) VALUES
	(2, 8, '68e50afde0747f909a774634', '2025-10-27 00:00:00');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
