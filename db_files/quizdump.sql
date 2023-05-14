-- MariaDB dump 10.19  Distrib 10.4.24-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: quiz
-- ------------------------------------------------------
-- Server version	10.4.24-MariaDB

CREATE DATABASE IF NOT EXISTS quiz;
USE quiz;

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
-- Sequence structure for `answer_id`
--

DROP SEQUENCE IF EXISTS `answer_id`;
CREATE SEQUENCE `answer_id` start with 1 minvalue 1 maxvalue 9223372036854775806 increment by 1 nocache nocycle ENGINE=InnoDB;
SELECT SETVAL(`answer_id`, 132, 0);

--
-- Sequence structure for `auth_id`
--

DROP SEQUENCE IF EXISTS `auth_id`;
CREATE SEQUENCE `auth_id` start with 1 minvalue 1 maxvalue 9223372036854775806 increment by 1 nocache nocycle ENGINE=InnoDB;
SELECT SETVAL(`auth_id`, 6, 0);

--
-- Sequence structure for `category_id`
--

DROP SEQUENCE IF EXISTS `category_id`;
CREATE SEQUENCE `category_id` start with 1 minvalue 1 maxvalue 9223372036854775806 increment by 1 nocache nocycle ENGINE=InnoDB;
SELECT SETVAL(`category_id`, 9, 0);

--
-- Sequence structure for `question_id`
--

DROP SEQUENCE IF EXISTS `question_id`;
CREATE SEQUENCE `question_id` start with 1 minvalue 1 maxvalue 9223372036854775806 increment by 1 nocache nocycle ENGINE=InnoDB;
SELECT SETVAL(`question_id`, 45, 0);

--
-- Sequence structure for `quiz_id`
--

DROP SEQUENCE IF EXISTS `quiz_id`;
CREATE SEQUENCE `quiz_id` start with 1 minvalue 1 maxvalue 9223372036854775806 increment by 1 nocache nocycle ENGINE=InnoDB;
SELECT SETVAL(`quiz_id`, 7, 0);

--
-- Table structure for table `auth`
--

DROP TABLE IF EXISTS `auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth` (
  `pk_UserID` int(11) NOT NULL DEFAULT nextval(`quiz`.`auth_id`),
  `Username` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_UserID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth`
--

LOCK TABLES `auth` WRITE, auth_id WRITE;
-- LOCK TABLES auth_id WRITE;
/*!40000 ALTER TABLE `auth` DISABLE KEYS */;
INSERT INTO `auth` VALUES (1,'admin','$2y$10$k9.dNx17mjSKVtHU.nY5gu7tyjs9lwiRBtcQrhFHAw6gGdJriLddm'),(5,'alex','$2y$10$i8vBovncwTs5d8mUaGwy8e9vz0eDJirWFg4D6jLBdJMMwOhs9b4dy');
/*!40000 ALTER TABLE `auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `pk_CategoryID` int(11) NOT NULL DEFAULT nextval(`quiz`.`category_id`),
  `Description` varchar(255) NOT NULL,
  `fk_superCategoryID` int(11) DEFAULT NULL,
  PRIMARY KEY (`pk_CategoryID`),
  UNIQUE KEY `Description` (`Description`),
  KEY `fk_superCategoryID` (`fk_superCategoryID`),
  CONSTRAINT `category_ibfk_1` FOREIGN KEY (`fk_superCategoryID`) REFERENCES `category` (`pk_CategoryID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE, category_id WRITE;
-- LOCK TABLES category_id WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'NWT',NULL),(2,'SYT-BS',NULL),(3,'Linux Bash',2),(5,'MEDT',NULL),(6,'SYT-GETE',NULL),(7,'Prof. Wagner',NULL),(8,'IP-Adressierung',1);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz`
--

DROP TABLE IF EXISTS `quiz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz` (
  `pk_QuizID` int(11) NOT NULL DEFAULT nextval(`quiz`.`quiz_id`),
  `Title` varchar(255) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `createdTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `modifiedTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `fk_CategoryID` int(11) NOT NULL,
  PRIMARY KEY (`pk_QuizID`),
  UNIQUE KEY `Title` (`Title`),
  KEY `fk_CategoryID` (`fk_CategoryID`),
  CONSTRAINT `quiz_ibfk_1` FOREIGN KEY (`fk_CategoryID`) REFERENCES `category` (`pk_CategoryID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz`
--

LOCK TABLES `quiz` WRITE, quiz_id WRITE;
-- LOCK TABLES quiz_id WRITE;
/*!40000 ALTER TABLE `quiz` DISABLE KEYS */;
INSERT INTO `quiz` VALUES (1,'ACL Quiz','Ein Quiz über Access Control Lists auf Cisco Routern','2023-04-12 04:59:33','2023-04-12 04:59:33',1),(3,'Linux allgemein','Ein Linux-Quiz mit allgemeinen Fragen','2023-04-13 10:53:59','2023-04-13 10:53:59',3),(4,'Einfache Widerstandsnetzwerke','Quiz zu den Regeln in einfachen Widerstandsnetzwerken','2023-04-13 10:59:42','2023-04-13 10:59:42',6),(5,'Bulle von Tölz','Quiz für Prof. Wagner','2023-04-13 11:14:57','2023-04-13 11:15:21',7),(6,'DHCP','Ein Quiz zur Funktionsweise von DHCP','2023-04-13 11:48:45','2023-04-13 11:48:45',8);
/*!40000 ALTER TABLE `quiz` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question` (
  `pk_QuestionID` int(11) NOT NULL DEFAULT nextval(`quiz`.`question_id`),
  `Title` varchar(255) NOT NULL,
  `Description` varchar(1000) DEFAULT NULL,
  `isMultipleChoice` tinyint(1) NOT NULL,
  `OrderNr` int(11) DEFAULT NULL,
  `fk_QuizID` int(11) NOT NULL,
  PRIMARY KEY (`pk_QuestionID`),
  UNIQUE KEY `fk_QuizID` (`fk_QuizID`,`OrderNr`),
  CONSTRAINT `question_ibfk_1` FOREIGN KEY (`fk_QuizID`) REFERENCES `quiz` (`pk_QuizID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question`
--

LOCK TABLES `question` WRITE, question_id WRITE;
-- LOCK TABLES question_id WRITE;
/*!40000 ALTER TABLE `question` DISABLE KEYS */;
INSERT INTO `question` VALUES (20,'Was ist eine ACL?','',0,1,1),(21,'Was ist der Zweck einer ACL?','Wähle die passendste Antwort aus!',0,2,1),(22,'Was ist der Unterschied zwischen einer standard und einer erweiterten ACL?','',0,3,1),(23,'Welches ist das erste Feld in einem Cisco ACL-Eintrag?','',0,4,1),(24,'Welche Art von ACL wird normalerweise verwendet, um den Zugriff auf einen Router zu beschränken?','',0,5,1),(25,'Welche Option erlaubt es einem ACL, Datenverkehr basierend auf dem Quellport zu blockieren?','',0,6,1),(26,'Was ist der Standardverhalten von Cisco ACLs, wenn keine Übereinstimmung mit einem Eintrag gefunden wird?','',0,7,1),(27,'Welches Kommando wird verwendet, um das aktuelle Verzeichnis anzuzeigen?','Hinweis: Es ist das Arbeitsverzeichnis gefragt und nicht der Inhalt!',0,1,3),(28,'Welches Kommando wird verwendet, um eine Datei zu kopieren?','',0,2,3),(29,'Welches Kommando wird verwendet, um eine Datei zu löschen?','',0,3,3),(30,'Welches Kommando wird verwendet, um die Liste der laufenden Prozesse anzuzeigen?','',0,4,3),(31,'Welches Kommando wird verwendet, um einen neuen Benutzer auf einem Linux-System zu erstellen?','',0,5,3),(32,'Wie berechnet man den Gesamtwiderstand zweier Widerstände in Reihenschaltung?','',0,1,4),(33,'Wie verändert sich der Gesamtwiderstand eines Widerstandsnetzwerks, wenn man einen weiteren Widerstand in Reihenschaltung hinzufügt?','',0,2,4),(34,'Welche Formel wird verwendet, um den Strom in einem Widerstandsnetzwerk zu berechnen, wenn der Gesamtwiderstand und die anliegende Spannung bekannt sind?','',0,3,4),(35,'Wer spielt die Hauptrolle des \"Bullen von Tölz\"?','',0,1,5),(36,'In welchem Bundesland spielt die Serie \"Der Bulle von Tölz\"?','',0,2,5),(37,'Wie viele Staffeln hat die Serie \"Der Bulle von Tölz\" insgesamt?','',0,3,5),(38,'Wie heißt die Mutter von Hauptkommissar Benno Berghammer?','',0,4,5),(39,'Welche Art von Serie ist \"Der Bulle von Tölz\"?','',0,5,5),(40,'Was ist DHCP?','',0,1,6),(41,'Welche Rolle spielt DHCP bei der Netzwerkkommunikation?','',0,2,6),(42,'Welche Version von DHCP wird am häufigsten verwendet?','',0,3,6),(43,'Wie funktioniert DHCP?','',0,4,6),(44,'Was passiert, wenn ein Netzwerkgerät eine IP-Adresse anfordert, aber kein DHCP-Server verfügbar ist?','Es ist davon auszugehen, dass der User nicht in der Lage ist, IP-Adressen manuell zu konfigurieren!',0,5,6);
/*!40000 ALTER TABLE `question` ENABLE KEYS */;
UNLOCK TABLES;
--
-- Table structure for table `answer`
--

DROP TABLE IF EXISTS `answer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `answer` (
  `pk_AnswerID` int(11) NOT NULL DEFAULT nextval(`quiz`.`answer_id`),
  `Text` varchar(1000) NOT NULL,
  `isCorrect` tinyint(1) NOT NULL,
  `OrderNr` int(11) DEFAULT NULL,
  `fk_QuestionID` int(11) NOT NULL,
  PRIMARY KEY (`pk_AnswerID`),
  UNIQUE KEY `fk_QuestionID` (`fk_QuestionID`,`OrderNr`),
  CONSTRAINT `answer_ibfk_1` FOREIGN KEY (`fk_QuestionID`) REFERENCES `question` (`pk_QuestionID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answer`
--

LOCK TABLES `answer` WRITE, answer_id WRITE;
-- LOCK TABLES answer_id WRITE;
/*!40000 ALTER TABLE `answer` DISABLE KEYS */;
INSERT INTO `answer` VALUES (39,'Ein Gerät, das den Zugriff auf Netzwerke kontrolliert',0,1,20),(40,'Eine Liste von Regeln, die den Zugriff auf Netzwerke steuert',1,2,20),(41,'Ein Switch, der Datenpakete filtert',0,3,20),(42,'Eine Firewall, die den Zugriff auf das Netzwerk überwacht',0,4,20),(43,'Einschränkung des Zugriffs auf das Netzwerk',1,1,21),(44,'Verwaltung der Netzwerkressourcen',0,2,21),(45,'Verbesserung der Netzwerkleistung',0,3,21),(46,'Erhöhung der Netzwerksicherheit',0,4,21),(47,'Eine Standard ACL kann nur Quell-IP-Adressen filtern, während eine erweiterte ACL sowohl Quell- als auch Ziel-IP-Adressen filtern kann.',1,1,22),(48,'Eine standard ACL kann nur auf Layer-2-Switches angewendet werden, während eine erweiterte ACL auf Layer-3-Routern angewendet werden kann.',0,2,22),(49,'Eine standard ACL kann nur den Zugriff auf bestimmte Dienste einschränken, während eine erweiterte ACL den Zugriff auf bestimmte Dienste und Protokolle einschränken kann.',0,3,22),(50,'Es gibt keinen Unterschied zwischen einer standard- und einer erweiterten ACL.',0,4,22),(51,'Permit/Deny',1,1,23),(52,'Quell-IP-Adresse',0,2,23),(53,'Ziel-IP-Adresse',0,3,23),(54,'Protokoll',0,4,23),(55,'Standard-ACL',1,3,24),(56,'Erweiterte-ACL',0,1,24),(58,'Dynamische-ACL',0,2,24),(59,'Reflexive-ACL',0,4,24),(60,'Source IP',0,1,25),(61,'Destination IP',0,2,25),(62,'Source Port',1,3,25),(63,'Destination Port',0,4,25),(64,'Allow',0,1,26),(65,'Deny',1,2,26),(66,'Forward',0,3,26),(67,'Reject',0,4,26),(68,'pwd',1,1,27),(69,'cd',0,2,27),(70,'ls',0,3,27),(71,'cp',0,4,27),(72,'pwd',0,1,28),(73,'cd',0,2,28),(74,'ls',0,3,28),(75,'cp',1,4,28),(76,'rm',1,1,29),(77,'cp',0,2,29),(78,'mv',0,3,29),(79,'mkdir',0,4,29),(80,'ps',1,4,30),(81,'ls',0,2,30),(82,'cd',0,3,30),(83,'mkdir',0,1,30),(84,'adduser',1,3,31),(85,'edituser',0,2,31),(86,'rmuser',0,1,31),(87,'chuser',0,4,31),(88,'Addieren',1,3,32),(89,'Multiplizieren',0,2,32),(90,'Subtrahieren',0,1,32),(91,'Dividieren',0,4,32),(92,'Er sinkt',0,1,33),(93,'Er steigt',1,2,33),(94,'Er bleibt gleich',0,3,33),(95,'U = R * I',0,1,34),(96,'R = U / I',0,2,34),(97,'I = U / R',1,3,34),(98,'Ottfried Fischer',1,1,35),(99,'Christian Tramitz',0,2,35),(100,'Wolfgang Fierek',0,3,35),(101,'Hansi Hinterseer',0,4,35),(102,'Hessen',0,1,36),(103,'Baden-Württemberg',0,2,36),(104,'Bayern',1,3,36),(105,'Niedersachsen',0,4,36),(106,'14',1,4,37),(107,'10',0,2,37),(108,'12',0,3,37),(109,'Sabrina Lorenz',0,1,38),(110,'Theresia \"Resi\" Berghammer',1,2,38),(111,'Katja \"Hasi\" Flemisch',0,3,38),(112,'Nadine Richter',0,4,38),(113,'Action',0,1,39),(114,'Dokumentation',0,2,39),(115,'Horror',0,3,39),(116,'Krimiserie',1,4,39),(117,'Ein Protokoll zum automatischen Konfigurieren von Netzwerkeinstellungen.',1,1,40),(118,'Ein Protokoll zur Übertragung von Dateien zwischen Computern.',0,2,40),(119,'Ein Protokoll zur Überwachung des Netzwerkverkehrs.',0,3,40),(120,'Es schützt das Netzwerk vor externen Angriffen.',0,1,41),(121,'Es erleichtert die Zuweisung von IP-Adressen und anderen Netzwerkkonfigurationen.',1,2,41),(122,'Es steuert den Datenverkehr im Netzwerk.',0,3,41),(123,'DHCPv1',0,1,42),(124,'DHCPv2',0,2,42),(125,'DHCPv4',1,3,42),(126,'Es leitet den Datenverkehr im Netzwerk an den richtigen Ort weiter.',0,1,43),(127,'Es gibt IP-Adressen und andere Konfigurationen automatisch an Netzwerkgeräte aus.',1,2,43),(128,'Es blockiert unerwünschten Datenverkehr im Netzwerk.',0,3,43),(129,'Das Gerät wird keine Netzwerkverbindung herstellen können.',1,1,44),(130,'Das Gerät wird automatisch eine IP-Adresse aus einem reservierten Bereich zugewiesen bekommen.',0,2,44),(131,'Das Gerät wird eine zufällige IP-Adresse zugewiesen bekommen.',0,3,44);
/*!40000 ALTER TABLE `answer` ENABLE KEYS */;
UNLOCK TABLES;

-- Dump completed on 2023-04-13 14:04:34
