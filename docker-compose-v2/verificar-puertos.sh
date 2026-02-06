#!/bin/bash
echo "=========================================="
echo "   VERIFICACIÓN CON NUEVOS PUERTOS"
echo "=========================================="

echo ""
echo "1. CONTENEDORES:"
docker compose ps
echo ""

echo "2. APACHE (nuevo puerto 8081):"
curl -s -o /dev/null -w "HTTP %{http_code}\n" http://localhost:8081
echo "URL: http://localhost:8081"
echo ""

echo "3. PHPMYADMIN (nuevo puerto 8082):"
curl -s -o /dev/null -w "HTTP %{http_code}\n" http://localhost:8082
echo "URL: http://localhost:8082"
echo "Usuario: usuario_web"
echo "Contraseña: ClaveSegura456"
echo ""

echo "4. MARIADB (nuevo puerto 3308):"
mysql -h 127.0.0.1 -P 3308 -u root -pAdmin123! --skip-ssl -e "SHOW DATABASES;" 2>/dev/null && echo "✅ MariaDB accesible en puerto 3308" || echo "❌ No se pudo conectar a MariaDB"
echo ""

echo "5. ACCESO DESDE CONTENEDOR:"
docker compose exec mariadb mysql -u usuario_web -pClaveSegura456 mi_empresa -e "
SELECT '✅ Base de datos funcionando' as mensaje;
SELECT 'Estudiantes:' as '', COUNT(*) as total FROM estudiantes;
SELECT 'Cursos:' as '', COUNT(*) as total FROM cursos;
SELECT 'Matrículas:' as '', COUNT(*) as total FROM matriculas;
" 2>/dev/null || echo "❌ Error al conectar desde contenedor"
echo ""

echo "6. TU INFORMACIÓN:"
docker compose exec mariadb mysql -u usuario_web -pClaveSegura456 mi_empresa -e "
SELECT CONCAT('👤 ', nombre, ' ', apellido) as 'Nombre' FROM estudiantes WHERE carnet = 'SG001';
SELECT CONCAT('🎓 ', carrera) as 'Carrera' FROM estudiantes WHERE carnet = 'SG001';
SELECT CONCAT('📧 ', email) as 'Email' FROM estudiantes WHERE carnet = 'SG001';
" 2>/dev/null || echo "⚠️  No se pudo obtener información"
echo ""

echo "=========================================="
echo "   ACCESOS FINALES"
echo "=========================================="
echo "🌐 SISTEMA WEB:      http://localhost:8081"
echo "📊 PHPMYADMIN:       http://localhost:8082"
echo "🗄️  MARIADB:          localhost:3308"
echo "👤 USUARIO BD:       usuario_web"
echo "🔑 CONTRASEÑA BD:    ClaveSegura456"
echo "👑 USUARIO ROOT:     root / Admin123!"
echo "👤 TU NOMBRE:        Izan Gómez"
echo "=========================================="
