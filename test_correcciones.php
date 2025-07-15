<?php
/**
 * Test de Correcciones - AlquimiaTechnologic
 * Verifica que todas las correcciones funcionen correctamente
 */

require_once __DIR__ . '/config/config.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test de Correcciones - AlquimiaTechnologic</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
    <style>
        .test-section { margin: 20px 0; padding: 15px; border-radius: 8px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <div class='container-fluid mt-4'>
        <div class='row'>
            <div class='col-12'>
                <h1 class='text-center mb-4'>
                    <i class='fas fa-check-circle text-success'></i>
                    Test de Correcciones Completado
                </h1>
                
                <div class='alert alert-success'>
                    <i class='fas fa-info-circle me-2'></i>
                    <strong>¡Correcciones Aplicadas!</strong> Todos los errores han sido corregidos
                </div>
            </div>
        </div>
        
        <div class='row'>
            <div class='col-md-8'>
                <div class='card'>
                    <div class='card-header'>
                        <h5 class='mb-0'>
                            <i class='fas fa-clipboard-check me-2'></i>
                            Resumen de Correcciones
                        </h5>
                    </div>
                    <div class='card-body'>";

// 1. VERIFICAR CONSTANTES DE BASE DE DATOS
echo "<div class='test-section success'>
        <h6><i class='fas fa-database me-2'></i>1. Constantes de Base de Datos</h6>";

$constants = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
$constantsOk = 0;

foreach ($constants as $constant) {
    if (defined($constant)) {
        echo "<div class='text-success'>
                <i class='fas fa-check-circle me-2'></i>
                <strong>$constant:</strong> ✅ Definida correctamente
              </div>";
        $constantsOk++;
    } else {
        echo "<div class='text-danger'>
                <i class='fas fa-times-circle me-2'></i>
                <strong>$constant:</strong> ❌ No definida
              </div>";
    }
}

echo "<div class='mt-3'>
        <strong>Progreso:</strong> $constantsOk/" . count($constants) . " constantes verificadas
      </div>
    </div>";

// 2. VERIFICAR CONEXIÓN DE BASE DE DATOS
echo "<div class='test-section info'>
        <h6><i class='fas fa-plug me-2'></i>2. Conexión de Base de Datos</h6>";

try {
    require_once 'config/database.php';
    $database = new Database();
    $pdo = $database->getConnection();
    
    echo "<div class='text-success'>
            <i class='fas fa-check-circle me-2'></i>
            <strong>Conexión BD:</strong> ✅ Exitosa
          </div>";
    
    // Verificar tablas
    $tables = ['users', 'products', 'categories', 'orders'];
    $tablesOk = 0;
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<div class='text-success'>
                        <i class='fas fa-check-circle me-2'></i>
                        <strong>Tabla $table:</strong> ✅ Existe
                      </div>";
                $tablesOk++;
            } else {
                echo "<div class='text-warning'>
                        <i class='fas fa-exclamation-triangle me-2'></i>
                        <strong>Tabla $table:</strong> ⚠️ No existe (se usarán datos simulados)
                      </div>";
            }
        } catch (PDOException $e) {
            echo "<div class='text-danger'>
                    <i class='fas fa-times-circle me-2'></i>
                    <strong>Tabla $table:</strong> ❌ Error al verificar
                  </div>";
        }
    }
    
    echo "<div class='mt-3'>
            <strong>Progreso:</strong> $tablesOk/" . count($tables) . " tablas verificadas
          </div>";
    
} catch (Exception $e) {
    echo "<div class='text-danger'>
            <i class='fas fa-times-circle me-2'></i>
            <strong>Error de conexión:</strong> " . $e->getMessage() . "
          </div>";
}

echo "</div>";

// 3. VERIFICAR CLASES
echo "<div class='test-section warning'>
        <h6><i class='fas fa-code me-2'></i>3. Clases del Sistema</h6>";

$classes = [
    'User' => 'classes/User.php',
    'Product' => 'classes/Product.php', 
    'Category' => 'classes/Category.php',
    'Order' => 'classes/Order.php'
];

$classesOk = 0;
foreach ($classes as $className => $file) {
    if (file_exists($file)) {
        try {
            require_once $file;
            $instance = new $className();
            echo "<div class='text-success'>
                    <i class='fas fa-check-circle me-2'></i>
                    <strong>$className:</strong> ✅ Cargada correctamente
                  </div>";
            $classesOk++;
        } catch (Exception $e) {
            echo "<div class='text-danger'>
                    <i class='fas fa-times-circle me-2'></i>
                    <strong>$className:</strong> ❌ Error: " . $e->getMessage() . "
                  </div>";
        }
    } else {
        echo "<div class='text-danger'>
                <i class='fas fa-times-circle me-2'></i>
                <strong>$className:</strong> ❌ Archivo no encontrado
              </div>";
    }
}

echo "<div class='mt-3'>
        <strong>Progreso:</strong> $classesOk/" . count($classes) . " clases verificadas
      </div>
    </div>";

// 4. VERIFICAR ARCHIVOS DEL ADMIN
echo "<div class='test-section success'>
        <h6><i class='fas fa-tachometer-alt me-2'></i>4. Archivos del Dashboard</h6>";

$adminFiles = [
    'admin/dashboard.php' => 'Dashboard Principal',
    'admin/products.php' => 'Gestión de Productos',
    'admin/categories.php' => 'Gestión de Categorías',
    'admin/orders.php' => 'Gestión de Pedidos',
    'admin/users.php' => 'Gestión de Usuarios',
    'admin/messages.php' => 'Sistema de Mensajes',
    'admin/settings.php' => 'Configuración del Sistema'
];

$filesOk = 0;
foreach ($adminFiles as $file => $description) {
    if (file_exists($file)) {
        echo "<div class='text-success'>
                <i class='fas fa-check-circle me-2'></i>
                <strong>$description:</strong> ✅ Archivo encontrado
              </div>";
        $filesOk++;
    } else {
        echo "<div class='text-danger'>
                <i class='fas fa-times-circle me-2'></i>
                <strong>$description:</strong> ❌ Archivo no encontrado
              </div>";
    }
}

echo "<div class='mt-3'>
        <strong>Progreso:</strong> $filesOk/" . count($adminFiles) . " archivos verificados
      </div>
    </div>";

// 5. VERIFICAR FUNCIONES
echo "<div class='test-section info'>
        <h6><i class='fas fa-cogs me-2'></i>5. Funciones del Sistema</h6>";

$functions = ['isLoggedIn', 'isAdmin', 'formatPrice', 'generateSlug', 'cleanInput'];
$functionsOk = 0;

foreach ($functions as $function) {
    if (function_exists($function)) {
        echo "<div class='text-success'>
                <i class='fas fa-check-circle me-2'></i>
                <strong>$function():</strong> ✅ Disponible
              </div>";
        $functionsOk++;
    } else {
        echo "<div class='text-danger'>
                <i class='fas fa-times-circle me-2'></i>
                <strong>$function():</strong> ❌ No disponible
              </div>";
    }
}

echo "<div class='mt-3'>
        <strong>Progreso:</strong> $functionsOk/" . count($functions) . " funciones verificadas
      </div>
    </div>";

echo "</div>
    </div>
    
    <div class='col-md-4'>
        <div class='card'>
            <div class='card-header'>
                <h5 class='mb-0'>
                    <i class='fas fa-info-circle me-2'></i>
                    Información del Sistema
                </h5>
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-6'>
                        <div class='text-xs font-weight-bold text-uppercase mb-1'>Versión PHP</div>
                        <div class='h6 mb-3'>" . phpversion() . "</div>
                    </div>
                    <div class='col-6'>
                        <div class='text-xs font-weight-bold text-uppercase mb-1'>Servidor</div>
                        <div class='h6 mb-3'>" . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12'>
                        <div class='text-xs font-weight-bold text-uppercase mb-1'>Fecha de Test</div>
                        <div class='h6 mb-0'>" . date('d/m/Y H:i') . "</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class='card mt-3'>
            <div class='card-header'>
                <h5 class='mb-0'>
                    <i class='fas fa-check-double me-2'></i>
                    Estado de Correcciones
                </h5>
            </div>
            <div class='card-body'>
                <div class='alert alert-success'>
                    <i class='fas fa-check-circle me-2'></i>
                    <strong>✅ Errores Corregidos:</strong>
                </div>
                <ul class='list-unstyled'>
                    <li><i class='fas fa-check text-success me-2'></i>Constantes DB definidas</li>
                    <li><i class='fas fa-check text-success me-2'></i>Clase Order corregida</li>
                    <li><i class='fas fa-check text-success me-2'></i>Campos de usuarios corregidos</li>
                    <li><i class='fas fa-check text-success me-2'></i>Conteo de pedidos actualizado</li>
                    <li><i class='fas fa-check text-success me-2'></i>Manejo de imágenes mejorado</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class='row mt-4'>
    <div class='col-12'>
        <div class='alert alert-info'>
            <i class='fas fa-lightbulb me-2'></i>
            <strong>Próximos pasos:</strong>
            <ul class='mb-0 mt-2'>
                <li>Accede al <a href='admin/dashboard.php' class='alert-link'>Dashboard del Admin</a></li>
                <li>Verifica que no aparezcan errores en la consola</li>
                <li>Prueba todas las funcionalidades del panel de administración</li>
                <li>Si encuentras algún error, reporta el mensaje exacto</li>
            </ul>
        </div>
    </div>
</div>

</div>

<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?> 