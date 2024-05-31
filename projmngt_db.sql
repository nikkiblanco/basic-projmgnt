CREATE DATABASE  IF NOT EXISTS `projmgnt_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `projmgnt_db`;
-- MySQL dump 10.13  Distrib 8.0.29, for Win64 (x86_64)
--
-- Host: localhost    Database: projmgnt_db
-- ------------------------------------------------------
-- Server version	8.0.29

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `m_project`
--

DROP TABLE IF EXISTS `m_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `m_project` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` longtext,
  `due_date` date NOT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0 - Todo\n1 - In progress\n2 - Completed',
  `image_path` varchar(255) DEFAULT NULL,
  `delete_flg` int NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user` int NOT NULL DEFAULT '0',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `update_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `m_project`
--

LOCK TABLES `m_project` WRITE;
/*!40000 ALTER TABLE `m_project` DISABLE KEYS */;
INSERT INTO `m_project` VALUES (1,'AI-Photo System','upDescription goes here.....\r\nDescription goes here.....Description goes here.....Description goes here.....','2024-07-06',0,'../files/projects/1/1_31052024_1717140811.jpeg',0,'2024-05-31 07:33:31',2,'2024-05-31 07:55:44',1);
/*!40000 ALTER TABLE `m_project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `m_tasks`
--

DROP TABLE IF EXISTS `m_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `m_tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `user_id` int NOT NULL COMMENT 'Assigned User',
  `task_name` varchar(255) NOT NULL,
  `description` longtext,
  `priority` int NOT NULL DEFAULT '0' COMMENT '0 - High\n1 - Medium\n2 - Low',
  `due_date` date NOT NULL,
  `delete_flg` int NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user` int NOT NULL DEFAULT '0',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `update_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `m_tasks`
--

LOCK TABLES `m_tasks` WRITE;
/*!40000 ALTER TABLE `m_tasks` DISABLE KEYS */;
INSERT INTO `m_tasks` VALUES (1,1,1,'Create Function','Add detail..',1,'2024-06-01',0,'2024-05-31 07:36:29',2,'2024-05-31 07:36:40',2);
/*!40000 ALTER TABLE `m_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `m_user`
--

DROP TABLE IF EXISTS `m_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `m_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` int NOT NULL DEFAULT '0' COMMENT '0 - Admin\n1 - User',
  `delete_flg` int NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user` int NOT NULL DEFAULT '0',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `update_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `m_user`
--

LOCK TABLES `m_user` WRITE;
/*!40000 ALTER TABLE `m_user` DISABLE KEYS */;
INSERT INTO `m_user` VALUES (1,'admin','Mina','$2y$10$hO/l/AUd3cHlwZd8qqzaI..WeutWY/WBfd.zjXdCVlIEM/2Yr6lt6',0,0,'2024-05-30 05:35:19',1,'2024-05-31 07:38:46',1),(2,'kaii','Nikki Blanco','$2y$10$AIcjKtEcmZ9ONNASAxXGyOlNviHDVBfz6iemcXOEwM.DMtIa9BUaW',0,0,'2024-05-30 05:36:33',1,'2024-05-31 07:55:33',1),(3,'cain_el','Elias Cain','$2y$10$9MsmozA7B6flD0cSQf/hTOEbbvGfnegzGsdcq2pjhlkHTrc/7YgfG',1,1,'2024-05-30 05:36:51',1,'2024-05-31 07:55:28',1),(4,'kim_joe','Joel Kim','$2y$10$Gtw3BXk8hQ//0K.a5micI.s4QjEwFI4msE8kS8VswCMtxJ/E2Kjcq',1,0,'2024-05-31 03:32:27',2,'2024-05-31 07:38:32',2);
/*!40000 ALTER TABLE `m_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-05-31 15:59:33
