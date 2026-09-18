-- MABISA 2.0 canonical schema
-- Extracted from the canonical database dump (MariaDB 10.4 / PHP 8.2).
-- Contains table structure only; no user/submission data.
-- Reference data (place names, roles, permissions) lives in seed.sql.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `mabisa2.0` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `mabisa2.0`;

CREATE TABLE `audit_log` (
  `action_id` int(11) NOT NULL,
  `user_id` int(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `action` varchar(500) NOT NULL,
  `time_and_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `barangay_assessment` (
  `keyctr` int(11) NOT NULL,
  `barangay_id` varchar(10) NOT NULL,
  `last_modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_ready` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `barangay_assessment_files` (
  `file_id` int(11) NOT NULL,
  `barangay_id` varchar(10) NOT NULL,
  `criteria_keyctr` bigint(20) NOT NULL,
  `date_uploaded` datetime NOT NULL,
  `comments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`comments`)),
  `status` enum('approved','pending','declined') NOT NULL,
  `file_path` text NOT NULL,
  `file_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `maintenance_area` (
  `keyctr` bigint(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  `trail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `maintenance_area_description` (
  `keyctr` bigint(20) NOT NULL,
  `description` varchar(200) NOT NULL,
  `trail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `maintenance_area_indicators` (
  `keyctr` bigint(20) NOT NULL,
  `governance_code` bigint(20) NOT NULL,
  `desc_keyctr` bigint(20) NOT NULL,
  `area_description` varchar(200) NOT NULL,
  `indicator_code` varchar(6) NOT NULL,
  `indicator_description` text NOT NULL,
  `relevance_def` text NOT NULL,
  `min_requirement` tinyint(1) NOT NULL DEFAULT 0,
  `trail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `maintenance_area_mininumreqs` (
  `keyctr` bigint(20) NOT NULL,
  `indicator_keyctr` bigint(20) NOT NULL,
  `reqs_code` varchar(10) NOT NULL,
  `description` text NOT NULL,
  `sub_mininumreqs` tinyint(1) NOT NULL DEFAULT 0,
  `trail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `maintenance_area_mininumreqs_sub` (
  `keyctr` bigint(20) NOT NULL,
  `mininumreq_keyctr` bigint(20) DEFAULT NULL,
  `indicator_keyctr` bigint(20) NOT NULL,
  `reqs_code` varchar(10) NOT NULL,
  `description` text NOT NULL,
  `trail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `maintenance_category` (
  `code` varchar(6) NOT NULL,
  `short_def` varchar(15) NOT NULL,
  `description` varchar(200) NOT NULL,
  `trail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `maintenance_criteria_setup` (
  `keyctr` bigint(20) UNSIGNED NOT NULL,
  `version_keyctr` bigint(20) DEFAULT NULL,
  `indicator_keyctr` bigint(20) DEFAULT NULL,
  `minreqs_keyctr` bigint(20) NOT NULL,
  `sub_minimumreqs` tinyint(1) NOT NULL,
  `movdocs_reqs` text NOT NULL,
  `template` varchar(300) NOT NULL,
  `data_source` int(11) DEFAULT NULL,
  `trail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `maintenance_criteria_version` (
  `keyctr` bigint(20) NOT NULL,
  `short_def` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `active_yr` varchar(4) NOT NULL,
  `active_` tinyint(1) NOT NULL DEFAULT 1,
  `duration` varchar(100) NOT NULL,
  `is_accepting_response` tinyint(1) NOT NULL,
  `trail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `maintenance_document_source` (
  `keyctr` bigint(20) NOT NULL,
  `srccode` varchar(10) NOT NULL,
  `srcdesc` varchar(200) NOT NULL,
  `trail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `maintenance_governance` (
  `keyctr` bigint(20) NOT NULL,
  `cat_code` varchar(6) NOT NULL,
  `area_keyctr` bigint(20) NOT NULL,
  `desc_keyctr` bigint(20) NOT NULL,
  `trail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `super_admin_delete` tinyint(1) DEFAULT 0,
  `super_admin_read` tinyint(1) DEFAULT 0,
  `super_admin_create` tinyint(1) DEFAULT 0,
  `users_create` tinyint(1) DEFAULT 0,
  `users_delete` tinyint(1) DEFAULT 0,
  `users_update` tinyint(1) DEFAULT 0,
  `users_read` tinyint(1) DEFAULT 0,
  `roles_create` tinyint(1) DEFAULT 0,
  `roles_delete` tinyint(1) DEFAULT 0,
  `roles_update` tinyint(1) DEFAULT 0,
  `roles_read` tinyint(1) DEFAULT 0,
  `criteria_create` tinyint(1) DEFAULT 0,
  `criteria_read` tinyint(1) DEFAULT 0,
  `criteria_update` tinyint(1) DEFAULT 0,
  `criteria_delete` tinyint(1) DEFAULT 0,
  `assessment_submissions_create` tinyint(1) DEFAULT 0,
  `assessment_submissions_read` tinyint(1) DEFAULT 0,
  `assessment_submissions_update` tinyint(1) DEFAULT 0,
  `assessment_submissions_delete` tinyint(1) DEFAULT 0,
  `assessment_submissions_approve_disapprove` tinyint(1) DEFAULT 0,
  `assessment_comments_create` tinyint(1) DEFAULT 0,
  `assessment_comments_read` tinyint(1) DEFAULT 0,
  `assessment_comments_update` tinyint(1) DEFAULT 0,
  `assessment_comments_delete` tinyint(1) DEFAULT 0,
  `map_read` tinyint(1) DEFAULT 0,
  `reports_read` tinyint(1) DEFAULT 0,
  `reports_generate` tinyint(1) DEFAULT 0,
  `last_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `refbarangay` (
  `brgyid` varchar(10) NOT NULL,
  `brgyname` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `refcity` (
  `provid` varchar(10) NOT NULL,
  `cityid` varchar(10) NOT NULL,
  `cityname` varchar(30) NOT NULL,
  `trail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `refprovince` (
  `provid` varchar(10) NOT NULL,
  `provname` varchar(50) NOT NULL,
  `country` varchar(30) NOT NULL,
  `trail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `allow_bar` tinyint(1) NOT NULL DEFAULT 0,
  `bar_perms` int(11) DEFAULT NULL,
  `gen_perms` int(11) DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile_num` varchar(10) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `is_disabled` tinyint(1) NOT NULL DEFAULT 0,
  `profile_pic` varchar(200) DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `permissions_id` int(11) NOT NULL,
  `user_roles_barangay_id` int(11) DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_roles_barangay` (
  `user_id` int(11) NOT NULL,
  `barangay_id` varchar(10) DEFAULT NULL,
  `indicator_id` bigint(20) DEFAULT NULL,
  `permission_id` int(11) DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

