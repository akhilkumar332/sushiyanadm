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
    <title data-translate="page_title">Digitale Speisekarte</title>
    <link rel="stylesheet" href="<?php echo addCacheBuster(ASSETS_CSS . 'styles.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preload" href="<?php echo ASSETS_IMAGES; ?>logo.webp" as="image">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'translate.js'); ?>"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'skripte.js'); ?>"></script>
</head>
<body class="navigation" data-page="index" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart="<?php echo htmlspecialchars(json_encode($_SESSION['cart'])); ?>" data-lang="<?php echo htmlspecialchars($current_lang); ?>">
    <header>
        <a href="<?php echo URL_HOME . '?lang=' . htmlspecialchars($current_lang); ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <?php 
    $page_title = 'Speisekarte';
    $data_translate = 'page_title';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/config/page-title.php'; 
    ?>
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
                ['href' => MENU_YANA . '?category=suppen', 'img' => ASSETS_IMAGES . 'startseite/Suppe.jpg', 'alt' => 'Suppen', 'text' => 'Suppe', 'key' => 'soup'],
                ['href' => MENU_YANA . '?category=fingerfood', 'img' => ASSETS_IMAGES . 'startseite/gegrillt.jpg', 'alt' => 'Vorspeisen', 'text' => 'Vorspeise', 'key' => 'starter'],
                ['href' => MENU_YANA . '?category=gyoza', 'img' => ASSETS_IMAGES . 'startseite/Gyoza.jpg', 'alt' => 'Gyoza', 'text' => 'Gyoza', 'key' => 'gyoza'],
                ['href' => MENU_YANA . '?category=salate', 'img' => ASSETS_IMAGES . 'startseite/glasnudelsalat_scampis.jpg', 'alt' => 'Salate', 'text' => 'Salat', 'key' => 'salad'],
                ['href' => MENU_YANA . '?category=sommerrollen', 'img' => ASSETS_IMAGES . 'startseite/Sommerrolle_Ebi.jpg', 'alt' => 'Sommerrolle', 'text' => 'Sommerrolle', 'key' => 'summer_roll'],
                ['href' => URL_SUSHI, 'img' => ASSETS_IMAGES . 'startseite/sushi.jpg', 'alt' => 'Sushi', 'text' => 'Sushi', 'key' => 'sushi'],
                ['href' => URL_WARMEKUECHE, 'img' => ASSETS_IMAGES . 'startseite/Nudeln.jpg', 'alt' => 'Warme Küche', 'text' => 'Warme Küche', 'key' => 'warm_kitchen'],
                ['href' => MENU_YANA . '?category=bowls', 'img' => ASSETS_IMAGES . 'startseite/Poke_Bowl.jpg', 'alt' => 'Bowl', 'text' => 'Bowl', 'key' => 'bowl'],
                ['href' => MENU_YANA . '?category=desserts', 'img' => ASSETS_IMAGES . 'startseite/Desserts.jpg', 'alt' => 'Dessert', 'text' => 'Dessert', 'key' => 'dessert'],
                ['href' => MENU_YANA . '?category=extras', 'img' => ASSETS_IMAGES . 'startseite/Extra_Soßen.jpg', 'alt' => 'Extras', 'text' => 'Extras', 'key' => 'extras'],
                ['href' => MENU_YANA . '?category=getraenke', 'img' => ASSETS_IMAGES . 'startseite/Getraenke.jpg', 'alt' => 'Getränke', 'text' => 'Getränke', 'key' => 'drinks'],
                ['href' => MENU_YANA . '?category=warmgetraenke', 'img' => ASSETS_IMAGES . 'startseite/kaffee.jpg', 'alt' => 'Warme Getränke', 'text' => 'Warme Getränke', 'key' => 'warm_drinks'],
            ];
            $texts_to_translate = [];
            foreach ($menu_items as $item) {
                $texts_to_translate[] = $item['text'];
                $texts_to_translate[] = $item['alt'];
            }
            $translated_texts = translateText($texts_to_translate, 'de', $current_lang, $conn);
            $text_index = 0;

            foreach ($menu_items as $item) {
                $translated_text = $translated_texts[$text_index++];
                $translated_alt = $translated_texts[$text_index++];
                echo '<a href="' . htmlspecialchars($item['href']) . '" class="grid-item">';
                echo '<div class="category-image-container">';
                echo '<img src="' . htmlspecialchars($item['img']) . '" alt="' . htmlspecialchars($translated_alt) . '" class="category-image" loading="lazy">';
                echo '</div>';
                echo '<div class="category-details">';
                echo '<h2 class="category-name" data-translate="' . htmlspecialchars($item['key']) . '">' . htmlspecialchars($translated_text) . '</h2>';
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