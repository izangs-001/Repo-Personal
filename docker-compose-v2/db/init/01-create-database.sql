CREATE DATABASE IF NOT EXISTS mi_empresa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mi_empresa;

CREATE TABLE estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    carnet VARCHAR(20) UNIQUE NOT NULL,
    carrera VARCHAR(100),
    email VARCHAR(150) UNIQUE NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    creditos INT DEFAULT 3,
    profesor VARCHAR(150),
    semestre VARCHAR(20),
    cupo INT DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE matriculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT,
    curso_id INT,
    fecha_matricula TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    nota_final DECIMAL(4,2),
    estado ENUM('activo', 'completado', 'retirado') DEFAULT 'activo',
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id),
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO estudiantes (nombre, apellido, carnet, carrera, email) VALUES
('Izan', 'Gómez', 'SG001', 'Ingeniería en Sistemas', 'izan.gomez@universidad.edu'),
('María', 'López', 'ML002', 'Administración', 'maria.lopez@universidad.edu');

INSERT INTO cursos (nombre, codigo, creditos, profesor) VALUES
('Programación Web', 'PW101', 4, 'Dr. Pérez'),
('Bases de Datos', 'BD102', 4, 'Dra. González'),
('Redes', 'RC103', 3, 'Ing. Martínez');

INSERT INTO matriculas (estudiante_id, curso_id, nota_final, estado) VALUES
(1, 1, 8.5, 'completado'),
(1, 2, 9.0, 'completado'),
(2, 1, 7.8, 'completado');

SELECT '✅ Base de datos inicializada' as mensaje;
