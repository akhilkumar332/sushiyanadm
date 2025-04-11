<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Prioritize query parameter 'lang' over session, then default to 'de'
$current_lang = isset($_GET['lang']) ? $_GET['lang'] : ($_SESSION['language'] ?? 'de');
$_SESSION['language'] = $current_lang; // Sync session with current language
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title data-translate="warm_kitchen_page_title">Digitale Speisekarte - Warme Küche</title>
    <link rel="stylesheet" href="<?php echo addCacheBuster(ASSETS_CSS . 'styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo addCacheBuster(ASSETS_CSS . 'style-menu.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preload" href="<?php echo ASSETS_IMAGES; ?>logo.webp" as="image">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'translate.js'); ?>"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'skripte.js'); ?>"></script>
</head>
<body class="navigation" data-page="warmekueche" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart="<?php echo htmlspecialchars(json_encode($_SESSION['cart'])); ?>" data-lang="<?php echo htmlspecialchars($current_lang); ?>">
    <header>
        <a href="<?php echo URL_HOME . '?lang=' . htmlspecialchars($current_lang); ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <?php
    $page_title = 'Warme Küche';
    $data_translate = 'warm_kitchen';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/config/page-title.php';
    ?>
    <div class="timer" id="inactivity-timer" aria-live="polite" data-translate="inactivity_timer">
        Inaktivitätstimer: <span id="timer-countdown"></span>
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
    </div>
    <noscript>
        <div class="grid-container" id="menu-grid">
            <?php
            $warmekueche_items = [
                ['href' => MENU_WARMEKUECHE . '?category=nudeln', 'img' => ASSETS_IMAGES . 'warmekueche/gebratene_nudeln.jpg', 'alt' => 'Gebratene Nudeln', 'text' => 'Gebratene Nudeln', 'key' => 'fried_noodles'],
                ['href' => MENU_WARMEKUECHE . '?category=reis', 'img' => ASSETS_IMAGES . 'warmekueche/gebratener_reis.jpg', 'alt' => 'Gebratener Reis', 'text' => 'Gebratener Reis', 'key' => 'fried_rice'],
                ['href' => MENU_WARMEKUECHE . '?category=gemuese', 'img' => ASSETS_IMAGES . 'warmekueche/gebratenes_gemuese.jpg', 'alt' => 'Gebratenes Gemüse', 'text' => 'Gebratenes Gemüse', 'key' => 'fried_vegetables'],
                ['href' => MENU_WARMEKUECHE . '?category=yellowcurry', 'img' => ASSETS_IMAGES . 'warmekueche/yellow_curry.jpg', 'alt' => 'Yellow Curry', 'text' => 'Yellow Curry', 'key' => 'yellow_curry'],
                ['href' => MENU_WARMEKUECHE . '?category=mangochutney', 'img' => ASSETS_IMAGES . 'warmekueche/mango_chutney.jpg', 'alt' => 'Mango Chutney', 'text' => 'Mango Chutney', 'key' => 'mango_chutney'],
                ['href' => MENU_WARMEKUECHE . '?category=erdnussgericht', 'img' => ASSETS_IMAGES . 'warmekueche/erdnussgericht.jpg', 'alt' => 'Erdnussgericht', 'text' => 'Erdnussgericht', 'key' => 'peanut_dish'],
                ['href' => MENU_WARMEKUECHE . '?category=chopsuey', 'img' => ASSETS_IMAGES . 'warmekueche/chop_suey.jpg', 'alt' => 'Chop Suey', 'text' => 'Chop Suey', 'key' => 'chop_suey'],
                ['href' => MENU_WARMEKUECHE . '?category=redcurry', 'img' => ASSETS_IMAGES . 'warmekueche/red_curry.jpg', 'alt' => 'Red Curry', 'text' => 'Red Curry', 'key' => 'red_curry'],
                ['href' => MENU_WARMEKUECHE . '?category=suesssauersauce', 'img' => ASSETS_IMAGES . 'warmekueche/suess_sauer_sauce.jpg', 'alt' => 'Süss-Sauer Sauce', 'text' => 'Süss-Sauer Sauce', 'key' => 'sweet_sour_sauce'],
                ['href' => MENU_WARMEKUECHE . '?category=extraswarm', 'img' => ASSETS_IMAGES . 'warmekueche/Extras.jpg', 'alt' => 'Extras Warm', 'text' => 'Extras Warm', 'key' => 'extraswarm'],
            ];
            foreach ($warmekueche_items as $item) {
                echo '<a href="' . htmlspecialchars($item['href']) . '" class="grid-item">';
                echo '<div class="category-image-container">';
                echo '<img src="' . htmlspecialchars($item['img']) . '" alt="' . htmlspecialchars($item['alt']) . '" class="category-image" loading="lazy">';
                echo '</div>';
                echo '<div class="category-details">';
                echo '<h2 class="category-name" data-translate="' . htmlspecialchars($item['key']) . '">' . htmlspecialchars($item['text']) . '</h2>';
                echo '</div>';
                echo '</a>';
            }
            ?>
        </div>
    </noscript>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/floating_bar.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/footer.php'; ?>
    <div id="branch-spinner"></div>
</body>
</html>