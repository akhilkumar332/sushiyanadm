<?php
// Set session cookie parameters before starting the session
session_set_cookie_params([
    'path' => '/',
    'lifetime' => 0, // Session lasts until browser closes
    'secure' => false, // Set to true if using HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Centralize session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default branch and language in session if not already set
if (!isset($_SESSION['branch'])) {
    $_SESSION['branch'] = 'neukoelln';
}
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'de';
}

// Define base path as root
define('BASE_PATH', '/');

// Asset paths
define('ASSETS_CSS', BASE_PATH . 'css/');
define('ASSETS_IMAGES', BASE_PATH . 'bilder/');
define('ASSETS_SCRIPTS', BASE_PATH . 'skripte/');

// URL constants for navigation (with language parameter support)
function getUrlWithLang($baseUrl, $lang = null) {
    $lang = $lang ?: $_SESSION['language'];
    return $baseUrl . '?lang=' . urlencode($lang);
}

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

// DeepL API key
define('DEEPL_API_KEY', '3d9b1d4b-06db-4c43-88ab-3e48a9a3ee99:fx'); // Replace with your actual DeepL API key

// Set DB_FILIALE dynamically from session
define('DB_FILIALE', $_SESSION['branch']);

// Initialize database connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Filiale identifier
$filiale = DB_FILIALE;

// Updated CSP (allow DeepL API)
header("Content-Security-Policy: style-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net 'unsafe-inline'; script-src 'self' https://code.jquery.com https://cdn.jsdelivr.net 'unsafe-inline'; connect-src 'self' https://api-free.deepl.com;");

// Translation function with caching
function translateText($texts, $sourceLang, $targetLang, $conn) {
    if (!is_array($texts)) {
        $texts = [$texts];
    }
    if ($sourceLang === $targetLang || empty($texts)) {
        error_log("translateText: Skipping - sourceLang ($sourceLang) equals targetLang ($targetLang) or texts empty");
        return $texts;
    }

    $sourceLang = strtoupper($sourceLang);
    $targetLang = strtoupper($targetLang);
    $translated_texts = array_fill(0, count($texts), null);
    $to_translate = [];
    $to_translate_indices = [];

    // Check cache for each text
    $cacheStmt = $conn->prepare("SELECT translated_text FROM translation_cache WHERE source_text = ? AND source_lang = ? AND target_lang = ?");
    if (!$cacheStmt) {
        error_log("Prepare failed for cache lookup: " . $conn->error);
        return $texts; // Fallback to original texts
    }

    foreach ($texts as $index => $text) {
        if (empty($text)) {
            $translated_texts[$index] = $text;
            continue;
        }
        $cacheStmt->bind_param("sss", $text, $sourceLang, $targetLang);
        $cacheStmt->execute();
        $cacheResult = $cacheStmt->get_result();
        if ($cacheRow = $cacheResult->fetch_assoc()) {
            $translated_texts[$index] = $cacheRow['translated_text'];
        } else {
            $to_translate[] = $text;
            $to_translate_indices[] = $index;
        }
    }
    $cacheStmt->close();

    // If all texts were cached, return early
    if (empty($to_translate)) {
        return $translated_texts;
    }

    // Batch translate uncached texts via DeepL
    $chunked_texts = array_chunk($to_translate, 10);
    $api_translated = [];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api-free.deepl.com/v2/translate');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

    foreach ($chunked_texts as $chunk) {
        $payload = 'auth_key=' . urlencode(DEEPL_API_KEY) .
                   '&source_lang=' . urlencode($sourceLang) .
                   '&target_lang=' . urlencode($targetLang);
        foreach ($chunk as $text) {
            $payload .= '&text=' . urlencode($text);
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $result = curl_exec($ch);

        if ($result === false) {
            error_log("translateText: cURL error - " . curl_error($ch));
            $api_translated = array_merge($api_translated, $chunk);
            continue;
        }

        $response = json_decode($result, true);
        if (!isset($response['translations']) || !is_array($response['translations'])) {
            error_log("translateText: Invalid DeepL response - " . json_encode($response));
            $api_translated = array_merge($api_translated, $chunk);
            continue;
        }

        $translated_chunk = array_map(function($t) { return $t['text']; }, $response['translations']);
        $api_translated = array_merge($api_translated, $translated_chunk);

        // Cache the new translations
        $insertStmt = $conn->prepare("INSERT IGNORE INTO translation_cache (source_text, source_lang, target_lang, translated_text) VALUES (?, ?, ?, ?)");
        if ($insertStmt) {
            foreach ($chunk as $i => $text) {
                if (isset($translated_chunk[$i])) {
                    $insertStmt->bind_param("ssss", $text, $sourceLang, $targetLang, $translated_chunk[$i]);
                    $insertStmt->execute();
                }
            }
            $insertStmt->close();
        } else {
            error_log("Prepare failed for cache insert: " . $conn->error);
        }
    }
    curl_close($ch);

    // Merge API translations into the result
    foreach ($to_translate_indices as $i => $index) {
        $translated_texts[$index] = $api_translated[$i] ?? $to_translate[$i];
    }

    return $translated_texts;
}
include_once __DIR__ . '/cache-buster.php';

function addCacheBuster($url) {
    $cacheBusterVersion = $GLOBALS['cacheBusterVersion'];
    return $url . '?v=' . htmlspecialchars($cacheBusterVersion);
}
?>