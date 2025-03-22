<?php
session_start();
include 'config/config.php';

// Clear the session cart
$_SESSION['cart'] = [];

// Attempt to log the action (example)
$response = ['status' => 'success'];
try {
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    // Example: Log the action (optional)
    // $conn->query("INSERT INTO logs (action) VALUES ('cart_cleared')");
} catch (Exception $e) {
    error_log('Clear cart error: ' . $e->getMessage());
    $response = ['status' => 'error', 'message' => 'db'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>