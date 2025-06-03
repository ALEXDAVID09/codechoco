<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_PORT', '3309');
define('DB_NAME', 'codechoco_denuncias');
define('DB_USER', 'root'); // Cambiar por tu usuario
define('DB_PASS', ''); // Cambiar por tu contraseña

// Configuración del sitio
define('SITE_NAME', 'CodeChoco - Sistema de Denuncias');
define('SITE_URL', 'http://localhost');
define('UPLOADS_DIR', 'uploads/evidencias/');

// Configuración de administrador
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'codechoco2024'); // Cambiar por una contraseña segura

// Función para conectar a la base de datos
function conectarDB() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Función para generar código de seguimiento único
function generarCodigoSeguimiento() {
    return 'CC-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

// Función para limpiar datos de entrada
function limpiarInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Iniciar sesión solo si no hay una activa - ESTO CORRIGE EL ERROR DE SESIÓN
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>