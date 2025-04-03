<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure JSON response
header('Content-Type: application/json; charset=utf-8');
ob_start(); // Buffer output to catch stray errors

try {
    // Initialize cart if not set
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Handle POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['cart'])) {
            throw new Exception('No cart data provided');
        }

        // Decode the cart data from the POST request
        $restored_cart = json_decode($_POST['cart'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($restored_cart)) {
            throw new Exception('Invalid cart data: ' . json_last_error_msg());
        }

        // Restore the cart to the session
        $_SESSION['cart'] = $restored_cart;
        $response = ['status' => 'success', 'message' => 'Cart restored', 'cart' => $_SESSION['cart']];
    } else {
        throw new Exception('Invalid request method');
    }

    // Clean buffer and send response
    ob_end_clean();
    echo json_encode($response);
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

exit;
?>