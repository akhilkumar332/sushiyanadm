<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Always use root-relative path
$base_path = '/'; // Ensures links start from document root
?>

<style>
    .floating-bar-wrapper {
        position: fixed !important;
        bottom: 70px !important;
        right: 20px !important;
        z-index: 2000 !important;
        pointer-events: none !important;
    }

    .floating-bar {
        display: flex !important;
        flex-direction: column !important;
        gap: 10px !important;
        background: none !important;
        pointer-events: auto !important;
    }

    .floating-bar .cart-button, 
    .floating-bar .home-button {
        background: #6A2477 !important;
        color: #fff !important;
        padding: 10px !important;
        border-radius: 50% !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
        text-decoration: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 40px !important;
        height: 40px !important;
        transition: all 0.3s ease !important;
        position: relative !important;
    }

    .floating-bar .cart-button:hover, 
    .floating-bar .home-button:hover {
        background: #4A1A55 !important;
        transform: scale(1.05) !important;
    }

    .floating-bar .cart-button .fa-shopping-cart {
        font-size: 18px !important;
    }

    .floating-bar .home-button .fa-home {
        font-size: 18px !important;
    }

    .floating-bar .cart-count {
        position: absolute !important;
        top: -8px !important;
        right: -8px !important;
        background: #6A2477 !important;
        color: #fff !important;
        font-size: 12px !important;
        font-weight: bold !important;
        padding: 2px 6px !important;
        border-radius: 10px !important;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2) !important;
    }

    @media (max-width: 768px) {
        .floating-bar-wrapper {
            bottom: 60px !important;
        }

        .floating-bar .cart-button, 
        .floating-bar .home-button {
            width: 32px !important;
            height: 32px !important;
            padding: 8px !important;
        }

        .floating-bar .cart-button .fa-shopping-cart, 
        .floating-bar .home-button .fa-home {
            font-size: 14px !important;
        }

        .floating-bar .cart-count {
            font-size: 10px !important;
            padding: 1px 4px !important;
            top: -6px !important;
            right: -6px !important;
        }
    }
</style>

<div class="floating-bar-wrapper">
    <div class="floating-bar">
        <a href="<?php echo $base_path; ?>cart.php" class="cart-button">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count" id="cart-count"><?php echo array_sum($_SESSION['cart'] ?? []); ?></span>
        </a>
        <a href="<?php echo $base_path; ?>index.php" class="home-button">
            <i class="fas fa-home"></i>
        </a>
    </div>
</div>