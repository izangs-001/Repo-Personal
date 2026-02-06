-- Dar permisos globales al usuario
CREATE USER IF NOT EXISTS 'usuario_web'@'%' IDENTIFIED BY 'ClaveSegura456';
GRANT ALL PRIVILEGES ON *.* TO 'usuario_web'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;

-- Crear base de datos con collation correcta
CREATE DATABASE IF NOT EXISTS mi_empresa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mi_empresa;

-- Tabla de estudiantes
CREATE TABLE IF NOT EXISTS estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    carnet VARCHAR(20) UNIQUE NOT NULL,
    carrera VARCHAR(100),
    email VARCHAR(150) UNIQUE NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_carrera (carrera),
    INDEX idx_apellido (apellido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ... (resto del script igual que antes)
