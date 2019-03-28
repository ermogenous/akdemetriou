/*
Navicat MySQL Data Transfer

Source Server         : Mysql Local
Source Server Version : 100130
Source Host           : localhost:3306
Source Database       : akdemetrioudb

Target Server Type    : MYSQL
Target Server Version : 100130
File Encoding         : 65001

Date: 2019-03-19 12:49:09
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for quotations
-- ----------------------------
DROP TABLE IF EXISTS `quotations`;
CREATE TABLE `quotations` (
  `quotations_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `effective_date` date DEFAULT NULL,
  `country` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `package` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `coverage_type` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `area_of_cover` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `frequency_of_payment` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `excess` int(11) NOT NULL,
  `individual_group` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `client_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `client_sur_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_address` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_age` int(11) NOT NULL,
  `client_birthdate` date DEFAULT NULL,
  `client_email` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_mobile` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `loading` double(8,0) DEFAULT '0',
  `under_10_discount` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`quotations_id`),
  UNIQUE KEY `id` (`quotations_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
