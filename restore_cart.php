<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
session_start();

ob_start();
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

    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Length: ' . strlen(json_encode($response)));
    echo json_encode($response);
    flush();
    exit;
}

ob_end_clean();
header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : URL_HOME));
exit;