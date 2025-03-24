<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
header('Content-Type: text/html; charset=UTF-8');

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

ob_start();
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
$output = ob_get_clean();
echo $output;