<?php
session_start();
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
                // Trim trailing % or whitespace
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
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Digitale Speisekarte</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body class="navigation">
    <header>
        <a href="./"><img src="/bilder/logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <div>
        <h1 class="page-title">Speisekarte</h1>
    </div>
    <div class="grid-container">
        <a href="yana/suppen.php" class="grid-item">
            <img src="bilder/startseite/Suppe.jpg" alt="Suppen">
            <div class="text">Suppe</div>
        </a>
        <a href="yana/fingerfood.php" class="grid-item">
            <img src="bilder/startseite/gegrillt.jpg" alt="Vorspeisen">
            <div class="text">Vorspeise</div>
        </a>
        <a href="yana/gyoza.php" class="grid-item">
            <img src="bilder/startseite/Gyoza.jpg" alt="Gyoza">
            <div class="text">Gyoza</div>
        </a>
        <a href="yana/salate.php" class="grid-item">
            <img src="bilder/startseite/glasnudelsalat_scampis.jpg" alt="Salate">
            <div class="text">Salat</div>
        </a>
        <a href="yana/sommerrollen.php" class="grid-item">
            <img src="bilder/startseite/Sommerrolle_Ebi.jpg" alt="Sommerrolle">
            <div class="text">Sommerrolle</div>
        </a>
        <a href="sushi.php" class="grid-item">
            <img src="bilder/startseite/sushi.jpg" alt="Sushi">
            <div class="text">Sushi</div>
        </a>
        <a href="warmekueche.php" class="grid-item">
            <img src="bilder/startseite/Nudeln.jpg" alt="Warme Küche">
            <div class="text">Warme Küche</div>
        </a>
        <a href="yana/bowls.php" class="grid-item">
            <img src="bilder/startseite/Poke_Bowl.jpg" alt="Bowl">
            <div class="text">Bowl</div>
        </a>   
        <a href="yana/desserts.php" class="grid-item">
            <img src="bilder/startseite/Desserts.jpg" alt="Dessert">
            <div class="text">Dessert</div>
        </a>
        <a href="yana/extras.php" class="grid-item">
            <img src="bilder/startseite/Extra_Soßen.jpg" alt="Extras">
            <div class="text">Extras</div>
        </a>
        <a href="yana/getraenke.php" class="grid-item">
            <img src="bilder/startseite/Getraenke.jpg" alt="Getränke">
            <div class="text">Getränke</div>
        </a>
        <a href="yana/warmgetraenke.php" class="grid-item">
            <img src="bilder/startseite/kaffee.jpg" alt="Warme Getränke">
            <div class="text">Warme Getränke</div>
        </a>
    </div>
    <div class="floating-bar">
        <a href="cart.php" class="cart-icon">
            <span>Cart (<span id="cart-count"><?php echo array_sum($_SESSION['cart']); ?></span>)</span>
        </a>
    </div>
    <?php include_once './config/footer.php'; ?>
    <script>
        function updateLocalCart() {
            const cart = <?php echo json_encode($_SESSION['cart']); ?>;
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
        }
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart') || '{}');
            document.getElementById('cart-count').innerText = Object.values(cart).reduce((a, b) => a + b, 0);
        }
        window.onload = updateLocalCart;
        setInterval(updateCartCount, 1000);
    </script>
</body>
</html>