<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

$current_page = basename($_SERVER['PHP_SELF']);
$hide_cart_button = in_array($current_page, ['cart.php', 'final_order.php', 'online-orders.php']);
$show_back_button = in_array($current_page, ['cart.php', 'final_order.php']);
$back_url = ($current_page === 'cart.php') ? URL_HOME : URL_CART;

// Calculate total cart items
$total_items = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item_key => $item_data) {
        $quantity = is_array($item_data) ? (int)($item_data['quantity'] ?? 0) : (int)$item_data;
        $total_items += $quantity;
    }
}
?>

<div class="floating-bar-wrapper">
    <div class="floating-bar">
        <?php if ($show_back_button): ?>
            <a href="<?php echo $back_url; ?>" class="back-button">
                <i class="fas fa-arrow-left"></i>
            </a>
        <?php endif; ?>
        <?php if (!$hide_cart_button): ?>
            <a href="<?php echo URL_CART; ?>" class="cart-button">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count" id="cart-count"><?php echo $total_items; ?></span>
            </a>
        <?php endif; ?>
        <a href="<?php echo URL_HOME; ?>" class="home-button">
            <i class="fas fa-home"></i>
        </a>
    </div>
</div>