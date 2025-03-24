<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
header('Content-Type: text/html; charset=UTF-8');

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

ob_start();
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
$output = ob_get_clean();
echo $output;