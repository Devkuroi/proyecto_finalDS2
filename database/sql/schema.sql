-- =====================================================================
-- SISTEMA DE GESTIÓN Y GEORREFERENCIACIÓN DE ENCUESTAS - SANTIAGO DE CALI
-- ENTREGA 1 - ESQUEMA RELACIONAL DE BASE DE DATOS (MySQL 8.0)
-- Sub-Agente 1: DB_Architect_Agent
-- =====================================================================

DROP DATABASE IF EXISTS gestion_encuestas_cali;
CREATE DATABASE IF NOT EXISTS gestion_encuestas_cali
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gestion_encuestas_cali;

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- 1. TABLA: roles
-- Define los 5 perfiles del sistema institucional de encuestas de Cali
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS roles;
CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE COMMENT 'ADMINISTRADOR, VENTANILLA, ENCUESTADOR, CALIDAD, ENRUTADOR',
    descripcion VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. TABLA: puntos_atencion
-- Puntos oficiales de atención al ciudadano (Sedes CALI y Oficina Central)
-- Sedes: Comunas 1, 6, 7, 13, 14, 20, 21 y Sede Principal (Comuna 17)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS puntos_atencion;
CREATE TABLE puntos_atencion (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    comuna_numero INT NOT NULL COMMENT 'Número de comuna donde opera la sede',
    direccion VARCHAR(255) NOT NULL,
    telefono VARCHAR(50) NULL,
    latitud DECIMAL(10, 8) NOT NULL,
    longitud DECIMAL(11, 8) NOT NULL,
    es_principal TINYINT(1) NOT NULL DEFAULT 0,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_puntos_comuna (comuna_numero)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. TABLA: usuarios
-- Usuarios autorizados del sistema vinculados a roles y sedes
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id INT UNSIGNED NOT NULL,
    punto_atencion_id INT UNSIGNED NULL,
    identificacion VARCHAR(30) NOT NULL,
    telefono VARCHAR(30) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) 
        REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_usuarios_punto_atencion FOREIGN KEY (punto_atencion_id) 
        REFERENCES puntos_atencion(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_usuarios_rol (rol_id),
    INDEX idx_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. CATÁLOGO TERRITORIAL: comunas_corregimientos
-- División territorial oficial de Santiago de Cali (Comunas 1 a 22 y 15 Corregimientos)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS comunas_corregimientos;
CREATE TABLE comunas_corregimientos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('COMUNA', 'CORREGIMIENTO') NOT NULL,
    numero INT NULL COMMENT 'Aplica para Comunas 1 a 22',
    nombre VARCHAR(100) NOT NULL UNIQUE,
    zona ENUM('URBANA', 'RURAL') NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cc_tipo (tipo),
    INDEX idx_cc_numero (numero)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. CATÁLOGO TERRITORIAL: barrios_veredas
-- Barrios de comunas y Veredas de corregimientos de Cali
-- Previene asignación errónea de barrios a comunas incorrectas
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS barrios_veredas;
CREATE TABLE barrios_veredas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    comuna_corregimiento_id INT UNSIGNED NOT NULL,
    tipo ENUM('BARRIO', 'VEREDA', 'SECTOR') NOT NULL DEFAULT 'BARRIO',
    nombre VARCHAR(150) NOT NULL,
    codigo_dane VARCHAR(20) NULL,
    latitud_referencia DECIMAL(10, 8) NULL,
    longitud_referencia DECIMAL(11, 8) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bv_comuna_corregimiento FOREIGN KEY (comuna_corregimiento_id) 
        REFERENCES comunas_corregimientos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_bv_cc (comuna_corregimiento_id),
    INDEX idx_bv_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. VISTA NORMALIZADA: division_sectorial_cali
-- Vista de consulta optimizada para integridad territorial
-- ---------------------------------------------------------------------
DROP VIEW IF EXISTS division_sectorial_cali;
CREATE VIEW division_sectorial_cali AS
SELECT 
    cc.id AS comuna_corregimiento_id,
    cc.tipo AS tipo_division,
    cc.numero AS comuna_numero,
    cc.nombre AS division_territorial,
    cc.zona AS zona_geografica,
    bv.id AS barrio_vereda_id,
    bv.tipo AS tipo_subdivision,
    bv.nombre AS barrio_vereda_nombre,
    bv.latitud_referencia,
    bv.longitud_referencia
FROM comunas_corregimientos cc
INNER JOIN barrios_veredas bv ON bv.comuna_corregimiento_id = cc.id;

-- ---------------------------------------------------------------------
-- 7. TABLA: solicitudes_encuesta
-- Solicitudes radicadas en ventanilla con georreferenciación y cálculo
-- automático de fecha límite (+15 días calendario de política)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS solicitudes_encuesta;
CREATE TABLE solicitudes_encuesta (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    radicado_codigo VARCHAR(30) NOT NULL UNIQUE,
    nombres_solicitante VARCHAR(100) NOT NULL,
    apellidos_solicitante VARCHAR(100) NOT NULL,
    celular VARCHAR(20) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    barrio_id INT UNSIGNED NOT NULL,
    comuna_corregimiento_id INT UNSIGNED NOT NULL,
    latitud DECIMAL(10, 8) NOT NULL,
    longitud DECIMAL(11, 8) NOT NULL,
    punto_atencion_id INT UNSIGNED NOT NULL,
    usuario_ventanilla_id INT UNSIGNED NOT NULL,
    fecha_radicacion DATETIME NOT NULL,
    fecha_limite DATE NOT NULL COMMENT 'Calculada automáticamente a +15 días calendario',
    estado ENUM('RADICADA', 'EN_RUTA', 'ASIGNADA', 'VISITADA', 'ANULADA') NOT NULL DEFAULT 'RADICADA',
    observaciones TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_solicitudes_barrio FOREIGN KEY (barrio_id) 
        REFERENCES barrios_veredas(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_solicitudes_comuna_corr FOREIGN KEY (comuna_corregimiento_id) 
        REFERENCES comunas_corregimientos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_solicitudes_punto_atencion FOREIGN KEY (punto_atencion_id) 
        REFERENCES puntos_atencion(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_solicitudes_usuario_ventanilla FOREIGN KEY (usuario_ventanilla_id) 
        REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_solicitudes_fecha_radicacion (fecha_radicacion),
    INDEX idx_solicitudes_estado (estado),
    INDEX idx_solicitudes_punto (punto_atencion_id),
    INDEX idx_solicitudes_cc (comuna_corregimiento_id),
    INDEX idx_solicitudes_coords (latitud, longitud)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
