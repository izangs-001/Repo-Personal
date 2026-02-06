#!/bin/bash
echo "=========================================="
echo "   INICIANDO SISTEMA DEFINITIVO"
echo "=========================================="

cd ~/docker-compose-v2

echo "1. Deteniendo servicios anteriores..."
docker compose down 2>/dev/null || true

echo "2. Limpiando contenedores viejos..."
docker rm -f mariadb-final apache-final phpmyadmin-final 2>/dev/null || true

echo "3. Iniciando servicios..."
docker compose up -d --build

echo "4. Esperando que los servicios inicien..."
sleep 25

echo "5. Verificando estado..."
docker compose ps

echo ""
echo "6. Probando conexiones..."
echo "Apache (PHP):"
curl -s -o /dev/null -w "HTTP %{http_code}\n" http://localhost:8090

echo ""
echo "phpMyAdmin:"
curl -s -o /dev/null -w "HTTP %{http_code}\n" http://localhost:8091

echo ""
echo "7. Verificando extensiones PHP..."
docker compose exec apache php -m | grep -i pdo

echo ""
echo "=========================================="
echo "   ✅ SISTEMA INICIADO"
echo "=========================================="
echo "🌐 Apache PHP:    http://localhost:8090"
echo "📊 phpMyAdmin:    http://localhost:8091"
echo "🗄️  MariaDB:       localhost:3309"
echo "👤 Usuario BD:    usuario_web"
echo "🔑 Contraseña:    ClaveSegura456"
echo "👤 Tu nombre:     Izan Gómez"
echo "=========================================="
