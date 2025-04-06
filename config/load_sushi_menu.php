<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
header('Content-Type: text/html; charset=UTF-8');

// Get current language from session or query string (for AJAX), prioritizing query string
$current_lang = isset($_GET['lang']) ? $_GET['lang'] : ($_SESSION['language'] ?? 'de');

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
    ['href' => URL_SUSHI_VEGETARISCH, 'img' => ASSETS_IMAGES . 'sushi/Maki_Wakame.jpg', 'alt' => 'Vegetarisch', 'text' => 'Vegetarisch', 'key' => 'vegetarian'],
];

$texts_to_translate = [];
foreach ($sushi_items as $item) {
    $texts_to_translate[] = $item['text'];
    $texts_to_translate[] = $item['alt'];
}
$translated_texts = translateText($texts_to_translate, 'de', $current_lang, $conn);
$text_index = 0;

ob_start();
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
$output = ob_get_clean();
echo $output;
?>