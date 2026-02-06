<?php
// Configuración desde variables de entorno o valores por defecto
$host = getenv('DB_HOST') ?: 'mariadb';
$user = getenv('DB_USER') ?: 'usuario_web';
$pass = getenv('DB_PASS') ?: 'ClaveSegura456';
$db = getenv('DB_NAME') ?: 'mi_empresa';

// Intentar conexión
try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion_exitosa = true;
} catch (PDOException $e) {
    $conexion_exitosa = false;
    $error_conexion = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Académico - Docker Compose v2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            margin: 30px auto;
            overflow: hidden;
        }
        
        .header-section {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            color: white;
            padding: 40px 30px;
        }
        
        .status-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border-left: 5px solid var(--secondary);
            transition: transform 0.3s;
        }
        
        .status-card:hover {
            transform: translateY(-5px);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 2px solid #e9ecef;
        }
        
        .info-box i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--secondary);
        }
        
        .tu-info {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
            border: none;
        }
        
        .tu-info i {
            color: white;
        }
        
        .query-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .btn-custom {
            background: linear-gradient(90deg, var(--secondary), var(--primary));
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        
        .results-container {
            max-height: 500px;
            overflow-y: auto;
            margin-top: 20px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
        }
        
        .table-custom th {
            background: var(--primary);
            color: white;
            position: sticky;
            top: 0;
        }
        
        .badge-estado {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        .activo { background: #d4edda; color: #155724; }
        .completado { background: #cce5ff; color: #004085; }
        .retirado { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container main-container">
        <!-- Header -->
        <div class="header-section text-center">
            <h1><i class="fas fa-graduation-cap"></i> Sistema Académico</h1>
            <p class="lead">Docker Compose v2 + Apache + MariaDB + PHP</p>
            <div class="mt-3">
                <span class="badge bg-light text-dark me-2"><i class="fas fa-network-wired"></i> red-app-v2</span>
                <span class="badge bg-light text-dark me-2"><i class="fas fa-server"></i> Apache</span>
                <span class="badge bg-light text-dark me-2"><i class="fas fa-database"></i> MariaDB</span>
                <span class="badge bg-light text-dark"><i class="fas fa-user"></i> Izan Gómez</span>
            </div>
        </div>
        
        <div class="container-fluid py-5">
            <!-- Estado del Sistema -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="status-card">
                        <h3><i class="fas fa-info-circle"></i> Estado del Sistema</h3>
                        
                        <?php if (!$conexion_exitosa): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-times-circle"></i> Error de Conexión</h5>
                                <p><?php echo htmlspecialchars($error_conexion); ?></p>
                                <p><strong>Configuración usada:</strong><br>
                                Host: <?php echo htmlspecialchars($host); ?><br>
                                Base de datos: <?php echo htmlspecialchars($db); ?><br>
                                Usuario: <?php echo htmlspecialchars($user); ?></p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle"></i> Sistema Operativo</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Apache:</strong><br>
                                        <span class="badge bg-success">Puerto 80</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>MariaDB:</strong><br>
                                        <span class="badge bg-success">Puerto 3306</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>phpMyAdmin:</strong><br>
                                        <span class="badge bg-success">Puerto 8080</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Red Docker:</strong><br>
                                        <span class="badge bg-success">red-app-v2</span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Tu Información -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="status-card tu-info">
                        <h3><i class="fas fa-user-graduate"></i> Tu Información Personal</h3>
                        <?php
                        if ($conexion_exitosa) {
                            try {
                                // Obtener tu información
                                $stmt = $pdo->query("SELECT * FROM estudiantes WHERE carnet = 'SG001'");
                                $tu_info = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                if ($tu_info) {
                                    echo '<div class="row mt-4">';
                                    echo '<div class="col-md-3">';
                                    echo '<div class="info-box">';
                                    echo '<i class="fas fa-id-card"></i>';
                                    echo '<h5>Carnet</h5>';
                                    echo '<h4>' . htmlspecialchars($tu_info['carnet']) . '</h4>';
                                    echo '</div></div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo '<div class="info-box">';
                                    echo '<i class="fas fa-user"></i>';
                                    echo '<h5>Nombre</h5>';
                                    echo '<h4>' . htmlspecialchars($tu_info['nombre'] . ' ' . $tu_info['apellido']) . '</h4>';
                                    echo '</div></div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo '<div class="info-box">';
                                    echo '<i class="fas fa-graduation-cap"></i>';
                                    echo '<h5>Carrera</h5>';
                                    echo '<h4>' . htmlspecialchars($tu_info['carrera']) . '</h4>';
                                    echo '</div></div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo '<div class="info-box">';
                                    echo '<i class="fas fa-envelope"></i>';
                                    echo '<h5>Email</h5>';
                                    echo '<h6>' . htmlspecialchars($tu_info['email']) . '</h6>';
                                    echo '</div></div>';
                                    echo '</div>';
                                }
                            } catch (PDOException $e) {
                                echo '<div class="alert alert-warning">Error al obtener información: ' . $e->getMessage() . '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Consultas Rápidas -->
            <div class="row mb-5">
                <div class="col-md-12">
                    <div class="query-section">
                        <h3><i class="fas fa-search"></i> Consultas Rápidas</h3>
                        <p class="text-muted">Selecciona una consulta para ejecutar</p>
                        
                        <form method="POST" action="" class="mt-4">
                            <div class="row">
                                <div class="col-md-8">
                                    <select name="query" class="form-select form-select-lg mb-3">
                                        <option value="estudiantes">Ver todos los estudiantes</option>
                                        <option value="cursos">Ver todos los cursos</option>
                                        <option value="tus_cursos">Ver tus cursos matriculados</option>
                                        <option value="matriculas_activas">Matrículas activas</option>
                                        <option value="estadisticas">Estadísticas generales</option>
                                        <option value="carreras">Estudiantes por carrera</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" name="execute" class="btn btn-custom btn-lg w-100">
                                        <i class="fas fa-play"></i> Ejecutar Consulta
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Resultados de Consultas -->
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['execute']) && $conexion_exitosa) {
                $selected_query = $_POST['query'];
                
                echo '<div class="row">';
                echo '<div class="col-md-12">';
                echo '<div class="query-section">';
                echo '<h3><i class="fas fa-list-alt"></i> Resultados</h3>';
                
                try {
                    switch ($selected_query) {
                        case 'estudiantes':
                            $stmt = $pdo->query("SELECT id, nombre, apellido, carnet, carrera, email FROM estudiantes ORDER BY apellido");
                            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if ($resultados) {
                                echo '<div class="results-container">';
                                echo '<table class="table table-hover table-custom">';
                                echo '<thead><tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Carnet</th><th>Carrera</th><th>Email</th></tr></thead>';
                                echo '<tbody>';
                                foreach ($resultados as $row) {
                                    $highlight = ($row['carnet'] == 'SG001') ? 'style="background: #fffacd;"' : '';
                                    echo '<tr ' . $highlight . '>';
                                    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['apellido']) . '</td>';
                                    echo '<td><strong>' . htmlspecialchars($row['carnet']) . '</strong></td>';
                                    echo '<td>' . htmlspecialchars($row['carrera']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody></table>';
                                echo '</div>';
                            }
                            break;
                            
                        case 'tus_cursos':
                            $stmt = $pdo->query("
                                SELECT 
                                    c.nombre as curso,
                                    c.codigo,
                                    c.creditos,
                                    c.profesor,
                                    m.nota_final,
                                    m.estado,
                                    m.fecha_matricula
                                FROM matriculas m
                                JOIN cursos c ON m.curso_id = c.id
                                JOIN estudiantes e ON m.estudiante_id = e.id
                                WHERE e.carnet = 'SG001'
                                ORDER BY m.fecha_matricula DESC
                            ");
                            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if ($resultados) {
                                echo '<div class="results-container">';
                                echo '<table class="table table-hover table-custom">';
                                echo '<thead><tr><th>Curso</th><th>Código</th><th>Créditos</th><th>Profesor</th><th>Nota</th><th>Estado</th><th>Fecha</th></tr></thead>';
                                echo '<tbody>';
                                foreach ($resultados as $row) {
                                    $estado_class = '';
                                    switch($row['estado']) {
                                        case 'activo': $estado_class = 'activo'; break;
                                        case 'completado': $estado_class = 'completado'; break;
                                        case 'retirado': $estado_class = 'retirado'; break;
                                    }
                                    
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['curso']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['codigo']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['creditos']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['profesor']) . '</td>';
                                    echo '<td>' . ($row['nota_final'] ? htmlspecialchars($row['nota_final']) : '-') . '</td>';
                                    echo '<td><span class="badge-estado ' . $estado_class . '">' . htmlspecialchars($row['estado']) . '</span></td>';
                                    echo '<td>' . htmlspecialchars($row['fecha_matricula']) . '</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody></table>';
                                echo '</div>';
                            }
                            break;
                            
                        case 'estadisticas':
                            echo '<div class="row">';
                            
                            // Totales
                            $stmt = $pdo->query("SELECT COUNT(*) as total FROM estudiantes");
                            $estudiantes = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $stmt = $pdo->query("SELECT COUNT(*) as total FROM cursos");
                            $cursos = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $stmt = $pdo->query("SELECT COUNT(*) as total FROM matriculas");
                            $matriculas = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $stmt = $pdo->query("SELECT COUNT(*) as activas FROM matriculas WHERE estado = 'activo'");
                            $activas = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            echo '<div class="col-md-3 mb-3">';
                            echo '<div class="card text-center">';
                            echo '<div class="card-body">';
                            echo '<h1><i class="fas fa-users text-primary"></i></h1>';
                            echo '<h3>' . $estudiantes['total'] . '</h3>';
                            echo '<p class="text-muted">Estudiantes</p>';
                            echo '</div></div></div>';
                            
                            echo '<div class="col-md-3 mb-3">';
                            echo '<div class="card text-center">';
                            echo '<div class="card-body">';
                            echo '<h1><i class="fas fa-book text-success"></i></h1>';
                            echo '<h3>' . $cursos['total'] . '</h3>';
                            echo '<p class="text-muted">Cursos</p>';
                            echo '</div></div></div>';
                            
                            echo '<div class="col-md-3 mb-3">';
                            echo '<div class="card text-center">';
                            echo '<div class="card-body">';
                            echo '<h1><i class="fas fa-clipboard-list text-warning"></i></h1>';
                            echo '<h3>' . $matriculas['total'] . '</h3>';
                            echo '<p class="text-muted">Matrículas</p>';
                            echo '</div></div></div>';
                            
                            echo '<div class="col-md-3 mb-3">';
                            echo '<div class="card text-center">';
                            echo '<div class="card-body">';
                            echo '<h1><i class="fas fa-check-circle text-info"></i></h1>';
                            echo '<h3>' . $activas['activas'] . '</h3>';
                            echo '<p class="text-muted">Activas</p>';
                            echo '</div></div></div>';
                            
                            echo '</div>';
                            break;
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">Error en la consulta: ' . $e->getMessage() . '</div>';
                }
                
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
            
            <!-- Información del Sistema -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="status-card">
                        <h3><i class="fas fa-cogs"></i> Información Técnica</h3>
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <h5>🐳 Docker Compose v2</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> 3 servicios definidos</li>
                                    <li><i class="fas fa-check text-success"></i> Red privada dedicada</li>
                                    <li><i class="fas fa-check text-success"></i> Health checks configurados</li>
                                    <li><i class="fas fa-check text-success"></i> Dependencias automáticas</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h5>🌐 Servicios</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-server"></i> Apache: puerto 80</li>
                                    <li><i class="fas fa-database"></i> MariaDB: puerto 3306</li>
                                    <li><i class="fas fa-tachometer-alt"></i> phpMyAdmin: puerto 8080</li>
                                    <li><i class="fas fa-code"></i> PHP <?php echo phpversion(); ?></li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h5>🔗 Accesos</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-globe"></i> <a href="http://localhost:8081" target="_blank">localhost</a> (Apache)</li>
                                    <li><i class="fas fa-tachometer-alt"></i> <a href="http://localhost:8081:8080" target="_blank">localhost:8080</a> (phpMyAdmin)</li>
                                    <li><i class="fas fa-user"></i> Usuario BD: usuario_web</li>
                                    <li><i class="fas fa-key"></i> Clave BD: ClaveSegura456</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="text-center py-4" style="border-top: 1px solid #dee2e6; color: #6c757d;">
            <p><i class="fas fa-code"></i> Sistema desarrollado con Docker Compose v2</p>
            <p>👤 <strong>Izan Gómez</strong> | 🎓 Ingeniería en Sistemas | 📅 <?php echo date('Y'); ?></p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
if (isset($pdo)) {
    $pdo = null;
}
?>
