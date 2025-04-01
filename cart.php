<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$tables = [
    'bowls', 'chopsuey', 'desserts', 'erdnussgericht', 'extras', 'extrasWarm', 'fingerfood',
    'gemuese', 'getraenke', 'gyoza', 'insideoutrolls', 'makis', 'mangochutney', 'menues',
    'miniyanarolls', 'nigiris', 'nudeln', 'redcurry', 'reis', 'salate', 'sashimi',
    'sommerrollen', 'specialrolls', 'suesssauersauce', 'suppen', 'temaki', 'warmgetraenke',
    'yanarolls', 'yellowcurry'
];

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    ob_start();
    header('Content-Type: application/json');

    if (isset($_POST['action'])) {
        $item_key = $_POST['item_key'] ?? '';

        if ($_POST['action'] === 'remove') {
            unset($_SESSION['cart'][$item_key]);
            session_write_close();
            ob_end_clean();
            echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
            exit;
        }

        if ($_POST['action'] === 'update') {
            $new_quantity = (int)($_POST['quantity'] ?? 0);
            if ($new_quantity <= 0) {
                unset($_SESSION['cart'][$item_key]);
            } else {
                $_SESSION['cart'][$item_key] = $new_quantity;
            }
            session_write_close();
            ob_end_clean();
            echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
            exit;
        }
    }

    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    if (isset($_POST['remove_from_cart'])) {
        $item_key = $_POST['item_key'];
        unset($_SESSION['cart'][$item_key]);
        session_write_close();
        header("Location: " . URL_CART);
        exit;
    }

    if (isset($_POST['update_quantity'])) {
        $item_key = $_POST['item_key'];
        $new_quantity = (int)$_POST['quantity'];
        if ($new_quantity <= 0) {
            unset($_SESSION['cart'][$item_key]);
        } else {
            $_SESSION['cart'][$item_key] = $new_quantity;
        }
        session_write_close();
        header("Location: " . URL_CART);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Digitale Speisekarte - Warenkorb</title>
    <link rel="stylesheet" href="<?php echo ASSETS_CSS; ?>styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?php echo ASSETS_SCRIPTS; ?>skripte.js"></script>
</head>
<body class="navigation" data-page="cart" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart="<?php echo htmlspecialchars(json_encode($_SESSION['cart'])); ?>">
    <header>
        <a href="<?php echo URL_HOME; ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <main>
        <div class="cart-wrapper">
            <div class="cart-header">
                <h1>Ihr Warenkorb</h1>
            </div>
            <div class="cart-items">
                <?php if (empty($_SESSION['cart'])): ?>
                    <div class="cart-empty">
                        <p>Ihr Warenkorb ist leer.</p>
                    </div>
                <?php else: ?>
                    <?php
                    $total = 0;
                    foreach ($_SESSION['cart'] as $item_key => $quantity) {
                        if (strpos($item_key, ':') === false) {
                            continue;
                        }
                        list($table, $item_id) = explode(':', $item_key);
                        if (!in_array($table, $tables)) continue;
                        $stmt = $conn->prepare("SELECT artikelname, preis, image FROM " . mysqli_real_escape_string($conn, $table) . " WHERE id = ?");
                        $stmt->bind_param("i", $item_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($item = $result->fetch_assoc()) {
                            $price = floatval($item['preis']);
                            $subtotal = $price * $quantity;
                            $total += $subtotal;
                            ?>
                            <div class="cart-item" data-item-key="<?php echo htmlspecialchars($item_key); ?>">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['artikelname']); ?>" 
                                     onerror="this.src='https://placehold.co/150';">
                                <div class="cart-item-details">
                                    <h3><?php echo htmlspecialchars($item['artikelname']); ?></h3>
                                    <p><?php echo number_format($price, 2); ?> €</p>
                                </div>
                                <div class="cart-item-actions">
                                    <div class="quantity-controls">
                                        <button type="button" class="btn-decrement">-</button>
                                        <input type="number" name="quantity" value="<?php echo $quantity; ?>" min="1" class="quantity-input">
                                        <button type="button" class="btn-increment">+</button>
                                    </div>
                                    <span class="quantity"><?php echo number_format($subtotal, 2); ?> €</span>
                                    <form method="POST" action="<?php echo URL_CART; ?>" class="remove-form">
                                        <input type="hidden" name="item_key" value="<?php echo $item_key; ?>">
                                        <button type="submit" name="remove_from_cart" class="btn-remove">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php
                        }
                        $stmt->close();
                    }
                    ?>
                    <div class="cart-total">
                        <span>Gesamtbetrag</span>
                        <span class="total-amount"><?php echo number_format($total, 2); ?> €</span>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($_SESSION['cart'])): ?>
                <div class="cart-buttons">
                    <a href="<?php echo URL_FINAL_ORDER; ?>" class="btn">Bestätigen</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/floating_bar.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/footer.php'; ?>
    <script>
        // Sync localStorage with server cart on page load
        const serverCart = <?php echo json_encode($_SESSION['cart']); ?>;
        localStorage.setItem('cart', JSON.stringify(serverCart));
        updateCartCount();

        // Debug redirect
        window.addEventListener('beforeunload', function(e) {
            console.log('Leaving cart.php to:', window.location.href);
        });
    </script>
    <?php $conn->close(); ?>
</body>
</html>