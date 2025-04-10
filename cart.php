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

// Use session language, override with URL parameter if provided
$lang = isset($_GET['lang']) ? $_GET['lang'] : $_SESSION['language'];
$_SESSION['language'] = $lang; // Sync session with URL param if provided
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title data-translate="cart_page_title">Digitale Speisekarte - Warenkorb</title>
    <link rel="stylesheet" href="<?php echo addCacheBuster(ASSETS_CSS . 'styles.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'translate.js'); ?>"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'skripte.js'); ?>"></script>
</head>
<body class="navigation" data-page="cart" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart="<?php echo htmlspecialchars(json_encode($_SESSION['cart'])); ?>" data-lang="<?php echo htmlspecialchars($lang); ?>">
    <header>
        <a href="<?php echo getUrlWithLang(URL_HOME); ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <?php 
    $page_title = 'Speisekarte';
    $data_translate = 'page_title';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/config/page-title.php'; 
    ?>
    <main>
        <div class="cart-wrapper">
            <div class="cart-header">
                <h1 data-translate="your_cart">Ihr Warenkorb</h1>
            </div>
            <div class="cart-items">
                <?php if (empty($_SESSION['cart'])): ?>
                    <div class="cart-empty">
                        <p data-translate="cart_empty">Ihr Warenkorb ist leer.</p>
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
                        $stmt = $conn->prepare("SELECT artikelnummer, artikelname, preis, image FROM " . mysqli_real_escape_string($conn, $table) . " WHERE id = ?");
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
                                    <h3><?php echo htmlspecialchars($item['artikelnummer']); ?></h3>
                                    <h3><?php echo htmlspecialchars($item['artikelname']); ?></h3>
                                    <p><?php echo number_format($price, 2, ',', '.'); ?> €</p>
                                </div>
                                <div class="cart-item-actions">
                                    <div class="quantity-controls">
                                        <button type="button" class="btn-decrement" data-item-key="<?php echo htmlspecialchars($item_key); ?>">-</button>
                                        <input type="number" name="quantity" value="<?php echo $quantity; ?>" min="0" class="quantity-input" data-item-key="<?php echo htmlspecialchars($item_key); ?>">
                                        <button type="button" class="btn-increment" data-item-key="<?php echo htmlspecialchars($item_key); ?>">+</button>
                                    </div>
                                    <span class="quantity"><?php echo number_format($subtotal, 2, ',', '.'); ?> €</span>
                                    <button type="button" class="btn-remove" data-item-key="<?php echo htmlspecialchars($item_key); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php
                        }
                        $stmt->close();
                    }
                    ?>
                    <div class="cart-total">
                        <span data-translate="total_amount">Gesamtbetrag</span>
                        <span class="total-amount"><?php echo number_format($total, 2, ',', '.'); ?> €</span>
                    </div>
                    <div class="timer" id="inactivity-timer" aria-live="polite" data-translate="inactivity_timer">
                        Inaktivitätstimer: <span id="timer-countdown"></span>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($_SESSION['cart'])): ?>
                <div class="cart-buttons">
                    <a href="<?php echo getUrlWithLang(URL_FINAL_ORDER); ?>" class="btn" data-translate="confirm">Bestätigen</a>
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
    </script>
    <div id="branch-spinner"></div>
    <?php $conn->close(); ?>
</body>
</html>