<?php
session_start();

// Clear cart and session
unset($_SESSION['cart']);
session_destroy();

header('Content-Type: application/json');
echo json_encode(['success' => true]);
exit;