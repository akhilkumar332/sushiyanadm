<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Prioritize query parameter 'lang' over session, then default to 'de'
$current_lang = isset($_GET['lang']) ? $_GET['lang'] : ($_SESSION['language'] ?? 'de');
$_SESSION['language'] = $current_lang; // Sync session with current language

// Define allowed categories with translation keys
$categories = [
    'suppen' => ['title' => 'Suppen', 'key' => 'soup'],
    'fingerfood' => ['title' => 'Vorspeise', 'key' => 'starter'],
    'gyoza' => ['title' => 'Gyoza', 'key' => 'gyoza'],
    'salate' => ['title' => 'Salat', 'key' => 'salad'],
    'sommerrollen' => ['title' => 'Sommerrolle', 'key' => 'summer_roll'],
    'bowls' => ['title' => 'Bowl', 'key' => 'bowl'],
    'desserts' => ['title' => 'Dessert', 'key' => 'dessert'],
    'extras' => ['title' => 'Extras', 'key' => 'extras'],
    'getraenke' => ['title' => 'Getränke', 'key' => 'drinks'],
    'warmgetraenke' => ['title' => 'Warme Getränke', 'key' => 'warm_drinks'],
];

// Get the category from the URL parameter
$category = isset($_GET['category']) ? strtolower($_GET['category']) : '';

// Validate the category
if (!array_key_exists($category, $categories)) {
    header('Location: ' . URL_HOME . '?lang=' . htmlspecialchars($current_lang));
    exit;
}

// Set the table and translated title
$table = $category;
$texts_to_translate = [$categories[$category]['title']];
$translated_texts = translateText($texts_to_translate, 'de', $current_lang, $conn);
$title = $translated_texts[0] ?? $categories[$category]['title'];
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title data-translate="yana_menu_page_title">Digitale Speisekarte - <?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="<?php echo addCacheBuster(ASSETS_CSS . 'styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo addCacheBuster(ASSETS_CSS . 'style-menu.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'translate.js'); ?>"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'skripte.js'); ?>"></script>
</head>
<body class="artikelliste" data-page="yana_menu" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart="<?php echo htmlspecialchars(json_encode($_SESSION['cart'])); ?>" data-table="<?php echo htmlspecialchars($table); ?>" data-lang="<?php echo htmlspecialchars($current_lang); ?>">
    <header>
        <a href="<?php echo URL_HOME . '?lang=' . htmlspecialchars($current_lang); ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <?php 
    $page_title = 'Speisekarte';
    $data_translate = 'page_title';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/config/page-title.php'; 
    ?>
    <div class="content">
        <h1 data-translate="<?php echo htmlspecialchars($categories[$category]['key']); ?>"><?php echo htmlspecialchars($title); ?></h1>
        <?php
        $sql = "SELECT * FROM " . mysqli_real_escape_string($conn, $table) . " ORDER BY artikelnummer ASC";
        $result = $conn->query($sql);
        include $_SERVER['DOCUMENT_ROOT'] . '/config/artikelliste.php';
        ?>
    </div>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/floating_bar.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/footer.php'; ?>
    <div id="branch-spinner"></div>
    <?php $conn->close(); ?>
</body>
</html>