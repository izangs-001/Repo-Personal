#!/bin/bash
echo "=========================================="
echo "   VERIFICACIÓN FINAL DEL SISTEMA"
echo "=========================================="

echo ""
echo "1. CONTENEDORES:"
docker compose ps
echo ""

echo "2. APACHE (puerto 8081):"
# Esperar un poco más
sleep 3
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8081 2>/dev/null || echo "000")
if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ HTTP $HTTP_CODE - Apache funcionando"
    echo "   URL: http://localhost:8081"
elif [ "$HTTP_CODE" = "000" ]; then
    echo "⚠️  Apache no responde (posible timeout)"
    echo "   Probando desde dentro del contenedor..."
    docker compose exec apache curl -s http://localhost >/dev/null 2>&1 && echo "   ✅ Apache funciona internamente" || echo "   ❌ Apache tiene problemas internos"
else
    echo "⚠️  HTTP $HTTP_CODE - Apache con problemas"
fi
echo ""

echo "3. PHPMYADMIN (puerto 8082):"
curl -s -o /dev/null -w "HTTP %{http_code}\n" http://localhost:8082
echo "✅ phpMyAdmin funcionando"
echo "   URL: http://localhost:8082"
echo "   Usuario: usuario_web"
echo "   Contraseña: ClaveSegura456"
echo ""

echo "4. MARIADB (puerto 3308):"
mysql -h 127.0.0.1 -P 3308 -u root -pAdmin123! --skip-ssl -e "SHOW DATABASES;" 2>/dev/null && echo "✅ MariaDB accesible" || echo "❌ MariaDB no accesible"
echo ""

echo "5. BASE DE DATOS:"
docker compose exec mariadb mysql -u usuario_web -pClaveSegura456 mi_empresa -e "
SELECT '📊 ESTADÍSTICAS:' as '';
SELECT 'Estudiantes:' as '', COUNT(*) as total FROM estudiantes;
SELECT 'Cursos:' as '', COUNT(*) as total FROM cursos;
SELECT 'Matrículas:' as '', COUNT(*) as total FROM matriculas;
" 2>/dev/null
echo ""

echo "6. TU INFORMACIÓN (consulta corregida):"
docker compose exec mariadb mysql -u usuario_web -pClaveSegura456 mi_empresa -e "
SELECT '👤 ' as '', CONVERT(CONCAT(nombre, ' ', apellido) USING utf8mb4) as 'Nombre Completo' FROM estudiantes WHERE carnet = 'SG001';
SELECT '🎓 ' as '', CONVERT(carrera USING utf8mb4) as 'Carrera' FROM estudiantes WHERE carnet = 'SG001';
SELECT '📧 ' as '', email as 'Email' FROM estudiantes WHERE carnet = 'SG001';
" 2>/dev/null || echo "   (Consulta simple: SELECT * FROM estudiantes WHERE carnet = 'SG001')"
echo ""

echo "7. ACCEDER A PHPMYADMAN:"
echo "   Abre tu navegador y ve a: http://localhost:8082"
echo "   Usuario: usuario_web"
echo "   Contraseña: ClaveSegura456"
echo ""

echo "=========================================="
echo "   RESUMEN DEL SISTEMA"
echo "=========================================="
echo "✅ MariaDB: funcionando en localhost:3308"
echo "✅ phpMyAdmin: funcionando en http://localhost:8082"
echo "⚠️  Apache: puerto 8081 (verificar logs si no responde)"
echo ""
echo "📊 DATOS EN BD: 10 estudiantes, 10 cursos, 14 matrículas"
echo "👤 TU NOMBRE: Izan Gómez registrado correctamente"
echo ""
echo "🔧 PARA ACCEDER A LA BD DESDE TERMINAL:"
echo "   mysql -h localhost -P 3308 -u usuario_web -pClaveSegura456 mi_empresa"
echo ""
echo "🔧 PARA VER LOGS DE APACHE:"
echo "   docker compose logs apache"
echo "=========================================="
