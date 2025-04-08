<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Prioritize query parameter 'lang' over session, then default to 'de'
$current_lang = isset($_GET['lang']) ? $_GET['lang'] : ($_SESSION['language'] ?? 'de');
$_SESSION['language'] = $current_lang; // Sync session with current language

// Define categories with titles and suffixes
$categories = [
    'menues' => ['title' => 'Menüs', 'suffix' => ''],
    'makis' => ['title' => 'Makis', 'suffix' => ' <span class="anzahl">- je 6 Stück</span>'],
    'insideoutrolls' => ['title' => 'Inside Out Rolls', 'suffix' => ' <span class="anzahl">- je 8 Stück</span>'],
    'miniyanarolls' => ['title' => 'Mini Yana Rolls', 'suffix' => ' <span class="anzahl">- je 6 Stück</span>'],
    'yanarolls' => ['title' => 'Yana Rolls', 'suffix' => ' <span class="anzahl">- je 6 Stück</span>'],
    'nigiris' => ['title' => 'Nigiris', 'suffix' => ' <span class="anzahl">- 1 Stück</span>'],
    'specialrolls' => ['title' => 'Special Rolls', 'suffix' => ' <span class="anzahl">- je 8 Stück</span>'],
    'temaki' => ['title' => 'Temaki', 'suffix' => ' <span class="anzahl">- 1 Stück</span>'],
];

// Translate category titles
$texts_to_translate = array_column($categories, 'title');
$translated_texts = translateText($texts_to_translate, 'de', $current_lang, $conn);
$translated_categories = [];
foreach ($categories as $table => $data) {
    $translated_categories[$table] = [
        'title' => array_shift($translated_texts),
        'suffix' => $data['suffix']
    ];
}
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title data-translate="vegetarian_page_title">Digitale Speisekarte - Vegetarisch</title>
    <link rel="stylesheet" href="<?php echo addCacheBuster(ASSETS_CSS . 'styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo addCacheBuster(ASSETS_CSS . 'style-menu.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'translate.js'); ?>"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'skripte.js'); ?>"></script>
</head>
<body class="artikelliste" data-page="sushi_vegetarisch" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart="<?php echo htmlspecialchars(json_encode($_SESSION['cart'])); ?>" data-lang="<?php echo htmlspecialchars($current_lang); ?>">
    <header>
        <a href="<?php echo URL_HOME . '?lang=' . htmlspecialchars($current_lang); ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <div class="content">
        <?php
        foreach ($translated_categories as $table => $data) {
            $sql = "SELECT *, '$table' AS source_table FROM " . mysqli_real_escape_string($conn, $table) . " WHERE vegetarisch = 1 ORDER BY artikelnummer ASC";
            $result = $conn->query($sql);
            if (!$result) {
                echo "<p>Error executing query for $table: " . $conn->error . "</p>";
                continue;
            }
            echo '<h1>' . htmlspecialchars($data['title']) . $data['suffix'] . '</h1>';
            include $_SERVER['DOCUMENT_ROOT'] . '/config/artikelliste.php';
        }
        ?>
    </div>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/floating_bar.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/footer.php'; ?>
    <div id="branch-spinner"></div>
    <?php $conn->close(); ?>
</body>
</html>