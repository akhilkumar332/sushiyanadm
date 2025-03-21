<?php
session_start();

// Start buffering
ob_start();

// Suppress errors to avoid output
error_reporting(0);
ini_set('display_errors', 0);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart'])) {
    $restored_cart = json_decode($_POST['cart'], true);
    $response = ['status' => 'error', 'message' => 'Invalid cart data'];

    if (is_array($restored_cart)) {
        $_SESSION['cart'] = $restored_cart;
        $response = ['status' => 'success', 'cart' => $_SESSION['cart']];
    }

    // Clear buffer, set headers, and output JSON
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Length: ' . strlen(json_encode($response))); // Explicit length
    echo json_encode($response);
    flush(); // Force send
    exit;
}

// For non-POST, redirect
ob_end_clean();
header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/index.php'));
exit;