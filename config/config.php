<?php
// Set session cookie parameters before starting the session
session_set_cookie_params([
    'path' => '/',
    'lifetime' => 0, // Session lasts until browser closes (optional, adjust as needed)
    'secure' => false, // Set to true if using HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Centralize session start, ensuring it only starts if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default branch in session if not already set
if (!isset($_SESSION['branch'])) {
    $_SESSION['branch'] = 'neukoelln'; // Default branch
}

// Define base path as root
define('BASE_PATH', '/');

// Asset paths
define('ASSETS_CSS', BASE_PATH . 'css/');
define('ASSETS_IMAGES', BASE_PATH . 'bilder/');
define('ASSETS_SCRIPTS', BASE_PATH . 'skripte/');

// URL constants for navigation
define('URL_HOME', BASE_PATH . 'index.php');
define('URL_CART', BASE_PATH . 'cart.php');
define('URL_FINAL_ORDER', BASE_PATH . 'final_order.php');
define('URL_IMPRESSUM', BASE_PATH . 'impressum.php');
define('URL_DATENSCHUTZ', BASE_PATH . 'datenschutz.php');
define('URL_RESTORE_CART', BASE_PATH . 'restore_cart.php');
define('URL_CLEAR_CART', BASE_PATH . 'clear_cart.php');
define('URL_SUSHI', BASE_PATH . 'sushi.php');
define('URL_WARMEKUECHE', BASE_PATH . 'warmekueche.php');
define('URL_YANA', BASE_PATH . 'yana.php');
define('URL_SUSHI_VEGETARISCH', BASE_PATH . 'sushi/vegetarisch.php');
define('URL_API', BASE_PATH . 'config/api.php');

// Menu URLs for subdirectories
define('MENU_SUSHI', BASE_PATH . 'sushi/menu.php');
define('MENU_WARMEKUECHE', BASE_PATH . 'warmekueche/menu.php');
define('MENU_YANA', BASE_PATH . 'yana/menu.php');

// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'db105950');
define('DB_PORT', 3306);

// Set DB_FILIALE dynamically from session
define('DB_FILIALE', $_SESSION['branch']);

// Initialize database connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Filiale identifier
$filiale = DB_FILIALE;

// Updated CSP
header("Content-Security-Policy: style-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net 'unsafe-inline'; script-src 'self' https://code.jquery.com https://cdn.jsdelivr.net 'unsafe-inline';");
?>