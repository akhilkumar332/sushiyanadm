<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
header('Content-Type: text/html; charset=UTF-8');

// Get language from GET or session, default to 'de'
$current_lang = isset($_GET['lang']) ? $_GET['lang'] : ($_SESSION['language'] ?? 'de');

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

// Translate category names
$texts_to_translate = array_column($warmekueche_items, 'text');
$translated_texts = translateText($texts_to_translate, 'de', $current_lang, $conn);

if (count($translated_texts) !== count($texts_to_translate)) {
    die('Error: Translation count mismatch.');
}

ob_start();
foreach ($warmekueche_items as $index => $item) {
    $translated_name = $translated_texts[$index];
    echo '<a href="' . htmlspecialchars($item['href']) . '" class="grid-item">';
    echo '<div class="category-image-container">';
    echo '<img src="' . htmlspecialchars($item['img']) . '" alt="' . htmlspecialchars($item['alt']) . '" class="category-image" loading="lazy">';
    echo '</div>';
    echo '<div class="category-details">';
    echo '<h2 class="category-name" data-translate="' . htmlspecialchars($item['key']) . '">' . htmlspecialchars($translated_name) . '</h2>';
    echo '</div>';
    echo '</a>';
}
$output = ob_get_clean();
echo $output;