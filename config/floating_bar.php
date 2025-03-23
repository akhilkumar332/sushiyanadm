<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_path = '/';
$current_page = basename($_SERVER['PHP_SELF']);
$hide_cart_button = in_array($current_page, ['cart.php', 'final_order.php']);
// Show back button on both cart.php and final_order.php
$show_back_button = in_array($current_page, ['cart.php', 'final_order.php']);

// Determine the back button URL based on the current page
$back_url = ($current_page === 'cart.php') ? $base_path . 'index.php' : $base_path . 'cart.php';
?>

<div class="floating-bar-wrapper">
    <div class="floating-bar">
        <?php if ($show_back_button): ?>
            <a href="<?php echo $back_url; ?>" class="back-button">
                <i class="fas fa-arrow-left"></i>
            </a>
        <?php endif; ?>
        <?php if (!$hide_cart_button): ?>
            <a href="<?php echo $base_path; ?>cart.php" class="cart-button">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count" id="cart-count"><?php echo array_sum($_SESSION['cart'] ?? []); ?></span>
            </a>
        <?php endif; ?>
        <a href="<?php echo $base_path; ?>index.php" class="home-button">
            <i class="fas fa-home"></i>
        </a>
    </div>
</div>