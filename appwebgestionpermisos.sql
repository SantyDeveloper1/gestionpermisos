-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-01-2026 a las 16:38:29
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `appwebgestionpermisos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaturas`
--

CREATE TABLE `asignaturas` (
  `idAsignatura` char(13) NOT NULL,
  `codigo_asignatura` varchar(20) NOT NULL,
  `nom_asignatura` varchar(120) NOT NULL,
  `creditos` int(11) NOT NULL,
  `horas_teoria` int(11) NOT NULL DEFAULT 0,
  `horas_practica` int(11) NOT NULL DEFAULT 0,
  `IdCiclo` char(13) NOT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asignaturas`
--

INSERT INTO `asignaturas` (`idAsignatura`, `codigo_asignatura`, `nom_asignatura`, `creditos`, `horas_teoria`, `horas_practica`, `IdCiclo`, `tipo`, `estado`, `created_at`, `updated_at`) VALUES
('695f27345dd7e', 'AIS11', 'Introducción a la informática y sistemas', 3, 1, 4, '695f26cee336f', 'ESPECIALIDAD', 'Activo', '2026-01-08 08:40:36', '2026-01-08 08:40:36'),
('695f3b895e299', 'AGG1', 'Lengua castellana y argumentación', 3, 2, 2, '695f26cee336f', 'GENERAL', 'Activo', '2026-01-08 10:07:21', '2026-01-08 10:07:21'),
('695f3baa24505', 'AGG2', 'Matemática básica', 4, 3, 2, '695f26cee336f', 'GENERAL', 'Activo', '2026-01-08 10:07:54', '2026-01-08 10:07:54'),
('695f3bd01347c', 'AGG3', 'Historia del Perú y del mundo', 2, 1, 2, '695f26cee336f', 'GENERAL', 'Activo', '2026-01-08 10:08:32', '2026-01-08 10:08:32'),
('695f3bf136d70', 'AGG4', 'Geografía y recursos naturales', 2, 1, 2, '695f26cee336f', 'GENERAL', 'Activo', '2026-01-08 10:09:05', '2026-01-08 10:09:05'),
('695f3c13407a7', 'AGG6', 'Filosofía y ética', 2, 1, 2, '695f26cee336f', 'GENERAL', 'Activo', '2026-01-08 10:09:39', '2026-01-08 10:09:39'),
('695f3c337d560', 'AGG7', 'Psicología', 2, 1, 2, '695f26cee336f', 'GENERAL', 'Activo', '2026-01-08 10:10:11', '2026-01-08 10:10:11'),
('695f3c5466d1b', 'AGG19', 'Dibujo en ingeniería I', 3, 2, 2, '695f26cee336f', 'GENERAL', 'Activo', '2026-01-08 10:10:44', '2026-01-08 10:10:44'),
('695f3c7758de4', 'AIS21', 'Análisis y diseño de sistemas de información', 4, 3, 2, '695f26da1e29c', 'ESPECIALIDAD', 'Activo', '2026-01-08 10:11:19', '2026-01-08 10:11:19'),
('696fbea23150c', 'AIS22', 'Fundamentos de programación', 4, 3, 2, '695f26da1e29c', 'ESPECIALIDAD', 'Activo', '2026-01-20 22:42:58', '2026-01-20 22:42:58'),
('696fbf5101b90', 'AIS23', 'Liderazgo y habilidades sociales', 3, 2, 4, '695f26da1e29c', 'ESPECIALIDAD', 'Activo', '2026-01-20 22:45:53', '2026-01-20 22:45:53'),
('696fbfd538bd6', 'AGG5', 'Ecología y desarrollo sostenible', 2, 4, 2, '695f26da1e29c', 'GENERAL', 'Activo', '2026-01-20 22:48:05', '2026-01-20 22:48:05'),
('696fc001a2067', 'AGG20', 'Física I', 4, 1, 2, '695f26da1e29c', 'GENERAL', 'Activo', '2026-01-20 22:48:49', '2026-01-20 22:48:49'),
('696fc02319aa5', 'AGG23', 'Cálculo diferencial', 4, 2, 2, '695f26da1e29c', 'GENERAL', 'Activo', '2026-01-20 22:49:23', '2026-01-20 22:49:23'),
('696fc0798e6b1', 'AIS31', 'Matemática discreta I', 4, 3, 2, '695f26ec9d092', 'ESPECIFICO', 'Activo', '2026-01-20 22:50:49', '2026-01-20 22:50:49'),
('696fc09e86996', 'AIS32', 'Circuitos electrónicos', 4, 3, 2, '695f26ec9d092', 'ESPECIFICO', 'Activo', '2026-01-20 22:51:26', '2026-01-20 22:51:26'),
('696fc0c17c028', 'AIS33', 'Estructura de datos', 4, 2, 4, '695f26ec9d092', 'ESPECIALIDAD', 'Activo', '2026-01-20 22:52:01', '2026-01-20 22:52:01'),
('696fc0e1d34ec', 'AIS34', 'Base de datos I', 4, 2, 4, '695f26ec9d092', 'ESPECIALIDAD', 'Activo', '2026-01-20 22:52:33', '2026-01-20 22:52:33'),
('696fc105690b4', 'AIS35', 'Formulación de proyectos informáticos', 3, 2, 2, '695f26ec9d092', 'ESPECIALIDAD', 'Activo', '2026-01-20 22:53:09', '2026-01-20 22:54:04'),
('696fc12f798ed', 'AGG22', 'Inglés', 3, 2, 2, '695f26ec9d092', 'GENERAL', 'Activo', '2026-01-20 22:53:51', '2026-01-20 22:53:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciclos`
--

CREATE TABLE `ciclos` (
  `IdCiclo` char(13) NOT NULL,
  `NombreCiclo` varchar(100) NOT NULL,
  `NumeroCiclo` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ciclos`
--

INSERT INTO `ciclos` (`IdCiclo`, `NombreCiclo`, `NumeroCiclo`, `created_at`, `updated_at`) VALUES
('695f26cee336f', 'PRIMER SEMESTRE', 'I', '2026-01-08 08:38:54', '2026-01-08 08:38:54'),
('695f26da1e29c', 'SEGUNDO SEMESTRE', 'II', '2026-01-08 08:39:06', '2026-01-08 08:39:06'),
('695f26ec9d092', 'TERCER SEMESTRE', 'III', '2026-01-08 08:39:24', '2026-01-08 08:39:24'),
('696fbec31c38a', 'CUARTO SEMESTRE', 'IV', '2026-01-20 22:43:31', '2026-01-20 22:43:31'),
('696fbed3464c8', 'QUINTO SEMESTRE', 'V', '2026-01-20 22:43:47', '2026-01-20 22:43:47'),
('696fbeec140d8', 'SEXTO SEMESTRE', 'VI', '2026-01-20 22:44:12', '2026-01-20 22:44:12'),
('696fbef61ccb8', 'SEPTIMO SEMESTRE', 'VII', '2026-01-20 22:44:22', '2026-01-20 22:44:22'),
('696fbf0149355', 'OCTAVO SEMESTRE', 'VIII', '2026-01-20 22:44:33', '2026-01-20 22:44:33'),
('696fbf10d1597', 'NOVENO SEMESTRE', 'IX', '2026-01-20 22:44:48', '2026-01-20 22:44:48'),
('696fbf19a82b4', 'DECIMO SEMESTRE', 'X', '2026-01-20 22:44:57', '2026-01-20 22:44:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes`
--

CREATE TABLE `docentes` (
  `idDocente` char(13) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `codigo_unamba` varchar(15) DEFAULT NULL,
  `grado_id` char(13) NOT NULL,
  `tipo_contrato_id` char(13) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `docentes`
--

INSERT INTO `docentes` (`idDocente`, `user_id`, `codigo_unamba`, `grado_id`, `tipo_contrato_id`, `estado`, `created_at`, `updated_at`) VALUES
('695f139dd66c2', 2, NULL, '695f12df6fdc9', '695f12f53d45f', 1, '2026-01-08 07:17:01', '2026-01-08 07:17:01'),
('695f43144a8a6', 3, '201065', '695f12df6fdc9', '695f12ebeb52f', 1, '2026-01-08 10:39:32', '2026-01-08 10:39:32'),
('696fbbca273fd', 5, '213421', '6961e18b08a3e', '695f12ebeb52f', 1, '2026-01-20 22:30:50', '2026-01-20 22:30:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evidencia_recuperacion`
--

CREATE TABLE `evidencia_recuperacion` (
  `id_evidencia` char(13) NOT NULL,
  `id_sesion` char(13) NOT NULL,
  `tipo_evidencia` enum('ACTA','ASISTENCIA','CAPTURA','OTRO') NOT NULL COMMENT 'Tipo de sustento de recuperación',
  `archivo` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `evidencia_recuperacion`
--

INSERT INTO `evidencia_recuperacion` (`id_evidencia`, `id_sesion`, `tipo_evidencia`, `archivo`, `descripcion`, `fecha_subida`) VALUES
('EVI-2026-0001', 'SES-2026-0008', 'ASISTENCIA', 'storage/evidencias/EVI-2026-0001_1768577519.pdf', NULL, '2026-01-16 15:31:59'),
('EVI-2026-0002', 'SES-2026-0009', 'ASISTENCIA', 'storage/evidencias/EVI-2026-0002_1768843708.pdf', NULL, '2026-01-19 17:28:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados_academicos`
--

CREATE TABLE `grados_academicos` (
  `idGrados_academicos` char(13) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `grados_academicos`
--

INSERT INTO `grados_academicos` (`idGrados_academicos`, `nombre`, `created_at`, `updated_at`) VALUES
('695f12df6fdc9', 'Ingeniero', '2026-01-08 07:13:51', '2026-01-08 07:13:51'),
('6961e18b08a3e', 'Doctorado', '2026-01-10 10:20:11', '2026-01-10 10:20:11'),
('6961e19385844', 'Magister', '2026-01-10 10:20:19', '2026-01-10 10:20:19'),
('6961e19b40611', 'Licenciado', '2026-01-10 10:20:27', '2026-01-10 10:20:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_12_30_174942_create_roles_table', 1),
(5, '2025_12_30_175029_create_user_role_table', 1),
(6, '2025_12_30_175045_create_grados_academicos_table', 1),
(7, '2025_12_30_175100_create_tipos_contrato_table', 1),
(8, '2025_12_30_175111_create_docentes_table', 1),
(9, '2025_12_30_175215_create_permissions_table', 1),
(10, '2025_12_30_175252_create_role_permission_table', 1),
(11, '2025_12_31_140231_create_tipo_permiso_table', 1),
(12, '2025_12_31_153026_create_permiso_table', 1),
(13, '2026_01_03_221430_create_plan_recuperacion_table', 1),
(14, '2026_01_04_010955_create_sesion_recuperacion_table', 1),
(15, '2026_01_04_033721_add_horario_to_sesion_recuperacion_table', 1),
(16, '2026_01_04_220116_create_evidencia_recuperacion_table', 1),
(17, '2026_01_07_134522_create_ciclos_table', 1),
(18, '2026_01_07_134653_create_asignaturas_table', 1),
(19, '2026_01_07_152341_add_asignatura_to_sesion_recuperacion_table', 1),
(20, '2026_01_07_222734_create_semestre_academico_table', 2),
(21, '2026_01_07_223228_add_documento_and_semestre_to_permiso_table', 2),
(22, '2026_01_08_add_tema_to_sesion_recuperacion_table', 3),
(23, '2026_01_14_034426_add_notificado_estado_to_permiso', 4),
(24, '2026_01_14_152323_add_estado_notificado_to_plan_recuperacion', 5),
(25, '2026_01_15_162924_create_reprogramacion_sesion_table', 6),
(26, '2026_01_16_042400_add_reprogramada_to_estado_sesion', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

CREATE TABLE `permiso` (
  `id_permiso` char(13) NOT NULL,
  `id_docente` char(13) NOT NULL,
  `id_tipo_permiso` char(13) NOT NULL,
  `id_semestre_academico` char(13) DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `dias_permiso` int(11) NOT NULL,
  `horas_afectadas` decimal(5,2) NOT NULL,
  `estado_permiso` enum('SOLICITADO','APROBADO','RECHAZADO','EN_RECUPERACION','RECUPERADO','CERRADO') NOT NULL,
  `motivo` text DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `documento_sustento` varchar(255) DEFAULT NULL,
  `fecha_solicitud` date NOT NULL,
  `fecha_resolucion` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `estado_notificado` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permiso`
--

INSERT INTO `permiso` (`id_permiso`, `id_docente`, `id_tipo_permiso`, `id_semestre_academico`, `fecha_inicio`, `fecha_fin`, `dias_permiso`, `horas_afectadas`, `estado_permiso`, `motivo`, `observacion`, `documento_sustento`, `fecha_solicitud`, `fecha_resolucion`, `created_at`, `updated_at`, `estado_notificado`) VALUES
('PER-2026-0001', '695f139dd66c2', '695f132c1ba2d', '695ee667ba5bc', '2026-01-08', '2026-01-08', 1, 2.00, 'APROBADO', 'Permiso por examen de nombramiento', NULL, 'storage/permisos/documentos/1767839622_695f1786de250.pdf', '2026-01-07', '2026-01-14', '2026-01-08 07:33:42', '2026-01-16 04:37:25', 'RECUPERADO'),
('PER-2026-0002', '695f43144a8a6', '695f4327ed00f', '695ee667ba5bc', '2026-01-09', '2026-01-09', 1, 4.00, 'EN_RECUPERACION', 'Permido por motivo de salud', 'hola', 'storage/permisos/documentos/1767850874_695f437a46f10.docx', '2026-01-08', '2026-01-10', '2026-01-08 10:41:14', '2026-01-22 05:44:07', 'EN_RECUPERACION'),
('PER-2026-0003', '695f43144a8a6', '695f132c1ba2d', '695ee667ba5bc', '2026-01-13', '2026-01-14', 2, 5.00, 'APROBADO', 'Permiso por elaboracion de examen', NULL, 'storage/permisos/documentos/1767881516_695fbb2cb74cc.pdf', '2026-01-08', NULL, '2026-01-08 19:11:56', '2026-01-20 08:45:52', 'APROBADO'),
('PER-2026-0004', '695f139dd66c2', '695fe5ecc1d65', '695ee667ba5bc', '2026-01-12', '2026-01-12', 1, 4.00, 'SOLICITADO', 'Permiso por onomastico', NULL, 'storage/permisos/documentos/1767892834_695fe762053dc.pdf', '2026-01-08', NULL, '2026-01-08 22:20:34', '2026-01-21 08:53:14', NULL),
('PER-2026-0005', '695f43144a8a6', '69643e478a20a', '695ee667ba5bc', '2026-01-14', '2026-01-16', 3, 6.00, 'RECUPERADO', 'Permiso por Conferencia Internacional', NULL, 'storage/permisos/documentos/1768253236_696567347a81e.pdf', '2026-01-12', '2026-01-13', '2026-01-13 02:27:16', '2026-01-15 19:39:45', 'RECUPERADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plan_recuperacion`
--

CREATE TABLE `plan_recuperacion` (
  `id_plan` char(13) NOT NULL,
  `id_permiso` char(13) NOT NULL,
  `fecha_presentacion` date NOT NULL,
  `total_horas_recuperar` decimal(5,2) NOT NULL,
  `estado_plan` enum('PRESENTADO','APROBADO','OBSERVADO') NOT NULL,
  `estado_notificado` varchar(255) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `plan_recuperacion`
--

INSERT INTO `plan_recuperacion` (`id_plan`, `id_permiso`, `fecha_presentacion`, `total_horas_recuperar`, `estado_plan`, `estado_notificado`, `observacion`, `created_at`, `updated_at`) VALUES
('PLN-2026-0002', 'PER-2026-0002', '2026-01-15', 4.00, 'PRESENTADO', NULL, NULL, '2026-01-15 19:59:27', '2026-01-16 10:06:35'),
('PLN-2026-0003', 'PER-2026-0001', '2026-01-15', 2.00, 'PRESENTADO', NULL, NULL, '2026-01-16 04:39:55', '2026-01-16 04:39:55'),
('PLN-2026-0004', 'PER-2026-0003', '2026-01-20', 5.00, 'PRESENTADO', NULL, NULL, '2026-01-20 08:57:34', '2026-01-20 08:57:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reprogramacion_sesion`
--

CREATE TABLE `reprogramacion_sesion` (
  `id_reprogramacion` char(13) NOT NULL,
  `id_sesion` char(13) NOT NULL,
  `fecha_anterior` date NOT NULL,
  `hora_inicio_anterior` time NOT NULL,
  `hora_fin_anterior` time NOT NULL,
  `aula_anterior` varchar(50) NOT NULL,
  `fecha_nueva` date NOT NULL,
  `hora_inicio_nueva` time NOT NULL,
  `hora_fin_nueva` time NOT NULL,
  `aula_nueva` varchar(50) NOT NULL,
  `motivo` text NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reprogramacion_sesion`
--

INSERT INTO `reprogramacion_sesion` (`id_reprogramacion`, `id_sesion`, `fecha_anterior`, `hora_inicio_anterior`, `hora_fin_anterior`, `aula_anterior`, `fecha_nueva`, `hora_inicio_nueva`, `hora_fin_nueva`, `aula_nueva`, `motivo`, `fecha_registro`) VALUES
('REP1768577192', 'SES-2026-0008', '2026-01-21', '02:00:00', '04:00:00', 'AULA 104', '2026-01-20', '04:00:00', '06:00:00', 'AULA 104', 'Reprogramación dentro del plan de recuperación', '2026-01-16 15:26:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Rol para el Administrador', '2026-01-07 22:13:43', '2026-01-07 22:13:43'),
(2, 'Docente', 'Rol del docente', '2026-01-07 22:14:18', '2026-01-07 22:14:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_permission`
--

CREATE TABLE `role_permission` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `semestre_academico`
--

CREATE TABLE `semestre_academico` (
  `IdSemestreAcademico` char(13) NOT NULL,
  `codigo_Academico` varchar(10) NOT NULL,
  `anio_academico` year(4) NOT NULL,
  `FechaInicioAcademico` date NOT NULL,
  `FechaFinAcademico` date NOT NULL,
  `EstadoAcademico` enum('Planificado','Activo','Cerrado') NOT NULL DEFAULT 'Planificado',
  `EsActualAcademico` tinyint(1) NOT NULL DEFAULT 0,
  `DescripcionAcademico` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `semestre_academico`
--

INSERT INTO `semestre_academico` (`IdSemestreAcademico`, `codigo_Academico`, `anio_academico`, `FechaInicioAcademico`, `FechaFinAcademico`, `EstadoAcademico`, `EsActualAcademico`, `DescripcionAcademico`, `created_at`, `updated_at`) VALUES
('695ee39ddbd61', '2025-I', '2025', '2026-01-26', '2026-05-26', 'Cerrado', 0, NULL, '2026-01-08 03:52:13', '2026-01-20 22:40:44'),
('695ee667ba5bc', '2025-II', '2026', '2026-04-13', '2026-09-07', 'Activo', 1, NULL, '2026-01-08 04:04:07', '2026-01-28 08:03:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesion_recuperacion`
--

CREATE TABLE `sesion_recuperacion` (
  `id_sesion` char(13) NOT NULL,
  `id_plan` char(13) NOT NULL,
  `idAsignatura` char(13) DEFAULT NULL,
  `fecha_sesion` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `aula` varchar(50) DEFAULT NULL,
  `tema` text DEFAULT NULL,
  `horas_recuperadas` decimal(5,2) NOT NULL,
  `estado_sesion` enum('PROGRAMADA','REPROGRAMADA','REALIZADA','CANCELADA') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sesion_recuperacion`
--

INSERT INTO `sesion_recuperacion` (`id_sesion`, `id_plan`, `idAsignatura`, `fecha_sesion`, `hora_inicio`, `hora_fin`, `aula`, `tema`, `horas_recuperadas`, `estado_sesion`, `created_at`, `updated_at`) VALUES
('SES-2026-0008', 'PLN-2026-0002', '695f3c5466d1b', '2026-01-20', '04:00:00', '06:00:00', 'AULA 104', 'Hola Mundo', 2.00, 'REALIZADA', '2026-01-15 19:59:27', '2026-01-16 20:32:27'),
('SES-2026-0009', 'PLN-2026-0003', '695f3c13407a7', '2026-01-19', '06:00:00', '07:00:00', 'LAB-304', 'Hola Mundo', 1.00, 'REALIZADA', '2026-01-16 04:39:55', '2026-01-19 22:28:28'),
('SES-2026-0010', 'PLN-2026-0002', '695f3c7758de4', '2026-01-16', '06:00:00', '08:00:00', 'AULA 104', 'Hola Mundo', 2.00, 'PROGRAMADA', '2026-01-16 07:09:42', '2026-01-16 08:20:06'),
('SES-2026-0011', 'PLN-2026-0004', '695f3baa24505', '2026-01-20', '08:00:00', '10:00:00', 'LAB-303', 'Matematica Basica', 2.00, 'PROGRAMADA', '2026-01-20 08:57:34', '2026-01-20 08:57:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Ml1YcS6qqlzngMOo8KKjJnz52Znk29NBdxZ34Ggs', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMzJ0aVh2TTZFanBDYXZIOHRtVXZCREZ2TTRpSlpNcHZqeVRLamNLZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kb2NlbnRlL3Nlc2lvbl9yZWN1cGVyYWNpb24iO3M6NToicm91dGUiO3M6Mjc6ImdlbmVyYXRlZDo6Zng5YzlzcWR2T01hYWtaRCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==', 1769578060),
('PnsTEwgQdZtKG2siOEUa11oqOCIksX3a5fZ1TZu0', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWFllZmhNUHVQbmF5QTlvNjZQbm00Zk1CcFRMREhDd2ZKN00xbTc3YiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi91c3VhcmlvcyI7czo1OiJyb3V0ZSI7czoyNzoiZ2VuZXJhdGVkOjpKYkFUZVZBeUU5dWgwaDl5Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1769540745),
('QgDT06tewJM7xzzgPUP1El1ECc6omQifOUIyL3AB', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRER3eTVUeXBxUUFYV1RHVkYxaDd4dk1Qd1ZteHdyclZITFlRdWJTQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7czo1OiJyb3V0ZSI7czoyNzoiZ2VuZXJhdGVkOjoyRkFFSEFSZktabGdwNU1jIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Nzt9', 1769539462),
('x8sw4AH8MyYpGHjRn0sNt0cC6pW3p6yfk1XHVPbC', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUHQxeUI5bHpVSnJDYXliVDk4OHRkeHhwUmFwWnFZRVljZXd2ajJSVyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7czo1OiJyb3V0ZSI7czoyNzoiZ2VuZXJhdGVkOjoyRkFFSEFSZktabGdwNU1jIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Nzt9', 1769574456);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_contrato`
--

CREATE TABLE `tipos_contrato` (
  `idTipo_contrato` char(13) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_contrato`
--

INSERT INTO `tipos_contrato` (`idTipo_contrato`, `nombre`, `created_at`, `updated_at`) VALUES
('695f12ebeb52f', 'Nombrado', '2026-01-08 07:14:03', '2026-01-08 07:14:03'),
('695f12f53d45f', 'Contratado', '2026-01-08 07:14:13', '2026-01-08 07:14:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_permiso`
--

CREATE TABLE `tipo_permiso` (
  `id_tipo_permiso` char(13) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `requiere_recupero` tinyint(1) NOT NULL,
  `con_goce_haber` tinyint(1) NOT NULL,
  `requiere_documento` tinyint(1) NOT NULL DEFAULT 0,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_permiso`
--

INSERT INTO `tipo_permiso` (`id_tipo_permiso`, `nombre`, `descripcion`, `requiere_recupero`, `con_goce_haber`, `requiere_documento`, `estado`, `created_at`, `updated_at`) VALUES
('695f132c1ba2d', 'Elaborarcion de examen de nombramiento', NULL, 1, 1, 0, 1, '2026-01-08 07:15:08', '2026-01-08 07:15:08'),
('695f4327ed00f', 'Permiso por salud', NULL, 1, 1, 0, 1, '2026-01-08 10:39:51', '2026-01-08 10:39:51'),
('695fe5ecc1d65', 'Permiso por onomastico', NULL, 1, 0, 0, 1, '2026-01-08 22:14:20', '2026-01-08 22:14:20'),
('69643e478a20a', 'Conferencia Internacional', NULL, 0, 1, 0, 1, '2026-01-12 05:20:23', '2026-01-16 19:09:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `document_type` varchar(20) DEFAULT NULL,
  `document_number` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `last_name`, `email`, `email_verified_at`, `password`, `phone`, `image`, `gender`, `document_type`, `document_number`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Dany Santy', 'Ramirez Limas', 'ramirez@gmail.com', NULL, '$2y$12$mE65YwBXKFcAJVX6vj8M1ut1b0dp.k.Zq6FEoetJfjlFdc9P1ADha', NULL, 'usuarios/1768344631_6966cc371c0c7.png', 'male', 'DNI', '11111112', 'active', NULL, '2026-01-08 03:13:18', '2026-01-17 05:46:44'),
(2, 'Betsabe Milagros', 'Ccolque Ruiz', 'ramirezlimasdanny@gmail.com', NULL, '$2y$12$4cXxXlx3gf7rIucAsMylBep9JFZXN4libI6b8PMh9ybY3MtOkRidK', '111111111', NULL, 'female', 'PASAPORTE', '11111111', 'active', NULL, '2026-01-08 07:13:18', '2026-01-15 09:01:11'),
(3, 'Dany Santiago', 'Ramirez Limas', 'ramirezlimas905@gmail.com', NULL, '$2y$12$ioNFq7gDeFqZn2XApuVmT..KMSSw2FYKYZRyLlyDONBg/m3lKQUma', NULL, NULL, 'male', 'DNI', '71104924', 'active', NULL, '2026-01-08 10:39:02', '2026-01-21 08:20:13'),
(4, 'Luz', 'Esmeralda Sierra Ramírez', 'user1@gmail.com', NULL, '$2y$12$0jD.7GXl7NP1AmPah8D7.uyW2FUqTLVyM9VeVlVCEL4ah84yez.hC', '923123421', NULL, 'male', 'DNI', '71234567', 'active', NULL, '2026-01-11 21:47:46', '2026-01-11 21:48:25'),
(5, 'Manuel Jesús', 'Ibarra Cabrera', 'manuel@unamba.edu.pe', NULL, '$2y$12$6M1uCqxWK3VrZXexZ.eESeIk8EeyssGg51d756f.TQWOvKix6bW12', '987123432', NULL, 'male', 'DNI', '12345671', 'inactive', NULL, '2026-01-20 22:27:02', '2026-01-28 07:01:56'),
(7, 'Administrador', 'Administrador', 'admin@gmail.com', NULL, '$2y$12$m.lAPxw9VX5By.uMrQ326Ouq9EAe9HOrYgJg7.PhadMu42aaM93US', '923123456', NULL, 'male', 'DNI', '12345678', 'active', NULL, '2026-01-27 23:44:01', '2026-01-27 23:44:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_role`
--

CREATE TABLE `user_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `user_role`
--

INSERT INTO `user_role` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-01-07 22:15:04', '2026-01-07 22:15:04'),
(3, 3, 2, NULL, NULL),
(4, 4, 1, NULL, NULL),
(9, 7, 1, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  ADD PRIMARY KEY (`idAsignatura`),
  ADD UNIQUE KEY `asignaturas_codigo_asignatura_unique` (`codigo_asignatura`),
  ADD KEY `asignaturas_idciclo_foreign` (`IdCiclo`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `ciclos`
--
ALTER TABLE `ciclos`
  ADD PRIMARY KEY (`IdCiclo`);

--
-- Indices de la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD PRIMARY KEY (`idDocente`),
  ADD UNIQUE KEY `docentes_codigo_unamba_unique` (`codigo_unamba`),
  ADD KEY `docentes_user_id_foreign` (`user_id`),
  ADD KEY `docentes_grado_id_foreign` (`grado_id`),
  ADD KEY `docentes_tipo_contrato_id_foreign` (`tipo_contrato_id`);

--
-- Indices de la tabla `evidencia_recuperacion`
--
ALTER TABLE `evidencia_recuperacion`
  ADD PRIMARY KEY (`id_evidencia`,`id_sesion`),
  ADD KEY `evidencia_recuperacion_id_sesion_foreign` (`id_sesion`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `grados_academicos`
--
ALTER TABLE `grados_academicos`
  ADD PRIMARY KEY (`idGrados_academicos`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD PRIMARY KEY (`id_permiso`),
  ADD KEY `permiso_id_docente_foreign` (`id_docente`),
  ADD KEY `permiso_id_tipo_permiso_foreign` (`id_tipo_permiso`),
  ADD KEY `permiso_id_semestre_academico_foreign` (`id_semestre_academico`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indices de la tabla `plan_recuperacion`
--
ALTER TABLE `plan_recuperacion`
  ADD PRIMARY KEY (`id_plan`),
  ADD UNIQUE KEY `plan_recuperacion_id_permiso_unique` (`id_permiso`);

--
-- Indices de la tabla `reprogramacion_sesion`
--
ALTER TABLE `reprogramacion_sesion`
  ADD PRIMARY KEY (`id_reprogramacion`),
  ADD KEY `reprogramacion_sesion_id_sesion_foreign` (`id_sesion`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indices de la tabla `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_permission_role_id_foreign` (`role_id`),
  ADD KEY `role_permission_permission_id_foreign` (`permission_id`);

--
-- Indices de la tabla `semestre_academico`
--
ALTER TABLE `semestre_academico`
  ADD PRIMARY KEY (`IdSemestreAcademico`),
  ADD UNIQUE KEY `semestre_academico_codigo_academico_unique` (`codigo_Academico`);

--
-- Indices de la tabla `sesion_recuperacion`
--
ALTER TABLE `sesion_recuperacion`
  ADD PRIMARY KEY (`id_sesion`),
  ADD KEY `sesion_recuperacion_id_plan_foreign` (`id_plan`),
  ADD KEY `sesion_recuperacion_idasignatura_foreign` (`idAsignatura`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `tipos_contrato`
--
ALTER TABLE `tipos_contrato`
  ADD PRIMARY KEY (`idTipo_contrato`);

--
-- Indices de la tabla `tipo_permiso`
--
ALTER TABLE `tipo_permiso`
  ADD PRIMARY KEY (`id_tipo_permiso`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_role_user_id_foreign` (`user_id`),
  ADD KEY `user_role_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  ADD CONSTRAINT `asignaturas_idciclo_foreign` FOREIGN KEY (`IdCiclo`) REFERENCES `ciclos` (`IdCiclo`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD CONSTRAINT `docentes_grado_id_foreign` FOREIGN KEY (`grado_id`) REFERENCES `grados_academicos` (`idGrados_academicos`),
  ADD CONSTRAINT `docentes_tipo_contrato_id_foreign` FOREIGN KEY (`tipo_contrato_id`) REFERENCES `tipos_contrato` (`idTipo_contrato`),
  ADD CONSTRAINT `docentes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `evidencia_recuperacion`
--
ALTER TABLE `evidencia_recuperacion`
  ADD CONSTRAINT `evidencia_recuperacion_id_sesion_foreign` FOREIGN KEY (`id_sesion`) REFERENCES `sesion_recuperacion` (`id_sesion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD CONSTRAINT `permiso_id_docente_foreign` FOREIGN KEY (`id_docente`) REFERENCES `docentes` (`idDocente`),
  ADD CONSTRAINT `permiso_id_semestre_academico_foreign` FOREIGN KEY (`id_semestre_academico`) REFERENCES `semestre_academico` (`IdSemestreAcademico`) ON DELETE SET NULL,
  ADD CONSTRAINT `permiso_id_tipo_permiso_foreign` FOREIGN KEY (`id_tipo_permiso`) REFERENCES `tipo_permiso` (`id_tipo_permiso`);

--
-- Filtros para la tabla `plan_recuperacion`
--
ALTER TABLE `plan_recuperacion`
  ADD CONSTRAINT `plan_recuperacion_id_permiso_foreign` FOREIGN KEY (`id_permiso`) REFERENCES `permiso` (`id_permiso`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reprogramacion_sesion`
--
ALTER TABLE `reprogramacion_sesion`
  ADD CONSTRAINT `reprogramacion_sesion_id_sesion_foreign` FOREIGN KEY (`id_sesion`) REFERENCES `sesion_recuperacion` (`id_sesion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `role_permission_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permission_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sesion_recuperacion`
--
ALTER TABLE `sesion_recuperacion`
  ADD CONSTRAINT `sesion_recuperacion_id_plan_foreign` FOREIGN KEY (`id_plan`) REFERENCES `plan_recuperacion` (`id_plan`) ON DELETE CASCADE,
  ADD CONSTRAINT `sesion_recuperacion_idasignatura_foreign` FOREIGN KEY (`idAsignatura`) REFERENCES `asignaturas` (`idAsignatura`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_role_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
