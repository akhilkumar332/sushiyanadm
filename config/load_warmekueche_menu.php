<?php
header('Content-Type: text/html; charset=UTF-8');

// Warme Küche menu data (static array)
$warmekueche_items = [
    ['href' => 'warmekueche/menu.php?category=nudeln', 'img' => 'bilder/warmekueche/gebratene_nudeln.jpg', 'alt' => 'Gebratene Nudeln', 'text' => 'Gebratene Nudeln'],
    ['href' => 'warmekueche/menu.php?category=reis', 'img' => 'bilder/warmekueche/gebratener_reis.jpg', 'alt' => 'Gebratener Reis', 'text' => 'Gebratener Reis'],
    ['href' => 'warmekueche/menu.php?category=gemuese', 'img' => 'bilder/warmekueche/gebratenes_gemuese.jpg', 'alt' => 'Gebratenes Gemüse', 'text' => 'Gebratenes Gemüse'],
    ['href' => 'warmekueche/menu.php?category=yellowcurry', 'img' => 'bilder/warmekueche/yellow_curry.jpg', 'alt' => 'Yellow Curry', 'text' => 'Yellow Curry'],
    ['href' => 'warmekueche/menu.php?category=mangochutney', 'img' => 'bilder/warmekueche/mango_chutney.jpg', 'alt' => 'Mango Chutney', 'text' => 'Mango Chutney'],
    ['href' => 'warmekueche/menu.php?category=erdnussgericht', 'img' => 'bilder/warmekueche/erdnussgericht.jpg', 'alt' => 'Erdnussgericht', 'text' => 'Erdnussgericht'],
    ['href' => 'warmekueche/menu.php?category=chopsuey', 'img' => 'bilder/warmekueche/chop_suey.jpg', 'alt' => 'Chop Suey', 'text' => 'Chop Suey'],
    ['href' => 'warmekueche/menu.php?category=redcurry', 'img' => 'bilder/warmekueche/red_curry.jpg', 'alt' => 'Red Curry', 'text' => 'Red Curry'],
    ['href' => 'warmekueche/menu.php?category=suesssauersauce', 'img' => 'bilder/warmekueche/suess_sauer_sauce.jpg', 'alt' => 'Süss-Sauer Sauce', 'text' => 'Süss-Sauer Sauce'],
    ['href' => 'warmekueche/menu.php?category=extras', 'img' => 'bilder/warmekueche/Extras.jpg', 'alt' => 'Extras', 'text' => 'Extras'],
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