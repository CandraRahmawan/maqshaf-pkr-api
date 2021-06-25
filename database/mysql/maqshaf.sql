/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.5.5-10.4.19-MariaDB : Database - maqshaf
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`maqshaf` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `maqshaf`;

/*Table structure for table `administrator` */

DROP TABLE IF EXISTS `administrator`;

CREATE TABLE `administrator` (
  `administrator_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(50) NOT NULL,
  `password` char(40) NOT NULL,
  `username` varchar(30) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(30) NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(30) DEFAULT NULL,
  `delete_at` datetime DEFAULT NULL,
  `delete_by` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`administrator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `administrator` */

LOCK TABLES `administrator` WRITE;

insert  into `administrator`(`administrator_id`,`full_name`,`password`,`username`,`created_at`,`created_by`,`updated_at`,`updated_by`,`delete_at`,`delete_by`) values (1,'Administrator','f865b53623b121fd34ee5426c792e5c33af8c227','administrator','2021-06-16 13:06:04', '0','2021-06-16 21:04:30',NULL,'2021-06-16 21:04:30','Dicky Dev');

UNLOCK TABLES;

/*Table structure for table `deposit` */

DROP TABLE IF EXISTS `deposit`;

CREATE TABLE `deposit` (
  `deposit_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `saldo` decimal(10,0) NOT NULL DEFAULT 0,
  `previous_saldo` decimal(10,0) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(30) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`deposit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/*Table structure for table `deposit_transactions` */

DROP TABLE IF EXISTS `deposit_transactions`;

CREATE TABLE `deposit_transactions` (
  `deposit_transaction_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `transaction_code` varchar(30) NOT NULL,
  `debet` decimal(10,0) NOT NULL DEFAULT 0 COMMENT 'pengurangan',
  `kredit` decimal(10,0) NOT NULL DEFAULT 0 COMMENT 'nambah',
  `transaction_date` datetime NOT NULL,
  `created_by` varchar(30) NOT NULL,
  `type` enum('0','1','3') NOT NULL COMMENT '0 = buy, 1 = withDrawl, 3 kredit',
  `deposit_id` bigint(20) NOT NULL,
  PRIMARY KEY (`deposit_transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/*Table structure for table `master_goods` */

DROP TABLE IF EXISTS `master_goods`;

CREATE TABLE `master_goods` (
  `master_goods_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `image` blob DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `price` decimal(10,0) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=false, 1 = true',
  `code` varchar(150) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(30) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`master_goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `transaction_items` */

DROP TABLE IF EXISTS `transaction_items`;

CREATE TABLE `transaction_items` (
  `transaction_items_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint(20) NOT NULL,
  `price` decimal(10,0) NOT NULL DEFAULT 0,
  `name` varchar(80) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`transaction_items_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `transactions` */

DROP TABLE IF EXISTS `transactions`;

CREATE TABLE `transactions` (
  `transaction_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `transaction_code` varchar(30) NOT NULL,
  `transaction_date` datetime NOT NULL,
  `total` decimal(10,0) NOT NULL DEFAULT 0 COMMENT 'total harga',
  `qty` int(11) NOT NULL DEFAULT 1 COMMENT 'jumlah barang',
  `user_id` bigint(20) NOT NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nis` varchar(60) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `class` varchar(10) NOT NULL,
  `address` varchar(500) DEFAULT NULL,
  `pin` char(40) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(30) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
