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
            .then(response => response.text())
            .then(text => JSON.parse(text.replace(/%+$/, '').trim()))
            .then(data => updateCartCount())
            .catch(error => {});
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
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Preload logo -->
    <link rel="preload" href="/bilder/logo.webp" as="image">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>
<body class="navigation">
    <header>
        <a href="./"><img src="/bilder/logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <div>
        <h1 class="page-title">Sushi</h1>
    </div>
    <div class="loading-spinner" id="loading-spinner"></div>
    <div class="grid-container" id="menu-grid" aria-busy="true">
        <!-- Skeleton placeholders (10 to match sushi items) -->
        <div class="skeleton-item">
            <div class="skeleton-image"></div>
            <div class="skeleton-details">
                <div class="skeleton-text"></div>
            </div>
        </div>
        <div class="skeleton-item">
            <div class="skeleton-image"></div>
            <div class="skeleton-details">
                <div class="skeleton-text"></div>
            </div>
        </div>
        <div class="skeleton-item">
            <div class="skeleton-image"></div>
            <div class="skeleton-details">
                <div class="skeleton-text"></div>
            </div>
        </div>
        <div class="skeleton-item">
            <div class="skeleton-image"></div>
            <div class="skeleton-details">
                <div class="skeleton-text"></div>
            </div>
        </div>
        <div class="skeleton-item">
            <div class="skeleton-image"></div>
            <div class="skeleton-details">
                <div class="skeleton-text"></div>
            </div>
        </div>
        <div class="skeleton-item">
            <div class="skeleton-image"></div>
            <div class="skeleton-details">
                <div class="skeleton-text"></div>
            </div>
        </div>
        <div class="skeleton-item">
            <div class="skeleton-image"></div>
            <div class="skeleton-details">
                <div class="skeleton-text"></div>
            </div>
        </div>
        <div class="skeleton-item">
            <div class="skeleton-image"></div>
            <div class="skeleton-details">
                <div class="skeleton-text"></div>
            </div>
        </div>
        <div class="skeleton-item">
            <div class="skeleton-image"></div>
            <div class="skeleton-details">
                <div class="skeleton-text"></div>
            </div>
        </div>
        <div class="skeleton-item">
            <div class="skeleton-image"></div>
            <div class="skeleton-details">
                <div class="skeleton-text"></div>
            </div>
        </div>
    </div>
    <noscript>
        <div class="grid-container" id="menu-grid">
            <?php
            $sushi_items = [
                ['href' => 'sushi/menu.php?category=menues', 'img' => 'bilder/sushi/Ebi_Menu.jpg', 'alt' => 'Menüs', 'text' => 'Menüs'],
                ['href' => 'sushi/menu.php?category=sashimi', 'img' => 'bilder/sushi/Sashimi_Sake.jpg', 'alt' => 'Sashimi', 'text' => 'Sashimi'],
                ['href' => 'sushi/menu.php?category=makis', 'img' => 'bilder/sushi/maki.jpg', 'alt' => 'Makis', 'text' => 'Makis'],
                ['href' => 'sushi/menu.php?category=insideoutrolls', 'img' => 'bilder/sushi/ioroll.jpg', 'alt' => 'Inside Out Rolls', 'text' => 'Inside Out Rolls'],
                ['href' => 'sushi/menu.php?category=miniyanarolls', 'img' => 'bilder/sushi/miniyanaroll.jpg', 'alt' => 'Mini Yana Rolls', 'text' => 'Mini Yana Rolls'],
                ['href' => 'sushi/menu.php?category=yanarolls', 'img' => 'bilder/sushi/yanaroll.jpg', 'alt' => 'Yana Rolls', 'text' => 'Yana Rolls'],
                ['href' => 'sushi/menu.php?category=nigiris', 'img' => 'bilder/sushi/Nigiris_Head.jpg', 'alt' => 'Nigiris', 'text' => 'Nigiris'],
                ['href' => 'sushi/menu.php?category=specialrolls', 'img' => 'bilder/sushi/special.jpg', 'alt' => 'Special Rolls', 'text' => 'Special Rolls'],
                ['href' => 'sushi/menu.php?category=temaki', 'img' => 'bilder/sushi/temaki.jpg', 'alt' => 'Temaki', 'text' => 'Temaki'],
                ['href' => 'sushi/vegetarisch.php', 'img' => 'bilder/sushi/Maki_Wakame.jpg', 'alt' => 'Vegetarisch', 'text' => 'Vegetarisch'],
            ];
            foreach ($sushi_items as $item) {
                echo '<a href="' . htmlspecialchars($item['href']) . '" class="grid-item">';
                echo '<div class="category-image-container">';
                echo '<img src="' . htmlspecialchars($item['img']) . '" alt="' . htmlspecialchars($item['alt']) . '" class="category-image" loading="lazy">';
                echo '</div>';
                echo '<div class="category-details">';
                echo '<h2 class="category-name">' . htmlspecialchars($item['text']) . '</h2>';
                echo '</div>';
                echo '</a>';
            }
            ?>
        </div>
    </noscript>
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

        // Load sushi menu asynchronously
        $(document).ready(function() {
            const cachedSushiMenu = localStorage.getItem('cachedSushiMenu');
            $('#loading-spinner').show();
            $('#menu-grid').addClass('loading');
            if (cachedSushiMenu) {
                $('#menu-grid').html(cachedSushiMenu);
                $('#menu-grid .grid-item').each(function(index) {
                    $(this).css('animation-delay', (index * 0.1) + 's');
                    const categoryName = $(this).find('.category-name').text();
                    if (categoryName) {
                        $(this).attr('aria-label', categoryName);
                    }
                });
                $('#menu-grid').removeClass('loading');
                $('#menu-grid').attr('aria-busy', 'false');
                $('#loading-spinner').hide();
            } else {
                $.ajax({
                    url: '/config/load_sushi_menu.php',
                    method: 'GET',
                    success: function(data) {
                        $('#menu-grid').html(data);
                        $('#menu-grid .grid-item').each(function(index) {
                            $(this).css('animation-delay', (index * 0.1) + 's');
                            const categoryName = $(this).find('.category-name').text();
                            if (categoryName) {
                                $(this).attr('aria-label', categoryName);
                            }
                        });
                        localStorage.setItem('cachedSushiMenu', data);
                        $('#menu-grid').removeClass('loading');
                        $('#menu-grid').attr('aria-busy', 'false');
                        $('#loading-spinner').hide();
                    },
                    error: function() {
                        $('#menu-grid').html('<p>Fehler beim Laden des Menüs. <button id="retry-menu">Erneut versuchen</button></p>');
                        $('#menu-grid').removeClass('loading');
                        $('#menu-grid').attr('aria-busy', 'false');
                        $('#loading-spinner').hide();
                        $('#retry-menu').on('click', function() {
                            $('#menu-grid').html('');
                            $('#menu-grid').addClass('loading');
                            $('#menu-grid').attr('aria-busy', 'true');
                            $('#loading-spinner').show();
                            $.ajax({
                                url: '/config/load_sushi_menu.php',
                                method: 'GET',
                                success: function(data) {
                                    $('#menu-grid').html(data);
                                    $('#menu-grid .grid-item').each(function(index) {
                                        $(this).css('animation-delay', (index * 0.1) + 's');
                                        const categoryName = $(this).find('.category-name').text();
                                        if (categoryName) {
                                            $(this).attr('aria-label', categoryName);
                                        }
                                    });
                                    localStorage.setItem('cachedSushiMenu', data);
                                    $('#menu-grid').removeClass('loading');
                                    $('#menu-grid').attr('aria-busy', 'false');
                                    $('#loading-spinner').hide();
                                },
                                error: function() {
                                    $('#menu-grid').html('<p>Fehler beim Laden des Menüs. <button id="retry-menu">Erneut versuchen</button></p>');
                                    $('#menu-grid').removeClass('loading');
                                    $('#menu-grid').attr('aria-busy', 'false');
                                    $('#loading-spinner').hide();
                                }
                            });
                        });
                    }
                });
            }
        });

        window.onload = updateLocalCart;
        setInterval(updateCartCount, 1000);
    </script>
</body>
</html>