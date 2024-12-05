-- Active: 1731922560437@@127.0.0.1@3306@salonbelleza
DROP DATABASE salonbelleza;
CREATE DATABASE IF NOT EXISTS salonbelleza;
SET NAMES utf8mb4;
USE salonbelleza;

-- Tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id              INT AUTO_INCREMENT NOT NULL,
    nombre          VARCHAR(100) NOT NULL,
    correo          VARCHAR(255) NOT NULL,
    telefono        VARCHAR(15) NOT NULL,
    fecha_nacimiento DATE DEFAULT NULL,
    password        VARCHAR(255) NOT NULL,
    CONSTRAINT pk_clientes PRIMARY KEY (id),
    UNIQUE KEY correo_UNIQUE (correo),
    UNIQUE KEY telefono_UNIQUE (telefono)
);

-- Tabla de especialidades
CREATE TABLE IF NOT EXISTS especialidades (
    id              INT AUTO_INCREMENT NOT NULL,
    nombre          VARCHAR(100) NOT NULL,
    CONSTRAINT pk_especialidades PRIMARY KEY (id),
    UNIQUE KEY nombre_UNIQUE (nombre)
);

-- Tabla de empleados
CREATE TABLE IF NOT EXISTS empleados (
    id              INT AUTO_INCREMENT NOT NULL,
    nombre          VARCHAR(100) NOT NULL,
    correo          VARCHAR(255) NOT NULL,
    telefono        VARCHAR(15) NOT NULL,
    password        VARCHAR(255) NOT NULL, -- Contraseña encriptada para iniciar sesión
    rol             ENUM('empleado', 'administrador') DEFAULT 'empleado',
    id_especialidad INT NOT NULL, -- Relación con la especialidad (solo una especialidad por empleado)
    CONSTRAINT pk_empleados PRIMARY KEY (id),
    UNIQUE KEY correo_UNIQUE (correo),
    UNIQUE KEY telefono_UNIQUE (telefono),
    CONSTRAINT fk_empleados_especialidad FOREIGN KEY (id_especialidad) REFERENCES especialidades (id) ON DELETE CASCADE
);

-- Tabla de servicios
CREATE TABLE IF NOT EXISTS servicios (
    id              INT AUTO_INCREMENT NOT NULL,
    nombre          VARCHAR(100) NOT NULL,
    precio          DECIMAL(10, 2) NOT NULL,
    duracion_minutos INT NOT NULL,
    id_especialidad INT NOT NULL, -- Relación con especialidad
    CONSTRAINT pk_servicios PRIMARY KEY (id),
    UNIQUE KEY nombre_UNIQUE (nombre),
    CONSTRAINT fk_servicios_especialidades FOREIGN KEY (id_especialidad) REFERENCES especialidades (id) ON DELETE CASCADE
);

-- Tabla de citas
CREATE TABLE IF NOT EXISTS citas (
    id              INT AUTO_INCREMENT NOT NULL,
    id_cliente      INT NOT NULL,
    id_empleado     INT NOT NULL,
    id_servicio     INT NOT NULL, -- Relación con el servicio solicitado
    fecha_cita      DATE NOT NULL,
    hora_cita       TIME NOT NULL,
    precio_total    DECIMAL(10, 2) NOT NULL,
    CONSTRAINT pk_citas PRIMARY KEY (id),
    CONSTRAINT fk_citas_clientes FOREIGN KEY (id_cliente) REFERENCES clientes (id) ON DELETE CASCADE,
    CONSTRAINT fk_citas_empleados FOREIGN KEY (id_empleado) REFERENCES empleados (id) ON DELETE CASCADE,
    CONSTRAINT fk_citas_servicios FOREIGN KEY (id_servicio) REFERENCES servicios (id) ON DELETE CASCADE
);

-- Tabla de historial de servicios
CREATE TABLE IF NOT EXISTS historial_servicios (
    id              INT AUTO_INCREMENT NOT NULL,
    id_cliente      INT NOT NULL,
    detalles_servicios TEXT NOT NULL,
    fecha_atencion  DATE NOT NULL,
    precio_total    DECIMAL(10, 2) NOT NULL,
    CONSTRAINT pk_historial PRIMARY KEY (id),
    CONSTRAINT fk_historial_clientes FOREIGN KEY (id_cliente) REFERENCES clientes (id) ON DELETE CASCADE
);

-- Insert para las especialidades
INSERT INTO especialidades (nombre) VALUES 
('Estilista'), 
('Masajista'),
('Manicura'),
('Pedicura'),
('Colorista'),
('Tratamientos Faciales'),
('Barbero'),
('Depilación'),
('Maquillaje'),
('Asesoría de Imagen');

-- Insertar para los servicios
INSERT INTO servicios (nombre, precio, duracion_minutos, id_especialidad) VALUES 
('Corte de Caballero', 15.00, 30, 7),  -- Barbero
('Ajuste de Barba', 8.00, 20, 7),      -- Barbero
('Corte de Dama', 25.00, 45, 1),       -- Estilista
('Peinado Básico', 12.00, 30, 1),      -- Estilista
('Peinado de Fiesta', 30.00, 60, 1),   -- Estilista
('Tinte de Cabello', 40.00, 90, 5),    -- Colorista
('Mechas', 50.00, 120, 5),             -- Colorista
('Manicura Clásica', 20.00, 30, 3),    -- Manicura
('Manicura Gel', 30.00, 45, 3),        -- Manicura
('Pedicura Clásica', 25.00, 45, 4),    -- Pedicura
('Pedicura Spa', 40.00, 60, 4),        -- Pedicura
('Masaje Relajante', 35.00, 60, 2),    -- Masajista
('Masaje Terapéutico', 50.00, 75, 2),  -- Masajista
('Depilación Cera', 15.00, 30, 8),     -- Depilación
('Depilación Láser', 60.00, 120, 8),   -- Depilación
('Maquillaje Social', 40.00, 60, 9),   -- Maquillaje
('Maquillaje de Novia', 80.00, 120, 9),-- Maquillaje
('Asesoría de Imagen', 60.00, 90, 10); -- Asesoría de Imagen