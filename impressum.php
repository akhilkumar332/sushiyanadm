<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

// Ensure cart is initialized
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get current language from session or query parameter, default to 'de'
$current_lang = isset($_GET['lang']) ? $_GET['lang'] : ($_SESSION['language'] ?? 'de');
$_SESSION['language'] = $current_lang;

// Get current branch (will be overridden by JavaScript if changed)
$current_branch = $_SESSION['branch'] ?? 'neukoelln';
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_lang); ?>" dir="<?php echo $current_lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Impressum</title>
    <link rel="stylesheet" href="<?php echo addCacheBuster(ASSETS_CSS . 'styles.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preload" href="<?php echo ASSETS_IMAGES; ?>logo.webp" as="image">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'translate.js'); ?>"></script>
    <script src="<?php echo addCacheBuster(ASSETS_SCRIPTS . 'skripte.js'); ?>"></script>
    <style>
        .legal-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            text-align: <?php echo $current_lang === 'ar' ? 'right' : 'left'; ?>;
        }
        .legal-content h1 { font-size: 2rem; margin-bottom: 2rem; }
        .legal-content p { margin-bottom: 1.5rem; line-height: 1.6; }
        .legal-content a { color: #007bff; text-decoration: none; }
        .legal-content a:hover { text-decoration: underline; }
        .address-block { white-space: pre-line; }
    </style>
</head>
<body class="navigation" data-page="impressum" data-base-path="<?php echo BASE_PATH; ?>" data-session-cart="<?php echo htmlspecialchars(json_encode($_SESSION['cart'])); ?>" data-lang="<?php echo htmlspecialchars($current_lang); ?>" data-branch="<?php echo htmlspecialchars($current_branch); ?>">
    <header>
        <a href="<?php echo URL_HOME . '?lang=' . htmlspecialchars($current_lang); ?>"><img src="<?php echo ASSETS_IMAGES; ?>logo.webp" alt="Restaurant Logo" class="logo"></a>
    </header>
    <main>
        <div class="content legal-content">
            <!-- Content will be populated by translate.js -->
            <div class="loading-spinner" style="display: block;"></div>
        </div>
    </main>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/floating_bar.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/footer.php'; ?>
    <div id="branch-spinner"></div>
</body>
</html>