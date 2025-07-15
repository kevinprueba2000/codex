<?php
require_once __DIR__ . '/../config/config.php';

class Product {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Obtener todos los productos
    public function getAllProducts($limit = null, $offset = 0) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.status = 'active' 
                    ORDER BY p.created_at DESC";
            
            if ($limit) {
                $sql .= " LIMIT ? OFFSET ?";
            }
            
            $stmt = $this->db->prepare($sql);
            
            if ($limit) {
                $stmt->execute([$limit, $offset]);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
    
    // Obtener productos destacados
    public function getFeaturedProducts($limit = 8) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.status = 'active' AND p.is_featured = 1 
                    ORDER BY p.created_at DESC LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
    
    // Obtener producto por ID
    public function getProductById($id) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.id = ? AND p.status = 'active'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Obtener producto por slug
    public function getProductBySlug($slug) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.slug = ? AND p.status = 'active'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$slug]);
            
            return $stmt->fetch();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Obtener productos por categoría
    public function getProductsByCategory($categoryId, $limit = null, $offset = 0) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.category_id = ? AND p.status = 'active' 
                    ORDER BY p.created_at DESC";
            
            if ($limit) {
                $sql .= " LIMIT ? OFFSET ?";
            }
            
            $stmt = $this->db->prepare($sql);
            
            if ($limit) {
                $stmt->execute([$categoryId, $limit, $offset]);
            } else {
                $stmt->execute([$categoryId]);
            }
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
    
    // Buscar productos
    public function searchProducts($query, $limit = 20) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.status = 'active' AND (
                        p.name LIKE ? OR 
                        p.description LIKE ? OR 
                        p.short_description LIKE ?
                    ) ORDER BY p.created_at DESC LIMIT ?";
            
            $searchTerm = '%' . $query . '%';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $limit]);
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
    
    // Crear producto (Admin)
    public function createProduct($data) {
        try {
            $sql = "INSERT INTO products (name, description, short_description, price, discount_price, sku, stock_quantity, category_id, images, features, specifications, weight, dimensions, is_featured, meta_title, meta_description, slug) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            
            $result = $stmt->execute([
                $data['name'],
                $data['description'],
                $data['short_description'],
                $data['price'],
                $data['discount_price'] ?? null,
                $data['sku'],
                $data['stock_quantity'] ?? 0,
                $data['category_id'],
                $data['images'] ?? null,
                $data['features'] ?? null,
                $data['specifications'] ?? null,
                $data['weight'] ?? null,
                $data['dimensions'] ?? null,
                $data['is_featured'] ?? 0,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['slug']
            ]);
            
            if ($result) {
                return $this->db->lastInsertId();
            }
            
            return false;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Actualizar producto (Admin)
    public function updateProduct($id, $data) {
        try {
            $sql = "UPDATE products SET name = ?, description = ?, short_description = ?, price = ?, discount_price = ?, sku = ?, stock_quantity = ?, category_id = ?, images = ?, features = ?, specifications = ?, weight = ?, dimensions = ?, is_featured = ?, meta_title = ?, meta_description = ?, slug = ? WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $data['name'],
                $data['description'],
                $data['short_description'],
                $data['price'],
                $data['discount_price'] ?? null,
                $data['sku'],
                $data['stock_quantity'] ?? 0,
                $data['category_id'],
                $data['images'] ?? null,
                $data['features'] ?? null,
                $data['specifications'] ?? null,
                $data['weight'] ?? null,
                $data['dimensions'] ?? null,
                $data['is_featured'] ?? 0,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['slug'],
                $id
            ]);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Eliminar producto (Admin)
    public function deleteProduct($id) {
        try {
            $sql = "UPDATE products SET status = 'inactive' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Verificar si el SKU existe
    public function skuExists($sku, $excludeId = null) {
        try {
            $sql = "SELECT id FROM products WHERE sku = ?";
            if ($excludeId) {
                $sql .= " AND id != ?";
            }
            
            $stmt = $this->db->prepare($sql);
            $params = [$sku];
            if ($excludeId) {
                $params[] = $excludeId;
            }
            
            $stmt->execute($params);
            return $stmt->fetch() !== false;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Actualizar stock
    public function updateStock($productId, $quantity) {
        try {
            $sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$quantity, $productId, $quantity]);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Obtener productos relacionados
    public function getRelatedProducts($categoryId, $excludeId, $limit = 4) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.category_id = ? AND p.id != ? AND p.status = 'active' 
                    ORDER BY p.created_at DESC LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$categoryId, $excludeId, $limit]);
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
    
    // Obtener cantidad de productos por categoría
    public function getProductCountByCategory($categoryId) {
        try {
            $sql = "SELECT COUNT(*) as total FROM products 
                    WHERE category_id = ? AND status = 'active'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$categoryId]);
            
            $result = $stmt->fetch();
            return $result['total'];
        } catch(PDOException $e) {
            return 0;
        }
    }
}
?> 