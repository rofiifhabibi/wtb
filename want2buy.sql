/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.11-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: want2buy
-- ------------------------------------------------------
-- Server version	10.11.11-MariaDB-0+deb12u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `posting_images`
--

DROP TABLE IF EXISTS `posting_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `posting_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `posting_id` int(11) DEFAULT NULL,
  `image_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_post_id` (`posting_id`),
  CONSTRAINT `fk_post_id` FOREIGN KEY (`posting_id`) REFERENCES `postings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `posting_images_ibfk_1` FOREIGN KEY (`posting_id`) REFERENCES `postings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posting_images`
--

LOCK TABLES `posting_images` WRITE;
/*!40000 ALTER TABLE `posting_images` DISABLE KEYS */;
INSERT INTO `posting_images` VALUES
(1,4,'1770453498_download (18).jpg'),
(2,5,'IMG_6986fd3766b65_1770454327.jpg'),
(3,5,'IMG_6986fd376766a_1770454327.jpg'),
(8,6,'IMG_6987106f3bc13_1770459247.jpeg'),
(9,9,'IMG_6987484aa1bb9.jpg'),
(10,9,'IMG_6987484aa273c.jpg'),
(11,9,'IMG_6987484aa34dc.jpg');
/*!40000 ALTER TABLE `posting_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `postings`
--

DROP TABLE IF EXISTS `postings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `postings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `budget_min` decimal(15,2) DEFAULT NULL,
  `budget_max` decimal(15,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `postings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `postings`
--

LOCK TABLES `postings` WRITE;
/*!40000 ALTER TABLE `postings` DISABLE KEYS */;
INSERT INTO `postings` VALUES
(4,1,'Teman',1000.00,20000.00,'yang gak karbitt',NULL,'2026-02-07 08:38:18'),
(5,1,'akun epep sultann',1000.00,10000.00,'spek sultan no tipu tipu',NULL,'2026-02-07 08:52:07'),
(6,1,'blek hol',1.00,1000000.00,'ambatukammm',NULL,'2026-02-07 08:58:26'),
(9,3,'laptop spek gaming abiss',100000.00,500000.00,'yang gacor gacor aja',NULL,'2026-02-07 14:12:26');
/*!40000 ALTER TABLE `postings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `address` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.png',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'AMBADARR','62895327468806','chaidar','chaidar@ender.dev','1','2026-02-07 13:55:31','Sleman','1770360633_Grecian symbol for Chaos.jpg'),
(2,'chaidar sija',NULL,'ender','ender@ender.dev','1','2026-02-07 13:55:31',NULL,'default.png'),
(3,'Ender Verstappen',NULL,'cukidar','cukidar@ender.dev','1123','2026-02-07 13:55:31','jogja','default.png');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-07 21:33:10
