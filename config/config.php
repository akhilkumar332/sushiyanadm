<?php
// Set session cookie parameters before starting the session
session_set_cookie_params(['path' => '/']);

// Centralize session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

// Menu URLs for subdirectories
define('MENU_SUSHI', BASE_PATH . 'sushi/menu.php');
define('MENU_WARMEKUECHE', BASE_PATH . 'warmekueche/menu.php');
define('MENU_YANA', BASE_PATH . 'yana/menu.php');

// Database configuration (from your working config.php)
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'db105950');
define('DB_PORT', 3306);
define('DB_FILIALE', 'neukoelln');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Filiale identifier
$filiale = DB_FILIALE; // Matches your previous config