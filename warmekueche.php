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
    <!-- Add Font Awesome CDN -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="navigation">
    <header>
        <a href="./"><img src="/bilder/logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>

    <div class="grid-container">
        <a href="warmekueche/nudeln.php" class="grid-item">
            <img src="/bilder/warmekueche/gebratene_nudeln.jpg" alt="Gebratene Nudeln">
            <div class="text">Gebratene Nudeln</div>
        </a>
        <a href="warmekueche/reis.php" class="grid-item">
            <img src="/bilder/warmekueche/gebratener_reis.jpg" alt="Gebratener Reis">
            <div class="text">Gebratener Reis</div>
        </a>
        <a href="warmekueche/gemuese.php" class="grid-item">
            <img src="/bilder/warmekueche/gebratenes_gemuese.jpg" alt="Gebratenes Gemüse">
            <div class="text">Gebratenes Gemüse</div>
        </a>
        <a href="warmekueche/yellowcurry.php" class="grid-item">
            <img src="/bilder/warmekueche/yellow_curry.jpg" alt="Yellow Curry">
            <div class="text">Yellow Curry</div>
        </a>
        <a href="warmekueche/mangochutney.php" class="grid-item">
            <img src="/bilder/warmekueche/mango_chutney.jpg" alt="Mango Chutney">
            <div class="text">Mango Chutney</div>
        </a>
        <a href="warmekueche/erdnussgericht.php" class="grid-item">
            <img src="/bilder/warmekueche/erdnussgericht.jpg" alt="Erdnussgericht">
            <div class="text">Erdnussgericht</div>
        </a>
        <a href="warmekueche/chopsuey.php" class="grid-item">
            <img src="/bilder/warmekueche/chop_suey.jpg" alt="Chop Suey">
            <div class="text">Chop Suey</div>
        </a>
        <a href="warmekueche/redcurry.php" class="grid-item">
            <img src="/bilder/warmekueche/red_curry.jpg" alt="Red Curry">
            <div class="text">Red Curry</div>
        </a>
        <a href="warmekueche/suesssauersauce.php" class="grid-item">
            <img src="/bilder/warmekueche/suess_sauer_sauce.jpg" alt="Süss-Sauer Sauce">
            <div class="text">Süss-Sauer Sauce</div>
        </a>
        <a href="warmekueche/extras.php" class="grid-item">
            <img src="/bilder/warmekueche/Extras.jpg" alt="Extras">
            <div class="text">Extras</div>
        </a>
    </div>
    
<?php include_once './config/floating_bar.php'; ?>
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