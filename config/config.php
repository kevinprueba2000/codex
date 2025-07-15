<?php
// Iniciar sesión
session_start();

// Configuración general
define('SITE_URL', 'http://localhost/TiendawebAlquimia');
define('SITE_NAME', 'AlquimiaTechnologic');
define('ADMIN_EMAIL', 'admin@alquimiatechnologic.com');

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'alquimia_technologic');
define('DB_USER', 'root');
define('DB_PASS', '');

require_once __DIR__ . '/database.php';

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Función para incluir archivos de forma segura
function includeFile($file) {
    if (file_exists($file)) {
        include $file;
    } else {
        die("Archivo no encontrado: $file");
    }
}

// Función para redireccionar
function redirect($url) {
    header("Location: $url");
    exit();
}

// Función para limpiar datos de entrada
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Función para verificar si el usuario es administrador
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Función para generar token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Función para verificar token CSRF
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Función para formatear precio
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Función para generar slug
function generateSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

// Configuración de errores (solo para desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?> 