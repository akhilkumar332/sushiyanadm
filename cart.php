<?php
session_start();
include 'config/config.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    ob_start();
    header('Content-Type: application/json');

    if (isset($_POST['action'])) {
        $item_key = $_POST['item_key'] ?? '';

        if ($_POST['action'] === 'remove') {
            unset($_SESSION['cart'][$item_key]);
            ob_end_clean();
            echo json_encode(['success' => true]);
            exit;
        }

        if ($_POST['action'] === 'update') {
            $new_quantity = (int)($_POST['quantity'] ?? 0);
            if ($new_quantity <= 0) {
                unset($_SESSION['cart'][$item_key]);
            } else {
                $_SESSION['cart'][$item_key] = $new_quantity;
            }
            ob_end_clean();
            echo json_encode(['success' => true]);
            exit;
        }
    }

    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    echo "<script>
        if (localStorage.getItem('cart')) {
            fetch('restore_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'cart=' + encodeURIComponent(localStorage.getItem('cart'))
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok: ' + response.status);
                return response.text();
            })
            .then(text => {
                console.log('Raw response:', text);
                const cleanedText = text.replace(/%+$/, '').trim();
                return JSON.parse(cleanedText);
            })
            .then(data => {
                console.log('Cart restored:', data);
                updateCartCount();
            })
            .catch(error => console.error('Error restoring cart:', error));
        }
    </script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    if (isset($_POST['remove_from_cart'])) {
        $item_key = $_POST['item_key'];
        unset($_SESSION['cart'][$item_key]);
        header("Location: cart.php");
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
        header("Location: cart.php");
        exit;
    }
}

$tables = [
    'bowls', 'chopsuey', 'desserts', 'erdnussgericht', 'extras', 'extrasWarm', 'fingerfood',
    'gemuese', 'getraenke', 'gyoza', 'insideoutrolls', 'makis', 'mangochutney', 'menues',
    'miniyanarolls', 'nigiris', 'nudeln', 'redcurry', 'reis', 'salate', 'sashimi',
    'sommerrollen', 'specialRolls', 'suesssauersauce', 'suppen', 'temaki', 'warmgetraenke',
    'yanarolls', 'yellowcurry'
];
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Digitale Speisekarte - Warenkorb</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <style>
        /* Existing styles unchanged */
        .cart-wrapper { max-width: 1200px; margin: 20px auto; padding: 0 15px; font-family: 'Arial', sans-serif; }
        .cart-header { text-align: center; padding: 20px 0; color: #fff !important; background: #6A2477; border-radius: 10px 10px 0 0; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); }
        .cart-header h1 { margin: 0; font-size: 28px; font-weight: 600; letter-spacing: 1px; color: #fff !important; }
        .cart-items { background-color: #fff; border-radius: 0 0 10px 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); overflow: hidden; }
        .cart-item { display: flex; align-items: center; padding: 15px; border-bottom: 1px solid #eee; transition: transform 0.2s, box-shadow 0.2s; }
        .cart-item:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1); }
        .cart-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px; }
        .cart-item-details { flex: 1; color: #333; }
        .cart-item-details h3 { margin: 0 0 5px; font-size: 16px; font-weight: 500; }
        .cart-item-details p { margin: 0; font-size: 14px; color: #666; }
        .cart-item-actions { display: flex; align-items: center; gap: 10px; }
        .cart-item-actions .quantity { font-size: 16px; font-weight: bold; color: #333; }
        .cart-item-actions .btn-remove { padding: 8px; background-color: #6A2477; color: #fff; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s; font-size: 16px; line-height: 1; }
        .cart-item-actions .btn-remove:hover { background-color: #4A1A55; }
        .cart-item-actions .btn-remove i { margin: 0; }
        .quantity-controls { display: flex; align-items: center; gap: 5px; }
        .quantity-controls button { padding: 5px 10px; background-color: #6A2477; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; transition: background-color 0.3s; }
        .quantity-controls button:hover { background-color: #4A1A55; }
        .quantity-controls input { width: 40px; text-align: center; border: 1px solid #ddd; border-radius: 5px; padding: 5px; font-size: 14px; }
        .cart-empty { padding: 20px; text-align: center; color: #666; background-color: #fff; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .cart-total { display: flex; justify-content: space-between; align-items: center; padding: 15px; background-color: #f9f9f9; border-top: 1px solid #eee; font-size: 18px; font-weight: 600; color: #333; }
        .cart-buttons { padding: 20px 0; text-align: center; }
        .cart-buttons .btn { display: inline-block; padding: 12px 25px; margin: 10px; text-decoration: none; border-radius: 50px; font-size: 16px; font-weight: 500; background-color: #6A2477; color: #fff !important; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); }
        .cart-buttons .btn:hover { background-color: #4A1A55; transform: translateY(-2px); }
        @media (max-width: 768px) {
            .cart-wrapper { margin: 10px; padding: 0; width: 390px; max-width: 100%; }
            .cart-header { margin: 0 0 10px; border-radius: 10px 10px 0 0; padding: 15px 0; }
            .cart-header h1 { font-size: 24px; }
            .cart-items { margin: 0; border-radius: 0 0 10px 10px; width: 100%; }
            .cart-item { flex-direction: column; align-items: flex-start; padding: 10px 15px; width: 100%; box-sizing: border-box; }
            .cart-item img { width: 70px; height: 70px; margin: 0 0 10px; }
            .cart-item-details { margin-bottom: 10px; width: 100%; }
            .cart-item-details h3 { font-size: 18px; }
            .cart-item-details p { font-size: 15px; }
            .cart-item-actions { width: 100%; justify-content: space-between; flex-wrap: wrap; }
            .cart-total { flex-direction: column; text-align: center; padding: 10px 15px; font-size: 16px; width: 100%; }
            .cart-buttons { padding: 15px 0; }
            .cart-buttons .btn { display: block; width: 90%; max-width: 400px; margin: 10px auto; padding: 12px 20px; }
        }
        @media (min-width: 769px) {
            .cart-item-details h3 { font-size: 18px; }
            .cart-item-details p { font-size: 15px; }
        }
    </style>
</head>
<body class="navigation">
    <header>
        <a href="./"><img src="/bilder/logo.webp" alt="Restaurant Logo" class="logo"></a>
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
                            error_log("Invalid cart item key: $item_key");
                            continue;
                        }
                        list($table, $item_id) = explode(':', $item_key);
                        if (!in_array($table, $tables)) continue;
                        $stmt = $conn->prepare("SELECT artikelname, preis, image FROM " . mysqli_real_escape_string($conn, $table) . " WHERE id = ?");
                        $stmt->bind_param("i", $item_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $item = $result->fetch_assoc();
                        $price = floatval($item['preis']);
                        $subtotal = $price * $quantity;
                        $total += $subtotal;
                        ?>
                        <div class="cart-item" data-item-key="<?php echo htmlspecialchars($item_key); ?>">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                 onerror="this.onerror=null; this.src='https://placehold.co/150';" 
                                 alt="<?php echo htmlspecialchars($item['artikelname']); ?>">
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
                                <form method="POST" action="cart.php" class="remove-form" style="display:inline;">
                                    <input type="hidden" name="item_key" value="<?php echo $item_key; ?>">
                                    <button type="submit" name="remove_from_cart" class="btn-remove">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php $stmt->close(); } ?>
                    <div class="cart-total">
                        <span>Gesamtbetrag</span>
                        <span class="total-amount"><?php echo number_format($total, 2); ?> €</span>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($_SESSION['cart'])): ?>
                <div class="cart-buttons">
                    <a href="index.php" class="btn">Zurück zur Startseite</a>
                    <a href="final_order.php" class="btn">Bestätigen</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script>
        $(document).ready(function() {
            function updateCart(itemKey, quantity, element) {
                $.ajax({
                    url: 'cart.php',
                    type: 'POST',
                    data: { action: 'update', item_key: itemKey, quantity: quantity },
                    success: function(response) {
                        console.log('Raw response:', response);
                        if (response.success) {
                            const $item = element.closest('.cart-item');
                            const price = parseFloat($item.find('.cart-item-details p').text().replace(' €', '').replace(',', '.'));
                            const newSubtotal = price * quantity;
                            if (quantity <= 0) {
                                $item.remove();
                                updateTotal();
                                if ($('.cart-item').length === 0) {
                                    $('.cart-items').html('<div class="cart-empty"><p>Ihr Warenkorb ist leer.</p></div>');
                                    $('.cart-buttons').remove();
                                }
                            } else {
                                $item.find('.quantity').text(newSubtotal.toFixed(2).replace('.', ',') + ' €');
                                updateTotal();
                            }
                        } else {
                            console.error('Update failed:', response.error);
                        }
                    },
                    error: function(xhr, status, error) { console.error('AJAX error:', status, error); }
                });
            }

            function removeCartItem(itemKey, element) {
                $.ajax({
                    url: 'cart.php',
                    type: 'POST',
                    data: { action: 'remove', item_key: itemKey },
                    success: function(response) {
                        console.log('Raw response:', response);
                        if (response.success) {
                            element.closest('.cart-item').remove();
                            updateTotal();
                            if ($('.cart-item').length === 0) {
                                $('.cart-items').html('<div class="cart-empty"><p>Ihr Warenkorb ist leer.</p></div>');
                                $('.cart-buttons').remove();
                            }
                        } else {
                            console.error('Remove failed:', response.error);
                        }
                    },
                    error: function(xhr, status, error) { console.error('AJAX error:', status, error); }
                });
            }

            function updateTotal() {
                let total = 0;
                $('.cart-item').each(function() {
                    const subtotal = parseFloat($(this).find('.quantity').text().replace(' €', '').replace(',', '.'));
                    total += subtotal;
                });
                $('.total-amount').text(total.toFixed(2).replace('.', ',') + ' €');
            }

            $('.btn-increment').on('click', function() {
                const $input = $(this).siblings('.quantity-input');
                const itemKey = $(this).closest('.cart-item').data('item-key');
                let quantity = parseInt($input.val()) + 1;
                $input.val(quantity);
                updateCart(itemKey, quantity, $(this));
            });

            $('.btn-decrement').on('click', function() {
                const $input = $(this).siblings('.quantity-input');
                const itemKey = $(this).closest('.cart-item').data('item-key');
                let quantity = parseInt($input.val()) - 1;
                if (quantity < 1) quantity = 0;
                $input.val(quantity);
                updateCart(itemKey, quantity, $(this));
            });

            $('.quantity-input').on('change', function() {
                const itemKey = $(this).closest('.cart-item').data('item-key');
                const quantity = parseInt($(this).val());
                if (quantity < 1) $(this).val(1);
                updateCart(itemKey, quantity, $(this));
            });

            $('.btn-remove').on('click', function(e) {
                e.preventDefault();
                const itemKey = $(this).closest('.cart-item').data('item-key');
                removeCartItem(itemKey, $(this));
            });
        });
    </script>
    <?php include_once './config/footer.php'; ?>
</body>
</html>