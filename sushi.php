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
    <title>Digitale Speisekarte - Sushi</title>
    <link rel="stylesheet" href="<?php echo ASSETS_CSS; ?>styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preload" href="<?php echo ASSETS_IMAGES; ?>logo.webp" as="image">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="/skripte/skripte.js"></script>
</head>
<body class="navigation" data-page="sushi" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart="<?php echo htmlspecialchars(json_encode($_SESSION['cart'])); ?>">
    <header>
        <a href="<?php echo URL_HOME; ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <div>
        <h1 class="page-title">Sushi</h1>
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
                ['href' => MENU_SUSHI . '?category=menues', 'img' => ASSETS_IMAGES . 'sushi/Ebi_Menu.jpg', 'alt' => 'Menüs', 'text' => 'Menüs'],
                ['href' => MENU_SUSHI . '?category=sashimi', 'img' => ASSETS_IMAGES . 'sushi/Sashimi_Sake.jpg', 'alt' => 'Sashimi', 'text' => 'Sashimi'],
                ['href' => MENU_SUSHI . '?category=makis', 'img' => ASSETS_IMAGES . 'sushi/maki.jpg', 'alt' => 'Makis', 'text' => 'Makis'],
                ['href' => MENU_SUSHI . '?category=insideoutrolls', 'img' => ASSETS_IMAGES . 'sushi/ioroll.jpg', 'alt' => 'Inside Out Rolls', 'text' => 'Inside Out Rolls'],
                ['href' => MENU_SUSHI . '?category=miniyanarolls', 'img' => ASSETS_IMAGES . 'sushi/miniyanaroll.jpg', 'alt' => 'Mini Yana Rolls', 'text' => 'Mini Yana Rolls'],
                ['href' => MENU_SUSHI . '?category=yanarolls', 'img' => ASSETS_IMAGES . 'sushi/yanaroll.jpg', 'alt' => 'Yana Rolls', 'text' => 'Yana Rolls'],
                ['href' => MENU_SUSHI . '?category=nigiris', 'img' => ASSETS_IMAGES . 'sushi/Nigiris_Head.jpg', 'alt' => 'Nigiris', 'text' => 'Nigiris'],
                ['href' => MENU_SUSHI . '?category=specialrolls', 'img' => ASSETS_IMAGES . 'sushi/special.jpg', 'alt' => 'Special Rolls', 'text' => 'Special Rolls'],
                ['href' => MENU_SUSHI . '?category=temaki', 'img' => ASSETS_IMAGES . 'sushi/temaki.jpg', 'alt' => 'Temaki', 'text' => 'Temaki'],
                ['href' => URL_SUSHI_VEGETARISCH, 'img' => ASSETS_IMAGES . 'sushi/Maki_Wakame.jpg', 'alt' => 'Vegetarisch', 'text' => 'Vegetarisch'],
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
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/floating_bar.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/footer.php'; ?>
</body>
</html>