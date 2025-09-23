-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.33 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for power
CREATE DATABASE IF NOT EXISTS `power` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `power`;

-- Dumping structure for table power.atletas
CREATE TABLE IF NOT EXISTS `atletas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `entrenador_id` bigint(20) unsigned NOT NULL,
  `gimnasio_id` bigint(20) unsigned NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'atletas_fotos/default_photo.png',
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('Masculino','Femenino') COLLATE utf8mb4_unicode_ci NOT NULL,
  `altura` decimal(5,2) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `estilo_vida` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lesiones_previas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `atletas_user_id_foreign` (`user_id`),
  KEY `atletas_entrenador_id_foreign` (`entrenador_id`),
  KEY `atletas_gimnasio_id_foreign` (`gimnasio_id`),
  CONSTRAINT `atletas_entrenador_id_foreign` FOREIGN KEY (`entrenador_id`) REFERENCES `entrenadors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `atletas_gimnasio_id_foreign` FOREIGN KEY (`gimnasio_id`) REFERENCES `gimnasios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `atletas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.atletas: ~3 rows (approximately)
/*!40000 ALTER TABLE `atletas` DISABLE KEYS */;
INSERT INTO `atletas` (`id`, `user_id`, `entrenador_id`, `gimnasio_id`, `foto`, `fecha_nacimiento`, `genero`, `altura`, `peso`, `estilo_vida`, `lesiones_previas`, `created_at`, `updated_at`) VALUES
	(1, 9, 5, 4, 'atletas/Captura de pantalla 2025-08-14 095219.png', '2006-06-06', 'Masculino', 178.00, NULL, 'Poco activa', 'Dolor en muñeca', '2025-08-21 16:20:03', '2025-08-21 16:20:03'),
	(2, 10, 5, 4, 'atletas/Captura de pantalla 2025-08-14 095219.png', '1998-08-11', 'Masculino', 188.00, 80.00, 'Entrenamiento constante', 'Dolor en muñeca', '2025-08-21 16:38:54', '2025-08-21 20:17:28'),
	(3, 13, 6, 3, 'atletas/161459108_10222382565686596_7341632390520240378_n.jpg', '1992-12-30', 'Masculino', 180.00, 96.00, 'sano', 'no', '2025-09-16 15:30:53', '2025-09-16 15:30:53');
/*!40000 ALTER TABLE `atletas` ENABLE KEYS */;

-- Dumping structure for table power.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.cache: ~8 rows (approximately)
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('laravel_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3', 'i:1;', 1758211311),
	('laravel_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1758211311;', 1758211311),
	('laravel_cache_spatie.permission.cache', 'a:3:{s:5:"alias";a:4:{s:1:"a";s:2:"id";s:1:"b";s:4:"name";s:1:"c";s:10:"guard_name";s:1:"r";s:5:"roles";}s:11:"permissions";a:60:{i:0;a:4:{s:1:"a";i:1;s:1:"b";s:9:"view_role";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:1;a:4:{s:1:"a";i:2;s:1:"b";s:13:"view_any_role";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:2;a:4:{s:1:"a";i:3;s:1:"b";s:11:"create_role";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:3;a:4:{s:1:"a";i:4;s:1:"b";s:11:"update_role";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:4;a:4:{s:1:"a";i:5;s:1:"b";s:12:"restore_role";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:5;a:4:{s:1:"a";i:6;s:1:"b";s:16:"restore_any_role";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:6;a:4:{s:1:"a";i:7;s:1:"b";s:14:"replicate_role";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:7;a:4:{s:1:"a";i:8;s:1:"b";s:12:"reorder_role";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:8;a:4:{s:1:"a";i:9;s:1:"b";s:11:"delete_role";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:9;a:4:{s:1:"a";i:10;s:1:"b";s:15:"delete_any_role";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:10;a:4:{s:1:"a";i:11;s:1:"b";s:17:"force_delete_role";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:11;a:4:{s:1:"a";i:12;s:1:"b";s:21:"force_delete_any_role";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:12;a:4:{s:1:"a";i:13;s:1:"b";s:11:"view_atleta";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:3;}}i:13;a:4:{s:1:"a";i:14;s:1:"b";s:15:"view_any_atleta";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:3;}}i:14;a:4:{s:1:"a";i:15;s:1:"b";s:13:"create_atleta";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:3;}}i:15;a:4:{s:1:"a";i:16;s:1:"b";s:13:"update_atleta";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:3;}}i:16;a:4:{s:1:"a";i:17;s:1:"b";s:14:"restore_atleta";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:3;}}i:17;a:4:{s:1:"a";i:18;s:1:"b";s:18:"restore_any_atleta";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:3;}}i:18;a:4:{s:1:"a";i:19;s:1:"b";s:16:"replicate_atleta";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:3;}}i:19;a:4:{s:1:"a";i:20;s:1:"b";s:14:"reorder_atleta";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:3;}}i:20;a:4:{s:1:"a";i:21;s:1:"b";s:13:"delete_atleta";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:3;}}i:21;a:4:{s:1:"a";i:22;s:1:"b";s:17:"delete_any_atleta";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:3;}}i:22;a:4:{s:1:"a";i:23;s:1:"b";s:19:"force_delete_atleta";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:3;}}i:23;a:4:{s:1:"a";i:24;s:1:"b";s:23:"force_delete_any_atleta";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:3;}}i:24;a:4:{s:1:"a";i:25;s:1:"b";s:15:"view_entrenador";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:25;a:4:{s:1:"a";i:26;s:1:"b";s:19:"view_any_entrenador";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:26;a:4:{s:1:"a";i:27;s:1:"b";s:17:"create_entrenador";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:27;a:4:{s:1:"a";i:28;s:1:"b";s:17:"update_entrenador";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:28;a:4:{s:1:"a";i:29;s:1:"b";s:18:"restore_entrenador";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:29;a:4:{s:1:"a";i:30;s:1:"b";s:22:"restore_any_entrenador";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:30;a:4:{s:1:"a";i:31;s:1:"b";s:20:"replicate_entrenador";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:31;a:4:{s:1:"a";i:32;s:1:"b";s:18:"reorder_entrenador";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:32;a:4:{s:1:"a";i:33;s:1:"b";s:17:"delete_entrenador";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:33;a:4:{s:1:"a";i:34;s:1:"b";s:21:"delete_any_entrenador";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:34;a:4:{s:1:"a";i:35;s:1:"b";s:23:"force_delete_entrenador";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:35;a:4:{s:1:"a";i:36;s:1:"b";s:27:"force_delete_any_entrenador";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:36;a:4:{s:1:"a";i:37;s:1:"b";s:13:"view_gimnasio";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:37;a:4:{s:1:"a";i:38;s:1:"b";s:17:"view_any_gimnasio";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:38;a:4:{s:1:"a";i:39;s:1:"b";s:15:"create_gimnasio";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:39;a:4:{s:1:"a";i:40;s:1:"b";s:15:"update_gimnasio";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:40;a:4:{s:1:"a";i:41;s:1:"b";s:16:"restore_gimnasio";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:41;a:4:{s:1:"a";i:42;s:1:"b";s:20:"restore_any_gimnasio";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:42;a:4:{s:1:"a";i:43;s:1:"b";s:18:"replicate_gimnasio";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:43;a:4:{s:1:"a";i:44;s:1:"b";s:16:"reorder_gimnasio";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:44;a:4:{s:1:"a";i:45;s:1:"b";s:15:"delete_gimnasio";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:45;a:4:{s:1:"a";i:46;s:1:"b";s:19:"delete_any_gimnasio";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:46;a:4:{s:1:"a";i:47;s:1:"b";s:21:"force_delete_gimnasio";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:47;a:4:{s:1:"a";i:48;s:1:"b";s:25:"force_delete_any_gimnasio";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:2;}}i:48;a:4:{s:1:"a";i:49;s:1:"b";s:15:"view_permission";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:49;a:4:{s:1:"a";i:50;s:1:"b";s:19:"view_any_permission";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:50;a:4:{s:1:"a";i:51;s:1:"b";s:17:"create_permission";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:51;a:4:{s:1:"a";i:52;s:1:"b";s:17:"update_permission";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:52;a:4:{s:1:"a";i:53;s:1:"b";s:18:"restore_permission";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:53;a:4:{s:1:"a";i:54;s:1:"b";s:22:"restore_any_permission";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:54;a:4:{s:1:"a";i:55;s:1:"b";s:20:"replicate_permission";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:55;a:4:{s:1:"a";i:56;s:1:"b";s:18:"reorder_permission";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:56;a:4:{s:1:"a";i:57;s:1:"b";s:17:"delete_permission";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:57;a:4:{s:1:"a";i:58;s:1:"b";s:21:"delete_any_permission";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:58;a:4:{s:1:"a";i:59;s:1:"b";s:23:"force_delete_permission";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}i:59;a:4:{s:1:"a";i:60;s:1:"b";s:27:"force_delete_any_permission";s:1:"c";s:3:"web";s:1:"r";a:1:{i:0;i:1;}}}s:5:"roles";a:3:{i:0;a:3:{s:1:"a";i:1;s:1:"b";s:11:"super_admin";s:1:"c";s:3:"web";}i:1;a:3:{s:1:"a";i:3;s:1:"b";s:10:"entrenador";s:1:"c";s:3:"web";}i:2;a:3:{s:1:"a";i:2;s:1:"b";s:5:"admin";s:1:"c";s:3:"web";}}}', 1758222287);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;

-- Dumping structure for table power.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.cache_locks: ~0 rows (approximately)
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;

-- Dumping structure for table power.dias_entrenamiento
CREATE TABLE IF NOT EXISTS `dias_entrenamiento` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `semana_rutina_id` bigint(20) unsigned NOT NULL,
  `dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `completado_por_atleta` tinyint(1) DEFAULT '0',
  `fecha_completado` timestamp NULL DEFAULT NULL,
  `porcentaje_completado` tinyint(3) unsigned DEFAULT '0',
  `notas_atleta` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dias_entrenamiento_semana_rutina_id_foreign` (`semana_rutina_id`),
  CONSTRAINT `dias_entrenamiento_semana_rutina_id_foreign` FOREIGN KEY (`semana_rutina_id`) REFERENCES `semanas_rutina` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.dias_entrenamiento: ~0 rows (approximately)
/*!40000 ALTER TABLE `dias_entrenamiento` DISABLE KEYS */;
INSERT INTO `dias_entrenamiento` (`id`, `semana_rutina_id`, `dia_semana`, `completado_por_atleta`, `fecha_completado`, `porcentaje_completado`, `notas_atleta`, `created_at`, `updated_at`) VALUES
	(31, 18, 'Lunes', 0, NULL, 0, NULL, '2025-09-18 15:56:12', '2025-09-18 15:56:12'),
	(32, 18, 'Miércoles', 0, NULL, 0, NULL, '2025-09-18 15:56:12', '2025-09-18 15:56:12'),
	(33, 18, 'Viernes', 0, NULL, 0, NULL, '2025-09-18 15:56:12', '2025-09-18 15:56:12');
/*!40000 ALTER TABLE `dias_entrenamiento` ENABLE KEYS */;

-- Dumping structure for table power.ejercicios
CREATE TABLE IF NOT EXISTS `ejercicios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `tipo` enum('Fuerza','Cardio','Flexibilidad','Potencia') COLLATE utf8mb4_unicode_ci NOT NULL,
  `grupo_muscular` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.ejercicios: ~0 rows (approximately)
/*!40000 ALTER TABLE `ejercicios` DISABLE KEYS */;
INSERT INTO `ejercicios` (`id`, `nombre`, `descripcion`, `tipo`, `grupo_muscular`, `created_at`, `updated_at`) VALUES
	(1, 'Press Militar', NULL, 'Fuerza', NULL, '2025-09-16 18:15:22', '2025-09-16 18:15:22'),
	(2, 'Remo con Barra', NULL, 'Fuerza', NULL, '2025-09-16 18:15:29', '2025-09-16 18:15:29');
/*!40000 ALTER TABLE `ejercicios` ENABLE KEYS */;

-- Dumping structure for table power.ejercicios_completados
CREATE TABLE IF NOT EXISTS `ejercicios_completados` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ejercicio_dia_id` bigint(20) unsigned NOT NULL,
  `completado` tinyint(1) DEFAULT '0',
  `fecha_completado` timestamp NULL DEFAULT NULL,
  `porcentaje_series_completadas` tinyint(3) unsigned DEFAULT '0',
  `notas_atleta` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ejercicios_completados_ejercicio_dia_id_foreign` (`ejercicio_dia_id`),
  CONSTRAINT `ejercicios_completados_ejercicio_dia_id_foreign` FOREIGN KEY (`ejercicio_dia_id`) REFERENCES `ejercicios_dia` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.ejercicios_completados: ~0 rows (approximately)
/*!40000 ALTER TABLE `ejercicios_completados` DISABLE KEYS */;
INSERT INTO `ejercicios_completados` (`id`, `ejercicio_dia_id`, `completado`, `fecha_completado`, `porcentaje_series_completadas`, `notas_atleta`, `created_at`, `updated_at`) VALUES
	(10, 19, 1, '2025-09-18 15:59:16', 0, NULL, '2025-09-18 15:59:16', '2025-09-18 15:59:16'),
	(11, 20, 1, '2025-09-18 15:59:48', 0, NULL, '2025-09-18 15:59:48', '2025-09-18 15:59:48'),
	(12, 21, 0, '2025-09-18 16:00:31', 0, NULL, '2025-09-18 16:00:27', '2025-09-18 16:00:31');
/*!40000 ALTER TABLE `ejercicios_completados` ENABLE KEYS */;

-- Dumping structure for table power.ejercicios_dia
CREATE TABLE IF NOT EXISTS `ejercicios_dia` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dia_entrenamiento_id` bigint(20) unsigned NOT NULL,
  `ejercicio_id` bigint(20) unsigned NOT NULL,
  `orden` tinyint(4) NOT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ejercicios_dia_dia_entrenamiento_id_foreign` (`dia_entrenamiento_id`),
  KEY `ejercicios_dia_ejercicio_id_foreign` (`ejercicio_id`),
  CONSTRAINT `ejercicios_dia_dia_entrenamiento_id_foreign` FOREIGN KEY (`dia_entrenamiento_id`) REFERENCES `dias_entrenamiento` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ejercicios_dia_ejercicio_id_foreign` FOREIGN KEY (`ejercicio_id`) REFERENCES `ejercicios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.ejercicios_dia: ~0 rows (approximately)
/*!40000 ALTER TABLE `ejercicios_dia` DISABLE KEYS */;
INSERT INTO `ejercicios_dia` (`id`, `dia_entrenamiento_id`, `ejercicio_id`, `orden`, `notas`, `created_at`, `updated_at`) VALUES
	(19, 31, 1, 1, NULL, '2025-09-18 15:56:12', '2025-09-18 15:56:12'),
	(20, 32, 2, 1, NULL, '2025-09-18 15:56:12', '2025-09-18 15:56:12'),
	(21, 33, 1, 1, NULL, '2025-09-18 15:56:12', '2025-09-18 15:56:12');
/*!40000 ALTER TABLE `ejercicios_dia` ENABLE KEYS */;

-- Dumping structure for table power.entrenadors
CREATE TABLE IF NOT EXISTS `entrenadors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `gimnasio_id` bigint(20) unsigned NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'entrenadores_fotos/default_photo.png',
  `especialidad` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `experiencia` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entrenadors_user_id_foreign` (`user_id`),
  KEY `entrenadors_gimnasio_id_foreign` (`gimnasio_id`),
  CONSTRAINT `entrenadors_gimnasio_id_foreign` FOREIGN KEY (`gimnasio_id`) REFERENCES `gimnasios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `entrenadors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.entrenadors: ~4 rows (approximately)
/*!40000 ALTER TABLE `entrenadors` DISABLE KEYS */;
INSERT INTO `entrenadors` (`id`, `user_id`, `gimnasio_id`, `foto`, `especialidad`, `experiencia`, `created_at`, `updated_at`) VALUES
	(4, 7, 4, 'gym_logos/Captura de pantalla 2025-08-14 095219.png', 'm', 'poca', '2025-08-14 15:44:54', '2025-08-14 15:44:54'),
	(5, 8, 4, 'gym_logos/Captura de pantalla 2025-08-14 095219.png', 'Musica de sexo en el oxo en exceso', 'Poca', '2025-08-19 15:24:31', '2025-08-19 15:24:31'),
	(6, 11, 3, 'gym_logos/Captura de pantalla 2025-08-22 123313.png', 'Musica de sexo en el oxo en exceso', 'Mucha', '2025-08-22 19:46:25', '2025-08-22 19:46:25'),
	(7, 12, 3, 'gym_logos/Captura de pantalla 2025-09-05 084815.png', 'Musica de sexo en el oxo en exceso', '2 años', '2025-09-05 15:16:03', '2025-09-05 15:16:03');
/*!40000 ALTER TABLE `entrenadors` ENABLE KEYS */;

-- Dumping structure for table power.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.failed_jobs: ~0 rows (approximately)
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;

-- Dumping structure for table power.gimnasios
CREATE TABLE IF NOT EXISTS `gimnasios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ubicacion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'gym_logos/default_logo.png',
  `celular` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gimnasios_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.gimnasios: ~2 rows (approximately)
/*!40000 ALTER TABLE `gimnasios` DISABLE KEYS */;
INSERT INTO `gimnasios` (`id`, `nombre`, `ubicacion`, `logo`, `celular`, `created_at`, `updated_at`) VALUES
	(3, 'Tkforce', 'Av. Capital Ustariz', 'gym_logos/Captura de pantalla 2025-08-10 083156.png', '6543210', '2025-08-10 19:19:02', '2025-08-10 19:19:02'),
	(4, 'Fittnes  Club', 'Av. America', 'gym_logos/powercheck_logo.png', '76543210', '2025-08-10 19:26:22', '2025-08-10 19:26:22');
/*!40000 ALTER TABLE `gimnasios` ENABLE KEYS */;

-- Dumping structure for table power.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.jobs: ~0 rows (approximately)
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;

-- Dumping structure for table power.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.job_batches: ~0 rows (approximately)
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;

-- Dumping structure for table power.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.migrations: ~10 rows (approximately)
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_08_10_115004_create_gimnasios_table', 1),
	(5, '2025_08_10_115005_create_entrenadors_table', 1),
	(6, '2025_08_11_135622_create_permission_tables', 2),
	(7, '2025_08_11_151357_create_atletas_table', 3),
	(8, '2025_08_25_173348_create_ejercicios_table', 4),
	(9, '2025_08_27_224035_create_rutinas_table', 5),
	(10, '2025_09_04_142503_create_rutina_sets_table', 5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dumping structure for table power.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.model_has_permissions: ~0 rows (approximately)
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;

-- Dumping structure for table power.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.model_has_roles: ~13 rows (approximately)
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\User', 1),
	(2, 'App\\Models\\User', 2),
	(3, 'App\\Models\\User', 3),
	(3, 'App\\Models\\User', 4),
	(2, 'App\\Models\\User', 5),
	(3, 'App\\Models\\User', 6),
	(3, 'App\\Models\\User', 7),
	(3, 'App\\Models\\User', 8),
	(4, 'App\\Models\\User', 9),
	(4, 'App\\Models\\User', 10),
	(3, 'App\\Models\\User', 11),
	(3, 'App\\Models\\User', 12),
	(4, 'App\\Models\\User', 13);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;

-- Dumping structure for table power.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.password_reset_tokens: ~0 rows (approximately)
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;

-- Dumping structure for table power.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.permissions: ~60 rows (approximately)
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'view_role', 'web', '2025-08-11 15:26:31', '2025-08-11 15:26:31'),
	(2, 'view_any_role', 'web', '2025-08-11 15:26:31', '2025-08-11 15:26:31'),
	(3, 'create_role', 'web', '2025-08-11 15:26:31', '2025-08-11 15:26:31'),
	(4, 'update_role', 'web', '2025-08-11 15:26:31', '2025-08-11 15:26:31'),
	(5, 'restore_role', 'web', '2025-08-11 15:26:31', '2025-08-11 15:26:31'),
	(6, 'restore_any_role', 'web', '2025-08-11 15:26:31', '2025-08-11 15:26:31'),
	(7, 'replicate_role', 'web', '2025-08-11 15:26:31', '2025-08-11 15:26:31'),
	(8, 'reorder_role', 'web', '2025-08-11 15:26:31', '2025-08-11 15:26:31'),
	(9, 'delete_role', 'web', '2025-08-11 15:26:31', '2025-08-11 15:26:31'),
	(10, 'delete_any_role', 'web', '2025-08-11 15:26:31', '2025-08-11 15:26:31'),
	(11, 'force_delete_role', 'web', '2025-08-11 15:26:31', '2025-08-11 15:26:31'),
	(12, 'force_delete_any_role', 'web', '2025-08-11 15:26:31', '2025-08-11 15:26:31'),
	(13, 'view_atleta', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(14, 'view_any_atleta', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(15, 'create_atleta', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(16, 'update_atleta', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(17, 'restore_atleta', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(18, 'restore_any_atleta', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(19, 'replicate_atleta', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(20, 'reorder_atleta', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(21, 'delete_atleta', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(22, 'delete_any_atleta', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(23, 'force_delete_atleta', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(24, 'force_delete_any_atleta', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(25, 'view_entrenador', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(26, 'view_any_entrenador', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(27, 'create_entrenador', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(28, 'update_entrenador', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(29, 'restore_entrenador', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(30, 'restore_any_entrenador', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(31, 'replicate_entrenador', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(32, 'reorder_entrenador', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(33, 'delete_entrenador', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(34, 'delete_any_entrenador', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(35, 'force_delete_entrenador', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(36, 'force_delete_any_entrenador', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(37, 'view_gimnasio', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(38, 'view_any_gimnasio', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(39, 'create_gimnasio', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(40, 'update_gimnasio', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(41, 'restore_gimnasio', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(42, 'restore_any_gimnasio', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(43, 'replicate_gimnasio', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(44, 'reorder_gimnasio', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(45, 'delete_gimnasio', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(46, 'delete_any_gimnasio', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(47, 'force_delete_gimnasio', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(48, 'force_delete_any_gimnasio', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(49, 'view_permission', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(50, 'view_any_permission', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(51, 'create_permission', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(52, 'update_permission', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(53, 'restore_permission', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(54, 'restore_any_permission', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(55, 'replicate_permission', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(56, 'reorder_permission', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(57, 'delete_permission', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(58, 'delete_any_permission', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(59, 'force_delete_permission', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52'),
	(60, 'force_delete_any_permission', 'web', '2025-08-11 15:26:52', '2025-08-11 15:26:52');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;

-- Dumping structure for table power.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.roles: ~4 rows (approximately)
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'super_admin', 'web', '2025-08-11 15:26:31', '2025-08-11 15:26:31'),
	(2, 'admin', 'web', '2025-08-11 15:31:20', '2025-08-11 15:31:20'),
	(3, 'entrenador', 'web', '2025-08-11 15:31:20', '2025-08-11 15:31:20'),
	(4, 'atleta', 'web', '2025-08-11 15:31:20', '2025-08-11 15:31:20');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Dumping structure for table power.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.role_has_permissions: ~96 rows (approximately)
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(1, 1),
	(2, 1),
	(3, 1),
	(4, 1),
	(5, 1),
	(6, 1),
	(7, 1),
	(8, 1),
	(9, 1),
	(10, 1),
	(11, 1),
	(12, 1),
	(13, 1),
	(14, 1),
	(15, 1),
	(16, 1),
	(17, 1),
	(18, 1),
	(19, 1),
	(20, 1),
	(21, 1),
	(22, 1),
	(23, 1),
	(24, 1),
	(25, 1),
	(26, 1),
	(27, 1),
	(28, 1),
	(29, 1),
	(30, 1),
	(31, 1),
	(32, 1),
	(33, 1),
	(34, 1),
	(35, 1),
	(36, 1),
	(37, 1),
	(38, 1),
	(39, 1),
	(40, 1),
	(41, 1),
	(42, 1),
	(43, 1),
	(44, 1),
	(45, 1),
	(46, 1),
	(47, 1),
	(48, 1),
	(49, 1),
	(50, 1),
	(51, 1),
	(52, 1),
	(53, 1),
	(54, 1),
	(55, 1),
	(56, 1),
	(57, 1),
	(58, 1),
	(59, 1),
	(60, 1),
	(25, 2),
	(26, 2),
	(27, 2),
	(28, 2),
	(29, 2),
	(30, 2),
	(31, 2),
	(32, 2),
	(33, 2),
	(34, 2),
	(35, 2),
	(36, 2),
	(37, 2),
	(38, 2),
	(39, 2),
	(40, 2),
	(41, 2),
	(42, 2),
	(43, 2),
	(44, 2),
	(45, 2),
	(46, 2),
	(47, 2),
	(48, 2),
	(13, 3),
	(14, 3),
	(15, 3),
	(16, 3),
	(17, 3),
	(18, 3),
	(19, 3),
	(20, 3),
	(21, 3),
	(22, 3),
	(23, 3),
	(24, 3);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;

-- Dumping structure for table power.rutinas
CREATE TABLE IF NOT EXISTS `rutinas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entrenador_id` bigint(20) unsigned NOT NULL,
  `atleta_id` bigint(20) unsigned DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `objetivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dias_por_semana` tinyint(4) NOT NULL,
  `duracion_semanas` tinyint(4) NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semana_actual` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `dia_actual` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `ultimo_dia_completado_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rutinas_entrenador_id_foreign` (`entrenador_id`),
  KEY `rutinas_atleta_id_foreign` (`atleta_id`),
  CONSTRAINT `rutinas_atleta_id_foreign` FOREIGN KEY (`atleta_id`) REFERENCES `atletas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rutinas_entrenador_id_foreign` FOREIGN KEY (`entrenador_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.rutinas: ~0 rows (approximately)
/*!40000 ALTER TABLE `rutinas` DISABLE KEYS */;
INSERT INTO `rutinas` (`id`, `entrenador_id`, `atleta_id`, `nombre`, `objetivo`, `dias_por_semana`, `duracion_semanas`, `version`, `semana_actual`, `dia_actual`, `ultimo_dia_completado_at`, `created_at`, `updated_at`) VALUES
	(12, 11, 3, 'Volumen', 'Masa muscular', 3, 1, '1.0', 1, 1, NULL, '2025-09-18 15:56:12', '2025-09-18 15:56:12');
/*!40000 ALTER TABLE `rutinas` ENABLE KEYS */;

-- Dumping structure for table power.rutina_sets
CREATE TABLE IF NOT EXISTS `rutina_sets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rutina_id` bigint(20) unsigned NOT NULL,
  `ejercicio_id` bigint(20) unsigned NOT NULL,
  `semana` tinyint(3) unsigned NOT NULL,
  `dia_semana` tinyint(3) unsigned NOT NULL,
  `orden` smallint(5) unsigned NOT NULL DEFAULT '1',
  `series` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `repeticiones` smallint(5) unsigned NOT NULL DEFAULT '0',
  `peso_kg` decimal(6,2) DEFAULT NULL,
  `rpe` tinyint(3) unsigned DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rutina_sets_ejercicio_id_foreign` (`ejercicio_id`),
  KEY `rutina_sets_rutina_id_semana_dia_semana_index` (`rutina_id`,`semana`,`dia_semana`),
  CONSTRAINT `rutina_sets_ejercicio_id_foreign` FOREIGN KEY (`ejercicio_id`) REFERENCES `ejercicios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rutina_sets_rutina_id_foreign` FOREIGN KEY (`rutina_id`) REFERENCES `rutinas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.rutina_sets: ~0 rows (approximately)
/*!40000 ALTER TABLE `rutina_sets` DISABLE KEYS */;
/*!40000 ALTER TABLE `rutina_sets` ENABLE KEYS */;

-- Dumping structure for table power.semanas_rutina
CREATE TABLE IF NOT EXISTS `semanas_rutina` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rutina_id` bigint(20) unsigned NOT NULL,
  `numero_semana` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `semanas_rutina_rutina_id_foreign` (`rutina_id`),
  CONSTRAINT `semanas_rutina_rutina_id_foreign` FOREIGN KEY (`rutina_id`) REFERENCES `rutinas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.semanas_rutina: ~0 rows (approximately)
/*!40000 ALTER TABLE `semanas_rutina` DISABLE KEYS */;
INSERT INTO `semanas_rutina` (`id`, `rutina_id`, `numero_semana`, `created_at`, `updated_at`) VALUES
	(18, 12, 1, '2025-09-18 15:56:12', '2025-09-18 15:56:12');
/*!40000 ALTER TABLE `semanas_rutina` ENABLE KEYS */;

-- Dumping structure for table power.series_ejercicio
CREATE TABLE IF NOT EXISTS `series_ejercicio` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ejercicio_dia_id` bigint(20) unsigned NOT NULL,
  `numero_serie` tinyint(4) NOT NULL,
  `repeticiones_objetivo` tinyint(4) NOT NULL,
  `peso_objetivo` decimal(5,2) DEFAULT NULL,
  `descanso_segundos` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `series_ejercicio_ejercicio_dia_id_foreign` (`ejercicio_dia_id`),
  CONSTRAINT `series_ejercicio_ejercicio_dia_id_foreign` FOREIGN KEY (`ejercicio_dia_id`) REFERENCES `ejercicios_dia` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.series_ejercicio: ~0 rows (approximately)
/*!40000 ALTER TABLE `series_ejercicio` DISABLE KEYS */;
INSERT INTO `series_ejercicio` (`id`, `ejercicio_dia_id`, `numero_serie`, `repeticiones_objetivo`, `peso_objetivo`, `descanso_segundos`, `created_at`, `updated_at`) VALUES
	(27, 19, 1, 12, 50.00, 60, '2025-09-18 15:56:12', '2025-09-18 15:56:12'),
	(28, 19, 2, 12, 60.00, 60, '2025-09-18 15:56:12', '2025-09-18 15:56:12'),
	(29, 19, 3, 12, 70.00, 60, '2025-09-18 15:56:12', '2025-09-18 15:56:12'),
	(30, 20, 1, 12, 80.00, 60, '2025-09-18 15:56:12', '2025-09-18 15:56:12'),
	(31, 20, 2, 12, 100.00, 60, '2025-09-18 15:56:12', '2025-09-18 15:56:12'),
	(32, 21, 1, 12, 80.00, 60, '2025-09-18 15:56:12', '2025-09-18 15:56:12'),
	(33, 21, 2, 12, 100.00, 60, '2025-09-18 15:56:12', '2025-09-18 15:56:12');
/*!40000 ALTER TABLE `series_ejercicio` ENABLE KEYS */;

-- Dumping structure for table power.series_realizadas
CREATE TABLE IF NOT EXISTS `series_realizadas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `serie_ejercicio_id` bigint(20) unsigned NOT NULL,
  `ejercicio_completado_id` bigint(20) unsigned DEFAULT NULL,
  `repeticiones_realizadas` tinyint(4) NOT NULL,
  `peso_realizado` decimal(5,2) DEFAULT NULL,
  `completada` tinyint(1) DEFAULT '0',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `fecha_realizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `series_realizadas_serie_ejercicio_id_foreign` (`serie_ejercicio_id`),
  KEY `series_realizadas_ejercicio_completado_id_foreign` (`ejercicio_completado_id`),
  CONSTRAINT `series_realizadas_ejercicio_completado_id_foreign` FOREIGN KEY (`ejercicio_completado_id`) REFERENCES `ejercicios_completados` (`id`) ON DELETE CASCADE,
  CONSTRAINT `series_realizadas_serie_ejercicio_id_foreign` FOREIGN KEY (`serie_ejercicio_id`) REFERENCES `series_ejercicio` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.series_realizadas: ~0 rows (approximately)
/*!40000 ALTER TABLE `series_realizadas` DISABLE KEYS */;
INSERT INTO `series_realizadas` (`id`, `serie_ejercicio_id`, `ejercicio_completado_id`, `repeticiones_realizadas`, `peso_realizado`, `completada`, `notas`, `fecha_realizacion`, `created_at`, `updated_at`) VALUES
	(12, 27, 10, 12, 50.00, 1, NULL, '2025-09-18 15:59:28', '2025-09-18 15:59:28', '2025-09-18 15:59:28'),
	(13, 28, 10, 10, 55.00, 1, NULL, '2025-09-18 15:59:36', '2025-09-18 15:59:36', '2025-09-18 15:59:36'),
	(14, 29, 10, 12, 70.00, 1, NULL, '2025-09-18 15:59:42', '2025-09-18 15:59:42', '2025-09-18 15:59:42'),
	(15, 30, 11, 11, 70.00, 1, NULL, '2025-09-18 16:00:19', '2025-09-18 16:00:19', '2025-09-18 16:00:19'),
	(16, 31, 11, 10, 90.00, 1, NULL, '2025-09-18 16:00:24', '2025-09-18 16:00:24', '2025-09-18 16:00:24');
/*!40000 ALTER TABLE `series_realizadas` ENABLE KEYS */;

-- Dumping structure for table power.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.sessions: ~0 rows (approximately)
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('cAlA8zkgbD5ojfoGX4acEnTGllTzguACpS4hAF5P', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiRmZ0ZlVvT0FNSDh3TUJ0QWJlVGc0T2JESG5tVVdoRGJEUFI2U2k3bSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wb3dlckNoZWNrL3J1dGluYXMvMS9lZGl0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6ODtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJGNqOG5TaENyM3BJb3prWXVNWUxvcC5rY1pEaS93NzRla08xQnl6MUQubUxFcUJqdUhTT2tPIjtzOjg6ImZpbGFtZW50IjthOjA6e319', 1757610990);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;

-- Dumping structure for table power.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celular` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table power.users: ~9 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `apellidos`, `celular`, `email`, `email_verified_at`, `password`, `profile_photo_path`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'admin', NULL, NULL, 'admin@example.com', NULL, '$2y$12$6O1kZPBjhYatDvBn1n5hzuQo0b9XvyBk/Ur9N.xKN0AzrztASFlqW', NULL, NULL, '2025-08-10 12:24:09', '2025-09-16 14:47:44'),
	(5, 'ABP', NULL, NULL, 'abp@gmail.com', NULL, '$2y$12$6O1kZPBjhYatDvBn1n5hzuQo0b9XvyBk/Ur9N.xKN0AzrztASFlqW', NULL, NULL, '2025-08-14 15:35:32', '2025-08-14 15:35:32'),
	(7, 'Sebastian', 'Copa', '785432', 'sebits@gmail.com', NULL, '$2y$12$6O1kZPBjhYatDvBn1n5hzuQo0b9XvyBk/Ur9N.xKN0AzrztASFlqW', NULL, NULL, '2025-08-14 15:44:54', '2025-08-14 15:44:54'),
	(8, 'Nacho', 'Lopez', '7678683', 'nachin@gmail.com', NULL, '$2y$12$6O1kZPBjhYatDvBn1n5hzuQo0b9XvyBk/Ur9N.xKN0AzrztASFlqW', NULL, 'DS3RPUknHw2NtwOia821Fhe2I9rNoQDGWfGHY2NzdW6QZYE0hA1nOQ774GVg', '2025-08-19 15:24:31', '2025-08-19 15:24:31'),
	(9, 'Benito', 'Camelo', '7869234', 'benitocamelo@gmail.com', NULL, '$2y$12$6O1kZPBjhYatDvBn1n5hzuQo0b9XvyBk/Ur9N.xKN0AzrztASFlqW', NULL, NULL, '2025-08-21 16:20:03', '2025-08-21 16:20:03'),
	(10, 'Odin', 'Chopper', '65471925', 'odincho@gmail.com', NULL, '$2y$12$6O1kZPBjhYatDvBn1n5hzuQo0b9XvyBk/Ur9N.xKN0AzrztASFlqW', NULL, NULL, '2025-08-21 16:38:54', '2025-08-21 16:38:54'),
	(11, 'Jorge', 'Torrez', '65371931', 'jorgetorrez@gmal.com', NULL, '$2y$12$6O1kZPBjhYatDvBn1n5hzuQo0b9XvyBk/Ur9N.xKN0AzrztASFlqW', NULL, NULL, '2025-08-22 19:46:25', '2025-08-22 19:46:25'),
	(12, 'mateo', 'Mamani', '65432178', 'mama@gmail.com', NULL, '$2y$12$6O1kZPBjhYatDvBn1n5hzuQo0b9XvyBk/Ur9N.xKN0AzrztASFlqW', NULL, NULL, '2025-09-05 15:16:03', '2025-09-05 15:16:03'),
	(13, 'fernando', 'cruz banegas', '68774551', 'nano@gmail.com', NULL, '$2y$12$6O1kZPBjhYatDvBn1n5hzuQo0b9XvyBk/Ur9N.xKN0AzrztASFlqW', NULL, NULL, '2025-09-16 15:30:53', '2025-09-16 15:30:53');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
