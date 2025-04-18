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
    <title data-translate="sushi_page_title">Digitale Speisekarte - Sushi</title>
    <link rel="stylesheet" href="<?php echo addCacheBuster(ASSETS_CSS . 'styles.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preload" href="<?php echo ASSETS_IMAGES; ?>logo.webp" as="image">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'translate.js'); ?>"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'skripte.js'); ?>"></script>
</head>
<body class="navigation" data-page="sushi" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart="<?php echo htmlspecialchars(json_encode($_SESSION['cart'])); ?>" data-lang="<?php echo htmlspecialchars($current_lang); ?>">
    <header>
        <a href="<?php echo URL_HOME . '?lang=' . htmlspecialchars($current_lang); ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <?php
    $page_title = 'Sushi';
    $data_translate = 'sushi';
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
            $sushi_items = [
                ['href' => MENU_SUSHI . '?category=menues', 'img' => ASSETS_IMAGES . 'sushi/Ebi_Menu.jpg', 'alt' => 'Menüs', 'text' => 'Menüs', 'key' => 'menus'],
                ['href' => MENU_SUSHI . '?category=sashimi', 'img' => ASSETS_IMAGES . 'sushi/Sashimi_Sake.jpg', 'alt' => 'Sashimi', 'text' => 'Sashimi', 'key' => 'sashimi'],
                ['href' => MENU_SUSHI . '?category=makis', 'img' => ASSETS_IMAGES . 'sushi/maki.jpg', 'alt' => 'Makis', 'text' => 'Makis', 'key' => 'makis'],
                ['href' => MENU_SUSHI . '?category=insideoutrolls', 'img' => ASSETS_IMAGES . 'sushi/ioroll.jpg', 'alt' => 'Inside Out Rolls', 'text' => 'Inside Out Rolls', 'key' => 'inside_out_rolls'],
                ['href' => MENU_SUSHI . '?category=miniyanarolls', 'img' => ASSETS_IMAGES . 'sushi/miniyanaroll.jpg', 'alt' => 'Mini Yana Rolls', 'text' => 'Mini Yana Rolls', 'key' => 'mini_yana_rolls'],
                ['href' => MENU_SUSHI . '?category=yanarolls', 'img' => ASSETS_IMAGES . 'sushi/yanaroll.jpg', 'alt' => 'Yana Rolls', 'text' => 'Yana Rolls', 'key' => 'yana_rolls'],
                ['href' => MENU_SUSHI . '?category=nigiris', 'img' => ASSETS_IMAGES . 'sushi/Nigiris_Head.jpg', 'alt' => 'Nigiris', 'text' => 'Nigiris', 'key' => 'nigiris'],
                ['href' => MENU_SUSHI . '?category=specialrolls', 'img' => ASSETS_IMAGES . 'sushi/special.jpg', 'alt' => 'Special Rolls', 'text' => 'Special Rolls', 'key' => 'special_rolls'],
                ['href' => MENU_SUSHI . '?category=temaki', 'img' => ASSETS_IMAGES . 'sushi/temaki.jpg', 'alt' => 'Temaki', 'text' => 'Temaki', 'key' => 'temaki'],
                ['href' => URL_SUSHI_VEGETARISCH, 'img' => ASSETS_IMAGES . 'sushi/Maki_Wakame.jpg', 'alt' => 'Vegetarisches Sushi', 'text' => 'Vegetarisches Sushi', 'key' => 'vegetarian'],
            ];
            $texts_to_translate = [];
            foreach ($sushi_items as $item) {
                $texts_to_translate[] = $item['text'];
                $texts_to_translate[] = $item['alt'];
            }
            $translated_texts = translateText($texts_to_translate, 'de', $current_lang, $conn);
            $text_index = 0;

            foreach ($sushi_items as $item) {
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