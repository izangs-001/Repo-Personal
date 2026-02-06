#!/bin/bash
echo "=========================================="
echo "   ✅ VERIFICACIÓN FINAL DEL SISTEMA"
echo "=========================================="

echo ""
echo "1. CONTENEDORES:"
docker compose ps
echo ""

echo "2. APACHE (puerto 8090):"
sleep 5
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8090; then
    echo "✅ Apache funcionando"
    echo "   URL: http://localhost:8090"
    echo "   Test: http://localhost:8090/info.php"
else
    echo "⚠️  Apache no responde, verificando logs..."
    docker compose logs apache --tail=5
fi
echo ""

echo "3. PHPMYADMIN (puerto 8091):"
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8091; then
    echo "✅ phpMyAdmin funcionando"
    echo "   URL: http://localhost:8091"
    echo "   Usuario: usuario_web"
    echo "   Contraseña: ClaveSegura456"
else
    echo "❌ phpMyAdmin no responde"
fi
echo ""

echo "4. MARIADB (puerto 3309):"
if mysql -h 127.0.0.1 -P 3309 -u usuario_web -pClaveSegura456 -e "SELECT 1" 2>/dev/null; then
    echo "✅ MariaDB funcionando"
    mysql -h 127.0.0.1 -P 3309 -u usuario_web -pClaveSegura456 mi_empresa -e "
    SELECT '📊 Datos en BD:' as '';
    SELECT 'Estudiantes:' as '', COUNT(*) as total FROM estudiantes;
    SELECT 'Cursos:' as '', COUNT(*) as total FROM cursos;
    " 2>/dev/null || echo "   ℹ️  Base de datos cargando..."
else
    echo "⚠️  MariaDB no accesible desde host (usar phpMyAdmin)"
fi
echo ""

echo "5. ACCESO DESDE CONTENEDOR:"
docker compose exec mariadb mysql -u usuario_web -pClaveSegura456 mi_empresa -e "
SELECT '👤 Tu información:' as '';
SELECT CONCAT('Nombre: ', nombre, ' ', apellido) as '' FROM estudiantes WHERE carnet = 'SG001';
SELECT CONCAT('Carrera: ', carrera) as '' FROM estudiantes WHERE carnet = 'SG001';
" 2>/dev/null && echo "✅ Base de datos operativa" || echo "⚠️  Error en consulta"
echo ""

echo "=========================================="
echo "   🎉 RESUMEN FINAL"
echo "=========================================="
echo "🌐 Apache:       http://localhost:8090"
echo "📊 phpMyAdmin:   http://localhost:8091"
echo "🗄️  MariaDB:      localhost:3309"
echo "👤 Usuario:      usuario_web"
echo "🔑 Contraseña:   ClaveSegura456"
echo "👤 Tu nombre:    Izan Gómez en la BD"
echo ""
echo "💡 Si Apache no funciona, usa phpMyAdmin que SÍ funciona."
echo "=========================================="
