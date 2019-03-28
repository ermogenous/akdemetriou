/*
Navicat MySQL Data Transfer

Source Server         : Mysql Local
Source Server Version : 100130
Source Host           : localhost:3306
Source Database       : akdemetrioudb

Target Server Type    : MYSQL
Target Server Version : 100130
File Encoding         : 65001

Date: 2019-03-19 10:01:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ip_locations
-- ----------------------------
DROP TABLE IF EXISTS `ip_locations`;
CREATE TABLE `ip_locations` (
  `ipl_ip_location_serial` int(10) NOT NULL AUTO_INCREMENT,
  `ipl_ip` varchar(15) COLLATE utf8_bin DEFAULT NULL,
  `ipl_hostname` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `ipl_city` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `ipl_region` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `ipl_country` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `ipl_location` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `ipl_provider` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `ipl_last_check` datetime DEFAULT NULL,
  PRIMARY KEY (`ipl_ip_location_serial`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for log_file
-- ----------------------------
DROP TABLE IF EXISTS `log_file`;
CREATE TABLE `log_file` (
  `lgf_log_file_ID` int(10) NOT NULL AUTO_INCREMENT,
  `lgf_user_ID` int(10) DEFAULT NULL,
  `lgf_ip` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `lgf_date_time` datetime DEFAULT NULL,
  `lgf_table_name` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `lgf_row_serial` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `lgf_action` varchar(150) COLLATE utf8_bin DEFAULT NULL,
  `lgf_new_values` text COLLATE utf8_bin,
  `lgf_old_values` text COLLATE utf8_bin,
  `lgf_description` text COLLATE utf8_bin,
  PRIMARY KEY (`lgf_log_file_ID`),
  KEY `lgf_user_ID` (`lgf_user_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=583 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `prm_permissions_ID` int(8) NOT NULL AUTO_INCREMENT,
  `prm_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `prm_filename` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `prm_type` varchar(8) COLLATE utf8_bin DEFAULT NULL,
  `prm_parent` int(11) DEFAULT NULL,
  `prm_restricted` int(1) DEFAULT NULL,
  `prm_view` int(1) DEFAULT '0',
  `prm_insert` int(1) DEFAULT '0',
  `prm_update` int(1) DEFAULT '0',
  `prm_delete` int(1) DEFAULT '0',
  `prm_extra_1` int(1) DEFAULT '0',
  `prm_extra_2` int(1) DEFAULT '0',
  `prm_extra_3` int(1) DEFAULT '0',
  `prm_extra_4` int(1) DEFAULT '0',
  `prm_extra_5` int(1) DEFAULT '0',
  `prm_extra_name_1` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `prm_extra_name_2` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `prm_extra_name_3` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `prm_extra_name_4` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `prm_extra_name_5` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`prm_permissions_ID`),
  UNIQUE KEY `primary_serial` (`prm_permissions_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for permissions_lines
-- ----------------------------
DROP TABLE IF EXISTS `permissions_lines`;
CREATE TABLE `permissions_lines` (
  `prl_permissions_lines_ID` int(10) NOT NULL AUTO_INCREMENT,
  `prl_permissions_ID` int(10) DEFAULT NULL,
  `prl_users_groups_ID` int(11) DEFAULT NULL,
  `prl_view` int(1) DEFAULT NULL,
  `prl_insert` int(1) DEFAULT NULL,
  `prl_update` int(1) DEFAULT NULL,
  `prl_delete` int(1) DEFAULT NULL,
  `prl_extra_1` int(1) DEFAULT NULL,
  `prl_extra_2` int(1) DEFAULT NULL,
  `prl_extra_3` int(1) DEFAULT NULL,
  `prl_extra_4` int(1) DEFAULT NULL,
  `prl_extra_5` int(1) DEFAULT NULL,
  PRIMARY KEY (`prl_permissions_lines_ID`),
  UNIQUE KEY `primary_serial` (`prl_permissions_lines_ID`),
  KEY `permissions_serial` (`prl_permissions_ID`),
  KEY `users_groups_serial` (`prl_users_groups_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for pricing
-- ----------------------------
DROP TABLE IF EXISTS `pricing`;
CREATE TABLE `pricing` (
  `pricing_id` int(11) NOT NULL AUTO_INCREMENT,
  `coverage_type` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `package` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `area_of_cover` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `frequency_of_payment` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `age_from` int(11) NOT NULL,
  `age_to` int(11) NOT NULL,
  `excess` decimal(10,0) NOT NULL,
  `value` decimal(10,0) NOT NULL,
  PRIMARY KEY (`pricing_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23745 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for process_lock
-- ----------------------------
DROP TABLE IF EXISTS `process_lock`;
CREATE TABLE `process_lock` (
  `pl_process_lock_ID` int(8) NOT NULL AUTO_INCREMENT,
  `pl_description` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `pl_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `pl_user_serial` int(8) DEFAULT NULL,
  `pl_active` int(1) DEFAULT NULL,
  `pl_start_timestamp` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `pl_end_timestamp` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pl_process_lock_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for quotations
-- ----------------------------
DROP TABLE IF EXISTS `quotations`;
CREATE TABLE `quotations` (
  `quotations_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
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

-- ----------------------------
-- Table structure for quotation_approvals
-- ----------------------------
DROP TABLE IF EXISTS `quotation_approvals`;
CREATE TABLE `quotation_approvals` (
  `oqa_quotation_approvals_ID` int(8) NOT NULL AUTO_INCREMENT,
  `oqa_quotation_ID` int(8) DEFAULT NULL,
  `oqa_status` varchar(1) COLLATE utf8_bin DEFAULT NULL,
  `oqa_process_status` varchar(1) COLLATE utf8_bin DEFAULT NULL,
  `oqa_from_user_ID` int(8) DEFAULT NULL,
  `oqa_to_user_ID` int(8) DEFAULT NULL,
  `oqa_group_ID` int(8) DEFAULT NULL,
  `oqa_message` longtext COLLATE utf8_bin,
  `oqa_reply_message` longtext COLLATE utf8_bin,
  `oqa_send_date_time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `oqa_reply_date_time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `oqa_created_by` int(8) DEFAULT NULL,
  `oqa_created_on` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `oqa_last_edit_by` int(8) DEFAULT NULL,
  `oqa_last_edit_on` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`oqa_quotation_approvals_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for quotation_members
-- ----------------------------
DROP TABLE IF EXISTS `quotation_members`;
CREATE TABLE `quotation_members` (
  `quotation_members_ID` int(8) NOT NULL AUTO_INCREMENT,
  `quotations_id` int(8) DEFAULT NULL,
  `type` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `surname` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `id` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `age` int(4) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `order` int(4) DEFAULT NULL,
  `total_members` int(4) DEFAULT NULL,
  `individual_group` varchar(1) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`quotation_members_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for send_auto_emails
-- ----------------------------
DROP TABLE IF EXISTS `send_auto_emails`;
CREATE TABLE `send_auto_emails` (
  `sae_send_auto_emails_serial` int(10) NOT NULL AUTO_INCREMENT,
  `sae_active` varchar(1) COLLATE utf8_bin DEFAULT NULL COMMENT 'A -> Active',
  `sae_type` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `sae_created_datetime` datetime DEFAULT NULL,
  `sae_send_result` int(3) DEFAULT NULL,
  `sae_send_datetime` datetime DEFAULT NULL,
  `sae_primary_serial` int(10) DEFAULT NULL,
  `sae_primary_label` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `sae_secondary_serial` int(10) DEFAULT NULL,
  `sae_secondary_label` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `sae_label1` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `sae_label1_info` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `sae_label2` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `sae_label2_info` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `sae_email_to` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `sae_email_to_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `sae_email_from` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `sae_email_from_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `sae_email_subject` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `sae_email_reply_to` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `sae_email_reply_to_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `sae_email_cc` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `sae_email_bcc` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `sae_email_body` text COLLATE utf8_bin,
  `sae_attachment_file` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `sae_agent_code` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `sae_send_result_description` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`sae_send_auto_emails_serial`),
  UNIQUE KEY `unique_serial` (`sae_send_auto_emails_serial`),
  KEY `send_result` (`sae_send_result`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for settings
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `stg_settings_ID` int(10) NOT NULL AUTO_INCREMENT,
  `stg_section` varchar(50) COLLATE utf8_bin NOT NULL,
  `stg_value` varchar(250) COLLATE utf8_bin NOT NULL,
  `stg_value_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`stg_settings_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `usr_users_ID` int(8) NOT NULL AUTO_INCREMENT,
  `usr_users_groups_ID` int(8) DEFAULT NULL,
  `usr_active` int(1) NOT NULL,
  `usr_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `usr_username` varchar(100) COLLATE utf8_bin NOT NULL,
  `usr_password` varchar(30) COLLATE utf8_bin NOT NULL,
  `usr_user_rights` int(2) NOT NULL,
  `usr_restrict_ip` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `usr_email` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `usr_email2` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `usr_emailcc` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `usr_emailbcc` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `usr_tel` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `usr_is_agent` int(1) NOT NULL,
  `usr_agent_code` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `usr_agent_level` int(2) NOT NULL,
  `usr_issuing_office_serial` int(10) NOT NULL,
  `usr_description` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `usr_signature_gr` text COLLATE utf8_bin,
  `usr_signature_en` text COLLATE utf8_bin,
  `usr_name_gr` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `usr_name_en` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`usr_users_ID`),
  UNIQUE KEY `primary_serial` (`usr_users_ID`),
  KEY `group_serial` (`usr_users_groups_ID`),
  KEY `issuing` (`usr_issuing_office_serial`),
  KEY `active` (`usr_active`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for users_groups
-- ----------------------------
DROP TABLE IF EXISTS `users_groups`;
CREATE TABLE `users_groups` (
  `usg_users_groups_ID` int(10) NOT NULL AUTO_INCREMENT,
  `usg_group_name` varchar(80) COLLATE utf8_bin DEFAULT NULL,
  `usg_permissions` text COLLATE utf8_bin,
  `usg_restrict_ip` varchar(25) COLLATE utf8_bin NOT NULL,
  `usg_approvals` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`usg_users_groups_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
