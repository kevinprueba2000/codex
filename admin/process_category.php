<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Category.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado']);
    exit();
}

// Verificar token CSRF
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['error' => 'Token CSRF inválido']);
    exit();
}

$category = new Category();
$response = ['success' => false, 'message' => ''];

try {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $name = cleanInput($_POST['name'] ?? '');
            $description = cleanInput($_POST['description'] ?? '');
            $image_url = cleanInput($_POST['image_url'] ?? '');
            $active = isset($_POST['active']) ? 1 : 0;
            
            // Validaciones
            if (empty($name)) {
                throw new Exception('El nombre de la categoría es obligatorio');
            }
            
            if (strlen($name) < 2) {
                throw new Exception('El nombre debe tener al menos 2 caracteres');
            }
            
            // Verificar si la categoría ya existe
            if ($category->getCategoryByName($name)) {
                throw new Exception('Ya existe una categoría con ese nombre');
            }
            
            $categoryId = $category->createCategory($name, $description, $image_url, $active);
            
            if ($categoryId) {
                $response = [
                    'success' => true,
                    'message' => 'Categoría creada correctamente',
                    'category_id' => $categoryId
                ];
            } else {
                throw new Exception('Error al crear la categoría');
            }
            break;
            
        case 'update':
            $categoryId = (int)($_POST['id'] ?? 0);
            $name = cleanInput($_POST['name'] ?? '');
            $description = cleanInput($_POST['description'] ?? '');
            $image_url = cleanInput($_POST['image_url'] ?? '');
            $active = isset($_POST['active']) ? 1 : 0;
            
            if ($categoryId <= 0) {
                throw new Exception('ID de categoría inválido');
            }
            
            if (empty($name)) {
                throw new Exception('El nombre de la categoría es obligatorio');
            }
            
            if (strlen($name) < 2) {
                throw new Exception('El nombre debe tener al menos 2 caracteres');
            }
            
            // Verificar si el nombre ya existe en otra categoría
            $existingCategory = $category->getCategoryByName($name);
            if ($existingCategory && $existingCategory['id'] != $categoryId) {
                throw new Exception('Ya existe otra categoría con ese nombre');
            }
            
            $result = $category->updateCategory($categoryId, $name, $description, $image_url, $active);
            
            if ($result) {
                $response = [
                    'success' => true,
                    'message' => 'Categoría actualizada correctamente'
                ];
            } else {
                throw new Exception('Error al actualizar la categoría');
            }
            break;
            
        case 'delete':
            $categoryId = (int)($_POST['id'] ?? 0);
            
            if ($categoryId <= 0) {
                throw new Exception('ID de categoría inválido');
            }
            
            // Verificar si hay productos en esta categoría
            $productCount = $category->getProductCountByCategory($categoryId);
            if ($productCount > 0) {
                throw new Exception("No se puede eliminar la categoría porque tiene $productCount productos asociados");
            }
            
            $result = $category->deleteCategory($categoryId);
            
            if ($result) {
                $response = [
                    'success' => true,
                    'message' => 'Categoría eliminada correctamente'
                ];
            } else {
                throw new Exception('Error al eliminar la categoría');
            }
            break;
            
        case 'get_category':
            $categoryId = (int)($_POST['id'] ?? 0);
            
            if ($categoryId <= 0) {
                throw new Exception('ID de categoría inválido');
            }
            
            $categoryData = $category->getCategoryById($categoryId);
            
            if ($categoryData) {
                $response = [
                    'success' => true,
                    'category' => $categoryData
                ];
            } else {
                throw new Exception('Categoría no encontrada');
            }
            break;
            
        case 'toggle_active':
            $categoryId = (int)($_POST['id'] ?? 0);
            
            if ($categoryId <= 0) {
                throw new Exception('ID de categoría inválido');
            }
            
            $result = $category->toggleActive($categoryId);
            
            if ($result !== false) {
                $response = [
                    'success' => true,
                    'message' => 'Estado de la categoría actualizado',
                    'active' => $result
                ];
            } else {
                throw new Exception('Error al actualizar el estado');
            }
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?> 