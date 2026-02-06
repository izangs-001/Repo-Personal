#!/bin/bash
echo "=========================================="
echo "   REINICIANDO SISTEMA COMPLETO"
echo "=========================================="

cd ~/docker-compose-v2

echo "1. Deteniendo servicios..."
docker compose down 2>/dev/null || true

echo "2. Eliminando contenedores..."
docker rm -f mariadb-final apache-final phpmyadmin-final 2>/dev/null || true

echo "3. Eliminando volúmenes..."
docker volume prune -f

echo "4. Creando script SQL de inicialización..."
mkdir -p db/init
cat > db/init/01-create-database.sql << 'SQL'
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
SQL

echo "5. Iniciando servicios..."
docker compose up -d --build

echo "6. Esperando inicialización..."
sleep 25

echo "7. Verificando..."
docker compose ps

echo ""
echo "8. Probando base de datos..."
docker compose exec mariadb mysql -u usuario_web -pClaveSegura456 mi_empresa -e "
SHOW TABLES;
SELECT '--- Tu información ---' as '';
SELECT * FROM estudiantes WHERE carnet = 'SG001';
SELECT '--- Conteo ---' as '';
SELECT 'Estudiantes:' as '', COUNT(*) as total FROM estudiantes;
SELECT 'Cursos:' as '', COUNT(*) as total FROM cursos;
SELECT 'Matrículas:' as '', COUNT(*) as total FROM matriculas;
"

echo ""
echo "9. Probando Apache..."
curl -s -o /dev/null -w "Apache: HTTP %{http_code}\n" http://localhost:8090
curl -s -o /dev/null -w "phpMyAdmin: HTTP %{http_code}\n" http://localhost:8091

echo ""
echo "=========================================="
echo "   ✅ SISTEMA REINICIADO COMPLETAMENTE"
echo "=========================================="
echo "🌐 Apache:       http://localhost:8090"
echo "📊 phpMyAdmin:   http://localhost:8091"
echo "🗄️  MariaDB:      localhost:3309"
echo "👤 Tu nombre:    Izan Gómez (SG001)"
echo "=========================================="
