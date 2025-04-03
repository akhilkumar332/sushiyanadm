<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

// Check if cart is initialized
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// API Endpoints
header('Content-Type: application/json');
ob_start();

try {
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
        if ($_GET['action'] === 'get_cart') {
            ob_end_clean();
            echo json_encode(['status' => 'success', 'cart' => $_SESSION['cart']]);
            exit;
        } elseif ($_GET['action'] === 'get_branches') {
            $query = "SELECT DISTINCT branch FROM orders WHERE branch IS NOT NULL AND branch != '' ORDER BY branch ASC";
            $result = $conn->query($query);
            if (!$result) throw new Exception("Query failed: " . $conn->error);
            $branches = [];
            while ($row = $result->fetch_assoc()) {
                $branches[] = $row['branch'];
            }
            $result->free();
            ob_end_clean();
            echo json_encode(['status' => 'success', 'branches' => $branches]);
            exit;
        } elseif ($_GET['action'] === 'get_active_orders' || $_GET['action'] === 'get_completed_orders') {
            $filter = $_GET['filter'] ?? 'daily';
            $order_by = $_GET['order_by'] ?? 'desc';
            $page = max(1, (int)($_GET['page'] ?? 1));
            $per_page = 20;
            $offset = ($page - 1) * $per_page;
            $branch = $_GET['branch'] ?? null;

            $status = $_GET['action'] === 'get_active_orders' ? 'active' : 'completed';
            $date_field = $status === 'active' ? 'created_at' : 'updated_at';
            $query = "SELECT * FROM orders WHERE status = ?";
            $count_query = "SELECT COUNT(*) as total FROM orders WHERE status = ?";
            $params = [$status];
            $types = "s";

            if ($branch) {
                $query .= " AND branch = ?";
                $count_query .= " AND branch = ?";
                $params[] = $branch;
                $types .= "s";
            }

            switch ($filter) {
                case 'daily':
                    $query .= " AND DATE($date_field) = CURDATE()";
                    $count_query .= " AND DATE($date_field) = CURDATE()";
                    break;
                case 'weekly':
                    $query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                    $count_query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                    break;
                case 'monthly':
                    $query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                    $count_query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                    break;
                case 'quarterly':
                    $query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
                    $count_query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
                    break;
                case 'semi-annually':
                    $query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
                    $count_query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
                    break;
                case 'annually':
                    $query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                    $count_query .= " AND $date_field >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                    break;
            }

            $query .= " ORDER BY $date_field " . ($order_by === 'asc' ? 'ASC' : 'DESC') . " LIMIT ?, ?";
            $params[] = $offset;
            $params[] = $per_page;
            $types .= "ii";

            $stmt = $conn->prepare($query);
            if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
            $stmt->bind_param($types, ...$params);
            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            $result = $stmt->get_result();
            $orders = [];

            $tables = ['bowls', 'chopsuey', 'desserts', 'erdnussgericht', 'extras', 'extrasWarm', 'fingerfood',
                       'gemuese', 'getraenke', 'gyoza', 'insideoutrolls', 'makis', 'mangochutney', 'menues',
                       'miniyanarolls', 'nigiris', 'nudeln', 'redcurry', 'reis', 'salate', 'sashimi',
                       'sommerrollen', 'specialrolls', 'suesssauersauce', 'suppen', 'temaki', 'warmgetraenke',
                       'yanarolls', 'yellowcurry'];

            while ($row = $result->fetch_assoc()) {
                $items = json_decode($row['order_details'], true);
                if (!is_array($items)) continue;
                $detailed_items = [];
                $total = 0;
                foreach ($items as $item_key => $quantity) {
                    if (strpos($item_key, ':') === false) continue;
                    list($table, $item_id) = explode(':', $item_key);
                    if (!in_array($table, $tables)) continue;
                    $item_stmt = $conn->prepare("SELECT artikelnummer, artikelname, preis FROM " . mysqli_real_escape_string($conn, $table) . " WHERE id = ?");
                    if (!$item_stmt) continue;
                    $item_stmt->bind_param("i", $item_id);
                    $item_stmt->execute();
                    $item_result = $item_stmt->get_result();
                    $item = $item_result->fetch_assoc();
                    if ($item) {
                        $price = floatval($item['preis']);
                        $subtotal = $price * $quantity;
                        $total += $subtotal;
                        $detailed_items[] = [
                            'artikelnummer' => $item['artikelnummer'],
                            'artikelname' => $item['artikelname'],
                            'quantity' => $quantity,
                            'price' => $price,
                            'subtotal' => $subtotal
                        ];
                    }
                    $item_stmt->close();
                }
                $row['items'] = $detailed_items;
                $row['total'] = $total;
                $orders[] = $row;
            }
            $stmt->close();

            $count_stmt = $conn->prepare($count_query);
            if (!$count_stmt) throw new Exception("Prepare failed: " . $conn->error);
            $count_stmt->bind_param(substr($types, 0, -2), ...array_slice($params, 0, -2));
            if (!$count_stmt->execute()) throw new Exception("Execute failed: " . $count_stmt->error);
            $count_result = $count_stmt->get_result();
            $total_orders = $count_result->fetch_assoc()['total'];
            $count_stmt->close();

            ob_end_clean();
            echo json_encode([
                'status' => 'success',
                'orders' => $orders,
                'total' => $total_orders,
                'page' => $page,
                'per_page' => $per_page
            ]);
            exit;
        }
        throw new Exception('Invalid GET action');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? 'add';

        if ($action === 'submit_order') {
            $order_details = json_encode($_SESSION['cart']);
            $branch = $_SESSION['branch'] ?? null;
            $stmt = $conn->prepare("INSERT INTO orders (order_details, status, branch, created_at) VALUES (?, 'active', ?, NOW())");
            if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
            $stmt->bind_param("ss", $order_details, $branch);
            if ($stmt->execute()) {
                $_SESSION['cart'] = [];
                ob_end_clean();
                echo json_encode(['status' => 'success', 'message' => 'Order submitted']);
            } else {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            $stmt->close();
            exit;
        } elseif ($action === 'complete_order' || $action === 'delete_order') {
            $order_id = (int)($_POST['order_id'] ?? 0);
            if ($order_id <= 0) throw new Exception('Invalid or missing order_id');

            if ($action === 'complete_order') {
                $stmt = $conn->prepare("UPDATE orders SET status = 'completed', updated_at = NOW() WHERE id = ? AND status = 'active'");
                if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
                $stmt->bind_param("i", $order_id);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows === 0) throw new Exception('Order not found or already completed');
                    ob_end_clean();
                    echo json_encode(['status' => 'success', 'message' => 'Order completed']);
                } else {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
            } else {
                $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
                if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
                $stmt->bind_param("i", $order_id);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows === 0) throw new Exception('Order not found');
                    ob_end_clean();
                    echo json_encode(['status' => 'success', 'message' => 'Order deleted']);
                } else {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
            }
            $stmt->close();
            exit;
        } elseif ($action === 'set_branch') {
            $branch = $_POST['branch'] ?? null;
            $valid_branches = ['charlottenburg', 'friedrichshain', 'lichtenrade', 'mitte', 'moabit',
                               'neukoelln', 'potsdam', 'rudow', 'spandau', 'tegel', 'weissensee',
                               'zehlendorf', 'FFO'];
            if ($branch && in_array($branch, $valid_branches)) {
                $_SESSION['branch'] = $branch;
                ob_end_clean();
                echo json_encode(['status' => 'success', 'message' => 'Branch updated to ' . $branch]);
            } else {
                throw new Exception('Invalid branch');
            }
            exit;
        }

        $item_id = $_POST['item_id'] ?? null;
        $table = $_POST['table'] ?? null;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : null;
        $item_key = ($item_id && $table) ? "$table:$item_id" : ($_POST['item_key'] ?? null);

        switch ($action) {
            case 'add':
                if (!$item_id || !$table) throw new Exception('Missing item_id or table');
                $_SESSION['cart'][$item_key] = ($_SESSION['cart'][$item_key] ?? 0) + 1;
                $message = 'Item added to cart';
                break;

            case 'update':
                if (!$item_key || $quantity === null) throw new Exception('Missing item_key or quantity');
                if ($quantity <= 0) {
                    unset($_SESSION['cart'][$item_key]);
                    $message = 'Item removed from cart';
                } else {
                    $_SESSION['cart'][$item_key] = $quantity;
                    $message = 'Cart updated';
                }
                break;

            case 'remove':
                if (!$item_key) throw new Exception('Missing item_key');
                unset($_SESSION['cart'][$item_key]);
                $message = 'Item removed from cart';
                break;

            default:
                throw new Exception('Invalid action');
        }

        session_write_close();
        ob_end_clean();
        echo json_encode(['status' => 'success', 'message' => $message, 'cart' => $_SESSION['cart']]);
        exit;
    }

    throw new Exception('Invalid request method');
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}

exit;
?>