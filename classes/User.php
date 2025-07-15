<?php
require_once __DIR__ . '/../config/config.php';

class User {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Registrar nuevo usuario
    public function register($data) {
        try {
            $sql = "INSERT INTO users (username, email, password, first_name, last_name, phone, address, city, country, postal_code) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            return $stmt->execute([
                $data['username'],
                $data['email'],
                $hashedPassword,
                $data['first_name'],
                $data['last_name'],
                $data['phone'] ?? null,
                $data['address'] ?? null,
                $data['city'] ?? null,
                $data['country'] ?? null,
                $data['postal_code'] ?? null
            ]);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Login de usuario
    public function login($username, $password) {
        try {
            $sql = "SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'active'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$username, $username]);
            
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Obtener usuario por ID
    public function getUserById($id) {
        try {
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Obtener todos los usuarios
    public function getAllUsers() {
        try {
            $sql = "SELECT id, username, email, first_name, last_name, role, status, created_at FROM users ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
    
    // Actualizar usuario
    public function updateUser($id, $data) {
        try {
            $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, city = ?, country = ?, postal_code = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['address'] ?? null,
                $data['city'] ?? null,
                $data['country'] ?? null,
                $data['postal_code'] ?? null,
                $id
            ]);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Cambiar contraseña
    public function changePassword($userId, $newPassword) {
        try {
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            return $stmt->execute([$hashedPassword, $userId]);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Verificar si el email existe
    public function emailExists($email, $excludeId = null) {
        try {
            $sql = "SELECT id FROM users WHERE email = ?";
            if ($excludeId) {
                $sql .= " AND id != ?";
            }
            
            $stmt = $this->db->prepare($sql);
            $params = [$email];
            if ($excludeId) {
                $params[] = $excludeId;
            }
            
            $stmt->execute($params);
            return $stmt->fetch() !== false;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Verificar si el username existe
    public function usernameExists($username, $excludeId = null) {
        try {
            $sql = "SELECT id FROM users WHERE username = ?";
            if ($excludeId) {
                $sql .= " AND id != ?";
            }
            
            $stmt = $this->db->prepare($sql);
            $params = [$username];
            if ($excludeId) {
                $params[] = $excludeId;
            }
            
            $stmt->execute($params);
            return $stmt->fetch() !== false;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Logout
    public function logout() {
        session_destroy();
        return true;
    }
}
?> 