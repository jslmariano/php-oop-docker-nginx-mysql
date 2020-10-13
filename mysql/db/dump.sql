-- MySQL dump 10.13  Distrib 5.7.30, for Linux (x86_64)
--
-- Host: localhost    Database: test
-- ------------------------------------------------------
-- Server version   5.7.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `test`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `test` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `test`;

--
-- Table structure for table `tips`
--

DROP TABLE IF EXISTS `tips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tips` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Tip identifier',
  `print_dt` datetime DEFAULT NULL COMMENT 'Print Date',
  `outcome` varchar(256) DEFAULT '' COMMENT 'Game outcome',
  `bet` decimal(15,2) DEFAULT '0.00' COMMENT 'Bet amount',
  `state` varchar(100) DEFAULT 'SOLD' COMMENT 'Tip state',
  `game_round_id` varchar(256) DEFAULT '' COMMENT 'Game round identifier',
  `ticket_id` varchar(256) DEFAULT NULL COMMENT 'Ticket identifier',
  `operator_session_id` varchar(100) DEFAULT '' COMMENT 'Game operator session identifier',
  `tip_id` varchar(100) DEFAULT NULL COMMENT 'Ticket Tip identifier',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8 COMMENT='Contains games tips';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tips`
--

LOCK TABLES `tips` WRITE;
/*!40000 ALTER TABLE `tips` DISABLE KEYS */;
INSERT INTO `tips` VALUES (117,'2020-10-14 00:00:00','Number 6',500.00,'SOLD','1','1733727178','123456','8816644716'),(118,'2020-10-14 00:00:00','Number 7',600.00,'SOLD','2','1733727178','123456','0869774975'),(119,'2020-10-14 00:00:00','Number 8',700.00,'SOLD','3','1733727178','123456','0358657332'),(120,'2020-10-14 00:00:00','Number 8',700.00,'CANCELED','3','1733727179','123456','0358657377');
/*!40000 ALTER TABLE `tips` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-10-14 10:31:29
