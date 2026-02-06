<?php
// Configuración
$host = 'mariadb';
$db   = 'mi_empresa';
$user = 'usuario_web';
$pass = 'ClaveSegura456';
$charset = 'utf8mb4';

// Opciones de PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Intentar conexión
try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, $options);
    $conexion_ok = true;
    $error_msg = '';
} catch (\PDOException $e) {
    $conexion_ok = false;
    $error_msg = $e->getMessage();
    $pdo = null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Académico - Docker</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        tr:hover { background: #f5f5f5; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 Sistema Académico con Docker Compose</h1>
        <p>Apache + MariaDB + PHP + phpMyAdmin</p>
        
        <div class="info">
            <h3>📊 Estado del Sistema</h3>
            <?php if ($conexion_ok): ?>
                <p class="success">✅ Conexión exitosa a la base de datos</p>
                <p><strong>Host:</strong> <?php echo $host; ?></p>
                <p><strong>Base de datos:</strong> <?php echo $db; ?></p>
                <p><strong>Usuario:</strong> <?php echo $user; ?></p>
                
                <?php
                // Verificar si las tablas existen
                try {
                    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                    if (count($tables) > 0) {
                        echo "<p class='success'>✅ Tablas encontradas: " . count($tables) . "</p>";
                    } else {
                        echo "<p class='warning'>⚠️ Base de datos vacía. Las tablas no se crearon.</p>";
                    }
                } catch (PDOException $e) {
                    echo "<p class='error'>❌ Error al verificar tablas: " . $e->getMessage() . "</p>";
                }
                ?>
                
            <?php else: ?>
                <p class="error">❌ Error de conexión: <?php echo $error_msg; ?></p>
            <?php endif; ?>
        </div>
        
        <?php if ($conexion_ok): ?>
        
        <!-- Verificar y crear tablas si no existen -->
        <?php
        // Lista de tablas necesarias
        $tablas_necesarias = ['estudiantes', 'cursos', 'matriculas'];
        $tablas_faltantes = [];
        
        foreach ($tablas_necesarias as $tabla) {
            try {
                $pdo->query("SELECT 1 FROM $tabla LIMIT 1");
            } catch (PDOException $e) {
                $tablas_faltantes[] = $tabla;
            }
        }
        
        if (!empty($tablas_faltantes)): ?>
        <div class="warning">
            <h3>⚠️ Tablas faltantes</h3>
            <p>Las siguientes tablas no existen: <?php echo implode(', ', $tablas_faltantes); ?></p>
            <a href="crear-tablas.php" class="btn">Crear tablas automáticamente</a>
        </div>
        <?php else: ?>
        
        <!-- Tu información -->
        <div class="info">
            <h3>👤 Tu Información</h3>
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM estudiantes WHERE carnet = 'SG001'");
                $tu_info = $stmt->fetch();
                
                if ($tu_info):
            ?>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($tu_info['nombre'] . ' ' . $tu_info['apellido']); ?></p>
            <p><strong>Carnet:</strong> <?php echo htmlspecialchars($tu_info['carnet']); ?></p>
            <p><strong>Carrera:</strong> <?php echo htmlspecialchars($tu_info['carrera']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($tu_info['email']); ?></p>
            <?php
                else:
                    echo "<p>No se encontró tu información en la base de datos.</p>";
                endif;
            } catch (PDOException $e) {
                echo "<p class='error'>Error al obtener tu información: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
        
        <!-- Estadísticas -->
        <div class="info">
            <h3>📈 Estadísticas del Sistema</h3>
            <?php
            try {
                $estudiantes = $pdo->query("SELECT COUNT(*) as total FROM estudiantes")->fetch()['total'];
                echo "<p>Total estudiantes: $estudiantes</p>";
                
                $cursos = $pdo->query("SELECT COUNT(*) as total FROM cursos")->fetch()['total'];
                echo "<p>Total cursos: $cursos</p>";
                
                $matriculas = $pdo->query("SELECT COUNT(*) as total FROM matriculas")->fetch()['total'];
                echo "<p>Total matrículas: $matriculas</p>";
                
            } catch (PDOException $e) {
                echo "<p class='error'>Error en estadísticas: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
        
        <!-- Lista de estudiantes -->
        <h3>🎓 Lista de Estudiantes</h3>
        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM estudiantes ORDER BY apellido");
            if ($stmt->rowCount() > 0):
        ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Carnet</th>
                <th>Carrera</th>
            </tr>
            <?php while ($row = $stmt->fetch()): ?>
            <tr <?php echo ($row['carnet'] == 'SG001') ? 'style="background: #fffacd;"' : ''; ?>>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                <td><strong><?php echo htmlspecialchars($row['carnet']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['carrera']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php
            else:
                echo "<p>No hay estudiantes registrados.</p>";
            endif;
        } catch (PDOException $e) {
            echo "<p class='error'>Error al listar estudiantes: " . $e->getMessage() . "</p>";
        }
        ?>
        
        <?php endif; // Fin del if de tablas existentes ?>
        
        <!-- Información del sistema -->
        <div class="info">
            <h3>⚙️ Información Técnica</h3>
            <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
            <p><strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
        </div>
        
        <?php 
        endif; // Fin del if de conexión exitosa 
        
        if ($pdo) {
            $pdo = null;
        }
        ?>
        
        <!-- Accesos -->
        <div class="info">
            <h3>🔗 Accesos del Sistema</h3>
            <p><strong>Esta página:</strong> http://localhost:8090</p>
            <p><strong>phpMyAdmin:</strong> <a href="http://localhost:8091" target="_blank">http://localhost:8091</a></p>
            <p><strong>Usuario BD:</strong> usuario_web</p>
            <p><strong>Contraseña BD:</strong> ClaveSegura456</p>
            <p><strong>Base de datos:</strong> mi_empresa</p>
            <a href="http://localhost:8091" class="btn" target="_blank">Abrir phpMyAdmin</a>
        </div>
        
        <footer style="text-align: center; margin-top: 40px; color: #666;">
            <p>Sistema desarrollado con Docker Compose - Izan Gómez</p>
        </footer>
    </div>
</body>
</html>
