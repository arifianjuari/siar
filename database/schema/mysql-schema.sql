/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `causer_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint unsigned DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint unsigned DEFAULT NULL,
  `changes` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  KEY `activity_logs_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `activity_logs_action_index` (`action`),
  KEY `activity_logs_tenant_id_index` (`tenant_id`),
  CONSTRAINT `activity_logs_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `correspondences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `correspondences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `work_unit_id` bigint unsigned DEFAULT NULL,
  `document_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_type` enum('Regulasi','Bukti') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bukti',
  `document_version` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_date` date NOT NULL,
  `confidentiality_level` enum('Internal','Publik','Rahasia') COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `next_review` timestamp NULL DEFAULT NULL,
  `origin_module` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_record_id` bigint DEFAULT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_to` text COLLATE utf8mb4_unicode_ci,
  `sender_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_position` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient_position` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cc_list` text COLLATE utf8mb4_unicode_ci,
  `signed_at_location` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signed_at_date` date NOT NULL,
  `signatory_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signatory_position` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signatory_rank` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signatory_nrp` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `correspondences_tenant_id_foreign` (`tenant_id`),
  KEY `correspondences_created_by_foreign` (`created_by`),
  KEY `correspondences_work_unit_id_index` (`work_unit_id`),
  CONSTRAINT `correspondences_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `correspondences_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `correspondences_work_unit_id_foreign` FOREIGN KEY (`work_unit_id`) REFERENCES `work_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `document_references`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_references` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_type` enum('Peraturan Perundangan','Peraturan Kapolri','Surat Keputusan','Surat Eksternal','Surat Internal','Pedoman','SOP','Dokumen Lainnya') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `issued_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issued_date` date NOT NULL,
  `related_unit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_url` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `tags` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `document_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_tag` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` bigint unsigned NOT NULL,
  `document_id` bigint unsigned NOT NULL,
  `document_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_tag_unique` (`tag_id`,`document_id`,`document_type`),
  KEY `document_tag_document_id_document_type_index` (`document_id`,`document_type`),
  CONSTRAINT `document_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `documentables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint unsigned NOT NULL,
  `documentable_id` bigint unsigned NOT NULL,
  `documentable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `relation_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentables_document_id_foreign` (`document_id`),
  KEY `documentables_documentable_id_documentable_type_index` (`documentable_id`,`documentable_type`),
  CONSTRAINT `documentables_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `document_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_date` timestamp NULL DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `confidentiality_level` enum('public','internal','confidential') COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_type` enum('policy','guideline','spo','program','evidence') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_scope` enum('rumahsakit','unitkerja') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_regulation` tinyint(1) NOT NULL DEFAULT '0',
  `revision_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revision_date` date DEFAULT NULL,
  `superseded_by_id` bigint unsigned DEFAULT NULL,
  `storage_location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distribution_note` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_evaluated_at` timestamp NULL DEFAULT NULL,
  `evaluated_by` bigint unsigned DEFAULT NULL,
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploaded_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_tenant_id_foreign` (`tenant_id`),
  KEY `documents_uploaded_by_foreign` (`uploaded_by`),
  KEY `documents_superseded_by_id_foreign` (`superseded_by_id`),
  KEY `documents_evaluated_by_foreign` (`evaluated_by`),
  CONSTRAINT `documents_evaluated_by_foreign` FOREIGN KEY (`evaluated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_superseded_by_id_foreign` FOREIGN KEY (`superseded_by_id`) REFERENCES `documents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`),
  CONSTRAINT `documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modules_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `performance_indicators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `performance_indicators` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `measurement_type` enum('positive','negative','custom') COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_formula` text COLLATE utf8mb4_unicode_ci,
  `unit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` enum('financial','customer','process','learning') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_shared` tinyint(1) NOT NULL DEFAULT '0',
  `tenant_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `performance_indicators_code_unique` (`code`),
  KEY `performance_indicators_tenant_id_foreign` (`tenant_id`),
  KEY `performance_indicators_created_by_foreign` (`created_by`),
  KEY `performance_indicators_updated_by_foreign` (`updated_by`),
  CONSTRAINT `performance_indicators_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `performance_indicators_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `performance_indicators_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `performance_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `performance_scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `period` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `indicator_id` bigint unsigned DEFAULT NULL,
  `target_value` double(8,2) NOT NULL,
  `actual_value` double(8,2) NOT NULL,
  `weight` double(8,2) NOT NULL,
  `score` double(8,2) NOT NULL,
  `grade` enum('A+','A','B','C','D') COLLATE utf8mb4_unicode_ci NOT NULL,
  `evaluator_id` bigint unsigned DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `tenant_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `performance_scores_evaluator_id_foreign` (`evaluator_id`),
  KEY `performance_scores_tenant_id_foreign` (`tenant_id`),
  KEY `performance_scores_created_by_foreign` (`created_by`),
  KEY `performance_scores_updated_by_foreign` (`updated_by`),
  KEY `performance_scores_user_id_period_index` (`user_id`,`period`),
  KEY `performance_scores_period_tenant_id_index` (`period`,`tenant_id`),
  KEY `performance_scores_indicator_id_period_index` (`indicator_id`,`period`),
  CONSTRAINT `performance_scores_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `performance_scores_evaluator_id_foreign` FOREIGN KEY (`evaluator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `performance_scores_indicator_id_foreign` FOREIGN KEY (`indicator_id`) REFERENCES `performance_indicators` (`id`) ON DELETE SET NULL,
  CONSTRAINT `performance_scores_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `performance_scores_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `performance_scores_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `performance_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `performance_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned DEFAULT NULL,
  `indicator_id` bigint unsigned DEFAULT NULL,
  `weight` double(8,2) NOT NULL,
  `default_target_value` double(8,2) DEFAULT NULL,
  `position` int DEFAULT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `performance_templates_indicator_id_foreign` (`indicator_id`),
  KEY `performance_templates_tenant_id_foreign` (`tenant_id`),
  KEY `performance_templates_created_by_foreign` (`created_by`),
  KEY `performance_templates_updated_by_foreign` (`updated_by`),
  KEY `performance_templates_role_id_indicator_id_index` (`role_id`,`indicator_id`),
  CONSTRAINT `performance_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `performance_templates_indicator_id_foreign` FOREIGN KEY (`indicator_id`) REFERENCES `performance_indicators` (`id`) ON DELETE SET NULL,
  CONSTRAINT `performance_templates_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `performance_templates_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `performance_templates_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `stock` int NOT NULL DEFAULT '0',
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_tenant_id_code_unique` (`tenant_id`,`code`),
  KEY `products_tenant_id_index` (`tenant_id`),
  CONSTRAINT `products_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `risk_analysis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `risk_analysis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `risk_report_id` bigint unsigned NOT NULL,
  `direct_cause` text COLLATE utf8mb4_unicode_ci,
  `root_cause` text COLLATE utf8mb4_unicode_ci,
  `contributor_factors` json DEFAULT NULL,
  `recommendation_short` text COLLATE utf8mb4_unicode_ci,
  `recommendation_medium` text COLLATE utf8mb4_unicode_ci,
  `recommendation_long` text COLLATE utf8mb4_unicode_ci,
  `analyzed_by` bigint unsigned DEFAULT NULL,
  `analyzed_at` timestamp NULL DEFAULT NULL,
  `analysis_status` enum('draft','in_progress','completed','reviewed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_analysis_risk_report_id_foreign` (`risk_report_id`),
  KEY `risk_analysis_analyzed_by_foreign` (`analyzed_by`),
  CONSTRAINT `risk_analysis_analyzed_by_foreign` FOREIGN KEY (`analyzed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `risk_analysis_risk_report_id_foreign` FOREIGN KEY (`risk_report_id`) REFERENCES `risk_reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `risk_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `risk_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  `work_unit_id` bigint unsigned DEFAULT NULL,
  `document_title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_type` enum('Regulasi','Bukti') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_version` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confidentiality_level` enum('Publik','Internal','Rahasia') COLLATE utf8mb4_unicode_ci DEFAULT 'Internal',
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chronology` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `immediate_action` text COLLATE utf8mb4_unicode_ci,
  `reporter_unit` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `risk_type` enum('KTD','KNC','KTC','KPC','Sentinel') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `risk_category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `occurred_at` date NOT NULL,
  `impact` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `probability` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `risk_level` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Draft','Ditinjau','Selesai') COLLATE utf8mb4_unicode_ci DEFAULT 'Draft',
  `recommendation` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `next_review` timestamp NULL DEFAULT NULL,
  `review_cycle_months` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `document_date` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_reports_created_by_foreign` (`created_by`),
  KEY `risk_reports_tenant_id_index` (`tenant_id`),
  KEY `risk_reports_reviewed_by_foreign` (`reviewed_by`),
  KEY `risk_reports_approved_by_foreign` (`approved_by`),
  KEY `risk_reports_work_unit_id_index` (`work_unit_id`),
  CONSTRAINT `risk_reports_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `risk_reports_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `risk_reports_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `risk_reports_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `risk_reports_work_unit_id_foreign` FOREIGN KEY (`work_unit_id`) REFERENCES `work_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_module_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_module_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `module_id` bigint unsigned NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT '0',
  `can_create` tinyint(1) NOT NULL DEFAULT '0',
  `can_edit` tinyint(1) NOT NULL DEFAULT '0',
  `can_delete` tinyint(1) NOT NULL DEFAULT '0',
  `can_export` tinyint(1) NOT NULL DEFAULT '0',
  `can_import` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_module_permissions_role_id_module_id_unique` (`role_id`,`module_id`),
  KEY `role_module_permissions_module_id_foreign` (`module_id`),
  CONSTRAINT `role_module_permissions_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_module_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_tenant_id_slug_unique` (`tenant_id`,`slug`),
  CONSTRAINT `roles_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `spos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spos` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `work_unit_id` bigint unsigned NOT NULL,
  `document_title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_type` enum('Kebijakan','Pedoman','SPO','Perencanaan','Program') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SPO',
  `document_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_date` date NOT NULL,
  `document_version` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `confidentiality_level` enum('Internal','Publik','Rahasia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Publik',
  `file_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `next_review` timestamp NULL DEFAULT NULL,
  `review_cycle_months` int NOT NULL DEFAULT '12',
  `status_validasi` enum('Draft','Disetujui','Kadaluarsa','Revisi') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `definition` text COLLATE utf8mb4_unicode_ci,
  `purpose` text COLLATE utf8mb4_unicode_ci,
  `policy` text COLLATE utf8mb4_unicode_ci,
  `procedure` longtext COLLATE utf8mb4_unicode_ci,
  `linked_unit` json DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `parent_id` bigint unsigned DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_tenant_id_name_unique` (`tenant_id`,`name`),
  UNIQUE KEY `tags_tenant_id_slug_unique` (`tenant_id`,`slug`),
  KEY `tags_parent_id_foreign` (`parent_id`),
  CONSTRAINT `tags_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `tags` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tags_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries` (
  `sequence` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `family_hash` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `should_display_on_index` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`sequence`),
  UNIQUE KEY `telescope_entries_uuid_unique` (`uuid`),
  KEY `telescope_entries_batch_id_index` (`batch_id`),
  KEY `telescope_entries_family_hash_index` (`family_hash`),
  KEY `telescope_entries_created_at_index` (`created_at`),
  KEY `telescope_entries_type_should_display_on_index_index` (`type`,`should_display_on_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_entries_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries_tags` (
  `entry_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`entry_uuid`,`tag`),
  KEY `telescope_entries_tags_tag_index` (`tag`),
  CONSTRAINT `telescope_entries_tags_entry_uuid_foreign` FOREIGN KEY (`entry_uuid`) REFERENCES `telescope_entries` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_monitoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_monitoring` (
  `tag` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tenant_module_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_module_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `module` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kode modul (contoh: risk-management)',
  `feature` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama fitur dalam modul (contoh: risk_analysis)',
  `config_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kunci konfigurasi tambahan (opsional)',
  `config_value` json DEFAULT NULL COMMENT 'Nilai konfigurasi dalam format JSON',
  `allowed_roles` json DEFAULT NULL COMMENT 'Daftar ID role yang diizinkan mengakses fitur',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_module_config_unique` (`tenant_id`,`module`,`feature`,`config_key`),
  KEY `tenant_module_configs_tenant_id_module_feature_index` (`tenant_id`,`module`,`feature`),
  CONSTRAINT `tenant_module_configs_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tenant_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_modules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `module_id` bigint unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `requested_at` timestamp NULL DEFAULT NULL,
  `requested_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_modules_tenant_id_module_id_unique` (`tenant_id`,`module_id`),
  KEY `tenant_modules_module_id_foreign` (`module_id`),
  KEY `tenant_modules_requested_by_foreign` (`requested_by`),
  KEY `tenant_modules_approved_by_foreign` (`approved_by`),
  CONSTRAINT `tenant_modules_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tenant_modules_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tenant_modules_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tenant_modules_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `letter_head` text COLLATE utf8mb4_unicode_ci,
  `settings` json DEFAULT NULL,
  `domain` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `database` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_domain_unique` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `position` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nrp` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_user_agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `work_unit_id` bigint unsigned DEFAULT NULL,
  `supervisor_id` bigint unsigned DEFAULT NULL,
  `employment_status` enum('aktif','resign','cuti','magang') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`id`),
  KEY `users_tenant_id_foreign` (`tenant_id`),
  KEY `users_role_id_foreign` (`role_id`),
  KEY `users_created_by_foreign` (`created_by`),
  KEY `users_updated_by_foreign` (`updated_by`),
  KEY `users_work_unit_id_foreign` (`work_unit_id`),
  KEY `users_supervisor_id_foreign` (`supervisor_id`),
  CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_work_unit_id_foreign` FOREIGN KEY (`work_unit_id`) REFERENCES `work_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `work_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `unit_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `unit_type` enum('medical','non-medical','supporting') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `head_of_unit_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `parent_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `work_units_tenant_id_name_unique` (`tenant_id`,`unit_name`),
  UNIQUE KEY `work_units_tenant_id_code_unique` (`tenant_id`,`unit_code`),
  KEY `work_units_head_of_unit_id_foreign` (`head_of_unit_id`),
  CONSTRAINT `work_units_head_of_unit_id_foreign` FOREIGN KEY (`head_of_unit_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_units_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2018_08_08_100000_create_telescope_entries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2023_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2023_01_01_000001_create_tenants_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2023_01_01_000002_create_modules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2023_01_01_000003_create_tenant_modules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2023_01_01_000004_create_roles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2023_01_01_000005_create_role_module_permissions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2023_01_01_000006_modify_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2024_03_21_000000_add_tenant_id_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2024_03_21_000001_create_tenants_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_03_26_170156_create_products_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_03_26_231145_create_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_03_26_231146_add_event_column_to_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_03_26_231147_add_batch_uuid_column_to_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_03_26_233444_create_risk_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_03_26_233853_add_risk_management_module',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_03_26_234156_add_risk_management_to_tenant_modules',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_03_26_235805_add_approval_fields_to_risk_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_03_27_001324_create_activity_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_03_27_010012_add_request_fields_to_tenant_modules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_03_27_130615_add_fields_to_tenants_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_03_27_132840_create_work_units_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_03_27_152256_create_risk_analysis_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_03_27_164520_create_tenant_module_configs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_03_28_000000_add_slug_to_modules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_03_28_000001_update_module_slugs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_03_28_145153_add_description_and_immediate_action_to_risk_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_03_28_214559_add_riskreport_number_to_risk_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_03_29_100000_update_status_enum_in_risk_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_03_29_110558_add_position_and_nrp_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_03_29_113646_rename_columns_in_risk_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_03_29_113716_rename_columns_in_risk_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_03_29_214449_create_tags_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_03_30_023535_add_slug_to_tags_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2025_03_30_023550_create_document_tag_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2025_03_30_023649_add_unique_slug_constraint_to_tags_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2025_03_30_080213_create_documents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2025_03_30_080217_create_documentables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2025_03_30_080537_add_document_management_module',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2025_03_30_083614_add_kars_fields_to_documents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2025_03_30_083920_add_is_active_to_documents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2025_03_30_104218_add_new_columns_to_risk_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2025_03_30_104516_update_confidentiality_level_in_risk_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2025_03_30_104733_update_confidentiality_level_to_indonesian_in_risk_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2025_03_30_221310_create_correspondences_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2025_03_30_223408_add_correspondence_module',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2025_03_30_224229_add_correspondence_module_to_superadmin',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2025_03_31_212924_create_document_references_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2025_03_31_214138_update_reference_type_enum_in_document_references_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2025_04_01_164212_add_document_link_to_correspondences_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2025_04_02_102408_add_letter_head_and_city_to_tenants_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2025_04_02_102630_change_letter_head_column_type_in_tenants_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2025_04_02_114020_create_work_units_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2025_04_02_114910_update_work_units_table_schema',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2025_04_02_130423_add_work_unit_id_to_risk_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2025_04_02_130429_add_work_unit_id_to_correspondences_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2025_04_02_203823_add_work_unit_id_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2025_04_02_224525_add_profile_photo_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2025_04_03_093413_add_supervisor_id_and_employment_status_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2025_04_03_094258_add_performance_management_module',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2025_04_03_094313_create_performance_indicators_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2025_04_03_094329_create_performance_templates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2025_04_03_094346_create_performance_scores_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2025_04_03_094504_add_performance_management_to_tenant_modules',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2025_04_03_094758_modify_performance_templates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2025_04_03_094824_create_performance_scores_table_fixed',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2025_04_03_124022_modify_file_path_in_correspondences_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2025_04_06_222137_create_spos_table',3);
