<?php
header('Content-Type: text/html; charset=UTF-8');

// Sushi menu data (static array)
$sushi_items = [
    ['href' => 'sushi/menu.php?category=menues', 'img' => 'bilder/sushi/Ebi_Menu.jpg', 'alt' => 'Menüs', 'text' => 'Menüs'],
    ['href' => 'sushi/menu.php?category=sashimi', 'img' => 'bilder/sushi/Sashimi_Sake.jpg', 'alt' => 'Sashimi', 'text' => 'Sashimi'],
    ['href' => 'sushi/menu.php?category=makis', 'img' => 'bilder/sushi/maki.jpg', 'alt' => 'Makis', 'text' => 'Makis'],
    ['href' => 'sushi/menu.php?category=insideoutrolls', 'img' => 'bilder/sushi/ioroll.jpg', 'alt' => 'Inside Out Rolls', 'text' => 'Inside Out Rolls'],
    ['href' => 'sushi/menu.php?category=miniyanarolls', 'img' => 'bilder/sushi/miniyanaroll.jpg', 'alt' => 'Mini Yana Rolls', 'text' => 'Mini Yana Rolls'],
    ['href' => 'sushi/menu.php?category=yanarolls', 'img' => 'bilder/sushi/yanaroll.jpg', 'alt' => 'Yana Rolls', 'text' => 'Yana Rolls'],
    ['href' => 'sushi/menu.php?category=nigiris', 'img' => 'bilder/sushi/Nigiris_Head.jpg', 'alt' => 'Nigiris', 'text' => 'Nigiris'],
    ['href' => 'sushi/menu.php?category=specialrolls', 'img' => 'bilder/sushi/special.jpg', 'alt' => 'Special Rolls', 'text' => 'Special Rolls'],
    ['href' => 'sushi/menu.php?category=temaki', 'img' => 'bilder/sushi/temaki.jpg', 'alt' => 'Temaki', 'text' => 'Temaki'],
    ['href' => 'sushi/vegetarisch.php', 'img' => 'bilder/sushi/Maki_Wakame.jpg', 'alt' => 'Vegetarisch', 'text' => 'Vegetarisch'],
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