<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// API Endpoints for Cart Operations and Branch Selection
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    if ($_GET['action'] === 'get_cart') {
        echo json_encode($_SESSION['cart']);
        exit;
    }
    echo json_encode(['status' => 'error', 'message' => 'Invalid GET action']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_start();
    header('Content-Type: application/json');

    $action = $_POST['action'] ?? 'add';

    if ($action === 'set_branch') {
        $branch = $_POST['branch'] ?? null;
        $valid_branches = ['charlottenburg', 'friedrichshain', 'lichtenrade', 'mitte', 'moabit', 
                           'neukoelln', 'potsdam', 'rudow', 'spandau', 'tegel', 'weissensee', 
                           'zehlendorf', 'FFO'];
        if ($branch && in_array($branch, $valid_branches)) {
            $_SESSION['branch'] = $branch;
            ob_end_clean();
            echo json_encode(['status' => 'success', 'message' => 'Branch updated to ' . $branch]);
            exit;
        } else {
            ob_end_clean();
            echo json_encode(['status' => 'error', 'message' => 'Invalid branch']);
            exit;
        }
    }

    $item_id = $_POST['item_id'] ?? null;
    $table = $_POST['table'] ?? null;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : null;
    $item_key = ($item_id && $table) ? "$table:$item_id" : ($_POST['item_key'] ?? null);

    switch ($action) {
        case 'add':
            if (!$item_id || !$table) {
                ob_end_clean();
                echo json_encode(['status' => 'error', 'message' => 'Missing item_id or table']);
                exit;
            }
            $_SESSION['cart'][$item_key] = ($_SESSION['cart'][$item_key] ?? 0) + 1;
            $message = 'Item added to cart';
            break;

        case 'update':
            if (!$item_key || $quantity === null) {
                ob_end_clean();
                echo json_encode(['status' => 'error', 'message' => 'Missing item_key or quantity']);
                exit;
            }
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$item_key]);
                $message = 'Item removed from cart';
            } else {
                $_SESSION['cart'][$item_key] = $quantity;
                $message = 'Cart updated';
            }
            break;

        case 'remove':
            if (!$item_key) {
                ob_end_clean();
                echo json_encode(['status' => 'error', 'message' => 'Missing item_key']);
                exit;
            }
            unset($_SESSION['cart'][$item_key]);
            $message = 'Item removed from cart';
            break;

        default:
            ob_end_clean();
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            exit;
    }

    session_write_close();
    ob_end_clean();
    echo json_encode(['status' => 'success', 'message' => $message, 'cart' => $_SESSION['cart']]);
    exit;
}

// If no valid request, return error
header('Content-Type: application/json');
echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
exit;
?>