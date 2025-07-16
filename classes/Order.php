<?php
require_once __DIR__ . '/../config/config.php';

class Order {
    private $pdo;
    
    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }
    
    /**
     * Obtener todos los pedidos
     */
    public function getAllOrders() {
        try {
            $stmt = $this->pdo->query("
                SELECT o.*, u.name as user_name, u.email as user_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Si la tabla no existe, devolver datos simulados
            return $this->getMockOrders();
        }
    }
    
    /**
     * Obtener pedido por ID
     */
    public function getOrderById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT o.*, u.name as user_name, u.email as user_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Obtener pedidos de un usuario
     */
    public function getOrdersByUserId($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM orders 
                WHERE user_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Crear nuevo pedido
     */
    public function createOrder($userId, $total, $items, $status = 'pending') {
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("
                INSERT INTO orders (user_id, total, status, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$userId, $total, $status]);
            
            $orderId = $this->pdo->lastInsertId();
            
            // Insertar items del pedido
            foreach ($items as $item) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO order_items (order_id, product_id, quantity, price) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
            }
            
            $this->pdo->commit();
            return $orderId;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
    
    /**
     * Actualizar estado del pedido
     */
    public function updateOrderStatus($orderId, $status) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE orders SET status = ? WHERE id = ?
            ");
            return $stmt->execute([$status, $orderId]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtener items de un pedido
     */
    public function getOrderItems($orderId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT
                    oi.*,
                    p.name as product_name,
                    p.images as product_images,
                    p.slug as product_slug
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$orderId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de pedidos
     */
    public function getOrderStats() {
        try {
            $stats = [];
            
            // Total de pedidos
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM orders");
            $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Pedidos por estado
            $stmt = $this->pdo->query("
                SELECT status, COUNT(*) as count 
                FROM orders 
                GROUP BY status
            ");
            $stats['by_status'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Total de ventas
            $stmt = $this->pdo->query("SELECT SUM(total) as total_sales FROM orders WHERE status != 'cancelled'");
            $stats['total_sales'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'] ?: 0;
            
            return $stats;
        } catch (PDOException $e) {
            return $this->getMockOrderStats();
        }
    }
    
    /**
     * Datos simulados para pedidos
     */
    private function getMockOrders() {
        return [
            [
                'id' => 1,
                'user_id' => 1,
                'user_name' => 'Juan Pérez',
                'user_email' => 'juan@ejemplo.com',
                'total' => 250000,
                'status' => 'pending',
                'created_at' => '2025-01-15 10:30:00'
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'user_name' => 'María González',
                'user_email' => 'maria@ejemplo.com',
                'total' => 180000,
                'status' => 'processing',
                'created_at' => '2025-01-14 15:45:00'
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'user_name' => 'Carlos Rodríguez',
                'user_email' => 'carlos@ejemplo.com',
                'total' => 99000,
                'status' => 'delivered',
                'created_at' => '2025-01-13 09:20:00'
            ],
            [
                'id' => 4,
                'user_id' => 4,
                'user_name' => 'Ana Martínez',
                'user_email' => 'ana@ejemplo.com',
                'total' => 45000,
                'status' => 'shipped',
                'created_at' => '2025-01-12 14:15:00'
            ],
            [
                'id' => 5,
                'user_id' => 5,
                'user_name' => 'Luis Fernández',
                'user_email' => 'luis@ejemplo.com',
                'total' => 120000,
                'status' => 'pending',
                'created_at' => '2025-01-11 11:30:00'
            ]
        ];
    }
    
    /**
     * Estadísticas simuladas
     */
    private function getMockOrderStats() {
        return [
            'total' => 5,
            'total_sales' => 644000,
            'by_status' => [
                ['status' => 'pending', 'count' => 2],
                ['status' => 'processing', 'count' => 1],
                ['status' => 'shipped', 'count' => 1],
                ['status' => 'delivered', 'count' => 1]
            ]
        ];
    }
    
    /**
     * Eliminar pedido
     */
    public function deleteOrder($orderId) {
        try {
            $this->pdo->beginTransaction();
            
            // Eliminar items del pedido
            $stmt = $this->pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->execute([$orderId]);
            
            // Eliminar pedido
            $stmt = $this->pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$orderId]);
            
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
    
    /**
     * Obtener pedidos recientes
     */
    public function getRecentOrders($limit = 5) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT o.*, u.name as user_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return array_slice($this->getMockOrders(), 0, $limit);
        }
    }
}
?> 