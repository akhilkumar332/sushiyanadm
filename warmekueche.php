<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    echo "<script>
        if (localStorage.getItem('cart')) {
            fetch('" . URL_RESTORE_CART . "', {
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
    <title>Digitale Speisekarte - Warme Küche</title>
    <link rel="stylesheet" href="<?php echo ASSETS_CSS; ?>styles.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preload" href="<?php echo ASSETS_IMAGES; ?>logo.webp" as="image">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>
<body class="navigation">
    <header>
        <a href="<?php echo URL_HOME; ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <div>
        <h1 class="page-title">Warme Küche</h1>
    </div>
    <div class="loading-spinner" id="loading-spinner"></div>
    <div class="grid-container" id="menu-grid" aria-busy="true">
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
            $warmekueche_items = [
                ['href' => MENU_WARMEKUECHE . '?category=nudeln', 'img' => ASSETS_IMAGES . 'warmekueche/gebratene_nudeln.jpg', 'alt' => 'Gebratene Nudeln', 'text' => 'Gebratene Nudeln'],
                ['href' => MENU_WARMEKUECHE . '?category=reis', 'img' => ASSETS_IMAGES . 'warmekueche/gebratener_reis.jpg', 'alt' => 'Gebratener Reis', 'text' => 'Gebratener Reis'],
                ['href' => MENU_WARMEKUECHE . '?category=gemuese', 'img' => ASSETS_IMAGES . 'warmekueche/gebratenes_gemuese.jpg', 'alt' => 'Gebratenes Gemüse', 'text' => 'Gebratenes Gemüse'],
                ['href' => MENU_WARMEKUECHE . '?category=yellowcurry', 'img' => ASSETS_IMAGES . 'warmekueche/yellow_curry.jpg', 'alt' => 'Yellow Curry', 'text' => 'Yellow Curry'],
                ['href' => MENU_WARMEKUECHE . '?category=mangochutney', 'img' => ASSETS_IMAGES . 'warmekueche/mango_chutney.jpg', 'alt' => 'Mango Chutney', 'text' => 'Mango Chutney'],
                ['href' => MENU_WARMEKUECHE . '?category=erdnussgericht', 'img' => ASSETS_IMAGES . 'warmekueche/erdnussgericht.jpg', 'alt' => 'Erdnussgericht', 'text' => 'Erdnussgericht'],
                ['href' => MENU_WARMEKUECHE . '?category=chopsuey', 'img' => ASSETS_IMAGES . 'warmekueche/chop_suey.jpg', 'alt' => 'Chop Suey', 'text' => 'Chop Suey'],
                ['href' => MENU_WARMEKUECHE . '?category=redcurry', 'img' => ASSETS_IMAGES . 'warmekueche/red_curry.jpg', 'alt' => 'Red Curry', 'text' => 'Red Curry'],
                ['href' => MENU_WARMEKUECHE . '?category=suesssauersauce', 'img' => ASSETS_IMAGES . 'warmekueche/suess_sauer_sauce.jpg', 'alt' => 'Süss-Sauer Sauce', 'text' => 'Süss-Sauer Sauce'],
                ['href' => MENU_WARMEKUECHE . '?category=extras', 'img' => ASSETS_IMAGES . 'warmekueche/Extras.jpg', 'alt' => 'Extras', 'text' => 'Extras'],
            ];
            foreach ($warmekueche_items as $item) {
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
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/floating_bar.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/footer.php'; ?>
    <script>
        const BASE_PATH = '<?php echo BASE_PATH; ?>';
        function updateLocalCart() {
            const cart = <?php echo json_encode($_SESSION['cart']); ?>;
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
        }
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart') || '{}');
            document.getElementById('cart-count').innerText = Object.values(cart).reduce((a, b) => a + b, 0);
        }

        $(document).ready(function() {
            const cachedWarmekuecheMenu = localStorage.getItem('cachedWarmekuecheMenu');
            $('#loading-spinner').show();
            $('#menu-grid').addClass('loading');
            if (cachedWarmekuecheMenu) {
                $('#menu-grid').html(cachedWarmekuecheMenu);
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
                    url: BASE_PATH + 'config/load_warmekueche_menu.php',
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
                        localStorage.setItem('cachedWarmekuecheMenu', data);
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
                                url: BASE_PATH + 'config/load_warmekueche_menu.php',
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
                                    localStorage.setItem('cachedWarmekuecheMenu', data);
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