-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: pcc_system_db
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `academic_backgrounds`
--

DROP TABLE IF EXISTS `academic_backgrounds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_backgrounds` (
  `background_id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) NOT NULL,
  `shs_school_attended` varchar(150) DEFAULT NULL,
  `shs_strand` varchar(50) DEFAULT NULL,
  `shs_year_graduated` int(11) DEFAULT NULL,
  `shs_school_address` varchar(150) DEFAULT NULL,
  `previous_college` varchar(150) DEFAULT NULL,
  `previous_course` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`background_id`),
  KEY `application_id` (`application_id`),
  CONSTRAINT `academic_backgrounds_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applicants` (`application_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_backgrounds`
--

LOCK TABLES `academic_backgrounds` WRITE;
/*!40000 ALTER TABLE `academic_backgrounds` DISABLE KEYS */;
INSERT INTO `academic_backgrounds` VALUES (6,7,'San Fernando Integrated','HUMSS',2023,'San Fernando Pampanga 781',NULL,NULL),(7,8,'Holy Heart Christian Academy','STEM',2024,'Dagupan',NULL,NULL);
/*!40000 ALTER TABLE `academic_backgrounds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_users` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('Admin') DEFAULT 'Admin',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin','admin123','System Admin','admin1@pcc.edu.ph','Admin',NULL,'2026-06-13 08:56:57');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `admin_id` varchar(50) NOT NULL,
  `pcc_email` varchar(100) NOT NULL,
  `access_code` varchar(255) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `pcc_email` (`pcc_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES ('ADM-2026-00007','admin2@pcc.edu.ph','$2y$10$PwNCDmT9wtNqe0Yxo5/3ieCAqRJ0gep0fNcOxp0BXmGU/XEsC622G','Admin 2','2026-06-20 03:26:03'),('ADM-2026-00008','admin1@pcc.edu.ph','$2y$10$iyZwgyUFiVuht.L25C1tmOwWISeNv2T05O0ud9vD43QQGrG7y/C3e','Admin 1','2026-06-20 09:01:29');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applicants`
--

DROP TABLE IF EXISTS `applicants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applicants` (
  `application_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(15) NOT NULL,
  `student_number` varchar(50) DEFAULT NULL,
  `classification` enum('freshman','transferee') NOT NULL,
  `academic_term` varchar(20) DEFAULT '1st Semester',
  `school_year` varchar(15) DEFAULT '2026-2027',
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `civil_status` enum('Single','Married','Widowed') NOT NULL,
  `nationality` varchar(50) NOT NULL,
  `religious_affiliation` varchar(100) DEFAULT NULL,
  `email_address` varchar(100) NOT NULL,
  `mobile_number` varchar(20) NOT NULL,
  `address_region` varchar(50) NOT NULL,
  `address_province` varchar(50) NOT NULL,
  `address_city` varchar(50) NOT NULL,
  `address_barangay` varchar(50) NOT NULL,
  `address_street` varchar(150) NOT NULL,
  `preferred_program` varchar(10) NOT NULL,
  `guardian_id` int(11) DEFAULT NULL,
  `application_status` enum('Pending','Under Review','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `address_postal` varchar(10) DEFAULT NULL,
  `shs_card_path` varchar(255) DEFAULT NULL,
  `psa_cert_path` varchar(255) DEFAULT NULL,
  `good_moral_path` varchar(255) DEFAULT NULL,
  `applicant_photo_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`application_id`),
  UNIQUE KEY `reference_number` (`reference_number`),
  UNIQUE KEY `email_address` (`email_address`),
  KEY `preferred_program` (`preferred_program`),
  KEY `guardian_id` (`guardian_id`),
  CONSTRAINT `applicants_ibfk_1` FOREIGN KEY (`preferred_program`) REFERENCES `courses` (`course_code`),
  CONSTRAINT `applicants_ibfk_2` FOREIGN KEY (`guardian_id`) REFERENCES `guardians` (`guardian_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applicants`
--

LOCK TABLES `applicants` WRITE;
/*!40000 ALTER TABLE `applicants` DISABLE KEYS */;
INSERT INTO `applicants` VALUES (7,'PCC-2026-3EFEAB','2026-00001','freshman','1st Semester','2026-2027','John','','Doe','Sr','2003-03-18','Male','Single','American','Catholic','johndoe@gmail.com','09768199263','Region III','Pampanga','San Fernando','San Jose','815-C San Fernando St, San Jose, San Fernando, Pampanga','BSCS',7,'Approved','2026-06-22 09:29:00','2000','../uploads/credentials/form_138_6a3900584c132.jpg','../uploads/credentials/birth_certificate_6a3900584c636.png','../uploads/credentials/good_moral_6a3900584cc15.png','../uploads/credentials/id_picture_6a3900584cf32.jpg'),(8,'PCC-2026-3453DB','2026-00002','freshman','1st Semester','2026-2027','Laurence','','Mendoza','','2005-12-08','Male','Single','Filipino','Catholic','mendozalaurence0818@gmail.com','09398977676','NCR','Metro Manila','Manila','Barangay 101','1501 Dagupan St Tondo Manila','BSCS',8,'Approved','2026-06-22 09:43:47','1013','../uploads/credentials/form_138_6a3903c87f6fa.jpg','../uploads/credentials/birth_certificate_6a3903c87fa57.png','../uploads/credentials/good_moral_6a3903c87fda0.png','../uploads/credentials/id_picture_6a3903c8800e4.jpg');
/*!40000 ALTER TABLE `applicants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses` (
  `course_code` varchar(10) NOT NULL,
  `course_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`course_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES ('BSCS','Bachelor of Science in Computer Science','Focuses on computing theory, algorithms, and development.'),('BSIT','Bachelor of Science in Information Technology','Focuses on information infrastructure, networking, and deployment.');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `drop_requests`
--

DROP TABLE IF EXISTS `drop_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `drop_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` varchar(50) DEFAULT 'Pending Review',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `enrollment_id` (`enrollment_id`),
  CONSTRAINT `drop_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  CONSTRAINT `drop_requests_ibfk_2` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drop_requests`
--

LOCK TABLES `drop_requests` WRITE;
/*!40000 ALTER TABLE `drop_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `drop_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `school_year` varchar(20) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `midterm_grade` varchar(10) DEFAULT NULL,
  `final_grade` varchar(10) DEFAULT NULL,
  `remarks` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`enrollment_id`),
  KEY `student_id` (`student_id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
/*!40000 ALTER TABLE `enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guardians`
--

DROP TABLE IF EXISTS `guardians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guardians` (
  `guardian_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `emergency_phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`guardian_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guardians`
--

LOCK TABLES `guardians` WRITE;
/*!40000 ALTER TABLE `guardians` DISABLE KEYS */;
INSERT INTO `guardians` VALUES (1,'Nixon Mendoza','Mother','09398977676','2026-06-17 17:08:40'),(2,'John Doe Mendoza','Legal Guardian','09398977676','2026-06-19 06:14:23'),(3,'Miguel Sid Tatay','Grandmother','09398977676','2026-06-19 17:22:40'),(4,'Nixon Mendoza','Sibling','09398977676','2026-06-20 05:55:06'),(6,'Clarissa Mae Mistica','Legal Guardian','09398977676','2026-06-20 10:43:24'),(7,'Jane Doe','Mother','09897619263','2026-06-22 09:29:00'),(8,'Nixon Mendoza','Father','09398977676','2026-06-22 09:43:47');
/*!40000 ALTER TABLE `guardians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notices`
--

DROP TABLE IF EXISTS `notices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `audience` varchar(100) NOT NULL DEFAULT 'All Students',
  `target_program` varchar(100) NOT NULL DEFAULT 'General / Global',
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Published',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notices`
--

LOCK TABLES `notices` WRITE;
/*!40000 ALTER TABLE `notices` DISABLE KEYS */;
INSERT INTO `notices` VALUES (2,'Semester Enrollment is now Open!','Students','All Programs','New admissions and Old Student admissions are now open, Take your slot now!\r\n\r\n#GoPCC! #PccChiefs!','uploads/notices/1781896910_noticecheck.jpg','Published','2026-06-19 19:21:50');
/*!40000 ALTER TABLE `notices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `section_subjects`
--

DROP TABLE IF EXISTS `section_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `section_subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_section_subject` (`section_id`,`subject_id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `section_subjects_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `section_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section_subjects`
--

LOCK TABLES `section_subjects` WRITE;
/*!40000 ALTER TABLE `section_subjects` DISABLE KEYS */;
INSERT INTO `section_subjects` VALUES (4,6,9,'2026-06-21 16:33:04'),(5,6,8,'2026-06-21 16:33:04'),(6,6,1,'2026-06-21 16:33:04'),(7,6,3,'2026-06-21 16:33:04'),(8,6,5,'2026-06-21 16:33:04'),(9,6,10,'2026-06-21 16:33:04'),(16,7,7,'2026-06-21 16:37:12'),(17,7,12,'2026-06-21 16:37:12'),(18,7,13,'2026-06-21 16:37:12'),(19,7,14,'2026-06-21 16:37:12'),(20,7,15,'2026-06-21 16:37:12'),(21,7,11,'2026-06-21 16:37:12'),(22,11,22,'2026-06-21 17:14:08'),(23,11,23,'2026-06-21 17:14:08'),(24,11,24,'2026-06-21 17:14:08'),(25,11,25,'2026-06-21 17:14:08'),(26,11,26,'2026-06-21 17:14:08'),(27,11,27,'2026-06-21 17:14:08'),(28,9,16,'2026-06-21 17:14:34'),(29,9,17,'2026-06-21 17:14:34'),(30,9,18,'2026-06-21 17:14:34'),(31,9,19,'2026-06-21 17:14:34'),(32,9,20,'2026-06-21 17:14:34'),(33,9,21,'2026-06-21 17:14:34');
/*!40000 ALTER TABLE `section_subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(50) NOT NULL,
  `program` varchar(10) DEFAULT 'BSIT',
  `target_year` varchar(50) NOT NULL,
  `status` varchar(50) DEFAULT 'Available',
  `is_block_section` tinyint(1) DEFAULT 1,
  `capacity` int(11) DEFAULT 40,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_name` (`section_name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES (1,'BSIT-101','BSIT','1st Year','Available',0,40,'2026-06-19 18:58:41'),(3,'BSIT-201','BSIT','2nd Year','Available',0,40,'2026-06-19 18:58:41'),(6,'BSIT-10A','BSIT','1st Year','Available',1,40,'2026-06-21 16:15:58'),(7,'BSIT-20B','BSIT','2nd Year','Available',1,40,'2026-06-21 16:18:54'),(8,'BSCS-501','BSCS','1st Year','Available',0,10,'2026-06-21 16:29:58'),(9,'BSCS-50A','BSCS','1st Year','Available',1,40,'2026-06-21 16:30:11'),(10,'BSCS-601','BSCS','2nd Year','Available',0,40,'2026-06-21 16:30:22'),(11,'BSCS-60B','BSCS','2nd Year','Available',1,40,'2026-06-21 16:30:34');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_number` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `current_course` varchar(10) NOT NULL,
  `section` varchar(50) DEFAULT NULL,
  `year_level` int(11) DEFAULT 1,
  `classification` varchar(50) DEFAULT 'Regular',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `enrollment_status` varchar(50) DEFAULT 'Enrolled',
  `payment_method` varchar(50) DEFAULT 'Cashier',
  `payment_scheme` varchar(50) DEFAULT 'Full Payment',
  `gcash_ref_id` varchar(50) DEFAULT NULL,
  `gcash_receipt_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `student_number` (`student_number`),
  UNIQUE KEY `email` (`email`),
  KEY `current_course` (`current_course`),
  KEY `application_id` (`application_id`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`current_course`) REFERENCES `courses` (`course_code`),
  CONSTRAINT `students_ibfk_2` FOREIGN KEY (`application_id`) REFERENCES `applicants` (`application_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,'2026-00001','$2y$10$9O8Myksz.otLyMbwVV.Vk.9P2efHKelHhhRcIzvQtpZ0xOyPK2J2a',7,'John',NULL,'Doe',NULL,'doe.john@pcc.edu.ph','BSCS',NULL,1,'Regular','2026-06-22 09:40:41','Not Enrolled','Cashier','Full Payment',NULL,NULL),(2,'2026-00002','$2y$10$4B3rfCirjmLzZQDsglVhgOLUE3wjZBBgmtbP5XlZVs1S7TO1z3rTO',8,'Laurence',NULL,'Mendoza',NULL,'mendoza.laurence@pcc.edu.ph','BSCS',NULL,1,'Regular','2026-06-22 09:43:55','Not Enrolled','Cashier','Full Payment',NULL,NULL);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(50) NOT NULL,
  `program` varchar(10) DEFAULT 'BSIT',
  `descriptive_title` varchar(255) NOT NULL,
  `target_year` varchar(50) DEFAULT '1st Year',
  `units` int(11) NOT NULL DEFAULT 3,
  `capacity` int(11) NOT NULL DEFAULT 40,
  `status` varchar(50) DEFAULT 'Inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `subject_code` (`subject_code`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'IT-IC101','BSIT','Introduction to Computing','1st Year',3,40,'Active','2026-06-19 18:58:41'),(3,'IT-IP101','BSIT','Improving Database','1st Year',3,10,'Active','2026-06-19 19:02:11'),(5,'IT-SI102','BSIT','System and Information','1st Year',3,40,'Active','2026-06-20 04:06:56'),(7,'CCS-102','BSIT','Computer Programming 1','2nd Year',4,40,'Active','2026-06-21 16:17:41'),(8,'IT-111','BSIT','Discrete Mathematics for IT','1st Year',3,40,'Active','2026-06-21 16:17:58'),(9,'GE-101','BSIT','Understanding the Self','1st Year',3,40,'Active','2026-06-21 16:18:10'),(10,'PE-101','BSIT','Physical Fitness and Wellness 1','1st Year',2,40,'Active','2026-06-21 16:18:28'),(11,'PE-102','BSIT','Physical Fitness and Wellness 2','2nd Year',2,40,'Active','2026-06-21 16:18:37'),(12,'IT-211','BSIT','Data Structures and Algorithms','2nd Year',3,40,'Active','2026-06-21 16:20:12'),(13,'IT-213','BSIT','Web Systems and Technologies','2nd Year',3,40,'Active','2026-06-21 16:20:38'),(14,'IT-214','BSIT','Object-Oriented Programming','2nd Year',3,40,'Active','2026-06-21 16:20:54'),(15,'IT-215','BSIT','Platform Technologies','2nd Year',3,40,'Active','2026-06-21 16:21:20'),(16,'CS-111','BSCS','Introduction to Computer Science','1st Year',3,40,'Active','2026-06-21 17:09:14'),(17,'CS-112','BSCS','Computer Programming 1','1st Year',3,40,'Active','2026-06-21 17:09:14'),(18,'CS-113','BSCS','Computer Programming 2','1st Year',3,40,'Active','2026-06-21 17:09:14'),(19,'CS-114','BSCS','Discrete Structures 1','1st Year',3,40,'Active','2026-06-21 17:09:14'),(20,'CS-115','BSCS','Digital Design','1st Year',3,40,'Active','2026-06-21 17:09:14'),(21,'GE-CS11','BSCS','Science, Technology, and Society','1st Year',3,40,'Active','2026-06-21 17:09:14'),(22,'CS-211','BSCS','Data Structures and Algorithms','2nd Year',3,40,'Active','2026-06-21 17:09:14'),(23,'CS-212','BSCS','Object-Oriented Programming','2nd Year',3,40,'Active','2026-06-21 17:09:14'),(24,'CS-213','BSCS','Database Management Systems','2nd Year',3,40,'Active','2026-06-21 17:09:14'),(25,'CS-214','BSCS','Discrete Structures 2','2nd Year',3,40,'Active','2026-06-21 17:09:14'),(26,'CS-215','BSCS','Design and Analysis of Algorithms','2nd Year',3,40,'Active','2026-06-21 17:09:14'),(27,'CS-216','BSCS','Computer Architecture and Organization','2nd Year',3,40,'Active','2026-06-21 17:09:14');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_year` varchar(20) NOT NULL DEFAULT '2026 - 2027',
  `semester` varchar(20) NOT NULL DEFAULT '1st Semester',
  `enrollment_status` varchar(20) NOT NULL DEFAULT 'Open',
  `old_student_enrollment` varchar(20) NOT NULL DEFAULT 'Open',
  `grading_status` varchar(20) NOT NULL DEFAULT 'Closed',
  `system_maintenance` varchar(20) NOT NULL DEFAULT 'Disabled',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` VALUES (1,'2026 - 2027','1st Semester','Open','Open','Closed','Disabled','2026-06-22 08:29:48');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_updates`
--

DROP TABLE IF EXISTS `system_updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_tab` varchar(50) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `admin_id` varchar(50) DEFAULT NULL,
  `custom_message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `system_updates_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_updates`
--

LOCK TABLES `system_updates` WRITE;
/*!40000 ALTER TABLE `system_updates` DISABLE KEYS */;
INSERT INTO `system_updates` VALUES (1,'SCHEDULES',NULL,'ADM-2026-00007','Added a new Section \"sadwadaw\" .','2026-06-20 09:44:39'),(2,'SCHEDULES',NULL,'ADM-2026-00007','Deleted a Section \"sadwadaw\" .','2026-06-20 10:37:14'),(3,'ADMISSIONS',0,NULL,'Submitted an Application .','2026-06-20 10:43:24'),(4,'STUDENTS',2038,'ADM-2026-00007','Updated Student profile data for {student_name} (2026-00001) .','2026-06-20 11:35:57'),(5,'USERS',NULL,'ADM-2026-00007','Published a new Notice, \"Notice3\" for \"All Programs\" .','2026-06-20 12:05:37'),(6,'USERS',NULL,'ADM-2026-00007','Deleted a Notice, \"Notice3\" for \"All Programs\" .','2026-06-20 12:05:46'),(7,'ADMISSIONS',6,'ADM-2026-00007','Declined the Application of {student_name} .','2026-06-20 12:13:25'),(8,'USERS',NULL,'ADM-2026-00008','Modified system announcement Advisory profile properties for \"Semester Enrollment is now Open!\" .','2026-06-21 08:21:24'),(9,'STUDENTS',2039,'ADM-2026-00008','Updated Student profile data for {student_name} (2026-00002) .','2026-06-21 08:48:21'),(10,'STUDENTS',2039,'ADM-2026-00008','Updated Student profile data for {student_name} (2026-00002) .','2026-06-21 08:48:27'),(11,'STUDENTS',2038,'ADM-2026-00008','Updated Student profile data for {student_name} (2026-00001) .','2026-06-21 09:33:07'),(12,'STUDENTS',2038,'ADM-2026-00008','Updated Student profile data for {student_name} (2026-00001) .','2026-06-21 09:33:12'),(13,'STUDENTS',NULL,'ADM-2026-00008','Deleted Student profile record for 2026-00001 from directory.','2026-06-21 12:03:13'),(14,'SUBJECTS',NULL,'ADM-2026-00008','Added a new Subject \"IT-ESC1104, Extracting System Compilation\" .','2026-06-21 12:54:57'),(15,'SUBJECTS',NULL,'ADM-2026-00008','Deleted a Subject \"IT-ESC1104, Extracting System Compilation\" .','2026-06-21 12:55:40'),(16,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSIT-101\" .','2026-06-21 16:12:57'),(17,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSIT-101\" .','2026-06-21 16:15:14'),(18,'SCHEDULES',NULL,'ADM-2026-00008','Deleted a Section \"BSIT-202\" .','2026-06-21 16:15:36'),(19,'SCHEDULES',NULL,'ADM-2026-00008','Deleted a Section \"BSIT-102\" .','2026-06-21 16:15:39'),(20,'SCHEDULES',NULL,'ADM-2026-00008','Added a new Section \"BSIT-10A\" .','2026-06-21 16:15:58'),(21,'SUBJECTS',NULL,'ADM-2026-00008','Added a new Subject \"CCS-102, Computer Programming 1\" .','2026-06-21 16:17:41'),(22,'SUBJECTS',NULL,'ADM-2026-00008','Added a new Subject \"IT-111, Discrete Mathematics for IT\" .','2026-06-21 16:17:58'),(23,'SUBJECTS',NULL,'ADM-2026-00008','Added a new Subject \"GE-101, Understanding the Self\" .','2026-06-21 16:18:10'),(24,'SUBJECTS',NULL,'ADM-2026-00008','Added a new Subject \"PE-101, Physical Fitness and Wellness 1\" .','2026-06-21 16:18:28'),(25,'SUBJECTS',NULL,'ADM-2026-00008','Added a new Subject \"PE-102, Physical Fitness and Wellness 2\" .','2026-06-21 16:18:37'),(26,'SCHEDULES',NULL,'ADM-2026-00008','Added a new Section \"BSIT-20B\" .','2026-06-21 16:18:54'),(27,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSIT-20B\" .','2026-06-21 16:19:00'),(28,'SUBJECTS',NULL,'ADM-2026-00008','Added a new Subject \"IT-211, Data Structures and Algorithms\" .','2026-06-21 16:20:12'),(29,'SUBJECTS',NULL,'ADM-2026-00008','Added a new Subject \"IT-213, Web Systems and Technologies\" .','2026-06-21 16:20:38'),(30,'SUBJECTS',NULL,'ADM-2026-00008','Added a new Subject \"IT-214, Object-Oriented Programming\" .','2026-06-21 16:20:54'),(31,'SUBJECTS',NULL,'ADM-2026-00008','Added a new Subject \"IT-215, Platform Technologies\" .','2026-06-21 16:21:20'),(32,'SCHEDULES',NULL,'ADM-2026-00008','Added a new Section \"BSCS-501\" under program BSCS.','2026-06-21 16:29:58'),(33,'SCHEDULES',NULL,'ADM-2026-00008','Added a new Section \"BSCS-50A\" under program BSIT.','2026-06-21 16:30:11'),(34,'SCHEDULES',NULL,'ADM-2026-00008','Added a new Section \"BSCS-601\" under program BSIT.','2026-06-21 16:30:22'),(35,'SCHEDULES',NULL,'ADM-2026-00008','Added a new Section \"BSCS-60B\" under program BSCS.','2026-06-21 16:30:34'),(36,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSCS-50A\".','2026-06-21 16:30:38'),(37,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSCS-601\".','2026-06-21 16:30:43'),(38,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSCS-60B\".','2026-06-21 16:30:50'),(39,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSCS-601\".','2026-06-21 16:30:54'),(40,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSCS-501\".','2026-06-21 17:05:10'),(41,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSCS-501\".','2026-06-21 17:07:05'),(42,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSCS-501\".','2026-06-21 17:09:18'),(43,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSCS-60B\".','2026-06-21 17:14:08'),(44,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSCS-50A\".','2026-06-21 17:14:34'),(45,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSCS-501\".','2026-06-21 17:25:15'),(46,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSCS-601\".','2026-06-21 17:25:21'),(47,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSIT-101\".','2026-06-21 17:25:26'),(48,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSIT-201\".','2026-06-21 17:25:29'),(49,'SCHEDULES',NULL,'ADM-2026-00008','Updated Section profile configurations for \"BSIT-201\".','2026-06-21 17:25:32'),(50,'ADMISSIONS',6,'ADM-2026-00008','Approved the Application of {student_name} . Status updated to Approved.','2026-06-21 17:36:51'),(51,'ADMISSIONS',4,'ADM-2026-00008','Approved the Application of {student_name} . Status updated to Approved.','2026-06-21 17:42:27'),(52,'STUDENTS',NULL,'ADM-2026-00008','Deleted Student profile record for 2026-00003 from directory.','2026-06-21 17:46:31'),(53,'ADMISSIONS',6,'ADM-2026-00008','Approved the Application of {student_name} . Status updated to Approved.','2026-06-21 17:46:41'),(54,'STUDENTS',2040,'ADM-2026-00008','Updated Student profile data for {student_name} (2026-00003) .','2026-06-21 17:51:23'),(55,'STUDENTS',2040,'ADM-2026-00008','Updated Student profile data for {student_name} (2026-00003) .','2026-06-21 17:51:30'),(56,'SCHEDULES',NULL,'ADM-2026-00008','Verified class schedule load matrix parameters for Student Database Token ID #2039 into formal section track BSIT-10A.','2026-06-22 06:27:12'),(57,'SCHEDULES',NULL,'ADM-2026-00008','Verified class schedule load matrix parameters for Student Database Token ID #2039 into formal section track BSIT-10A.','2026-06-22 06:28:14'),(58,'SCHEDULES',NULL,'ADM-2026-00008','Verified class schedule load matrix parameters for Student Database Token ID #2039 into formal section track BSIT-10A.','2026-06-22 06:28:14'),(59,'STUDENTS',2039,'ADM-2026-00008','Updated Student profile data for {student_name} (2026-00002) .','2026-06-22 06:33:18'),(60,'SCHEDULES',NULL,'ADM-2026-00008','Verified class schedule load matrix parameters for Student Database Token ID #2039 into formal section track BSIT-10A.','2026-06-22 06:41:49'),(61,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back to zero state rosters.','2026-06-22 06:54:12'),(62,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back to zero state rosters.','2026-06-22 06:54:59'),(63,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back to zero state rosters.','2026-06-22 06:54:59'),(64,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back to zero state rosters.','2026-06-22 06:55:00'),(65,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back to zero state rosters.','2026-06-22 06:55:00'),(66,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back to zero state rosters.','2026-06-22 06:55:00'),(67,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back to zero state rosters.','2026-06-22 06:57:53'),(68,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back to zero state rosters.','2026-06-22 06:58:05'),(69,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back to zero state rosters.','2026-06-22 07:03:22'),(70,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back to zero state rosters.','2026-06-22 07:10:30'),(71,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back to zero state rosters.','2026-06-22 07:14:01'),(72,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back to zero state rosters.','2026-06-22 07:14:22'),(73,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back to zero state rosters.','2026-06-22 07:16:39'),(74,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back to zero state rosters.','2026-06-22 07:19:00'),(75,'SCHEDULES',NULL,'ADM-2026-00008','Rejected load configuration setup variables for student record context entry key #2039. Session dropped back.','2026-06-22 07:37:47'),(76,'STUDENTS',2040,'ADM-2026-00008','Updated Student profile data for {student_name} (2026-00003) .','2026-06-22 07:46:11'),(77,'STUDENTS',2040,'ADM-2026-00008','Updated Student profile data for {student_name} (2026-00003) .','2026-06-22 07:46:16'),(78,'STUDENTS',2040,'ADM-2026-00008','Updated Student profile data for {student_name} (2026-00003) .','2026-06-22 07:46:44'),(79,'ADMISSIONS',3,'ADM-2026-00008','Declined the Application of {student_name} .','2026-06-22 07:47:04'),(80,'SCHEDULES',NULL,'ADM-2026-00008','Formally confirmed enrollment for Student ID Token #2039 into formal section block group row BSIT-10A.','2026-06-22 07:53:45'),(81,'USERS',NULL,'ADM-2026-00008','Published a new Notice, \"PCC STUDENT COUNCIL BODY NOW AVAILABLE!\" for \"All Programs\" .','2026-06-22 08:07:13'),(82,'USERS',NULL,'ADM-2026-00008','Deleted a Notice, \"PCC STUDENT COUNCIL BODY NOW AVAILABLE!\" for \"All Programs\" .','2026-06-22 08:08:13'),(83,'ADMISSIONS',0,NULL,'Submitted an Application .','2026-06-22 09:29:00'),(84,'STUDENTS',NULL,'ADM-2026-00008','Deleted Student profile record for 2026-00003 from directory.','2026-06-22 09:38:48'),(85,'STUDENTS',NULL,'ADM-2026-00008','Deleted Student profile record for 2026-00001 from directory.','2026-06-22 09:39:54'),(86,'STUDENTS',NULL,'ADM-2026-00008','Deleted Student profile record for 2026-00001 from directory.','2026-06-22 09:39:55'),(87,'STUDENTS',NULL,'ADM-2026-00008','Deleted Student profile record for 2026-00001 from directory.','2026-06-22 09:39:56'),(88,'STUDENTS',NULL,'ADM-2026-00008','Deleted Student profile record for 2026-00001 from directory.','2026-06-22 09:39:56'),(89,'STUDENTS',NULL,'ADM-2026-00008','Deleted Student profile record for 2026-00001 from directory.','2026-06-22 09:39:56'),(90,'STUDENTS',NULL,'ADM-2026-00008','Deleted Student profile record for 2026-00002 from directory.','2026-06-22 09:40:34'),(91,'STUDENTS',NULL,'ADM-2026-00008','Deleted Student profile record for 2026-00002 from directory.','2026-06-22 09:40:35'),(92,'STUDENTS',NULL,'ADM-2026-00008','Deleted Student profile record for 2026-00002 from directory.','2026-06-22 09:40:36'),(93,'ADMISSIONS',7,'ADM-2026-00008','Approved Admission for John Doe. Account created: 2026-00001','2026-06-22 09:40:41'),(94,'ADMISSIONS',0,NULL,'Submitted an Application .','2026-06-22 09:43:47'),(95,'ADMISSIONS',8,'ADM-2026-00008','Approved Admission for Laurence Mendoza. Account created: 2026-00002','2026-06-22 09:43:55');
/*!40000 ALTER TABLE `system_updates` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-22 17:48:57
