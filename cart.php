<?php
session_set_cookie_params(['path' => '/']);
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
                const cleanedText = text.replace(/%+$/, '').trim();
                return JSON.parse(cleanedText);
            })
            .then(data => {
                updateCartCount();
            })
            .catch(error => {});
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
    'sommerrollen', 'specialrolls', 'suesssauersauce', 'suppen', 'temaki', 'warmgetraenke',
    'yanarolls', 'yellowcurry'
];

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Digitale Speisekarte - Warenkorb</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
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
                            continue;
                        }
                        list($table, $item_id) = explode(':', $item_key);
                        if (!in_array($table, $tables)) continue;
                        // Use 'id' instead of 'artikelnummer' to match the item_key
                        $stmt = $conn->prepare("SELECT artikelname, preis, image FROM " . mysqli_real_escape_string($conn, $table) . " WHERE id = ?");
                        $stmt->bind_param("i", $item_id); // Use 'i' since id is an integer
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($item = $result->fetch_assoc()) {
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
                    <a href="final_order.php" class="btn">Bestätigen</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script>
        $(document).ready(function() {
            // Reusable toast function
            function showRemoveToast() {
                Toastify({
                    text: '<i class="fas fa-trash"></i> Artikel entfernt',
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    escapeMarkup: false,
                    style: {
                        background: "#6A2477",
                    }
                }).showToast();
            }

            function updateCart(itemKey, quantity, element) {
                $.ajax({
                    url: 'cart.php',
                    type: 'POST',
                    data: { action: 'update', item_key: itemKey, quantity: quantity },
                    success: function(response) {
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
                                showRemoveToast();
                            } else {
                                $item.find('.quantity').text(newSubtotal.toFixed(2).replace('.', ',') + ' €');
                                updateTotal();
                            }
                        }
                    },
                    error: function(xhr, status, error) {}
                });
            }

            function removeCartItem(itemKey, element) {
                $.ajax({
                    url: 'cart.php',
                    type: 'POST',
                    data: { action: 'remove', item_key: itemKey },
                    success: function(response) {
                        if (response.success) {
                            element.closest('.cart-item').remove();
                            updateTotal();
                            if ($('.cart-item').length === 0) {
                                $('.cart-items').html('<div class="cart-empty"><p>Ihr Warenkorb ist leer.</p></div>');
                                $('.cart-buttons').remove();
                            }
                            showRemoveToast();
                        }
                    },
                    error: function(xhr, status, error) {}
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
                if (quantity < 0) quantity = 0;
                $input.val(quantity);
                updateCart(itemKey, quantity, $(this));
            });

            $('.quantity-input').on('change', function() {
                const itemKey = $(this).closest('.cart-item').data('item-key');
                let quantity = parseInt($(this).val());
                if (isNaN(quantity) || quantity < 0) quantity = 0;
                $(this).val(quantity);
                updateCart(itemKey, quantity, $(this));
            });

            $('.btn-remove').on('click', function(e) {
                e.preventDefault();
                const itemKey = $(this).closest('.cart-item').data('item-key');
                removeCartItem(itemKey, $(this));
            });
        });
    </script>
    <?php include_once './config/floating_bar.php'; ?>
    <?php include_once './config/footer.php'; ?>
    <?php $conn->close(); ?>
</body>
</html>