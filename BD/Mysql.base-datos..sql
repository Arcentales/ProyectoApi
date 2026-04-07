-- ============================================================
--  BaseApp - Script SQL Completo
--  Optimizado para: PHP + API REST + Aplicativo Móvil
--  Motor: MySQL 8.0+  |  Charset: utf8mb4
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- ------------------------------------------------------------
--  BASE DE DATOS
-- ------------------------------------------------------------
DROP DATABASE IF EXISTS baseapp;
CREATE DATABASE baseapp
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE baseapp;

-- ============================================================
--  TABLAS INDEPENDIENTES (sin FK hacia otras tablas propias)
-- ============================================================

-- ------------------------------------------------------------
-- 1. distrito
-- ------------------------------------------------------------
CREATE TABLE distrito (
    iddistrito   TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nomdistrito  VARCHAR(80)      NOT NULL,
    PRIMARY KEY (iddistrito),
    UNIQUE KEY uq_distrito_nom (nomdistrito)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Distritos de Loreto';

-- ------------------------------------------------------------
-- 2. estado
-- ------------------------------------------------------------
CREATE TABLE estado (
    idestado   TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nomestado  VARCHAR(60)      NOT NULL,
    PRIMARY KEY (idestado),
    UNIQUE KEY uq_estado_nom (nomestado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Estados de gestión de un cliente';

-- ------------------------------------------------------------
-- 3. producto
-- ------------------------------------------------------------
CREATE TABLE producto (
    idproducto   TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nomproducto  VARCHAR(60)      NOT NULL,
    activo       TINYINT(1)       NOT NULL DEFAULT 1,
    PRIMARY KEY (idproducto),
    UNIQUE KEY uq_producto_nom (nomproducto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo de productos ofertados';

-- ------------------------------------------------------------
-- 4. rol
-- ------------------------------------------------------------
CREATE TABLE rol (
    idrol   TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nomrol  VARCHAR(40)      NOT NULL,
    PRIMARY KEY (idrol),
    UNIQUE KEY uq_rol_nom (nomrol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Roles del sistema (admin, vendedor, supervisor, etc.)';

-- ------------------------------------------------------------
-- 5. usuario
-- ------------------------------------------------------------
CREATE TABLE usuario (
    idusuario      INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    idrol          TINYINT UNSIGNED NOT NULL,
    nombres        VARCHAR(80)     NOT NULL,
    apellidos      VARCHAR(80)     NOT NULL,
    email          VARCHAR(120)    NOT NULL,
    password_hash  VARCHAR(255)    NOT NULL  COMMENT 'bcrypt / Argon2',
    token_refresh  VARCHAR(512)    NULL      COMMENT 'JWT refresh token',
    activo         TINYINT(1)      NOT NULL DEFAULT 1,
    ultimo_login   DATETIME        NULL,
    created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idusuario),
    UNIQUE KEY uq_usuario_email (email),
    KEY idx_usuario_rol (idrol),
    CONSTRAINT fk_usuario_rol FOREIGN KEY (idrol) REFERENCES rol (idrol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Usuarios del sistema (vendedores, supervisores, admins)';

-- ============================================================
--  TABLAS DEPENDIENTES (con FK hacia otras tablas)
-- ============================================================

-- ------------------------------------------------------------
-- 6. cliente
-- ------------------------------------------------------------
CREATE TABLE cliente (
    idcliente    INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    iddistrito   TINYINT UNSIGNED NOT NULL,
    idestado     TINYINT UNSIGNED NOT NULL,
    dni          CHAR(8)          NULL      COMMENT 'DNI peruano 8 dígitos',
    nombres      VARCHAR(80)      NOT NULL,
    apellidos    VARCHAR(80)      NOT NULL,
    telefono1    VARCHAR(15)      NULL,
    telefono2    VARCHAR(15)      NULL,
    direccion    VARCHAR(200)     NULL,
    referencia   VARCHAR(200)     NULL,
    latitud      DECIMAL(10,7)   NULL      COMMENT 'Coordenadas GPS',
    longitud     DECIMAL(10,7)   NULL,
    created_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idcliente),
    KEY idx_cliente_distrito (iddistrito),
    KEY idx_cliente_estado   (idestado),
    KEY idx_cliente_dni      (dni),
    KEY idx_cliente_nombres  (nombres, apellidos),
    CONSTRAINT fk_cliente_distrito FOREIGN KEY (iddistrito) REFERENCES distrito (iddistrito),
    CONSTRAINT fk_cliente_estado   FOREIGN KEY (idestado)   REFERENCES estado   (idestado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Cartera de clientes';

-- ------------------------------------------------------------
-- 7. visita  (gestión / visita de campo)
-- ------------------------------------------------------------
CREATE TABLE visita (
    idvisita     INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    idcliente    INT UNSIGNED     NOT NULL,
    idusuario    INT UNSIGNED     NOT NULL,
    idestado     TINYINT UNSIGNED NOT NULL,
    idproducto   TINYINT UNSIGNED NULL      COMMENT 'Producto ofertado en la visita',
    observacion  TEXT             NULL,
    fecha_visita DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    latitud      DECIMAL(10,7)   NULL,
    longitud     DECIMAL(10,7)   NULL,
    created_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idvisita),
    KEY idx_visita_cliente  (idcliente),
    KEY idx_visita_usuario  (idusuario),
    KEY idx_visita_estado   (idestado),
    KEY idx_visita_fecha    (fecha_visita),
    KEY idx_visita_producto (idproducto),
    CONSTRAINT fk_visita_cliente  FOREIGN KEY (idcliente)  REFERENCES cliente  (idcliente),
    CONSTRAINT fk_visita_usuario  FOREIGN KEY (idusuario)  REFERENCES usuario  (idusuario),
    CONSTRAINT fk_visita_estado   FOREIGN KEY (idestado)   REFERENCES estado   (idestado),
    CONSTRAINT fk_visita_producto FOREIGN KEY (idproducto) REFERENCES producto (idproducto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Registro de visitas / gestiones de campo';

-- ------------------------------------------------------------
-- 8. api_token  (tokens para la REST API / app móvil)
-- ------------------------------------------------------------
CREATE TABLE api_token (
    idtoken      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    idusuario    INT UNSIGNED  NOT NULL,
    token        VARCHAR(512)  NOT NULL,
    dispositivo  VARCHAR(100)  NULL  COMMENT 'Android / iOS / Web',
    expires_at   DATETIME      NOT NULL,
    revocado     TINYINT(1)    NOT NULL DEFAULT 0,
    created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idtoken),
    KEY idx_token_usuario  (idusuario),
    KEY idx_token_expires  (expires_at),
    CONSTRAINT fk_apitoken_usuario FOREIGN KEY (idusuario) REFERENCES usuario (idusuario)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Tokens JWT para autenticación de API REST / app móvil';

-- ============================================================
--  ÍNDICES ADICIONALES (optimización de consultas frecuentes)
-- ============================================================
CREATE INDEX idx_visita_cliente_fecha ON visita (idcliente, fecha_visita);
CREATE INDEX idx_visita_usuario_fecha ON visita (idusuario, fecha_visita);
CREATE INDEX idx_cliente_distrito_estado ON cliente (iddistrito, idestado);

-- ============================================================
--  VISTAS
-- ============================================================

-- Vista principal para la app móvil: detalle completo de visita
CREATE OR REPLACE VIEW vw_visita_detalle AS
SELECT
    v.idvisita,
    v.fecha_visita,
    c.idcliente,
    CONCAT(c.nombres, ' ', c.apellidos)   AS cliente,
    c.dni,
    c.telefono1,
    c.direccion,
    d.nomdistrito                          AS distrito,
    p.nomproducto                          AS producto,
    e.nomestado                            AS estado,
    v.observacion,
    CONCAT(u.nombres, ' ', u.apellidos)   AS vendedor,
    v.latitud,
    v.longitud
FROM visita     v
JOIN cliente    c ON c.idcliente  = v.idcliente
JOIN distrito   d ON d.iddistrito = c.iddistrito
JOIN estado     e ON e.idestado   = v.idestado
JOIN usuario    u ON u.idusuario  = v.idusuario
LEFT JOIN producto p ON p.idproducto = v.idproducto;

-- Vista resumen por vendedor (para reportes)
CREATE OR REPLACE VIEW vw_resumen_vendedor AS
SELECT
    u.idusuario,
    CONCAT(u.nombres, ' ', u.apellidos) AS vendedor,
    COUNT(v.idvisita)                   AS total_visitas,
    SUM(v.idestado = 3)                 AS desembolsados,   -- estado 3 = Desembolsado
    SUM(v.idestado = 6)                 AS interesados,
    SUM(v.idestado = 7)                 AS volver_visitar,
    DATE(MIN(v.fecha_visita))           AS primera_visita,
    DATE(MAX(v.fecha_visita))           AS ultima_visita
FROM usuario u
LEFT JOIN visita v ON v.idusuario = u.idusuario
GROUP BY u.idusuario, u.nombres, u.apellidos;

-- Vista resumen por distrito
CREATE OR REPLACE VIEW vw_resumen_distrito AS
SELECT
    d.iddistrito,
    d.nomdistrito,
    COUNT(DISTINCT c.idcliente) AS total_clientes,
    COUNT(DISTINCT v.idvisita)  AS total_visitas,
    SUM(v.idestado = 3)         AS desembolsados
FROM distrito d
LEFT JOIN cliente c ON c.iddistrito = d.iddistrito
LEFT JOIN visita  v ON v.idcliente  = c.idcliente
GROUP BY d.iddistrito, d.nomdistrito;

-- ============================================================
--  STORED PROCEDURES
-- ============================================================

DELIMITER $$

-- sp_login: autenticación desde la API (devuelve datos del usuario)
CREATE PROCEDURE sp_login(IN p_email VARCHAR(120))
BEGIN
    SELECT
        u.idusuario,
        u.password_hash,
        u.nombres,
        u.apellidos,
        u.email,
        u.activo,
        r.nomrol AS rol
    FROM usuario u
    JOIN rol r ON r.idrol = u.idrol
    WHERE u.email = p_email
    LIMIT 1;
END$$

-- sp_registrar_visita: inserta una visita y actualiza el estado del cliente
CREATE PROCEDURE sp_registrar_visita(
    IN p_idcliente  INT UNSIGNED,
    IN p_idusuario  INT UNSIGNED,
    IN p_idestado   TINYINT UNSIGNED,
    IN p_idproducto TINYINT UNSIGNED,
    IN p_observacion TEXT,
    IN p_latitud    DECIMAL(10,7),
    IN p_longitud   DECIMAL(10,7),
    OUT p_idvisita  INT UNSIGNED
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error al registrar visita';
    END;

    START TRANSACTION;

    INSERT INTO visita (idcliente, idusuario, idestado, idproducto, observacion, latitud, longitud)
    VALUES (p_idcliente, p_idusuario, p_idestado, p_idproducto, p_observacion, p_latitud, p_longitud);

    SET p_idvisita = LAST_INSERT_ID();

    -- Actualizar estado vigente del cliente
    UPDATE cliente
    SET idestado   = p_idestado,
        updated_at = NOW()
    WHERE idcliente = p_idcliente;

    COMMIT;
END$$

-- sp_clientes_por_distrito: lista para la app móvil con paginación
CREATE PROCEDURE sp_clientes_por_distrito(
    IN p_iddistrito TINYINT UNSIGNED,
    IN p_pagina     INT UNSIGNED,
    IN p_limite     INT UNSIGNED
)
BEGIN
    DECLARE v_offset INT UNSIGNED DEFAULT (p_pagina - 1) * p_limite;

    SELECT
        c.idcliente,
        CONCAT(c.nombres, ' ', c.apellidos) AS cliente,
        c.dni,
        c.telefono1,
        c.direccion,
        e.nomestado AS estado,
        c.latitud,
        c.longitud
    FROM cliente c
    JOIN estado  e ON e.idestado = c.idestado
    WHERE c.iddistrito = p_iddistrito
    ORDER BY c.apellidos, c.nombres
    LIMIT p_limite OFFSET v_offset;
END$$

-- sp_reporte_periodo: visitas en un rango de fechas
CREATE PROCEDURE sp_reporte_periodo(
    IN p_desde  DATE,
    IN p_hasta  DATE,
    IN p_iduser INT UNSIGNED   -- NULL = todos los usuarios
)
BEGIN
    SELECT
        v.idvisita,
        v.fecha_visita,
        CONCAT(c.nombres,' ',c.apellidos) AS cliente,
        d.nomdistrito,
        p.nomproducto,
        e.nomestado,
        CONCAT(u.nombres,' ',u.apellidos) AS vendedor
    FROM visita v
    JOIN cliente  c ON c.idcliente  = v.idcliente
    JOIN distrito d ON d.iddistrito = c.iddistrito
    JOIN estado   e ON e.idestado   = v.idestado
    JOIN usuario  u ON u.idusuario  = v.idusuario
    LEFT JOIN producto p ON p.idproducto = v.idproducto
    WHERE DATE(v.fecha_visita) BETWEEN p_desde AND p_hasta
      AND (p_iduser IS NULL OR v.idusuario = p_iduser)
    ORDER BY v.fecha_visita DESC;
END$$

DELIMITER ;

-- ============================================================
--  FUNCIONES
-- ============================================================

DELIMITER $$

-- fn_total_visitas_cliente: cuántas visitas tiene un cliente
CREATE FUNCTION fn_total_visitas_cliente(p_idcliente INT UNSIGNED)
RETURNS INT UNSIGNED
READS SQL DATA DETERMINISTIC
BEGIN
    DECLARE v_total INT UNSIGNED;
    SELECT COUNT(*) INTO v_total FROM visita WHERE idcliente = p_idcliente;
    RETURN v_total;
END$$

-- fn_ultimo_estado_cliente: devuelve el nombre del último estado registrado
CREATE FUNCTION fn_ultimo_estado_cliente(p_idcliente INT UNSIGNED)
RETURNS VARCHAR(60)
READS SQL DATA DETERMINISTIC
BEGIN
    DECLARE v_nomestado VARCHAR(60);
    SELECT e.nomestado INTO v_nomestado
    FROM visita v
    JOIN estado e ON e.idestado = v.idestado
    WHERE v.idcliente = p_idcliente
    ORDER BY v.fecha_visita DESC
    LIMIT 1;
    RETURN IFNULL(v_nomestado, 'Sin gestión');
END$$

DELIMITER ;

-- ============================================================
--  TRIGGERS
-- ============================================================

DELIMITER $$

-- Validar que el usuario esté activo antes de insertar una visita
CREATE TRIGGER trg_visita_before_insert
BEFORE INSERT ON visita
FOR EACH ROW
BEGIN
    DECLARE v_activo TINYINT(1);
    SELECT activo INTO v_activo FROM usuario WHERE idusuario = NEW.idusuario;
    IF v_activo = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El usuario está inactivo y no puede registrar visitas';
    END IF;
END$$

-- Auditoría: registrar cambio de estado del cliente
CREATE TRIGGER trg_cliente_estado_update
AFTER UPDATE ON cliente
FOR EACH ROW
BEGIN
    IF OLD.idestado <> NEW.idestado THEN
        INSERT INTO visita (idcliente, idusuario, idestado, observacion)
        VALUES (NEW.idcliente, 1, NEW.idestado,
                CONCAT('Cambio automático de estado: ', OLD.idestado, ' → ', NEW.idestado));
    END IF;
END$$

DELIMITER ;

-- ============================================================
--  EVENTOS PROGRAMADOS
-- ============================================================

SET GLOBAL event_scheduler = ON;

DELIMITER $$

-- Revocar tokens expirados cada hora
CREATE EVENT ev_revocar_tokens_expirados
ON SCHEDULE EVERY 1 HOUR
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    UPDATE api_token
    SET revocado = 1
    WHERE expires_at < NOW() AND revocado = 0;
END$$

DELIMITER ;

-- ============================================================
--  USUARIOS Y ROLES DEL SISTEMA (seguridad)
-- ============================================================

-- Usuario de la API (solo SELECT / INSERT / UPDATE sobre tablas propias)
CREATE USER IF NOT EXISTS 'api_user'@'localhost' IDENTIFIED BY 'CambiarEstaPassword_2024!';
GRANT SELECT, INSERT, UPDATE ON baseapp.cliente  TO 'api_user'@'localhost';
GRANT SELECT, INSERT         ON baseapp.visita   TO 'api_user'@'localhost';
GRANT SELECT                 ON baseapp.distrito TO 'api_user'@'localhost';
GRANT SELECT                 ON baseapp.estado   TO 'api_user'@'localhost';
GRANT SELECT                 ON baseapp.producto TO 'api_user'@'localhost';
GRANT SELECT, INSERT, UPDATE ON baseapp.api_token TO 'api_user'@'localhost';
GRANT EXECUTE                ON baseapp.*        TO 'api_user'@'localhost';

-- Usuario de reportes (solo lectura)
CREATE USER IF NOT EXISTS 'reporte_user'@'localhost' IDENTIFIED BY 'ReportesPass_2024!';
GRANT SELECT ON baseapp.* TO 'reporte_user'@'localhost';

FLUSH PRIVILEGES;

-- ============================================================
--  DATOS INICIALES
-- ============================================================

-- Roles
INSERT INTO rol (nomrol) VALUES
  ('Administrador'), ('Supervisor'), ('Vendedor'), ('Reportes');

-- Distritos de Loreto
INSERT INTO distrito (nomdistrito) VALUES
  ('Alto Nanay'), ('Andoas'), ('Barranca'), ('Belen'), ('Cahuapanas'),
  ('Capelo'), ('Fernando Lores'), ('Indiana'), ('Iquitos'), ('Jenaro Herreras'),
  ('Las Amazonas'), ('Manseriche'), ('Mazan'), ('Morona'), ('Napo'),
  ('Nauta'), ('Pastaza'), ('Pebas'), ('Punchana'), ('Putumayo'),
  ('Ramon Castilla'), ('Requena'), ('San Juan Bautista'), ('San Pablo'),
  ('Saquena'), ('Surquillo'), ('Teniente Manuel Clavero'), ('Tigre'),
  ('Torres Causana'), ('Trompeteros'), ('Urarinas'), ('Yaquerana'), ('Yavarí');

-- Estados de gestión
INSERT INTO estado (nomestado) VALUES
  ('No desea oferta'),
  ('No se encontró dirección'),
  ('Desembolsado'),
  ('Teléfonos errados'),
  ('Falleció'),
  ('Interesado'),
  ('Volver a visitar'),
  ('Sin gestión');

-- Productos
INSERT INTO producto (nomproducto) VALUES
  ('Ld'), ('Tc'), ('Combo'), ('No combo'), ('GI'), ('ET'), ('Xl');

-- Usuario admin inicial  (password: Admin@2024 con bcrypt)
INSERT INTO usuario (idrol, nombres, apellidos, email, password_hash)
VALUES (1, 'Administrador', 'Sistema', 'admin@baseapp.pe',
        '$2y$12$placeholder_reemplazar_con_hash_real');

-- ============================================================
--  CONSULTAS AVANZADAS DE EJEMPLO
-- ============================================================

/*
-- 1. Clientes interesados en Iquitos que no han sido visitados esta semana
SELECT c.idcliente, CONCAT(c.nombres,' ',c.apellidos) AS cliente, c.telefono1
FROM cliente c
JOIN estado e ON e.idestado = c.idestado
WHERE e.nomestado = 'Interesado'
  AND c.iddistrito = (SELECT iddistrito FROM distrito WHERE nomdistrito = 'Iquitos')
  AND c.idcliente NOT IN (
      SELECT idcliente FROM visita
      WHERE fecha_visita >= DATE_SUB(NOW(), INTERVAL 7 DAY)
  );

-- 2. Ranking de vendedores del mes actual
SELECT
    CONCAT(u.nombres,' ',u.apellidos) AS vendedor,
    COUNT(v.idvisita)  AS visitas,
    SUM(v.idestado = 3) AS desembolsados,
    ROUND(SUM(v.idestado = 3) / COUNT(v.idvisita) * 100, 1) AS pct_cierre
FROM usuario u
JOIN visita v ON v.idusuario = u.idusuario
WHERE MONTH(v.fecha_visita) = MONTH(NOW())
  AND YEAR(v.fecha_visita)  = YEAR(NOW())
GROUP BY u.idusuario
ORDER BY desembolsados DESC;

-- 3. Estadística de estados por producto (para reportes de Excel)
SELECT
    p.nomproducto,
    e.nomestado,
    COUNT(*) AS total
FROM visita v
JOIN producto p ON p.idproducto = v.idproducto
JOIN estado   e ON e.idestado   = v.idestado
GROUP BY p.idproducto, e.idestado
ORDER BY p.nomproducto, total DESC;

-- 4. Clientes con coordenadas (para mapa en la app móvil)
SELECT idcliente, CONCAT(nombres,' ',apellidos) AS cliente,
       latitud, longitud, telefono1
FROM cliente
WHERE latitud IS NOT NULL AND longitud IS NOT NULL;
*/

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  FIN DEL SCRIPT  |  BaseApp v1.0
-- ============================================================