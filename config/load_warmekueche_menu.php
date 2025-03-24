<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
header('Content-Type: text/html; charset=UTF-8');

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

ob_start();
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
$output = ob_get_clean();
echo $output;