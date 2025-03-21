<?php
header('Content-Type: text/html; charset=UTF-8');

// Menu data (static array)
$menu_items = [
    ['href' => 'yana/suppen.php', 'img' => 'bilder/startseite/Suppe.jpg', 'alt' => 'Suppen', 'text' => 'Suppe'],
    ['href' => 'yana/fingerfood.php', 'img' => 'bilder/startseite/gegrillt.jpg', 'alt' => 'Vorspeisen', 'text' => 'Vorspeise'],
    ['href' => 'yana/gyoza.php', 'img' => 'bilder/startseite/Gyoza.jpg', 'alt' => 'Gyoza', 'text' => 'Gyoza'],
    ['href' => 'yana/salate.php', 'img' => 'bilder/startseite/glasnudelsalat_scampis.jpg', 'alt' => 'Salate', 'text' => 'Salat'],
    ['href' => 'yana/sommerrollen.php', 'img' => 'bilder/startseite/Sommerrolle_Ebi.jpg', 'alt' => 'Sommerrolle', 'text' => 'Sommerrolle'],
    ['href' => 'sushi.php', 'img' => 'bilder/startseite/sushi.jpg', 'alt' => 'Sushi', 'text' => 'Sushi'],
    ['href' => 'warmekueche.php', 'img' => 'bilder/startseite/Nudeln.jpg', 'alt' => 'Warme Küche', 'text' => 'Warme Küche'],
    ['href' => 'yana/bowls.php', 'img' => 'bilder/startseite/Poke_Bowl.jpg', 'alt' => 'Bowl', 'text' => 'Bowl'],
    ['href' => 'yana/desserts.php', 'img' => 'bilder/startseite/Desserts.jpg', 'alt' => 'Dessert', 'text' => 'Dessert'],
    ['href' => 'yana/extras.php', 'img' => 'bilder/startseite/Extra_Soßen.jpg', 'alt' => 'Extras', 'text' => 'Extras'],
    ['href' => 'yana/getraenke.php', 'img' => 'bilder/startseite/Getraenke.jpg', 'alt' => 'Getränke', 'text' => 'Getränke'],
    ['href' => 'yana/warmgetraenke.php', 'img' => 'bilder/startseite/kaffee.jpg', 'alt' => 'Warme Getränke', 'text' => 'Warme Getränke'],
];

ob_start();
foreach ($menu_items as $item) {
    echo '<a href="' . htmlspecialchars($item['href']) . '" class="grid-item">';
    echo '<img src="' . htmlspecialchars($item['img']) . '" alt="' . htmlspecialchars($item['alt']) . '" loading="lazy">';
    echo '<div class="text">' . htmlspecialchars($item['text']) . '</div>';
    echo '</a>';
}
ob_end_flush();
?>