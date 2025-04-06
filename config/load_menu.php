<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
header('Content-Type: text/html; charset=UTF-8');

// Get current language from session or query string (for AJAX), prioritizing query string
$current_lang = isset($_GET['lang']) ? $_GET['lang'] : ($_SESSION['language'] ?? 'de');

$menu_items = [
    ['href' => MENU_YANA . '?category=suppen', 'img' => ASSETS_IMAGES . 'startseite/Suppe.jpg', 'alt' => 'Suppen', 'text' => 'Suppe', 'key' => 'soup'],
    ['href' => MENU_YANA . '?category=fingerfood', 'img' => ASSETS_IMAGES . 'startseite/gegrillt.jpg', 'alt' => 'Vorspeisen', 'text' => 'Vorspeise', 'key' => 'starter'],
    ['href' => MENU_YANA . '?category=gyoza', 'img' => ASSETS_IMAGES . 'startseite/Gyoza.jpg', 'alt' => 'Gyoza', 'text' => 'Gyoza', 'key' => 'gyoza'],
    ['href' => MENU_YANA . '?category=salate', 'img' => ASSETS_IMAGES . 'startseite/glasnudelsalat_scampis.jpg', 'alt' => 'Salate', 'text' => 'Salat', 'key' => 'salad'],
    ['href' => MENU_YANA . '?category=sommerrollen', 'img' => ASSETS_IMAGES . 'startseite/Sommerrolle_Ebi.jpg', 'alt' => 'Sommerrolle', 'text' => 'Sommerrolle', 'key' => 'summer_roll'],
    ['href' => BASE_PATH . 'sushi.php', 'img' => ASSETS_IMAGES . 'startseite/sushi.jpg', 'alt' => 'Sushi', 'text' => 'Sushi', 'key' => 'sushi'],
    ['href' => BASE_PATH . 'warmekueche.php', 'img' => ASSETS_IMAGES . 'startseite/Nudeln.jpg', 'alt' => 'Warme Küche', 'text' => 'Warme Küche', 'key' => 'warm_kitchen'],
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

ob_start();
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
$output = ob_get_clean();
echo $output;
?>