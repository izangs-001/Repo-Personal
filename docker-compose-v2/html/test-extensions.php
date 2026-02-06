<?php
echo "<h1>Extensiones PHP instaladas</h1>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";

// Verificar específicamente PDO
echo "<h2>Verificación PDO MySQL</h2>";
if (extension_loaded('pdo_mysql')) {
    echo "<p style='color:green;'>✅ pdo_mysql está instalado</p>";
} else {
    echo "<p style='color:red;'>❌ pdo_mysql NO está instalado</p>";
}

if (extension_loaded('mysqli')) {
    echo "<p style='color:green;'>✅ mysqli está instalado</p>";
} else {
    echo "<p style='color:red;'>❌ mysqli NO está instalado</p>";
}

// Probar conexión
echo "<h2>Prueba de conexión</h2>";
try {
    $pdo = new PDO('mysql:host=mariadb;dbname=mi_empresa;charset=utf8mb4', 
                   'usuario_web', 'ClaveSegura456');
    echo "<p style='color:green;'>✅ Conexión PDO exitosa</p>";
    
    // Probar consulta
    $stmt = $pdo->query("SELECT VERSION() as version");
    $version = $stmt->fetch();
    echo "<p>Versión MySQL: " . $version['version'] . "</p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Error PDO: " . $e->getMessage() . "</p>";
}
?>
