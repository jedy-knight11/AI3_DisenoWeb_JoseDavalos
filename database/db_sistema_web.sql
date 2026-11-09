-- ------------------------------------------------------------
-- Base de datos: db_sistema_web
-- Script DDL de creación (MySQL 8 / MariaDB 10.6 o superior)
-- ------------------------------------------------------------

CREATE DATABASE IF NOT EXISTS db_sistema_web
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE db_sistema_web;

-- Catálogo de servicios. Se separa de clientes para no repetir el nombre
-- del servicio en cada registro (tercera forma normal).
CREATE TABLE IF NOT EXISTS servicios (
    id              TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre          VARCHAR(60)      NOT NULL,
    fecha_creacion  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_servicios_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla principal del módulo
CREATE TABLE IF NOT EXISTS clientes (
    id                   INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    nombres              VARCHAR(100)     NOT NULL,
    correo               VARCHAR(150)     NOT NULL,
    telefono             VARCHAR(15)      NULL,
    empresa              VARCHAR(120)     NULL,
    servicio_id          TINYINT UNSIGNED NOT NULL,
    mensaje              TEXT             NULL,
    fecha_creacion       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_clientes_correo (correo),
    KEY idx_clientes_fecha_creacion (fecha_creacion),
    CONSTRAINT fk_clientes_servicio
        FOREIGN KEY (servicio_id) REFERENCES servicios (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos iniciales del catálogo
INSERT IGNORE INTO servicios (nombre) VALUES
    ('Sitio web corporativo'),
    ('Tienda en línea'),
    ('Mantenimiento web'),
    ('Optimización de rendimiento'),
    ('Posicionamiento SEO');
