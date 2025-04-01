<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Digitale Speisekarte</title>
    <link rel="stylesheet" href="<?php echo ASSETS_CSS; ?>styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preload" href="<?php echo ASSETS_IMAGES; ?>logo.webp" as="image">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="/skripte/skripte.js"></script>
</head>
<body class="navigation" data-page="index" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart="<?php echo htmlspecialchars(json_encode($_SESSION['cart'])); ?>">
    <header>
        <a href="<?php echo URL_HOME; ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <div>
        <h1 class="page-title">Speisekarte</h1>
    </div>
    <div class="loading-spinner" id="loading-spinner"></div>
    <div class="grid-container" id="menu-grid" aria-busy="true">
        <div class="skeleton-item"><div class="skeleton-image"></div><div class="skeleton-details"><div class="skeleton-text"></div></div></div>
        <div class="skeleton-item"><div class="skeleton-image"></div><div class="skeleton-details"><div class="skeleton-text"></div></div></div>
        <div class="skeleton-item"><div class="skeleton-image"></div><div class="skeleton-details"><div class="skeleton-text"></div></div></div>
        <div class="skeleton-item"><div class="skeleton-image"></div><div class="skeleton-details"><div class="skeleton-text"></div></div></div>
        <div class="skeleton-item"><div class="skeleton-image"></div><div class="skeleton-details"><div class="skeleton-text"></div></div></div>
        <div class="skeleton-item"><div class="skeleton-image"></div><div class="skeleton-details"><div class="skeleton-text"></div></div></div>
        <div class="skeleton-item"><div class="skeleton-image"></div><div class="skeleton-details"><div class="skeleton-text"></div></div></div>
        <div class="skeleton-item"><div class="skeleton-image"></div><div class="skeleton-details"><div class="skeleton-text"></div></div></div>
        <div class="skeleton-item"><div class="skeleton-image"></div><div class="skeleton-details"><div class="skeleton-text"></div></div></div>
        <div class="skeleton-item"><div class="skeleton-image"></div><div class="skeleton-details"><div class="skeleton-text"></div></div></div>
        <div class="skeleton-item"><div class="skeleton-image"></div><div class="skeleton-details"><div class="skeleton-text"></div></div></div>
        <div class="skeleton-item"><div class="skeleton-image"></div><div class="skeleton-details"><div class="skeleton-text"></div></div></div>
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
                ['href' => URL_SUSHI, 'img' => ASSETS_IMAGES . 'startseite/sushi.jpg', 'alt' => 'Sushi', 'text' => 'Sushi'],
                ['href' => URL_WARMEKUECHE, 'img' => ASSETS_IMAGES . 'startseite/Nudeln.jpg', 'alt' => 'Warme Küche', 'text' => 'Warme Küche'],
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
</body>
</html>