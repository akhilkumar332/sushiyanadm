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
    <title>Digitale Speisekarte</title>
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
        <h1 class="page-title">Speisekarte</h1>
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
            $menu_items = [
                ['href' => MENU_YANA . '?category=suppen', 'img' => ASSETS_IMAGES . 'startseite/Suppe.jpg', 'alt' => 'Suppen', 'text' => 'Suppe'],
                ['href' => MENU_YANA . '?category=fingerfood', 'img' => ASSETS_IMAGES . 'startseite/gegrillt.jpg', 'alt' => 'Vorspeisen', 'text' => 'Vorspeise'],
                ['href' => MENU_YANA . '?category=gyoza', 'img' => ASSETS_IMAGES . 'startseite/Gyoza.jpg', 'alt' => 'Gyoza', 'text' => 'Gyoza'],
                ['href' => MENU_YANA . '?category=salate', 'img' => ASSETS_IMAGES . 'startseite/glasnudelsalat_scampis.jpg', 'alt' => 'Salate', 'text' => 'Salat'],
                ['href' => MENU_YANA . '?category=sommerrollen', 'img' => ASSETS_IMAGES . 'startseite/Sommerrolle_Ebi.jpg', 'alt' => 'Sommerrolle', 'text' => 'Sommerrolle'],
                ['href' => BASE_PATH . 'sushi.php', 'img' => ASSETS_IMAGES . 'startseite/sushi.jpg', 'alt' => 'Sushi', 'text' => 'Sushi'],
                ['href' => BASE_PATH . 'warmekueche.php', 'img' => ASSETS_IMAGES . 'startseite/Nudeln.jpg', 'alt' => 'Warme Küche', 'text' => 'Warme Küche'],
                ['href' => MENU_YANA . '?category=bowls', 'img' => ASSETS_IMAGES . 'startseite/Poke_Bowl.jpg', 'alt' => 'Bowl', 'text' => 'Bowl'],
                ['href' => MENU_YANA . '?category=desserts', 'img' => ASSETS_IMAGES . 'startseite/Desserts.jpg', 'alt' => 'Dessert', 'text' => 'Dessert'],
                ['href' => MENU_YANA . '?category=extras', 'img' => ASSETS_IMAGES . 'startseite/Extra_Soßen.jpg', 'alt' => 'Extras', 'text' => 'Extras'],
                ['href' => MENU_YANA . '?category=getraenke', 'img' => ASSETS_IMAGES . 'startseite/Getraenke.jpg', 'alt' => 'Getränke', 'text' => 'Getränke'],
                ['href' => MENU_YANA . '?category=warmgetraenke', 'img' => ASSETS_IMAGES . 'startseite/kaffee.jpg', 'alt' => 'Warme Getränke', 'text' => 'Warme Getränke'],
            ];
            foreach ($menu_items as $item) {
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
        const cachedMenu = localStorage.getItem('cachedMenu');
        $('#loading-spinner').show();
        $('#menu-grid').addClass('loading');
        if (cachedMenu) {
            $('#menu-grid').html(cachedMenu);
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
                url: BASE_PATH + 'config/load_menu.php',
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
                    localStorage.setItem('cachedMenu', data);
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
                            url: BASE_PATH + 'config/load_menu.php',
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
                                localStorage.setItem('cachedMenu', data);
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