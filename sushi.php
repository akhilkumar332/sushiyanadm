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
    <title>Digitale Speisekarte - Sushi</title>
    <link rel="stylesheet" href="/css/styles.css">
    <!-- Add Font Awesome CDN -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="navigation">
    <header>
        <a href="./"><img src="/bilder/logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>

    <div class="grid-container">
        <a href="sushi/menu.php?category=menues" class="grid-item">
            <img src="/bilder/sushi/Ebi_Menu.jpg" alt="Menüs">
            <div class="text">Menüs</div>
        </a>
        <a href="sushi/menu.php?category=sashimi" class="grid-item">
            <img src="/bilder/sushi/Sashimi_Sake.jpg" alt="Sashimi">
            <div class="text">Sashimi</div>
        </a>
        <a href="sushi/menu.php?category=makis" class="grid-item">
            <img src="/bilder/sushi/maki.jpg" alt="Makis">
            <div class="text">Makis</div>
        </a>
        <a href="sushi/menu.php?category=insideoutrolls" class="grid-item">
            <img src="/bilder/sushi/ioroll.jpg" alt="Inside Out Rolls">
            <div class="text">Inside Out Rolls</div>
        </a>
        <a href="sushi/menu.php?category=miniyanarolls" class="grid-item">
            <img src="/bilder/sushi/miniyanaroll.jpg" alt="Mini Yana Rolls">
            <div class="text">Mini Yana Rolls</div>
        </a>
        <a href="sushi/menu.php?category=yanarolls" class="grid-item">
            <img src="/bilder/sushi/yanaroll.jpg" alt="Yana Rolls">
            <div class="text">Yana Rolls</div>
        </a>
        <a href="sushi/menu.php?category=nigiris" class="grid-item">
            <img src="/bilder/sushi/Nigiris_Head.jpg" alt="Nigiris">
            <div class="text">Nigiris</div>
        </a>
        <a href="sushi/menu.php?category=specialrolls" class="grid-item">
            <img src="/bilder/sushi/special.jpg" alt="Special Rolls">
            <div class="text">Special Rolls</div>
        </a>
        <a href="sushi/menu.php?category=temaki" class="grid-item">
            <img src="/bilder/sushi/temaki.jpg" alt="Temaki">
            <div class="text">Temaki</div>
        </a>
        <a href="sushi/vegetarisch.php" class="grid-item">
            <img src="/bilder/sushi/Maki_Wakame.jpg" alt="Vegetarisch">
            <div class="text">Vegetarisch</div>
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