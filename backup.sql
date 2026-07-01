-- MySQL dump 10.13  Distrib 9.3.0, for macos15.2 (arm64)
--
-- Host: localhost    Database: data_aset_barang
-- ------------------------------------------------------
-- Server version	9.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `barang_keluar_t`
--

DROP TABLE IF EXISTS `barang_keluar_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barang_keluar_t` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_transaksi` varchar(30) NOT NULL,
  `barang_id` int NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` int NOT NULL,
  `tujuan` varchar(150) NOT NULL,
  `keterangan` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_transaksi` (`nomor_transaksi`),
  KEY `fk_barang_keluar_barang` (`barang_id`),
  KEY `fk_barang_keluar_created_by` (`created_by`),
  KEY `fk_barang_keluar_updated_by` (`updated_by`),
  CONSTRAINT `fk_barang_keluar_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang_t` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_barang_keluar_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_barang_keluar_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barang_keluar_t`
--

LOCK TABLES `barang_keluar_t` WRITE;
/*!40000 ALTER TABLE `barang_keluar_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `barang_keluar_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `barang_masuk_t`
--

DROP TABLE IF EXISTS `barang_masuk_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barang_masuk_t` (
  `id` int NOT NULL AUTO_INCREMENT,
  `barang_id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jumlah` int NOT NULL,
  `harga` decimal(15,2) DEFAULT '0.00',
  `keterangan` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_barang_masuk_barang` (`barang_id`),
  KEY `fk_barang_masuk_supplier` (`supplier_id`),
  KEY `fk_barang_masuk_created_by` (`created_by`),
  KEY `fk_barang_masuk_updated_by` (`updated_by`),
  CONSTRAINT `fk_barang_masuk_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang_t` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_barang_masuk_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_barang_masuk_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_m` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_barang_masuk_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barang_masuk_t`
--

LOCK TABLES `barang_masuk_t` WRITE;
/*!40000 ALTER TABLE `barang_masuk_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `barang_masuk_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `barang_t`
--

DROP TABLE IF EXISTS `barang_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barang_t` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(30) NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `kategori_id` int DEFAULT NULL,
  `lokasi_id` int DEFAULT NULL,
  `merk` varchar(100) DEFAULT NULL,
  `tipe` varchar(100) DEFAULT NULL,
  `tahun` year DEFAULT NULL,
  `kondisi` enum('Baik','Rusak Ringan','Rusak Berat') DEFAULT 'Baik',
  `stok` int NOT NULL DEFAULT '0',
  `foto` varchar(255) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_barang` (`kode_barang`),
  KEY `fk_barang_kategori` (`kategori_id`),
  KEY `fk_barang_lokasi` (`lokasi_id`),
  KEY `fk_barang_created_by` (`created_by`),
  KEY `fk_barang_updated_by` (`updated_by`),
  CONSTRAINT `fk_barang_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_barang_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_m` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_barang_lokasi` FOREIGN KEY (`lokasi_id`) REFERENCES `lokasi_m` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_barang_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barang_t`
--

LOCK TABLES `barang_t` WRITE;
/*!40000 ALTER TABLE `barang_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `barang_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kategori_m`
--

DROP TABLE IF EXISTS `kategori_m`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kategori_m` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategori_m`
--

LOCK TABLES `kategori_m` WRITE;
/*!40000 ALTER TABLE `kategori_m` DISABLE KEYS */;
INSERT INTO `kategori_m` VALUES (5,'laptop',NULL,NULL,'2026-07-01 08:56:44','2026-07-01 08:56:44'),(6,'sepatu',NULL,NULL,'2026-07-01 08:58:25','2026-07-01 08:58:25'),(7,'sendal',NULL,NULL,'2026-07-01 08:58:44','2026-07-01 09:12:42');
/*!40000 ALTER TABLE `kategori_m` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lokasi_m`
--

DROP TABLE IF EXISTS `lokasi_m`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lokasi_m` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_lokasi` varchar(100) NOT NULL,
  `keterangan` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lokasi_m`
--

LOCK TABLES `lokasi_m` WRITE;
/*!40000 ALTER TABLE `lokasi_m` DISABLE KEYS */;
/*!40000 ALTER TABLE `lokasi_m` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_m`
--

DROP TABLE IF EXISTS `supplier_m`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_m` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_supplier` varchar(100) NOT NULL,
  `alamat` text,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_m`
--

LOCK TABLES `supplier_m` WRITE;
/*!40000 ALTER TABLE `supplier_m` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_m` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin','superadmin') NOT NULL DEFAULT 'admin',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'piyo aswandi','piyoaswandi','$2y$10$jm9/dSPsnHQg7yFG0ezCzeq36YfUBkHKGX8y0ibqNhw6s6eH4expu','superadmin',NULL,NULL,'2026-07-01 07:11:18','2026-07-01 07:11:38');
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

-- Dump completed on 2026-07-01 16:15:16
